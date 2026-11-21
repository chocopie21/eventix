<?php
/**
 * EVENTIX - Action: Simpan / Edit Kategori & Kuota Tiket
 * Wajib role Admin, validasi kuota_tersedia >= 0 dan harga >= 0.
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

$idTiket       = filter_input(INPUT_POST, 'id_tiket', FILTER_VALIDATE_INT) ?: 0;
$idEvent       = filter_input(INPUT_POST, 'id_event', FILTER_VALIDATE_INT);
$kategori      = trim($_POST['kategori'] ?? '');
$deskripsi     = trim($_POST['deskripsi'] ?? '');
$kuotaTersedia = filter_input(INPUT_POST, 'kuota_tersedia', FILTER_VALIDATE_INT);
$harga         = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_FLOAT);

if (!$idEvent || empty($kategori)) {
    setFlash('error', 'Pilih konser dan isi nama kategori tiket.');
    redirect('admin/tickets.php' . ($idEvent ? "?id_event={$idEvent}" : ''));
}

if ($kuotaTersedia === false || $kuotaTersedia < 0) {
    setFlash('error', 'Kuota tiket tidak boleh bernilai negatif (minimal 0).');
    redirect("admin/tickets.php?id_event={$idEvent}");
}

if ($harga === false || $harga < 0) {
    setFlash('error', 'Harga tiket tidak boleh bernilai negatif.');
    redirect("admin/tickets.php?id_event={$idEvent}");
}

try {
    if ($idTiket > 0) {
        // Mode UPDATE Kategori Tiket
        $sql = "UPDATE tiket 
                SET id_event = :id_event, kategori = :kategori, deskripsi = :deskripsi, 
                    kuota_tersedia = :kuota, harga = :harga 
                WHERE id_tiket = :id_tiket";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_event'  => $idEvent,
            ':kategori'  => $kategori,
            ':deskripsi' => $deskripsi,
            ':kuota'     => $kuotaTersedia,
            ':harga'     => $harga,
            ':id_tiket'  => $idTiket
        ]);

        setFlash('success', "Kategori tiket '{$kategori}' berhasil diperbarui.");
    } else {
        // Mode INSERT Tiket Baru
        $sql = "INSERT INTO tiket (id_event, kategori, deskripsi, kuota_tersedia, harga) 
                VALUES (:id_event, :kategori, :deskripsi, :kuota, :harga)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':id_event'  => $idEvent,
            ':kategori'  => $kategori,
            ':deskripsi' => $deskripsi,
            ':kuota'     => $kuotaTersedia,
            ':harga'     => $harga
        ]);

        setFlash('success', "Kategori tiket baru '{$kategori}' berhasil ditambahkan ke konser!");
    }

    redirect("admin/tickets.php?id_event={$idEvent}");

} catch (PDOException $e) {
    error_log("[Admin Ticket Save Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat menyimpan tiket: ' . $e->getMessage());
    redirect("admin/tickets.php?id_event={$idEvent}");
}
