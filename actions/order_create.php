<?php
/**
 * EVENTIX - Action: Buat Pesanan Tiket Konser
 * Wajib login, memproses transaksi pemesanan dengan PDO::beginTransaction(),
 * lock baris kuota dengan SELECT ... FOR UPDATE untuk mencegah overselling/race condition,
 * mengurangi kuota tiket, dan menyimpan ke tabel pesanan serta detail_pesanan.
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib login sebelum melakukan checkout/booking
if (!isLoggedIn()) {
    $idEvent = (int)($_POST['id_event'] ?? 0);
    // Simpan tujuan redirect setelah login
    if ($idEvent > 0) {
        $_SESSION['redirect_after_login'] = "detail_event.php?id={$idEvent}";
    }
    setFlash('error', 'Silakan masuk ke akun Anda terlebih dahulu untuk memesan tiket konser.');
    redirect('login.php');
}

// 2. Pastikan request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('index.php');
}

// 3. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan Anda kedaluwarsa. Silakan ulangi pemesanan.');
    redirect('index.php');
}

$user = currentUser();
$idPengguna = (int)$user['id_pengguna'];
$idEvent = (int)($_POST['id_event'] ?? 0);
$ticketsInput = $_POST['tickets'] ?? []; // Array [id_tiket => jumlah]

if ($idEvent <= 0) {
    setFlash('error', 'Data konser tidak valid.');
    redirect('index.php');
}

// Filter hanya tiket yang kuantitasnya > 0
$selectedTickets = [];
foreach ($ticketsInput as $tId => $qty) {
    $ticketId = (int)$tId;
    $quantity = (int)$qty;
    if ($ticketId > 0 && $quantity > 0) {
        $selectedTickets[$ticketId] = $quantity;
    }
}

if (empty($selectedTickets)) {
    setFlash('error', 'Silakan tentukan minimal 1 tiket konser yang ingin Anda pesan.');
    redirect("detail_event.php?id={$idEvent}");
}

// 4. Proses Transaksi Database dengan PDO Transaction & Row Locking
try {
    $pdo->beginTransaction();

    $totalHargaPesanan = 0;
    $validatedOrderItems = [];

    // Validasi dan kunci baris tiket masing-masing
    foreach ($selectedTickets as $ticketId => $qty) {
        // Kunci baris tiket dengan SELECT ... FOR UPDATE
        $lockStmt = $pdo->prepare("SELECT id_tiket, id_event, kategori, kuota_tersedia, harga 
                                   FROM tiket 
                                   WHERE id_tiket = :id_tiket AND id_event = :id_event 
                                   FOR UPDATE");
        $lockStmt->execute([
            ':id_tiket' => $ticketId,
            ':id_event' => $idEvent
        ]);
        $ticketData = $lockStmt->fetch(PDO::FETCH_ASSOC);

        if (!$ticketData) {
            $pdo->rollBack();
            setFlash('error', 'Kategori tiket yang dipilih tidak ditemukan pada konser ini.');
            redirect("detail_event.php?id={$idEvent}");
        }

        $currentQuota = (int)$ticketData['kuota_tersedia'];
        $ticketPrice = (float)$ticketData['harga'];
        $categoryName = $ticketData['kategori'];

        // Cek apakah kuota mencukupi
        if ($currentQuota < $qty) {
            $pdo->rollBack();
            setFlash('error', "Mohon maaf, kuota tiket '{$categoryName}' tidak mencukupi (sisa kuota saat ini: {$currentQuota} tiket).");
            redirect("detail_event.php?id={$idEvent}");
        }

        $subTotal = $qty * $ticketPrice;
        $totalHargaPesanan += $subTotal;

        $validatedOrderItems[] = [
            'id_tiket'   => $ticketId,
            'kategori'   => $categoryName,
            'jumlah'     => $qty,
            'sub_total'  => $subTotal
        ];

        // Kurangi kuota tiket di database
        $updateQuotaStmt = $pdo->prepare("UPDATE tiket 
                                          SET kuota_tersedia = kuota_tersedia - :qty 
                                          WHERE id_tiket = :id_tiket");
        $updateQuotaStmt->execute([
            ':qty'      => $qty,
            ':id_tiket' => $ticketId
        ]);
    }

    // Simpan ke tabel pesanan (status: 'menunggu')
    $insertOrderStmt = $pdo->prepare("INSERT INTO pesanan (id_pengguna, tanggal, harga, status) 
                                      VALUES (:id_pengguna, NOW(), :harga, 'menunggu')");
    $insertOrderStmt->execute([
        ':id_pengguna' => $idPengguna,
        ':harga'       => $totalHargaPesanan
    ]);

    $idPesanan = (int)$pdo->lastInsertId();

    // Simpan detail tiap tiket ke tabel detail_pesanan
    $insertDetailStmt = $pdo->prepare("INSERT INTO detail_pesanan (id_pesanan, id_tiket, jumlah, sub_total) 
                                       VALUES (:id_pesanan, :id_tiket, :jumlah, :sub_total)");
    foreach ($validatedOrderItems as $item) {
        $insertDetailStmt->execute([
            ':id_pesanan' => $idPesanan,
            ':id_tiket'   => $item['id_tiket'],
            ':jumlah'     => $item['jumlah'],
            ':sub_total'  => $item['sub_total']
        ]);
    }

    // Komit transaksi
    $pdo->commit();

    setFlash('success', 'Pesanan berhasil dibuat! Silakan konfirmasi metode pembayaran dan unggah bukti transfer.');
    redirect("checkout.php?id_pesanan={$idPesanan}");

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Order Create Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat memproses pemesanan tiket Anda. Silakan coba kembali.');
    redirect("detail_event.php?id={$idEvent}");
}
