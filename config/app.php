<?php
/**
 * EVENTIX - Application Core Configuration & Helpers
 * Mengatur session, timezone, BASE_URL dinamis, helper autentikasi, role guard, dan format data.
 */

// 1. Mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Set Timezone default Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// 3. Konfigurasi BASE_URL Otomatis / Dinamis
if (!defined('BASE_URL')) {
    // Deteksi protokol HTTP / HTTPS
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
    );
    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // Deteksi sub-direktori aplikasi secara relatif
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    
    // Cari letak folder 'eventix' di path URI
    $pos = strpos($scriptDir, '/eventix');
    if ($pos !== false) {
        $baseFolder = substr($scriptDir, 0, $pos + 8);
    } else {
        $baseFolder = rtrim($scriptDir, '/');
    }

    define('BASE_URL', rtrim($protocol . $host . $baseFolder, '/'));
}

// 4. Sertakan koneksi database
require_once __DIR__ . '/database.php';


// ============================================================
// HELPER URL & ASSET
// ============================================================

/**
 * Membuat URL lengkap mengacu pada BASE_URL aplikasi
 */
function url(string $path = ''): string
{
    $cleanPath = ltrim($path, '/');
    return BASE_URL . ($cleanPath !== '' ? '/' . $cleanPath : '');
}

/**
 * Mengambil path URL asset statis (css, js, uploads, dll)
 */
function asset(string $path = ''): string
{
    return url('assets/' . ltrim($path, '/'));
}


// ============================================================
// HELPER AUTENTIKASI & ROLE GUARD
// ============================================================

/**
 * Memeriksa apakah ada pengguna yang sedang login
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id_pengguna']);
}

/**
 * Mengambil data pengguna yang sedang login dari session
 */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Memeriksa apakah pengguna yang login memiliki role 'admin'
 */
function isAdmin(): bool
{
    return isLoggedIn() && (($_SESSION['user']['role'] ?? '') === 'admin');
}

/**
 * Guard: Mengharuskan pengguna login sebelum mengakses halaman.
 * Jika belum login, redirect ke halaman login dengan pesan flash.
 */
function requireLogin(string $redirectTo = 'login.php'): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        redirect($redirectTo);
    }
}

/**
 * Guard: Mengharuskan role Admin sebelum mengakses halaman.
 * Jika bukan admin, redirect ke beranda atau login dengan pesan flash.
 */
function requireAdmin(string $redirectTo = 'login.php'): void
{
    if (!isLoggedIn()) {
        setFlash('error', 'Silakan login sebagai Admin untuk mengakses menu ini.');
        redirect($redirectTo);
    }

    if (!isAdmin()) {
        setFlash('error', 'Akses ditolak. Anda tidak memiliki izin Administrator.');
        redirect('index.php');
    }
}


// ============================================================
// HELPER REDIRECT & FLASH MESSAGES
// ============================================================

/**
 * Melakukan redirect ke URL atau path tertentu dengan exit script
 */
function redirect(string $path): void
{
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        header("Location: " . $path);
    } else {
        header("Location: " . url($path));
    }
    exit;
}

/**
 * Menyimpan pesan flash sementara di session (tipe: success, error, warning, info)
 */
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Mengecek apakah terdapat pesan flash tertentu
 */
function hasFlash(string $type): bool
{
    return isset($_SESSION['flash'][$type]);
}

/**
 * Mengambil dan langsung menghapus pesan flash dari session
 */
function getFlash(string $type): ?string
{
    if (isset($_SESSION['flash'][$type])) {
        $message = $_SESSION['flash'][$type];
        unset($_SESSION['flash'][$type]);
        return $message;
    }
    return null;
}


// ============================================================
// HELPER KEAMANAN & FORMATTING
// ============================================================

/**
 * Menghasilkan token CSRF unik per session
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Input tersembunyi CSRF token untuk form HTML
 */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Memvalidasi token CSRF dari request POST
 */
function csrf_verify(?string $token = null): bool
{
    $token = $token ?? ($_POST['csrf_token'] ?? '');
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

/**
 * Sanitasi string input dari XSS
 */
function sanitize(mixed $data): string
{
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Format angka ke format mata uang Rupiah (contoh: Rp 1.500.000)
 */
function formatRupiah(float|int|string $angka): string
{
    $numeric = (float)$angka;
    return 'Rp ' . number_format($numeric, 0, ',', '.');
}

/**
 * Format tanggal ke nama hari & bulan bahasa Indonesia (contoh: Sabtu, 19 Desember 2026)
 */
function formatTanggalIndo(string $date, bool $withDay = true): string
{
    if (empty($date) || $date === '0000-00-00') {
        return '-';
    }

    $timestamp = strtotime($date);
    $bulanIndo = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $hariIndo = [
        'Sunday'    => 'Minggu',
        'Monday'    => 'Senin',
        'Tuesday'   => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday'  => 'Kamis',
        'Friday'    => 'Jumat',
        'Saturday'  => 'Sabtu'
    ];

    $hari = $hariIndo[date('l', $timestamp)] ?? '';
    $tgl = date('j', $timestamp);
    $bulan = $bulanIndo[(int)date('n', $timestamp)] ?? '';
    $tahun = date('Y', $timestamp);

    if ($withDay) {
        return "{$hari}, {$tgl} {$bulan} {$tahun}";
    }

    return "{$tgl} {$bulan} {$tahun}";
}

/**
 * Format jam (contoh: 19:30 WIB)
 */
function formatJam(string $time): string
{
    if (empty($time)) return '-';
    $timestamp = strtotime($time);
    return date('H:i', $timestamp) . ' WIB';
}

/**
 * Mengembalikan respon JSON terstandar untuk API/Fetch
 */
function jsonResponse(mixed $data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}
