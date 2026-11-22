<?php
/**
 * EVENTIX - Konfigurasi OAuth 2.0 (Google, Apple, dll)
 * 
 * Untuk mode Live Production dengan Google Cloud Console:
 * 1. Buka https://console.cloud.google.com/
 * 2. Buat Project baru dan buka menu 'APIs & Services' > 'Credentials'
 * 3. Buat 'OAuth 2.0 Client ID' (Web Application)
 * 4. Tambahkan Authorized Redirect URI: http://localhost/eventix/actions/auth_oauth.php?provider=google
 * 5. Masukkan GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di bawah ini.
 * 
 * CATATAN: Jika GOOGLE_CLIENT_ID dibiarkan kosong (''), sistem secara otomatis berjalan
 * dalam mode Smart Interactive SSO (Google Account Picker) yang 100% siap pakai di localhost/XAMPP.
 */

if (!defined('GOOGLE_CLIENT_ID')) {
    define('GOOGLE_CLIENT_ID', ''); // Isi Client ID dari Google Cloud Console jika ingin mode live
}

if (!defined('GOOGLE_CLIENT_SECRET')) {
    define('GOOGLE_CLIENT_SECRET', ''); // Isi Client Secret dari Google Cloud Console
}

if (!defined('GOOGLE_REDIRECT_URI')) {
    define('GOOGLE_REDIRECT_URI', (defined('BASE_URL') ? BASE_URL : '') . '/actions/auth_oauth.php?provider=google');
}
