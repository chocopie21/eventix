<?php
/**
 * EVENTIX - API: Live Search & Filter Events (Konser)
 * Mengembalikan data JSON daftar konser yang di-JOIN dengan tabel konser dan tiket (harga termurah).
 */

require_once __DIR__ . '/../config/app.php';

// Atur header response JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // Ambil parameter GET dengan sanitasi
    $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
    $genre   = isset($_GET['genre']) ? trim($_GET['genre']) : '';
    $date    = isset($_GET['date']) ? trim($_GET['date']) : '';

    // Bangun SQL Query Dasar
    $sql = "SELECT 
                e.id_event,
                e.venue,
                e.nama_event,
                e.tanggal_event,
                e.jam_event,
                e.pengisi_acara,
                e.deskripsi,
                e.poster,
                k.genre_musik,
                k.daftar_lagu,
                COALESCE(MIN(t.harga), 0) AS harga_termurah,
                COALESCE(SUM(t.kuota_tersedia), 0) AS total_kuota,
                COUNT(t.id_tiket) AS total_kategori
            FROM event e
            INNER JOIN konser k ON e.id_event = k.id_event
            LEFT JOIN tiket t ON e.id_event = t.id_event
            WHERE 1=1";

    $params = [];

    // Filter Keyword (Nama Event, Pengisi Acara, atau Venue)
    if ($keyword !== '') {
        $sql .= " AND (e.nama_event LIKE :q OR e.pengisi_acara LIKE :q OR e.venue LIKE :q)";
        $params[':q'] = '%' . $keyword . '%';
    }

    // Filter Genre Musik
    if ($genre !== '' && $genre !== 'all') {
        $sql .= " AND k.genre_musik = :genre";
        $params[':genre'] = $genre;
    }

    // Filter Tanggal Event
    if ($date !== '') {
        $sql .= " AND e.tanggal_event = :date";
        $params[':date'] = $date;
    }

    // Grouping & Sorting
    $sql .= " GROUP BY e.id_event ORDER BY e.tanggal_event ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Bulan Singkat Indonesia untuk Date Badge
    $bulanSingkat = [
        1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUN',
        7 => 'JUL', 8 => 'AGU', 9 => 'SEP', 10 => 'OKT', 11 => 'NOP', 12 => 'DES'
    ];

    // Format output untuk konsumsi Frontend JS
    $formattedEvents = [];
    foreach ($events as $ev) {
        $posterFile = $ev['poster'] ?? '';
        $posterPath = 'assets/uploads/posters/' . $posterFile;
        $hasPoster = !empty($posterFile) && file_exists(__DIR__ . '/../' . $posterPath);

        $timestamp = strtotime($ev['tanggal_event']);
        $dayNum = date('d', $timestamp);
        $monthNum = (int)date('n', $timestamp);
        $monthText = $bulanSingkat[$monthNum] ?? date('M', $timestamp);

        $formattedEvents[] = [
            'id_event'          => (int)$ev['id_event'],
            'nama_event'        => $ev['nama_event'],
            'venue'             => $ev['venue'],
            'tanggal_event'     => $ev['tanggal_event'],
            'jam_event'         => $ev['jam_event'],
            'pengisi_acara'     => $ev['pengisi_acara'],
            'deskripsi'         => $ev['deskripsi'],
            'genre_musik'       => $ev['genre_musik'],
            'daftar_lagu'       => $ev['daftar_lagu'],
            'harga_termurah'    => (float)$ev['harga_termurah'],
            'total_kuota'       => (int)$ev['total_kuota'],
            'total_kategori'    => (int)$ev['total_kategori'],
            // Field siap pakai
            'formatted_harga'   => (float)$ev['harga_termurah'] > 0 ? formatRupiah($ev['harga_termurah']) : 'Segera Hadir',
            'formatted_tanggal' => formatTanggalIndo($ev['tanggal_event'], false),
            'formatted_jam'     => formatJam($ev['jam_event']),
            'calendar_day'      => $dayNum,
            'calendar_month'    => $monthText,
            'poster_url'        => $hasPoster ? asset('uploads/posters/' . $posterFile) : null,
            'detail_url'        => url('detail_event.php?id=' . $ev['id_event']),
            'is_sold_out'       => (int)$ev['total_kuota'] <= 0
        ];
    }

    jsonResponse([
        'status'  => 'success',
        'count'   => count($formattedEvents),
        'filters' => [
            'q'     => $keyword,
            'genre' => $genre,
            'date'  => $date
        ],
        'data'    => $formattedEvents
    ]);

} catch (PDOException $e) {
    error_log('[API events.php Error] ' . $e->getMessage());
    jsonResponse([
        'status'  => 'error',
        'message' => 'Terjadi kesalahan saat memproses data konser.'
    ], 500);
}
