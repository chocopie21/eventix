<?php
/**
 * EVENTIX - Action: Login Pengguna
 * Memvalidasi kredensial login via PDO Prepared Statement dan password_verify().
 */

require_once __DIR__ . '/../config/app.php';

// Pastikan hanya request POST yang diproses
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

// 1. Validasi CSRF Token
if (!csrf_verify()) {
    setFlash('error', 'Sesi keamanan kedaluwarsa atau token tidak valid. Silakan coba lagi.');
    redirect('login.php');
}

// 2. Ambil dan sanitasi input form
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$rawEmail = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Simpan email lama untuk kemudahan user saat reload form
$_SESSION['old_login_email'] = $rawEmail;

// 3. Validasi kelengkapan form
if (!$email || empty($password)) {
    setFlash('error', 'Silakan masukkan format alamat email yang valid dan password Anda.');
    redirect('login.php');
}

// 4. Query data pengguna berdasarkan email dengan PDO Prepared Statement
try {
    $stmt = $pdo->prepare("SELECT id_pengguna, nama, email, alamat, no_telepon, password, role FROM pengguna WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Verifikasi pengguna dan password hash
    if ($user && password_verify($password, $user['password'])) {
        // Regenerasi ID sesi untuk mencegah Session Fixation attack
        session_regenerate_id(true);

        // Hapus password dari memory
        unset($user['password']);

        // Simpan data login ke session
        $_SESSION['user'] = [
            'id_pengguna' => (int)$user['id_pengguna'],
            'nama'        => $user['nama'],
            'email'       => $user['email'],
            'role'        => $user['role'],
            'alamat'      => $user['alamat'] ?? '',
            'no_telepon'  => $user['no_telepon'] ?? ''
        ];

        // Hapus memori old email
        unset($_SESSION['old_login_email']);

        // Beri pesan flash selamat datang
        setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8') . '!');

        // Redirect sesuai role pengguna
        if ($user['role'] === 'admin') {
            redirect('admin/index.php');
        } else {
            // Cek apakah ada redirect tujuan sebelum login (misal dari checkout)
            $redirectTarget = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            redirect($redirectTarget);
        }
    } else {
        // Kredensial tidak cocok
        setFlash('error', 'Email atau password yang Anda masukkan tidak sesuai.');
        redirect('login.php');
    }

} catch (PDOException $e) {
    error_log("[Auth Login Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kendala pada sistem server. Silakan coba kembali sesaat lagi.');
    redirect('login.php');
}
