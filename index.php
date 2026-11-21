<?php
/**
 * EVENTIX - Beranda & Katalog Konser Musik
 * Human-Crafted Editorial Minimalist
 */

require_once __DIR__ . '/config/app.php';

// Ambil data konser awal secara SSR dari PDO
try {
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
            GROUP BY e.id_event
            ORDER BY e.tanggal_event ASC";
    $stmt = $pdo->query($sql);
    $initialEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil daftar genre musik unik untuk pilihan filter
    $genreStmt = $pdo->query("SELECT DISTINCT genre_musik FROM konser ORDER BY genre_musik ASC");
    $genres = $genreStmt->fetchAll(PDO::FETCH_COLUMN);

} catch (PDOException $e) {
    error_log("[Index Query Error] " . $e->getMessage());
    $initialEvents = [];
    $genres = [];
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVENTIX - Beli Tiket Konser Musik Resmi Indonesia</title>
    <meta name="description" content="Platform tiket konser musik resmi Indonesia. Sheila on 7, Dewa 19, Hindia, Tulus, Bernadya, Perunggu.">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        heading: ['Outfit', 'Plus Jakarta Sans', 'sans-serif'],
                        display: ['Space Grotesk', 'Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">

    <!-- Navbar Component -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="flex-grow space-y-12 pb-16">

        <!-- HERO SECTION: Human-Designed Architectural Stage Header -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <div class="rounded-2xl bg-slate-900 text-white p-8 sm:p-12 lg:p-14 border border-slate-800">
                <div class="max-w-2xl space-y-5">
                    
                    <h1 class="text-3xl sm:text-5xl font-black font-display tracking-tight leading-tight text-white">
                        Tiket Resmi Konser Musik Indonesia
                    </h1>

                    <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                        Pesan tiket resmi konser Sheila on 7, Dewa 19, Hindia, dan musisi favorit lainnya langsung dari promotor tanpa calo.
                    </p>

                    <!-- Quick Search Bar inside Hero -->
                    <div class="pt-2">
                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center bg-white p-1.5 rounded-xl border border-slate-200 shadow-sm gap-2">
                            <div class="flex items-center flex-1 px-3 py-1 gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" 
                                       id="search-input" 
                                       placeholder="Cari artis, band, atau venue konser..." 
                                       class="w-full text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none bg-transparent">
                            </div>
                            <a href="#katalog" class="px-5 py-2.5 rounded-lg bg-[#FF6082] hover:bg-[#e54b6d] text-white font-semibold text-xs text-center transition-colors shadow-xs">
                                Cari Tiket
                            </a>
                        </div>
                    </div>

                    <!-- Honest Key Facts -->
                    <div class="pt-2 flex flex-wrap items-center gap-6 text-xs text-slate-400 font-medium">
                        <span>100% tiket resmi promotor</span>
                        <span>Verifikasi pembayaran cepat</span>
                        <span>E-Ticket QR langsung aktif</span>
                    </div>

                </div>
            </div>
        </section>

        <!-- KATALOG & FILTER SECTION -->
        <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- Section Header & Filter Pills -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200 pb-5">
                <div>
                    <h2 class="text-2xl font-bold font-heading text-slate-900">
                        Jadwal Konser Musik
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Daftar konser musik aktif dengan pemesanan tiket resmi.
                    </p>
                </div>

                <!-- Genre Filter Pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar text-xs">
                    <button type="button" 
                            class="category-pill active px-3.5 py-1.5 rounded-lg font-semibold transition-colors bg-[#FF6082] text-white"
                            data-genre="">
                        Semua Genre
                    </button>
                    <?php foreach ($genres as $g): ?>
                        <button type="button" 
                                class="category-pill px-3.5 py-1.5 rounded-lg font-medium border border-slate-200 bg-white text-slate-700 hover:border-slate-400 transition-colors"
                                data-genre="<?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($g, ENT_QUOTES, 'UTF-8') ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Additional Filter Bar (Tanggal & Reset) -->
            <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-slate-500">Tanggal konser:</span>
                    <input type="date" 
                           id="date-filter" 
                           class="px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-slate-800 text-xs focus:outline-none focus:border-slate-900">
                    <button type="button" id="reset-filter" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        Reset filter
                    </button>
                </div>
                <div id="events-count" class="text-slate-500">
                    Menampilkan <?= count($initialEvents) ?> konser musik
                </div>
            </div>

            <!-- CONCERTS GRID -->
            <div id="events-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($initialEvents)): ?>
                    <div class="col-span-full py-12 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                        Belum ada konser musik yang tersedia saat ini.
                    </div>
                <?php else: ?>
                    <?php foreach ($initialEvents as $index => $ev): 
                        $idEv = (int)$ev['id_event'];
                        $isSoldOut = ((int)$ev['total_kuota'] <= 0);
                        
                        // Solid colorful badge per genre
                        $genreLower = strtolower($ev['genre_musik'] ?? '');
                        $badgeClass = 'bg-slate-100 text-slate-800 border-slate-200';
                        if (str_contains($genreLower, 'rock')) $badgeClass = 'badge-coral';
                        elseif (str_contains($genreLower, 'pop')) $badgeClass = 'badge-blue';
                        elseif (str_contains($genreLower, 'indie')) $badgeClass = 'badge-violet';
                        elseif (str_contains($genreLower, 'jazz') || str_contains($genreLower, 'folk')) $badgeClass = 'badge-amber';
                    ?>
                        <article class="ticket-stub flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Poster Container -->
                            <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                                <img src="<?= asset('uploads/posters/' . ($ev['poster'] ?? 'default.jpg')) ?>" 
                                     alt="<?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?>"
                                     class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-200"
                                     onerror="this.src='<?= asset('uploads/posters/default.jpg') ?>'">
                                
                                <!-- Genre Badge -->
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="badge-clean <?= $badgeClass ?>">
                                        <?= htmlspecialchars($ev['genre_musik'] ?? 'Konser', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <!-- Sold Out or Quota Badge -->
                                <div class="absolute top-3 right-3 z-10">
                                    <?php if ($isSoldOut): ?>
                                        <span class="badge-clean bg-rose-600 text-white border-0">
                                            Habis
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-clean bg-emerald-600 text-white border-0">
                                            Tersedia
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Concert Content -->
                            <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-1.5">
                                    <p class="text-xs text-slate-500">
                                        <?= formatTanggalIndo($ev['tanggal_event'], false) ?> • <?= formatJam($ev['jam_event']) ?>
                                    </p>
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors font-heading line-clamp-1" title="<?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                                    </h3>
                                    <p class="text-xs text-slate-600 line-clamp-1">
                                        <?= htmlspecialchars($ev['pengisi_acara'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p class="text-xs text-slate-500 line-clamp-1 flex items-center gap-1 pt-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars($ev['venue'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </p>
                                </div>

                                <!-- Price & Action -->
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between mt-auto">
                                    <div>
                                        <span class="block text-[11px] text-slate-500">Mulai dari</span>
                                        <span class="text-base font-bold text-slate-900 font-heading">
                                            <?= $ev['harga_termurah'] > 0 ? formatRupiah($ev['harga_termurah']) : 'Segera Hadir' ?>
                                        </span>
                                    </div>

                                    <a href="<?= url('detail_event.php?id=' . $idEv) ?>" 
                                       class="px-4 py-2 rounded-lg text-xs font-semibold transition-colors <?= $isSoldOut ? 'bg-slate-200 text-slate-400 pointer-events-none' : 'bg-[#FF6082] hover:bg-[#e54b6d] text-white shadow-xs' ?>">
                                        <?= $isSoldOut ? 'Tiket Habis' : 'Pilih Tiket' ?>
                                    </a>
                                </div>
                            </div>

                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

        </section>

    </main>

    <!-- Footer Component -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Catalog Live Search Script -->
    <script src="<?= asset('js/catalog.js') ?>"></script>
</body>
</html>
