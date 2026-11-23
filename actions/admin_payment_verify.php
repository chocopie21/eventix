<?php
/**
 * EVENTIX - Action: Verifikasi Pembayaran & Status Pesanan
 * Wajib role Admin.
 * - Jika ACC (Konfirmasi): update status pembayaran & pesanan menjadi 'lunas'.
 * - Jika Tolak: update status pembayaran & pesanan menjadi 'ditolak',
 *   dan secara otomatis mengembalikan (restore) kuota tiket ke tiket.kuota_tersedia.
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

// 2. Pastikan request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/orders.php');
}

// 3. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan ulangi aksi.');
    redirect('admin/orders.php');
}

$idPesanan = filter_input(INPUT_POST, 'id_pesanan', FILTER_VALIDATE_INT);
$action = trim($_POST['action'] ?? ''); // 'acc' atau 'tolak'

if (!$idPesanan || !in_array($action, ['acc', 'tolak'], true)) {
    setFlash('error', 'Data verifikasi pesanan tidak valid.');
    redirect('admin/orders.php');
}

try {
    // Ambil data pesanan
    $stmtOrder = $pdo->prepare("SELECT id_pesanan, harga, status FROM pesanan WHERE id_pesanan = :id LIMIT 1");
    $stmtOrder->execute([':id' => $idPesanan]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        setFlash('error', 'Pesanan tidak ditemukan.');
        redirect('admin/orders.php');
    }

    $currentStatus = $order['status'];

    // Mulai Transaksi Database PDO
    $pdo->beginTransaction();

    if ($action === 'acc') {
        // ----------------------------------------------------
        // PROSES ACC / KONFIRMASI PEMBAYARAN LUNAS
        // ----------------------------------------------------
        
        // 1. Update status pesanan menjadi 'lunas'
        $stmtUpOrder = $pdo->prepare("UPDATE pesanan SET status = 'lunas' WHERE id_pesanan = :id");
        $stmtUpOrder->execute([':id' => $idPesanan]);

        // 2. Update status pembayaran menjadi 'lunas' jika ada row pembayaran
        $stmtCheckPay = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = :id LIMIT 1");
        $stmtCheckPay->execute([':id' => $idPesanan]);
        $existingPay = $stmtCheckPay->fetch(PDO::FETCH_ASSOC);

        if ($existingPay) {
            $stmtUpPay = $pdo->prepare("UPDATE pembayaran SET status = 'lunas' WHERE id_pesanan = :id");
            $stmtUpPay->execute([':id' => $idPesanan]);
        } else {
            // Jika belum ada row pembayaran (misal disetujui manual oleh admin)
            $stmtInsPay = $pdo->prepare("INSERT INTO pembayaran (id_pesanan, tanggal, metode, jumlah, status) 
                                         VALUES (:id, NOW(), 'Verifikasi Langsung Admin', :jumlah, 'lunas')");
            $stmtInsPay->execute([
                ':id'     => $idPesanan,
                ':jumlah' => $order['harga']
            ]);
        }

        $pdo->commit();
        setFlash('success', "Pesanan #EVX-{$idPesanan} berhasil diverifikasi LUNAS. E-ticket resmi kini aktif untuk pengguna.");

    } elseif ($action === 'tolak') {
        // ----------------------------------------------------
        // PROSES PENOLAKAN & PENGEMBALIAN (RESTORE) KUOTA TIKET
        // ----------------------------------------------------
        
        // 1. Update status pesanan menjadi 'ditolak'
        $stmtUpOrder = $pdo->prepare("UPDATE pesanan SET status = 'ditolak' WHERE id_pesanan = :id");
        $stmtUpOrder->execute([':id' => $idPesanan]);

        // 2. Update status pembayaran menjadi 'ditolak'
        $stmtUpPay = $pdo->prepare("UPDATE pembayaran SET status = 'ditolak' WHERE id_pesanan = :id");
        $stmtUpPay->execute([':id' => $idPesanan]);

        // 3. Kembalikan kuota tiket (Hanya jika pesanan belum pernah lunas/dikembalikan)
        $restoredCount = 0;
        if ($currentStatus !== 'ditolak') {
            $stmtItems = $pdo->prepare("SELECT id_tiket, jumlah FROM detail_pesanan WHERE id_pesanan = :id");
            $stmtItems->execute([':id' => $idPesanan]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $stmtRestore = $pdo->prepare("UPDATE tiket 
                                          SET kuota_tersedia = kuota_tersedia + :jumlah 
                                          WHERE id_tiket = :id_tiket");
            foreach ($items as $item) {
                $qty = (int)$item['jumlah'];
                $stmtRestore->execute([
                    ':jumlah'   => $qty,
                    ':id_tiket' => (int)$item['id_tiket']
                ]);
                $restoredCount += $qty;
            }
        }

        $pdo->commit();
        setFlash('info', "Pesanan #EVX-{$idPesanan} telah DIBATALKAN/DITOLAK dan {$restoredCount} kuota tiket telah dikembalikan otomatis ke sistem ketersediaan.");
    }

    redirect('admin/orders.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Admin Payment Verify Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat memproses verifikasi pesanan: ' . $e->getMessage());
    redirect('admin/orders.php');
}
