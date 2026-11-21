<?php
/**
 * EVENTIX - Action: Register Pengguna Baru
 * Validasi input form pendaftaran, cek email unik, hash password, simpan ke database dengan role default 'user'.
 */

require_once __DIR__ . '/../config/app.php';

// Pastikan hanya request POST yang diproses
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('register.php');
}

// 1. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan kedaluwarsa atau token tidak valid. Silakan coba lagi.');
    redirect('register.php');
}

// 2. Ambil dan bersihkan data input
$nama             = trim($_POST['nama'] ?? '');
$email            = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$rawEmail         = trim($_POST['email'] ?? '');
$noTelepon        = trim($_POST['no_telepon'] ?? '');
$alamat           = trim($_POST['alamat'] ?? '');
$password         = $_POST['password'] ?? '';
$passwordConfirm  = $_POST['password_confirm'] ?? '';

// Simpan data lama agar user tidak perlu mengisi ulang saat terjadi kesalahan validasi
$_SESSION['old_register'] = [
    'nama'       => $nama,
    'email'      => $rawEmail,
    'no_telepon' => $noTelepon,
    'alamat'     => $alamat
];

// 3. Validasi Form Sisi Server
if (empty($nama) || strlen($nama) < 3) {
    setFlash('error', 'Nama lengkap wajib diisi minimal 3 karakter.');
    redirect('register.php');
}

if (!$email) {
    setFlash('error', 'Format alamat email tidak valid.');
    redirect('register.php');
}

if (empty($noTelepon) || strlen($noTelepon) < 9) {
    setFlash('error', 'Nomor telepon aktif wajib diisi minimal 9 digit.');
    redirect('register.php');
}

if (empty($password) || strlen($password) < 6) {
    setFlash('error', 'Kata sandi minimal terdiri dari 6 karakter.');
    redirect('register.php');
}

if ($password !== $passwordConfirm) {
    setFlash('error', 'Konfirmasi kata sandi tidak cocok dengan kata sandi yang dibuat.');
    redirect('register.php');
}

// 4. Periksa apakah email sudah pernah terdaftar di database
try {
    $checkStmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = :email LIMIT 1");
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->fetch()) {
        setFlash('error', "Alamat email '{$rawEmail}' sudah terdaftar. Silakan masuk atau gunakan email lain.");
        redirect('register.php');
    }

    // 5. Hash password dengan PASSWORD_BCRYPT
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 6. Simpan pengguna baru ke tabel pengguna dengan role 'user'
    $insertSql = "INSERT INTO pengguna (nama, email, alamat, no_telepon, password, role) 
                  VALUES (:nama, :email, :alamat, :no_telepon, :password, 'user')";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([
        ':nama'       => $nama,
        ':email'      => $email,
        ':alamat'     => $alamat !== '' ? $alamat : null,
        ':no_telepon' => $noTelepon,
        ':password'   => $hashedPassword
    ]);

    // Bersihkan session data input lama
    unset($_SESSION['old_register']);

    // Set flash message sukses
    setFlash('success', 'Akun berhasil dibuat! Silakan masuk dengan email dan kata sandi Anda.');
    redirect('login.php');

} catch (PDOException $e) {
    error_log("[Auth Register Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat mendaftarkan akun. Silakan coba kembali.');
    redirect('register.php');
}
