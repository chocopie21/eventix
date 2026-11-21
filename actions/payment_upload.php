<?php
/**
 * EVENTIX - Action: Upload Bukti Pembayaran
 * Memvalidasi file gambar bukti transfer, menyimpannya di assets/uploads/payments/,
 * mencatat di tabel pembayaran, dan mengubah status pesanan menjadi 'menunggu_verifikasi'.
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
$metode = trim($_POST['metode'] ?? '');

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

    // Jika pesanan sudah lunas, tidak perlu upload lagi
    if ($order['status'] === 'lunas') {
        setFlash('info', 'Pesanan ini telah terverifikasi lunas.');
        redirect('my_orders.php');
    }

    $totalHarga = (float)$order['harga'];

    // 5. Validasi Upload File Bukti Pembayaran
    if (!isset($_FILES['bukti_bayar']) || $_FILES['bukti_bayar']['error'] !== UPLOAD_ERR_OK) {
        setFlash('error', 'Silakan pilih file gambar bukti transfer yang valid.');
        redirect("checkout.php?id_pesanan={$idPesanan}");
    }

    $fileTmpPath   = $_FILES['bukti_bayar']['tmp_name'];
    $fileName      = $_FILES['bukti_bayar']['name'];
    $fileSize      = $_FILES['bukti_bayar']['size'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Maksimal ukuran 2MB (2 * 1024 * 1024 bytes)
    $maxFileSize = 2 * 1024 * 1024;
    if ($fileSize > $maxFileSize) {
        setFlash('error', 'Ukuran file bukti pembayaran terlalu besar (Maksimal 2 MB).');
        redirect("checkout.php?id_pesanan={$idPesanan}");
    }

    // Ekstensi yang diizinkan
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array($fileExtension, $allowedExtensions, true)) {
        setFlash('error', 'Format file tidak didukung. Harap upload gambar JPG, JPEG, PNG, atau WEBP.');
        redirect("checkout.php?id_pesanan={$idPesanan}");
    }

    // Verifikasi MIME Type gambar sesungguhnya
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $fileTmpPath);
    finfo_close($finfo);

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($mimeType, $allowedMimes, true)) {
        setFlash('error', 'File yang diunggah bukan file gambar yang sah.');
        redirect("checkout.php?id_pesanan={$idPesanan}");
    }

    // Siapkan direktori penyimpanan
    $uploadDir = __DIR__ . '/../assets/uploads/payments/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Beri nama unik agar tidak terjadi collision / penimpaan file
    $newFileName = 'pay_' . $idPesanan . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
    $destinationPath = $uploadDir . $newFileName;

    if (!move_uploaded_file($fileTmpPath, $destinationPath)) {
        setFlash('error', 'Gagal memindahkan file yang diunggah ke server.');
        redirect("checkout.php?id_pesanan={$idPesanan}");
    }

    // 6. Transaksi Database: Simpan ke tabel pembayaran & update status pesanan
    $pdo->beginTransaction();

    // Cek apakah sudah pernah upload pembayaran sebelumnya untuk pesanan ini
    $checkPayment = $pdo->prepare("SELECT id_pembayaran FROM pembayaran WHERE id_pesanan = :id_pesanan LIMIT 1");
    $checkPayment->execute([':id_pesanan' => $idPesanan]);
    $existingPayment = $checkPayment->fetch(PDO::FETCH_ASSOC);

    if ($existingPayment) {
        // Update baris pembayaran yang sudah ada
        $updatePayStmt = $pdo->prepare("UPDATE pembayaran 
                                        SET tanggal = NOW(), metode = :metode, jumlah = :jumlah, bukti_bayar = :bukti_bayar, status = 'menunggu' 
                                        WHERE id_pesanan = :id_pesanan");
        $updatePayStmt->execute([
            ':metode'      => $metode,
            ':jumlah'      => $totalHarga,
            ':bukti_bayar' => $newFileName,
            ':id_pesanan'  => $idPesanan
        ]);
    } else {
        // Insert baris pembayaran baru
        $insertPayStmt = $pdo->prepare("INSERT INTO pembayaran (id_pesanan, tanggal, metode, jumlah, bukti_bayar, status) 
                                        VALUES (:id_pesanan, NOW(), :metode, :jumlah, :bukti_bayar, 'menunggu')");
        $insertPayStmt->execute([
            ':id_pesanan'  => $idPesanan,
            ':metode'      => $metode,
            ':jumlah'      => $totalHarga,
            ':bukti_bayar' => $newFileName
        ]);
    }

    // Update status pesanan menjadi 'menunggu_verifikasi'
    $updateOrderStmt = $pdo->prepare("UPDATE pesanan SET status = 'menunggu_verifikasi' WHERE id_pesanan = :id_pesanan");
    $updateOrderStmt->execute([':id_pesanan' => $idPesanan]);

    $pdo->commit();

    setFlash('success', 'Bukti transfer berhasil diunggah! Pembayaran Anda sedang diverifikasi oleh Administrator.');
    redirect('my_orders.php');

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("[Payment Upload Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat menyimpan bukti transfer. Silakan hubungi bantuan.');
    redirect("checkout.php?id_pesanan={$idPesanan}");
}
