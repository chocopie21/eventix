<?php
/**
 * EVENTIX - Action: Simpan / Edit Konser Musik
 * Wajib role Admin, memproses input data ke tabel event dan konser secara simultan via PDO Transaction,
 * serta menangani upload file poster ke assets/uploads/posters/.
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
    setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan ulangi penyimpanan form.');
    redirect('admin/index.php');
}

$idEvent       = filter_input(INPUT_POST, 'id_event', FILTER_VALIDATE_INT) ?: 0;
$namaEvent     = trim($_POST['nama_event'] ?? '');
$venue         = trim($_POST['venue'] ?? '');
$tanggalEvent  = trim($_POST['tanggal_event'] ?? '');
$jamEvent      = trim($_POST['jam_event'] ?? '');
$pengisiAcara  = trim($_POST['pengisi_acara'] ?? '');
$genreMusik    = trim($_POST['genre_musik'] ?? '');
$daftarLagu    = trim($_POST['daftar_lagu'] ?? '');
$deskripsi     = trim($_POST['deskripsi'] ?? '');

// Validasi input wajib
if (empty($namaEvent) || empty($venue) || empty($tanggalEvent) || empty($jamEvent) || empty($pengisiAcara) || empty($genreMusik)) {
    setFlash('error', 'Harap isi seluruh field wajib (Nama Konser, Venue, Tanggal, Jam, Pengisi Acara, dan Genre).');
    redirect('admin/index.php');
}

// Siapkan folder uploads/posters/
$postersDir = __DIR__ . '/../assets/uploads/posters/';
if (!is_dir($postersDir)) {
    mkdir($postersDir, 0755, true);
}

$posterFilename = null;

// Cek apakah ada file poster baru yang diupload
if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath   = $_FILES['poster']['tmp_name'];
    $fileName      = $_FILES['poster']['name'];
    $fileSize      = $_FILES['poster']['size'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    $allowedExt = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($fileExtension, $allowedExt, true)) {
        setFlash('error', 'Format poster tidak didukung. Harap upload format JPG, JPEG, PNG, atau WEBP.');
        redirect('admin/index.php');
    }

    if ($fileSize > 3 * 1024 * 1024) {
        setFlash('error', 'Ukuran file poster maksimal adalah 3 MB.');
        redirect('admin/index.php');
    }

    $posterFilename = 'poster_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
    $destination = $postersDir . $posterFilename;

    if (!move_uploaded_file($fileTmpPath, $destination)) {
        setFlash('error', 'Gagal menyimpan file poster ke server.');
        redirect('admin/index.php');
    }
}

// Proses PDO Transaction untuk menyimpan ke event dan konser sekaligus
try {
    $pdo->beginTransaction();

    if ($idEvent > 0) {
        // Mode UPDATE Konser yang sudah ada
        if ($posterFilename !== null) {
            $sqlEvent = "UPDATE event 
                         SET venue = :venue, nama_event = :nama, tanggal_event = :tanggal, 
                             jam_event = :jam, pengisi_acara = :pengisi, deskripsi = :deskripsi, 
                             poster = :poster 
                         WHERE id_event = :id";
            $stmtEvent = $pdo->prepare($sqlEvent);
            $stmtEvent->execute([
                ':venue'     => $venue,
                ':nama'      => $namaEvent,
                ':tanggal'   => $tanggalEvent,
                ':jam'       => $jamEvent,
                ':pengisi'   => $pengisiAcara,
                ':deskripsi' => $deskripsi,
                ':poster'    => $posterFilename,
                ':id'        => $idEvent
            ]);
        } else {
            $sqlEvent = "UPDATE event 
                         SET venue = :venue, nama_event = :nama, tanggal_event = :tanggal, 
                             jam_event = :jam, pengisi_acara = :pengisi, deskripsi = :deskripsi 
                         WHERE id_event = :id";
            $stmtEvent = $pdo->prepare($sqlEvent);
            $stmtEvent->execute([
                ':venue'     => $venue,
                ':nama'      => $namaEvent,
                ':tanggal'   => $tanggalEvent,
                ':jam'       => $jamEvent,
                ':pengisi'   => $pengisiAcara,
                ':deskripsi' => $deskripsi,
                ':id'        => $idEvent
            ]);
        }

        // Update tabel relasi konser (daftar lagu & genre)
        $sqlKonser = "UPDATE konser 
                      SET daftar_lagu = :lagu, genre_musik = :genre 
                      WHERE id_event = :id";
        $stmtKonser = $pdo->prepare($sqlKonser);
        $stmtKonser->execute([
            ':lagu'  => $daftarLagu,
            ':genre' => $genreMusik,
            ':id'    => $idEvent
        ]);

        $pdo->commit();
        setFlash('success', "Data konser '{$namaEvent}' berhasil diperbarui.");

    } else {
        // Mode INSERT Konser Baru
        $defaultPoster = $posterFilename ?? 'default.jpg';
        $sqlEvent = "INSERT INTO event (venue, nama_event, tanggal_event, jam_event, pengisi_acara, deskripsi, poster) 
                     VALUES (:venue, :nama, :tanggal, :jam, :pengisi, :deskripsi, :poster)";
        $stmtEvent = $pdo->prepare($sqlEvent);
        $stmtEvent->execute([
            ':venue'     => $venue,
            ':nama'      => $namaEvent,
            ':tanggal'   => $tanggalEvent,
            ':jam'       => $jamEvent,
            ':pengisi'   => $pengisiAcara,
            ':deskripsi' => $deskripsi,
            ':poster'    => $defaultPoster
        ]);

        $newIdEvent = (int)$pdo->lastInsertId();

        // Insert ke tabel relasi konser
        $sqlKonser = "INSERT INTO konser (id_event, daftar_lagu, genre_musik) 
                      VALUES (:id, :lagu, :genre)";
        $stmtKonser = $pdo->prepare($sqlKonser);
        $stmtKonser->execute([
            ':id'    => $newIdEvent,
            ':lagu'  => $daftarLagu,
            ':genre' => $genreMusik
        ]);

        $pdo->commit();
        setFlash('success', "Konser baru '{$namaEvent}' berhasil ditambahkan ke katalog!");
    }

    redirect('admin/index.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Admin Event Save Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat menyimpan konser: ' . $e->getMessage());
    redirect('admin/index.php');
}
