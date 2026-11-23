<?php
/**
 * EVENTIX - Action: Update Status Pesan Kontak (Admin)
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
$targetStatus = trim($_POST['status'] ?? 'dibaca');

if (!$idPesan || !in_array($targetStatus, ['belum_dibaca', 'dibaca'], true)) {
    setFlash('error', 'Data permintaan status pesan tidak valid.');
    redirect('admin/messages.php');
}

try {
    $stmt = $pdo->prepare("UPDATE pesan_kontak SET status = :status WHERE id_pesan = :id");
    $stmt->execute([
        ':status' => $targetStatus,
        ':id'     => $idPesan
    ]);

    $label = ($targetStatus === 'dibaca') ? 'sudah dibaca' : 'belum dibaca';
    setFlash('success', "Pesan #{$idPesan} berhasil ditandai sebagai {$label}.");
    redirect('admin/messages.php');

} catch (PDOException $e) {
    error_log("[Admin Message Status Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat memperbarui status pesan.');
    redirect('admin/messages.php');
}
