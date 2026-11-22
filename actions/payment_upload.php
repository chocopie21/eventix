<?php
/**
 * EVENTIX - Action: Konfirmasi Pembayaran Pesanan
 * Mencatat pembayaran dan memverifikasi pesanan langsung (Lunas),
 * menerbitkan e-ticket tanpa perlu upload bukti fisik.
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib login
requireLogin('login.php');

// 2. Pastikan request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('my_orders.php');
}

// 3. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan Anda kedaluwarsa. Silakan coba kembali.');
    redirect('my_orders.php');
}

$user = currentUser();
$idPengguna = (int)$user['id_pengguna'];
$idPesanan = filter_input(INPUT_POST, 'id_pesanan', FILTER_VALIDATE_INT);
$metode = trim($_POST['metode'] ?? 'Transfer Bank');

if (!$idPesanan || empty($metode)) {
    setFlash('error', 'Data pesanan atau metode pembayaran tidak valid.');
    redirect('my_orders.php');
}

// 4. Verifikasi bahwa pesanan benar-benar milik pengguna yang sedang login
try {
    $orderStmt = $pdo->prepare("SELECT id_pesanan, harga, status FROM pesanan WHERE id_pesanan = :id_pesanan AND id_pengguna = :id_pengguna LIMIT 1");
    $orderStmt->execute([
        ':id_pesanan'  => $idPesanan,
        ':id_pengguna' => $idPengguna
    ]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        setFlash('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki akses ke pesanan ini.');
        redirect('my_orders.php');
    }

    // Jika pesanan sudah lunas, langsung ke riwayat
    if ($order['status'] === 'lunas') {
        setFlash('info', 'Pesanan ini sudah terverifikasi lunas.');
        redirect('my_orders.php');
    }

    $totalHarga = (float)$order['harga'];

    // 5. Simpan transaksi pembayaran langsung tanpa upload bukti fisik
    $pdo->beginTransaction();

    $checkPayment = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = :id_pesanan LIMIT 1");
    $checkPayment->execute([':id_pesanan' => $idPesanan]);
    $existingPayment = $checkPayment->fetch(PDO::FETCH_ASSOC);

    if ($existingPayment) {
        $updatePayStmt = $pdo->prepare("UPDATE pembayaran 
                                        SET tanggal = NOW(), metode = :metode, jumlah = :jumlah, status = 'valid' 
                                        WHERE id_pesanan = :id_pesanan");
        $updatePayStmt->execute([
            ':metode'      => $metode,
            ':jumlah'      => $totalHarga,
            ':id_pesanan'  => $idPesanan
        ]);
    } else {
        $insertPayStmt = $pdo->prepare("INSERT INTO pembayaran (id_pesanan, tanggal, metode, jumlah, status) 
                                        VALUES (:id_pesanan, NOW(), :metode, :jumlah, 'valid')");
        $insertPayStmt->execute([
            ':id_pesanan'  => $idPesanan,
            ':metode'      => $metode,
            ':jumlah'      => $totalHarga
        ]);
    }

    // Ubah status pesanan langsung menjadi 'lunas' (E-Ticket Aktif)
    $updateOrderStmt = $pdo->prepare("UPDATE pesanan SET status = 'lunas' WHERE id_pesanan = :id_pesanan");
    $updateOrderStmt->execute([':id_pesanan' => $idPesanan]);

    $pdo->commit();

    setFlash('success', 'Pembayaran berhasil dikonfirmasi! E-Ticket konser resmi Anda telah aktif.');
    redirect('my_orders.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Payment Confirm Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat memproses konfirmasi pembayaran.');
    redirect("checkout.php?id_pesanan={$idPesanan}");
}
