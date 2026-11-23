<?php
/**
 * EVENTIX - Action: Hapus Pesan Kontak (Admin)
 */

require_once __DIR__ . '/../config/app.php';

requireAdmin('login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/messages.php');
}

if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan coba kembali.');
    redirect('admin/messages.php');
}

$idPesan = filter_input(INPUT_POST, 'id_pesan', FILTER_VALIDATE_INT);

if (!$idPesan) {
    setFlash('error', 'ID Pesan tidak valid.');
    redirect('admin/messages.php');
}

try {
    $stmt = $pdo->prepare("DELETE FROM pesan_kontak WHERE id_pesan = :id");
    $stmt->execute([':id' => $idPesan]);

    setFlash('success', "Pesan #{$idPesan} berhasil dihapus dari sistem.");
    redirect('admin/messages.php');

} catch (PDOException $e) {
    error_log("[Admin Message Delete Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat menghapus pesan.');
    redirect('admin/messages.php');
}
