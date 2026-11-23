<?php
/**
 * EVENTIX - Beranda & Katalog Konser Musik
 * Full Redesign: SF Pro Display & Hallmark Curated Experience
 */

require_once __DIR__ . '/config/app.php';

// Ambil parameter filter awal dari URL jika ada
$searchQuery = trim($_GET['q'] ?? '');
$selectedGenre = trim($_GET['genre'] ?? '');

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
            WHERE 1=1";
    
    $params = [];
    if ($searchQuery !== '') {
        $sql .= " AND (e.nama_event LIKE :q1 OR e.pengisi_acara LIKE :q2 OR e.venue LIKE :q3)";
        $searchTerm = '%' . $searchQuery . '%';
        $params[':q1'] = $searchTerm;
        $params[':q2'] = $searchTerm;
        $params[':q3'] = $searchTerm;
    }
    if ($selectedGenre !== '' && $selectedGenre !== 'all') {
        $sql .= " AND k.genre_musik = :genre";
        $params[':genre'] = $selectedGenre;
    }

    $sql .= " GROUP BY e.id_event ORDER BY e.tanggal_event ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
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
    <title>EVENTIX - Tiket Resmi Konser Musik Indonesia</title>
    <meta name="description" content="Pesan tiket konser musik resmi di Indonesia. Jadwal lengkap tur dunia dan festival musik terkini tanpa perantara calo.">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"SF Pro Display"', '-apple-system', 'BlinkMacSystemFont', 'system-ui', 'sans-serif'],
                        display: ['"SF Pro Display"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Lenis Smooth Scroll CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-[#FAFBFD] text-slate-900 antialiased flex flex-col min-h-screen font-sf">

    <!-- Navbar Component -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="flex-grow space-y-14 pb-24">

        <!-- HERO SECTION: Stage Atmosphere & Monumental SF Pro Typography -->
        <section class="stage-hero bg-[#070B12] text-white pt-20 pb-24 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
            <div class="max-w-7xl mx-auto relative z-10 space-y-8">
                
                <div class="max-w-3xl space-y-4">
                    <p class="text-xs font-semibold text-[#FF6082] tracking-normal uppercase">
                        Katalog Resmi Penyelenggara 2026/2027
                    </p>
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white leading-[1.04] gsap-words-reveal">
                        Tiket Konser Live & Festival Resmi.
                    </h1>
                    <p class="text-base sm:text-lg text-slate-300 leading-relaxed font-normal max-w-2xl">
                        Akses panggung musisi favorit Anda langsung dari kuota promotor resmi. E-Ticket QR code langsung terbit, aman, dan tanpa biaya spekulasi calo.
                    </p>
                </div>

                <!-- Sleek Search Bar -->
                <div class="max-w-2xl pt-2">
                    <form id="hero-search-form" action="#katalog" method="GET" class="bg-white/10 backdrop-blur-xl p-2 rounded-2xl border border-white/20 shadow-2xl flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                        <div class="flex items-center flex-1 px-3 py-1.5 gap-3">
                            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" 
                                   id="search-input" 
                                   name="q"
                                   value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>"
                                   placeholder="Cari artis, band, atau venue konser..." 
                                   autocomplete="off"
                                   class="w-full text-xs sm:text-sm text-white placeholder-slate-400 focus:outline-none bg-transparent font-sf">
                            <button type="button" 
                                    id="clear-search-btn" 
                                    class="<?= empty($searchQuery) ? 'hidden' : '' ?> text-slate-400 hover:text-white focus:outline-none"
                                    title="Hapus pencarian">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <button type="submit" 
                                id="search-btn" 
                                class="px-6 py-3 rounded-xl bg-[#FF6082] hover:bg-[#E0486D] text-white font-semibold text-xs tracking-normal transition-button shadow-sm shrink-0 cursor-pointer">
                            Cari Jadwal
                        </button>
                    </form>
                </div>

            </div>
        </section>

        <!-- KATALOG SECTION -->
        <section id="katalog" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pt-2">
            
            <!-- Section Header & Count -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-200/90 pb-6">
                <div class="space-y-1">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight gsap-words-reveal">
                        Daftar Pertunjukan Konser
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-normal">
                        Pilih konser untuk melihat denah kategori tempat duduk dan reservasi tiket resmi.
                    </p>
                </div>
                <div id="events-count" class="text-slate-500 font-medium text-xs">
                    Menampilkan <?= count($initialEvents) ?> jadwal konser musik
                </div>
            </div>

            <!-- CONCERTS GRID -->
            <div id="events-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (empty($initialEvents)): ?>
                    <div class="col-span-full py-16 text-center text-slate-500 bg-white rounded-2xl border border-slate-200">
                        <p class="font-bold text-slate-900 text-base">Tidak ada konser musik yang ditemukan</p>
                        <p class="text-xs text-slate-500 mt-1">
                            <?= !empty($searchQuery) ? 'Tidak ada konser yang cocok dengan kata kunci "' . htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') . '".' : 'Belum ada konser musik yang tersedia saat ini.' ?>
                            Coba kata kunci lain atau klik <a href="<?= url('index.php') ?>" class="text-[#FF6082] font-semibold underline">Lihat Semua Konser</a>.
                        </p>
                    </div>
                <?php else: ?>
                    <?php foreach ($initialEvents as $index => $ev): 
                        $idEv = (int)$ev['id_event'];
                        $isSoldOut = ((int)$ev['total_kuota'] <= 0);
                    ?>
                        <article class="concert-card modern-card flex flex-col justify-between overflow-hidden group">
                            
                            <!-- Poster Container -->
                            <div class="relative aspect-[16/10] overflow-hidden bg-slate-900 rounded-t-[19px]">
                                <a href="<?= url('detail_event.php?id=' . $idEv) ?>" class="block w-full h-full">
                                    <img src="<?= asset('uploads/posters/' . ($ev['poster'] ?? 'default.jpg')) ?>" 
                                         alt="<?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                                         onerror="this.src='<?= asset('uploads/posters/default.jpg') ?>'">
                                </a>
                                
                                <!-- Genre Badge -->
                                <div class="absolute top-3 left-3 z-10 pointer-events-none">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#0F172A]/85 backdrop-blur-md text-white shadow-xs">
                                        <?= htmlspecialchars($ev['genre_musik'] ?? 'Konser', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>

                                <!-- Sold Out or Quota Badge -->
                                <div class="absolute top-3 right-3 z-10 pointer-events-none">
                                    <?php if ($isSoldOut): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-rose-600/90 backdrop-blur-md text-white shadow-xs">
                                            Habis
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-emerald-600/90 backdrop-blur-md text-white shadow-xs">
                                            Tersedia
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Concert Content -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                <div class="space-y-1.5">
                                    <p class="text-xs font-semibold text-slate-500">
                                        <?= formatTanggalIndo($ev['tanggal_event'], false) ?> • <?= formatJam($ev['jam_event']) ?> WIB
                                    </p>
                                    <h3 class="text-lg font-bold text-slate-900 leading-snug line-clamp-1" title="<?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?>">
                                        <a href="<?= url('detail_event.php?id=' . $idEv) ?>" class="hover:text-[#FF6082] transition-colors">
                                            <?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-600 font-medium line-clamp-1">
                                        <?= htmlspecialchars($ev['pengisi_acara'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p class="text-xs text-slate-400 line-clamp-1 flex items-center gap-1.5 pt-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="truncate"><?= htmlspecialchars($ev['venue'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </p>
                                </div>

                                <!-- Price & Action -->
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between mt-auto">
                                    <div>
                                        <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400">Mulai dari</span>
                                        <span class="text-base font-extrabold text-slate-900">
                                            <?= $ev['harga_termurah'] > 0 ? formatRupiah($ev['harga_termurah']) : 'Segera Hadir' ?>
                                        </span>
                                    </div>

                                    <a href="<?= url('detail_event.php?id=' . $idEv) ?>" 
                                       class="px-4 py-2.5 rounded-xl text-xs font-semibold transition-button cursor-pointer <?= $isSoldOut ? 'bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 shadow-2xs hover:shadow-xs' : 'bg-[#FF6082] hover:bg-[#E0486D] text-white shadow-xs hover:shadow' ?>">
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

    <!-- Animation Libraries: GSAP, ScrollTrigger, Lenis, Motion -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
    <script src="<?= asset('js/animations.js') ?>"></script>

    <!-- Catalog API Endpoint Config -->
    <script>
        window.EVENTIX_API_EVENTS = '<?= url("api/events.php") ?>';
    </script>

    <!-- Catalog Live Search Script -->
    <script src="<?= asset('js/catalog.js') ?>"></script>
</body>
</html>
