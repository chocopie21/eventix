<?php
/**
 * EVENTIX - Action: Logout Pengguna
 * Menghapus session pengguna dan me-redirect kembali ke beranda dengan flash message.
 */

require_once __DIR__ . '/../config/app.php';

// Hapus sesi pengguna jika ada
if (isset($_SESSION['user'])) {
    $userName = $_SESSION['user']['nama'] ?? 'Pengguna';
    unset($_SESSION['user']);
    
    // Set pesan sukses
    setFlash('success', "Sampai jumpa lagi, {$userName}. Anda telah berhasil keluar.");
}

// Redirect ke beranda
redirect('index.php');
