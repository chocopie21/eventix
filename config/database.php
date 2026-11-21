<?php
/**
 * EVENTIX - Database Connection (PDO)
 * Menggunakan PDO dengan Prepared Statements untuk keamanan query dari SQL Injection.
 */

// Konfigurasi Database (dapat disesuaikan dengan environment XAMPP/Laragon/Server)
defined('DB_HOST')    or define('DB_HOST', 'localhost');
defined('DB_PORT')    or define('DB_PORT', '3306');
defined('DB_NAME')    or define('DB_NAME', 'eventix');
defined('DB_USER')    or define('DB_USER', 'root');
defined('DB_PASS')    or define('DB_PASS', '');
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4');

/**
 * Mendapatkan instance koneksi PDO tunggal (Singleton pattern)
 * 
 * @return PDO
 * @throws PDOException
 */
function getDBConnection(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Log pesan error teknis ke error_log server
            error_log("[EVENTIX Database Error] " . $e->getMessage());

            // Tampilkan pesan bersahabat jika koneksi gagal
            http_response_code(500);
            die("Koneksi database gagal. Pastikan service MySQL/MariaDB aktif dan database 'eventix' telah diimpor dari database/eventix.sql.");
        }
    }

    return $pdo;
}

// Inisialisasi variabel global $pdo agar dapat langsung digunakan di query script
$pdo = getDBConnection();
