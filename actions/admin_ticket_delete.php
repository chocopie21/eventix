<?php
/**
 * EVENTIX - Action: Hapus Kategori Tiket Konser
 * Wajib role Admin, menghapus tiket berdasarkan id_tiket.
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

// 2. Pastikan request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/tickets.php');
}

// 3. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan coba kembali.');
    redirect('admin/tickets.php');
}

$idTiket = filter_input(INPUT_POST, 'id_tiket', FILTER_VALIDATE_INT);

if (!$idTiket) {
    setFlash('error', 'ID Tiket tidak valid.');
    redirect('admin/tickets.php');
}

try {
    // Ambil data id_event dan kategori sebelum dihapus
    $stmtFind = $pdo->prepare("SELECT id_event, kategori FROM tiket WHERE id_tiket = :id LIMIT 1");
    $stmtFind->execute([':id' => $idTiket]);
    $ticket = $stmtFind->fetch(PDO::FETCH_ASSOC);

    if (!$ticket) {
        setFlash('error', 'Tiket tidak ditemukan atau sudah dihapus sebelumnya.');
        redirect('admin/tickets.php');
    }

    $idEvent = (int)$ticket['id_event'];
    $kategori = $ticket['kategori'];

    // Hapus tiket dari database
    $stmtDel = $pdo->prepare("DELETE FROM tiket WHERE id_tiket = :id");
    $stmtDel->execute([':id' => $idTiket]);

    setFlash('success', "Kategori tiket '{$kategori}' berhasil dihapus.");
    redirect("admin/tickets.php?id_event={$idEvent}");

} catch (PDOException $e) {
    error_log("[Admin Ticket Delete Error] " . $e->getMessage());
    setFlash('error', 'Gagal menghapus tiket. Kemungkinan tiket telah terikat dalam transaksi pesanan pengguna.');
    redirect('admin/tickets.php');
}
