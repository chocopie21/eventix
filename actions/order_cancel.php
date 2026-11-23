<?php
/**
 * EVENTIX - Action: Batalkan Pesanan (Pengguna)
 * Membatalkan pesanan yang belum lunas (status menunggu atau menunggu_verifikasi)
 * dan mengembalikan seluruh kuota tiket ke sistem.
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
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan coba kembali.');
    redirect('my_orders.php');
}

$idPesanan = filter_input(INPUT_POST, 'id_pesanan', FILTER_VALIDATE_INT);
$user = currentUser();

if (!$idPesanan || !$user) {
    setFlash('error', 'ID Pesanan tidak valid.');
    redirect('my_orders.php');
}

try {
    $pdo->beginTransaction();

    // 1. Ambil pesanan milik user
    $stmtFind = $pdo->prepare("SELECT id_pesanan, status FROM pesanan WHERE id_pesanan = :id AND id_pengguna = :uid LIMIT 1");
    $stmtFind->execute([
        ':id'  => $idPesanan,
        ':uid' => (int)$user['id_pengguna']
    ]);
    $order = $stmtFind->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        setFlash('error', 'Pesanan tidak ditemukan atau tidak sesuai akun Anda.');
        redirect('my_orders.php');
    }

    if ($order['status'] === 'lunas') {
        $pdo->rollBack();
        setFlash('error', 'Pesanan yang sudah lunas tidak dapat dibatalkan secara mandiri. Silakan hubungi admin.');
        redirect('my_orders.php');
    }

    if ($order['status'] === 'ditolak') {
        $pdo->rollBack();
        setFlash('info', 'Pesanan ini sudah berstatus batal sebelumnya.');
        redirect('my_orders.php');
    }

    // 2. Kembalikan kuota tiket
    $stmtItems = $pdo->prepare("SELECT id_tiket, jumlah FROM detail_pesanan WHERE id_pesanan = :id");
    $stmtItems->execute([':id' => $idPesanan]);
    $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

    $stmtRestore = $pdo->prepare("UPDATE tiket 
                                  SET kuota_tersedia = kuota_tersedia + :jumlah 
                                  WHERE id_tiket = :id_tiket");
    $restoredCount = 0;
    foreach ($items as $item) {
        $qty = (int)$item['jumlah'];
        $stmtRestore->execute([
            ':jumlah'   => $qty,
            ':id_tiket' => (int)$item['id_tiket']
        ]);
        $restoredCount += $qty;
    }

    // 3. Update status pesanan dan pembayaran menjadi 'ditolak'
    $stmtUpOrder = $pdo->prepare("UPDATE pesanan SET status = 'ditolak' WHERE id_pesanan = :id");
    $stmtUpOrder->execute([':id' => $idPesanan]);

    $stmtUpPay = $pdo->prepare("UPDATE pembayaran SET status = 'ditolak' WHERE id_pesanan = :id");
    $stmtUpPay->execute([':id' => $idPesanan]);

    $pdo->commit();

    setFlash('info', "Pesanan #EVX-{$idPesanan} telah berhasil dibatalkan. Kuota {$restoredCount} tiket telah dikembalikan ke sistem.");
    redirect('my_orders.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Order Cancel Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat membatalkan pesanan.');
    redirect('my_orders.php');
}
