<?php
/**
 * EVENTIX - Action: Hapus Konser Musik
 * Wajib role Admin, menghapus konser beserta seluruh relasi tiket & konser (CASCADE),
 * serta menghapus file poster kustom jika ada.
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

// 2. Pastikan request method POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin/index.php');
}

// 3. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan coba kembali.');
    redirect('admin/index.php');
}

$idEvent = filter_input(INPUT_POST, 'id_event', FILTER_VALIDATE_INT);

if (!$idEvent) {
    setFlash('error', 'ID Konser tidak valid.');
    redirect('admin/index.php');
}

try {
    // Ambil data event sebelum dihapus untuk mengetahui nama file poster
    $stmtFind = $pdo->prepare("SELECT nama_event, poster FROM event WHERE id_event = :id LIMIT 1");
    $stmtFind->execute([':id' => $idEvent]);
    $event = $stmtFind->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        setFlash('error', 'Konser tidak ditemukan atau sudah dihapus sebelumnya.');
        redirect('admin/index.php');
    }

    $namaEvent = $event['nama_event'];
    $posterFile = $event['poster'];

    // Hapus dari database (Relasi tabel konser, tiket, dan pesanan akan otomatis terhapus via CASCADE)
    $stmtDel = $pdo->prepare("DELETE FROM event WHERE id_event = :id");
    $stmtDel->execute([':id' => $idEvent]);

    // Hapus file fisik poster jika bukan file bawaan demo
    $demoPosters = ['bernadya.jpg', 'dewa_19.jpg', 'hindia.jpg', 'sheila_on_7.jpg', 'tulus.jpg', 'default.jpg'];
    if (!empty($posterFile) && !in_array($posterFile, $demoPosters, true)) {
        $posterPath = __DIR__ . '/../assets/uploads/posters/' . $posterFile;
        if (file_exists($posterPath)) {
            unlink($posterPath);
        }
    }

    setFlash('success', "Konser '{$namaEvent}' dan seluruh kategori tiket terkait berhasil dihapus.");
    redirect('admin/index.php');

} catch (PDOException $e) {
    error_log("[Admin Event Delete Error] " . $e->getMessage());
    setFlash('error', 'Gagal menghapus konser dari database: ' . $e->getMessage());
    redirect('admin/index.php');
}
