<?php
/**
 * EVENTIX - API: Cek Kuota Tiket Real-Time
 * Endpoint GET: ?id_event=...
 * Mengembalikan JSON status ketersediaan dan sisa kuota tiket terbaru.
 */

require_once __DIR__ . '/../config/app.php';

// Ambil ID event dari query string
$idEvent = filter_input(INPUT_GET, 'id_event', FILTER_VALIDATE_INT);

if (!$idEvent) {
    jsonResponse([
        'status'  => 'error',
        'message' => 'Parameter id_event tidak valid atau tidak disertakan.'
    ], 400);
}

try {
    // Ambil daftar tiket untuk event yang diminta
    $stmt = $pdo->prepare("SELECT id_tiket, id_event, kategori, kuota_tersedia, harga 
                           FROM tiket 
                           WHERE id_event = :id_event 
                           ORDER BY harga DESC");
    $stmt->execute([':id_event' => $idEvent]);
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tickets)) {
        jsonResponse([
            'status'  => 'error',
            'message' => 'Tidak ada kategori tiket yang ditemukan untuk event ini.'
        ], 404);
    }

    // Format output data
    $formattedTickets = array_map(function ($t) {
        return [
            'id_tiket'       => (int)$t['id_tiket'],
            'kategori'       => $t['kategori'],
            'kuota_tersedia' => (int)$t['kuota_tersedia'],
            'harga'          => (float)$t['harga'],
            'harga_rupiah'   => formatRupiah($t['harga']),
            'is_sold_out'    => (int)$t['kuota_tersedia'] <= 0
        ];
    }, $tickets);

    jsonResponse([
        'status'      => 'success',
        'id_event'    => $idEvent,
        'timestamp'   => time(),
        'tickets'     => $formattedTickets
    ], 200);

} catch (PDOException $e) {
    error_log("[API Check Quota Error] " . $e->getMessage());
    jsonResponse([
        'status'  => 'error',
        'message' => 'Gagal mengambil data kuota dari database.'
    ], 500);
}
