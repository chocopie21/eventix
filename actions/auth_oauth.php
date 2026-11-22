<?php
/**
 * EVENTIX - Action: OAuth & Social Login (Google, Apple, dll)
 * Mendukung 2 mode sekaligus:
 * 1. Smart Interactive SSO (Ready-to-use langsung di Localhost / XAMPP)
 * 2. Real Google OAuth 2.0 (Jika GOOGLE_CLIENT_ID di config/oauth.php telah diisi)
 */

require_once __DIR__ . '/../config/app.php';

// Pastikan hanya provider yang diizinkan
$allowedProviders = ['google', 'apple', 'github'];
$provider = strtolower(trim($_REQUEST['provider'] ?? 'google'));
if (!in_array($provider, $allowedProviders, true)) {
    $provider = 'google';
}

$providerNames = [
    'google' => 'Google',
    'apple'  => 'Apple',
    'github' => 'GitHub'
];
$providerLabel = $providerNames[$provider] ?? 'Google';

// ============================================================
// 1. PENANGANAN GET REQUEST (Real OAuth 2.0 Redirect & Callback)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Mode Google OAuth Live jika GOOGLE_CLIENT_ID telah dikonfigurasi
    if ($provider === 'google' && !empty(GOOGLE_CLIENT_ID) && !empty(GOOGLE_CLIENT_SECRET)) {
        // Jika belum ada code, arahkan pengguna ke consent screen Google
        if (!isset($_GET['code'])) {
            $state = bin2hex(random_bytes(16));
            $_SESSION['oauth_state'] = $state;

            $params = [
                'response_type' => 'code',
                'client_id'     => GOOGLE_CLIENT_ID,
                'redirect_uri'  => GOOGLE_REDIRECT_URI,
                'scope'         => 'openid email profile',
                'state'         => $state,
                'prompt'        => 'select_account'
            ];

            $authUrl = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
            header('Location: ' . $authUrl);
            exit;
        }

        // Jika callback diterima dengan parameter code
        $code = $_GET['code'] ?? '';
        $state = $_GET['state'] ?? '';

        if (empty($code) || empty($state) || $state !== ($_SESSION['oauth_state'] ?? '')) {
            unset($_SESSION['oauth_state']);
            setFlash('error', 'Validasi sesi Google OAuth gagal atau kedaluwarsa. Silakan ulangi.');
            redirect('login.php');
        }
        unset($_SESSION['oauth_state']);

        // Tukar auth code dengan access token via Google Token API
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $postData = [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code'
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $tokenResult = json_decode($response, true);
        $accessToken = $tokenResult['access_token'] ?? null;

        if (!$accessToken) {
            setFlash('error', 'Gagal memperoleh otorisasi dari Google. Silakan coba kembali.');
            redirect('login.php');
        }

        // Ambil profil data pengguna dari Google UserInfo API
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $accessToken]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $userInfoResponse = curl_exec($ch);
        curl_close($ch);

        $googleUser = json_decode($userInfoResponse, true);
        $ssoEmail = filter_var($googleUser['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $ssoName = trim($googleUser['name'] ?? '');

        if (!$ssoEmail) {
            setFlash('error', 'Email tidak ditemukan dari akun Google Anda.');
            redirect('login.php');
        }

        // Proses login/register menggunakan profil resmi Google
        processSsoLogin($pdo, $ssoEmail, $ssoName, $providerLabel);
        exit;
    }

    // Jika GET dipanggil tanpa credentials aktif, kembalikan ke login
    redirect('login.php');
}

// ============================================================
// 2. PENANGANAN POST REQUEST (Smart Interactive SSO / Local Mode)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Verifikasi CSRF token
    if (!csrf_verify()) {
        setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan ulangi login.');
        redirect('login.php');
    }

    // 2. Ambil dan validasi data akun SSO yang dipilih
    $ssoEmail = filter_input(INPUT_POST, 'sso_email', FILTER_VALIDATE_EMAIL);
    $rawEmail = trim($_POST['sso_email'] ?? '');
    $ssoName = trim($_POST['sso_name'] ?? '');

    if (!$ssoEmail) {
        setFlash('error', 'Alamat email akun tidak valid (' . htmlspecialchars($rawEmail, ENT_QUOTES, 'UTF-8') . ').');
        redirect('login.php');
    }

    if (empty($ssoName)) {
        $ssoName = ucwords(str_replace(['.', '_', '-'], ' ', explode('@', $ssoEmail)[0]));
    }

    // 3. Proses login / registrasi otomatis ke database
    processSsoLogin($pdo, $ssoEmail, $ssoName, $providerLabel);
}

/**
 * Fungsi pembantu untuk memproses autentikasi SSO (Login / Auto-Register)
 */
function processSsoLogin(PDO $pdo, string $email, string $name, string $providerLabel): void
{
    try {
        // Cek apakah email sudah terdaftar di database
        $stmt = $pdo->prepare("SELECT id_pengguna, nama, email, alamat, no_telepon, role FROM pengguna WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User sudah ada -> Langsung set sesi login
            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id_pengguna' => (int)$user['id_pengguna'],
                'nama'        => $user['nama'],
                'email'       => $user['email'],
                'role'        => $user['role'],
                'alamat'      => $user['alamat'] ?? '',
                'no_telepon'  => $user['no_telepon'] ?? ''
            ];

            setFlash('success', 'Selamat datang kembali, ' . htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8') . '! (Masuk dengan ' . $providerLabel . ')');

            // Redirect sesuai role dan riwayat halaman
            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                $target = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                redirect($target);
            }
        } else {
            // User baru -> Otomatis daftarkan ke database
            $randomPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT);
            $randomPhone = '0812' . random_int(10000000, 99999999);
            $alamatDefault = 'Terhubung via Akun ' . $providerLabel;

            $insertSql = "INSERT INTO pengguna (nama, email, alamat, no_telepon, password, role) 
                          VALUES (:nama, :email, :alamat, :no_telepon, :password, 'user')";
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute([
                ':nama'       => $name,
                ':email'      => $email,
                ':alamat'     => $alamatDefault,
                ':no_telepon' => $randomPhone,
                ':password'   => $randomPassword
            ]);

            $newId = (int)$pdo->lastInsertId();

            session_regenerate_id(true);

            $_SESSION['user'] = [
                'id_pengguna' => $newId,
                'nama'        => $name,
                'email'       => $email,
                'role'        => 'user',
                'alamat'      => $alamatDefault,
                'no_telepon'  => $randomPhone
            ];

            setFlash('success', 'Selamat datang di EVENTIX! Akun Anda berhasil terhubung dengan ' . $providerLabel . ' (' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . ').');

            $target = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            redirect($target);
        }
    } catch (PDOException $e) {
        error_log("[OAuth Process Error] " . $e->getMessage());
        setFlash('error', 'Terjadi kendala saat memproses login ' . $providerLabel . '. Silakan coba kembali.');
        redirect('login.php');
    }
}
