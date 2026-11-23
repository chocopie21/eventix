<?php
/**
 * EVENTIX - Action: Hapus Pesanan (Admin)
 * Menghapus pesanan secara permanen dan otomatis mengembalikan seluruh kuota tiket ke sistem.
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
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan coba kembali.');
    redirect('admin/orders.php');
}

$idPesanan = filter_input(INPUT_POST, 'id_pesanan', FILTER_VALIDATE_INT);

if (!$idPesanan) {
    setFlash('error', 'ID Pesanan tidak valid.');
    redirect('admin/orders.php');
}

try {
    $pdo->beginTransaction();

    // 1. Cek pesanan
    $stmtFind = $pdo->prepare("SELECT id_pesanan, status FROM pesanan WHERE id_pesanan = :id LIMIT 1");
    $stmtFind->execute([':id' => $idPesanan]);
    $order = $stmtFind->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $pdo->rollBack();
        setFlash('error', 'Pesanan tidak ditemukan atau sudah dihapus sebelumnya.');
        redirect('admin/orders.php');
    }

    $currentStatus = $order['status'];
    $restoredCount = 0;

    // 2. Jika pesanan belum pernah dibatalkan / ditolak, kembalikan kuota tiket
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

    // 3. Ambil file bukti bayar jika ada untuk dihapus fisiknya
    $stmtProof = $pdo->prepare("SELECT bukti_bayar FROM pembayaran WHERE id_pesanan = :id");
    $stmtProof->execute([':id' => $idPesanan]);
    $payments = $stmtProof->fetchAll(PDO::FETCH_ASSOC);

    // 4. Hapus data pesanan dari database (detail_pesanan dan pembayaran otomatis CASCADE terhapus)
    $stmtDel = $pdo->prepare("DELETE FROM pesanan WHERE id_pesanan = :id");
    $stmtDel->execute([':id' => $idPesanan]);

    // 5. Hapus file bukti pembayaran dari disk jika ada
    foreach ($payments as $p) {
        if (!empty($p['bukti_bayar'])) {
            $proofPath = __DIR__ . '/../assets/uploads/proofs/' . $p['bukti_bayar'];
            if (file_exists($proofPath)) {
                @unlink($proofPath);
            }
        }
    }

    $pdo->commit();

    $msg = "Pesanan #EVX-{$idPesanan} berhasil dihapus permanen.";
    if ($restoredCount > 0) {
        $msg .= " Sebanyak {$restoredCount} tiket telah dikembalikan otomatis ke kuota ketersediaan.";
    }
    setFlash('success', $msg);
    redirect('admin/orders.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Admin Order Delete Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat menghapus pesanan: ' . $e->getMessage());
    redirect('admin/orders.php');
}
