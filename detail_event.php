<?php
/**
 * EVENTIX - Halaman Detail Konser & Pemesanan Tiket
 * Full Redesign: SF Pro Display & Hallmark Curated Experience
 */

require_once __DIR__ . '/config/app.php';

// Ambil ID Event dari query parameter ?id=
$idEvent = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$idEvent) {
    setFlash('error', 'Konser musik yang Anda cari tidak ditemukan.');
    redirect('index.php');
}

// 1. Ambil Data Event + Konser dari Database
try {
    $stmtEvent = $pdo->prepare("SELECT 
                                    e.*, 
                                    k.daftar_lagu, 
                                    k.genre_musik 
                                FROM event e 
                                INNER JOIN konser k ON e.id_event = k.id_event 
                                WHERE e.id_event = :id 
                                LIMIT 1");
    $stmtEvent->execute([':id' => $idEvent]);
    $event = $stmtEvent->fetch(PDO::FETCH_ASSOC);

    if (!$event) {
        setFlash('error', 'Konser tidak ditemukan atau telah dihapus dari sistem.');
        redirect('index.php');
    }

    // 2. Ambil Semua Kategori Tiket untuk Konser Ini
    $stmtTickets = $pdo->prepare("SELECT * FROM tiket WHERE id_event = :id ORDER BY harga DESC");
    $stmtTickets->execute([':id' => $idEvent]);
    $tickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("[Detail Event Query Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat memuat data konser.');
    redirect('index.php');
}

// Pisahkan string daftar lagu menjadi array
$setlist = array_filter(array_map('trim', explode(',', $event['daftar_lagu'] ?? '')));

// Hitung harga minimum tiket & siapkan data visualisasi venue
$minPrice = 0;
if (!empty($tickets)) {
    $prices = array_column($tickets, 'harga');
    $minPrice = min($prices);
}

// Deteksi tipe denah venue: Stadium (GBK, JIS, Madya) vs Indoor Hall/Arena (ICE BSD, Istora, BCIS)
$isStadium = (bool)preg_match('/(stadium|stadion|jis|sugbk)/i', $event['venue']);

// Palet warna aksen untuk kategori tiket panggung
$zonePalette = [
    ['hex' => '#FF6082', 'bg' => 'rgba(255, 96, 130, 0.28)', 'border' => '#FF6082', 'text' => '#FF6082'], // VIP / Pink
    ['hex' => '#6366F1', 'bg' => 'rgba(99, 102, 241, 0.28)', 'border' => '#6366F1', 'text' => '#818CF8'], // CAT 1 / Indigo
    ['hex' => '#F59E0B', 'bg' => 'rgba(245, 158, 11, 0.28)', 'border' => '#F59E0B', 'text' => '#FBBF24'], // CAT 2 / Amber
    ['hex' => '#10B981', 'bg' => 'rgba(16, 185, 129, 0.28)', 'border' => '#10B981', 'text' => '#34D399'], // CAT 3 / Emerald
    ['hex' => '#06B6D4', 'bg' => 'rgba(6, 182, 212, 0.28)',  'border' => '#06B6D4', 'text' => '#22D3EE'], // CAT 4 / Cyan
];

// Pesan Flash (jika ada redirect dari order_create)
$flashError = getFlash('error');
$flashSuccess = getFlash('success');

$isUserLoggedIn = isLoggedIn();
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?> | EVENTIX</title>
    <meta name="description" content="Pemesanan tiket resmi konser <?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?> di <?= htmlspecialchars($event['venue'], ENT_QUOTES, 'UTF-8') ?>.">

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

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

        <!-- Flash Messages -->
        <?php if ($flashError): ?>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <span class="font-bold block">Pemberitahuan</span>
                    <span><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($flashSuccess): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <span class="font-bold block">Sukses</span>
                    <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        <?php endif; ?>

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs text-slate-400 font-medium">
            <a href="<?= url('index.php') ?>" class="hover:text-slate-800 transition-colors">Beranda</a>
            <span>/</span>
            <a href="<?= url('index.php#katalog') ?>" class="hover:text-slate-800 transition-colors">Katalog Konser</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md"><?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <!-- Concert Spotlight Hero (Editorial Clean, No Nested Boxes) -->
        <section class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 shadow-sm">
            <div class="flex flex-col md:flex-row items-start gap-8 lg:gap-12">
                
                <!-- Poster Artwork -->
                <div class="w-full md:w-72 lg:w-80 aspect-[16/10] md:aspect-[3/4] rounded-2xl overflow-hidden bg-slate-900 border border-slate-200 shadow-md shrink-0">
                    <img src="<?= asset('uploads/posters/' . ($event['poster'] ?? 'default.jpg')) ?>" 
                         alt="<?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?>"
                         class="w-full h-full object-cover"
                         onerror="this.src='<?= asset('uploads/posters/default.jpg') ?>'">
                </div>

                <!-- Event Header Info -->
                <div class="flex-1 space-y-6">
                    
                    <div class="space-y-2">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-[#0F172A] text-white">
                            <?= htmlspecialchars($event['genre_musik']) ?>
                        </span>
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.08] tracking-tight gsap-words-reveal">
                            <?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                        </h1>
                        <p class="text-base sm:text-lg text-slate-600 font-medium pt-1">
                            Artis Utama: <span class="font-bold text-slate-900"><?= htmlspecialchars($event['pengisi_acara']) ?></span>
                        </p>
                    </div>

                    <!-- Clean Editorial Metadata List & Share Action -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 flex-1">
                            <div class="space-y-1">
                                <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Waktu Acara</span>
                                <p class="text-base font-bold text-slate-900">
                                    <?= formatTanggalIndo($event['tanggal_event']) ?>
                                </p>
                                <p class="text-xs text-slate-500 font-medium">Mulai <?= formatJam($event['jam_event']) ?> WIB</p>
                            </div>

                            <div class="space-y-1">
                                <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Lokasi Panggung</span>
                                <p class="text-base font-bold text-slate-900">
                                    <?= htmlspecialchars($event['venue'], ENT_QUOTES, 'UTF-8') ?>
                                </p>
                                <p class="text-xs text-slate-500 font-medium">Alokasi Resmi Promotor</p>
                            </div>
                        </div>

                        <!-- Share Button -->
                        <div class="shrink-0 self-start sm:self-center">
                            <button type="button" 
                                    id="btn-share-event" 
                                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 text-xs font-semibold text-slate-700 transition-button shadow-2xs cursor-pointer">
                                <svg class="w-4 h-4 text-[#FF6082]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span id="share-btn-text">Bagikan Jadwal</span>
                            </button>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 2 Columns Layout: Left Content & Right Sticky Booking Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Concert Information & Guidelines -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-8">

                <!-- 1. Deskripsi Konser -->
                <article class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xs space-y-4">
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        Tentang Konser Ini
                    </h2>
                    <div class="text-sm text-slate-600 leading-relaxed space-y-3 font-normal">
                        <p><?= nl2br(htmlspecialchars($event['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </article>

                <!-- 2. Denah Panggung & Tata Letak Kursi Interaktif -->
                <article id="seating-map-card" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-2xs space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#FF6082] animate-pulse"></span>
                                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-sf">
                                    Denah Panggung & Tata Letak Kursi
                                </h2>
                            </div>
                            <p class="text-xs text-slate-500 font-normal mt-0.5">
                                <?= $isStadium ? 'Layout Stadion Berskala Internasional' : 'Layout Arena & Exhibition Hall Resmi' ?> • <?= htmlspecialchars($event['venue'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[11px] font-medium text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-200">
                                Sorot zona untuk cek tiket
                            </span>
                            <button type="button" 
                                    id="btn-zoom-seating" 
                                    onclick="openSeatingModal()"
                                    class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-600 transition-colors cursor-pointer" 
                                    title="Perbesar Denah Panggung">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- SVG Seating Map Graphic Frame -->
                    <div id="seating-canvas-container" class="relative w-full aspect-[16/11] sm:aspect-[16/10] bg-[#070B14] rounded-2xl overflow-hidden border border-slate-800 p-2 sm:p-4 select-none flex flex-col justify-between">
                        
                        <!-- Floating Category Tooltip on Hover -->
                        <div id="seating-tooltip" class="absolute pointer-events-none opacity-0 transition-opacity duration-150 bg-[#0F172A]/95 backdrop-blur-md text-white text-xs p-3 rounded-xl border border-slate-700 shadow-2xl z-30 space-y-1">
                            <p id="tooltip-cat-name" class="font-bold text-white leading-tight">Kategori</p>
                            <div class="flex items-center justify-between gap-3 text-[11px]">
                                <span id="tooltip-cat-price" class="font-extrabold text-[#FF6082]">-</span>
                                <span id="tooltip-cat-status" class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-400">Tersedia</span>
                            </div>
                            <p class="text-[10px] text-slate-400 pt-0.5 border-t border-slate-800">Klik untuk langsung memilih</p>
                        </div>

                        <!-- Vector Graphic Canvas -->
                        <svg id="seating-svg" viewBox="0 0 800 520" class="w-full h-full" preserveAspectRatio="xMidYMid meet">
                            <defs>
                                <pattern id="subtle-grid" width="20" height="20" patternUnits="userSpaceOnUse">
                                    <path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                </pattern>
                            </defs>

                            <!-- Background Grid Texture -->
                            <rect width="800" height="520" fill="url(#subtle-grid)" />

                            <?php if ($isStadium): ?>
                                <!-- Outer Stadium Outline Boundary -->
                                <path d="M 60 70 Q 400 -20 740 70 L 760 455 Q 400 515 40 455 Z" fill="none" stroke="#1E293B" stroke-width="1.5" stroke-dasharray="5,5" />

                                <!-- Main Stage Structure -->
                                <rect x="250" y="24" width="300" height="46" rx="6" fill="#131B2E" stroke="#334155" stroke-width="1.5"/>
                                <text x="400" y="52" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800" letter-spacing="1.5">STAGE / PANGGUNG UTAMA</text>
                                
                                <!-- Catwalk Runway & Substage -->
                                <rect x="378" y="70" width="44" height="65" fill="#131B2E" stroke="#334155" stroke-width="1.2"/>
                                <polygon points="400,155 375,135 425,135" fill="#131B2E" stroke="#334155" stroke-width="1.2"/>

                                <!-- Sound Console / FOH Desk -->
                                <rect x="355" y="260" width="90" height="24" rx="4" fill="#0A0F1D" stroke="#334155" stroke-dasharray="3,3"/>
                                <text x="400" y="276" text-anchor="middle" fill="#64748B" font-size="9" font-weight="700">FOH AUDIO / MIX</text>

                                <?php
                                    $t0 = $tickets[0] ?? null;
                                    $t1 = $tickets[1] ?? null;
                                    $t2 = $tickets[2] ?? null;
                                    $t3 = $tickets[3] ?? null;
                                ?>

                                <!-- Zone 0 (VIP / Soundcheck Pit) -->
                                <?php if ($t0): 
                                    $c0 = $zonePalette[0];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t0['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t0['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t0['harga']) ?>"
                                       data-quota="<?= (int)$t0['kuota_tersedia'] ?>">
                                        <rect x="220" y="80" width="145" height="90" rx="8" fill="<?= $c0['bg'] ?>" stroke="<?= $c0['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="435" y="80" width="145" height="90" rx="8" fill="<?= $c0['bg'] ?>" stroke="<?= $c0['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <text x="292" y="125" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t0['kategori'], ENT_QUOTES, 'UTF-8') ?> (A)</text>
                                        <text x="292" y="142" text-anchor="middle" fill="<?= $c0['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t0['harga']) ?></text>
                                        <text x="507" y="125" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t0['kategori'], ENT_QUOTES, 'UTF-8') ?> (B)</text>
                                        <text x="507" y="142" text-anchor="middle" fill="<?= $c0['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t0['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 1 (CAT 1 Floor / Festival) -->
                                <?php if ($t1): 
                                    $c1 = $zonePalette[1];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t1['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t1['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t1['harga']) ?>"
                                       data-quota="<?= (int)$t1['kuota_tersedia'] ?>">
                                        <rect x="220" y="180" width="360" height="110" rx="10" fill="<?= $c1['bg'] ?>" stroke="<?= $c1['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <text x="295" y="235" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800"><?= htmlspecialchars($t1['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="295" y="252" text-anchor="middle" fill="<?= $c1['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t1['harga']) ?></text>
                                        <text x="505" y="235" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800"><?= htmlspecialchars($t1['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="505" y="252" text-anchor="middle" fill="<?= $c1['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t1['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 2 (CAT 2 Lower Tribune) -->
                                <?php if ($t2): 
                                    $c2 = $zonePalette[2];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t2['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t2['harga']) ?>"
                                       data-quota="<?= (int)$t2['kuota_tersedia'] ?>">
                                        <rect x="70" y="80" width="135" height="210" rx="10" fill="<?= $c2['bg'] ?>" stroke="<?= $c2['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="595" y="80" width="135" height="210" rx="10" fill="<?= $c2['bg'] ?>" stroke="<?= $c2['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="220" y="300" width="360" height="75" rx="10" fill="<?= $c2['bg'] ?>" stroke="<?= $c2['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        
                                        <text x="137" y="175" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="137" y="195" text-anchor="middle" fill="<?= $c2['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t2['harga']) ?></text>
                                        
                                        <text x="662" y="175" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="662" y="195" text-anchor="middle" fill="<?= $c2['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t2['harga']) ?></text>
                                        
                                        <text x="400" y="335" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?> (Center Tribune)</text>
                                        <text x="400" y="352" text-anchor="middle" fill="<?= $c2['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t2['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 3 (CAT 3 Upper Tribune) -->
                                <?php if ($t3): 
                                    $c3 = $zonePalette[3];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t3['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t3['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t3['harga']) ?>"
                                       data-quota="<?= (int)$t3['kuota_tersedia'] ?>">
                                        <rect x="35" y="305" width="170" height="145" rx="12" fill="<?= $c3['bg'] ?>" stroke="<?= $c3['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="595" y="305" width="170" height="145" rx="12" fill="<?= $c3['bg'] ?>" stroke="<?= $c3['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="220" y="385" width="360" height="75" rx="12" fill="<?= $c3['bg'] ?>" stroke="<?= $c3['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        
                                        <text x="120" y="375" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t3['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="120" y="393" text-anchor="middle" fill="<?= $c3['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t3['harga']) ?></text>

                                        <text x="680" y="375" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t3['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="680" y="393" text-anchor="middle" fill="<?= $c3['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t3['harga']) ?></text>

                                        <text x="400" y="420" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t3['kategori'], ENT_QUOTES, 'UTF-8') ?> (Upper Ring)</text>
                                        <text x="400" y="438" text-anchor="middle" fill="<?= $c3['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t3['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                            <?php else: ?>
                                <!-- Indoor Arena / Exhibition Hall (ICE BSD, Istora, BCIS) -->
                                <rect x="50" y="15" width="700" height="475" rx="14" fill="none" stroke="#1E293B" stroke-width="1.5" stroke-dasharray="5,5"/>

                                <!-- Main Proscenium Stage -->
                                <rect x="240" y="24" width="320" height="46" rx="6" fill="#131B2E" stroke="#334155" stroke-width="1.5"/>
                                <text x="400" y="52" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800" letter-spacing="1.5">MAIN STAGE / PANGGUNG UTAMA</text>

                                <!-- Center Runway & Thrust Stage -->
                                <rect x="378" y="70" width="44" height="75" fill="#131B2E" stroke="#334155" stroke-width="1.2"/>
                                <circle cx="400" cy="155" r="18" fill="#131B2E" stroke="#334155" stroke-width="1.2"/>

                                <!-- Sound Console / FOH Desk -->
                                <rect x="355" y="260" width="90" height="24" rx="4" fill="#0A0F1D" stroke="#334155" stroke-dasharray="3,3"/>
                                <text x="400" y="276" text-anchor="middle" fill="#64748B" font-size="9" font-weight="700">FOH AUDIO / MIX</text>

                                <?php
                                    $t0 = $tickets[0] ?? null;
                                    $t1 = $tickets[1] ?? null;
                                    $t2 = $tickets[2] ?? null;
                                    $t3 = $tickets[3] ?? null;
                                    $t4 = $tickets[4] ?? null;
                                ?>

                                <!-- Zone 0 (VIP / Soundcheck Pit) -->
                                <?php if ($t0): 
                                    $c0 = $zonePalette[0];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t0['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t0['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t0['harga']) ?>"
                                       data-quota="<?= (int)$t0['kuota_tersedia'] ?>">
                                        <rect x="235" y="80" width="130" height="95" rx="8" fill="<?= $c0['bg'] ?>" stroke="<?= $c0['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="435" y="80" width="130" height="95" rx="8" fill="<?= $c0['bg'] ?>" stroke="<?= $c0['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <text x="300" y="125" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t0['kategori'], ENT_QUOTES, 'UTF-8') ?> (L)</text>
                                        <text x="300" y="142" text-anchor="middle" fill="<?= $c0['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t0['harga']) ?></text>
                                        <text x="500" y="125" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t0['kategori'], ENT_QUOTES, 'UTF-8') ?> (R)</text>
                                        <text x="500" y="142" text-anchor="middle" fill="<?= $c0['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t0['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 1 (CAT 1 Floor) -->
                                <?php if ($t1): 
                                    $c1 = $zonePalette[1];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t1['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t1['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t1['harga']) ?>"
                                       data-quota="<?= (int)$t1['kuota_tersedia'] ?>">
                                        <rect x="235" y="185" width="330" height="110" rx="8" fill="<?= $c1['bg'] ?>" stroke="<?= $c1['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <text x="300" y="238" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800"><?= htmlspecialchars($t1['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="300" y="255" text-anchor="middle" fill="<?= $c1['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t1['harga']) ?></text>
                                        <text x="500" y="238" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800"><?= htmlspecialchars($t1['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="500" y="255" text-anchor="middle" fill="<?= $c1['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t1['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 2 (CAT 2 Wings) -->
                                <?php if ($t2): 
                                    $c2 = $zonePalette[2];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t2['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t2['harga']) ?>"
                                       data-quota="<?= (int)$t2['kuota_tersedia'] ?>">
                                        <rect x="85" y="80" width="135" height="215" rx="8" fill="<?= $c2['bg'] ?>" stroke="<?= $c2['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <rect x="580" y="80" width="135" height="215" rx="8" fill="<?= $c2['bg'] ?>" stroke="<?= $c2['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        
                                        <text x="152" y="175" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="152" y="195" text-anchor="middle" fill="<?= $c2['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t2['harga']) ?></text>

                                        <text x="647" y="175" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t2['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="647" y="195" text-anchor="middle" fill="<?= $c2['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t2['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 3 (CAT 3 Rear Seating / Tribune) -->
                                <?php if ($t3): 
                                    $c3 = $zonePalette[3];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t3['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t3['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t3['harga']) ?>"
                                       data-quota="<?= (int)$t3['kuota_tersedia'] ?>">
                                        <rect x="85" y="310" width="630" height="85" rx="10" fill="<?= $c3['bg'] ?>" stroke="<?= $c3['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <text x="400" y="348" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800"><?= htmlspecialchars($t3['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="400" y="366" text-anchor="middle" fill="<?= $c3['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t3['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                                <!-- Zone 4 (CAT 4 jika ada) -->
                                <?php if ($t4): 
                                    $c4 = $zonePalette[4];
                                ?>
                                    <g class="seating-zone-group cursor-pointer transition-transform duration-150" 
                                       data-ticket-id="<?= (int)$t4['id_tiket'] ?>"
                                       data-name="<?= htmlspecialchars($t4['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                       data-price="<?= formatRupiah($t4['harga']) ?>"
                                       data-quota="<?= (int)$t4['kuota_tersedia'] ?>">
                                        <rect x="85" y="405" width="630" height="60" rx="10" fill="<?= $c4['bg'] ?>" stroke="<?= $c4['border'] ?>" stroke-width="1.5" class="zone-shape transition-all duration-200" />
                                        <text x="400" y="435" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800"><?= htmlspecialchars($t4['kategori'], ENT_QUOTES, 'UTF-8') ?></text>
                                        <text x="400" y="452" text-anchor="middle" fill="<?= $c4['text'] ?>" font-size="9" font-weight="600"><?= formatRupiah($t4['harga']) ?></text>
                                    </g>
                                <?php endif; ?>

                            <?php endif; ?>
                        </svg>

                        <!-- Bottom Help Note -->
                        <div class="flex items-center justify-between text-[10px] text-slate-400 border-t border-slate-800/80 pt-2 px-1">
                            <span>* Tata letak diadaptasi langsung dari denah panggung resmi promotor</span>
                            <span>Arah Panggung: Menghadap ke Atas</span>
                        </div>
                    </div>

                    <!-- Category Color Legend -->
                    <div class="flex flex-wrap items-center gap-2 pt-2 text-xs">
                        <?php foreach ($tickets as $idx => $tk): 
                            $clr = $zonePalette[$idx % count($zonePalette)];
                        ?>
                            <div class="seating-legend-item flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 transition-colors cursor-pointer select-none"
                                 data-ticket-id="<?= (int)$tk['id_tiket'] ?>">
                                <span class="w-3 h-3 rounded-full shrink-0" style="background-color: <?= $clr['hex'] ?>"></span>
                                <span class="font-bold text-slate-900"><?= htmlspecialchars($tk['kategori'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="text-slate-400 font-medium ml-0.5">• <?= formatRupiah($tk['harga']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <!-- 3. Syarat & Ketentuan Konser Musik -->
                <article class="bg-white rounded-3xl p-8 border border-slate-200/90 shadow-2xs space-y-4">
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                        Ketentuan Masuk & Penukaran Wristband
                    </h2>
                    <ul class="text-xs sm:text-sm text-slate-600 space-y-3 list-disc list-inside leading-relaxed font-normal">
                        <li>E-ticket resmi dengan QR code unik wajib ditunjukkan melalui smartphone saat penukaran gelang wristband di lokasi acara.</li>
                        <li>Penonton di bawah umur 12 tahun wajib didampingi oleh orang tua atau wali dewasa.</li>
                        <li>Dilarang membawa senjata tajam, kembang api, laser pointer, botol kaca, serta kamera profesional tanpa izin media resmi.</li>
                        <li>Tiket yang telah diverifikasi lunas tidak dapat dibatalkan atau dikembalikan, kecuali terdapat pengumuman resmi dari promotor.</li>
                    </ul>
                </article>

            </div>

            <!-- RIGHT COLUMN: Interactive Ticket Selection & Sticky Booking Panel -->
            <div class="lg:col-span-5 xl:col-span-4 sticky top-24">
                
                <form id="booking-form" 
                      action="<?= url('actions/order_create.php') ?>" 
                      method="POST" 
                      data-event-id="<?= $idEvent ?>" 
                      class="space-y-4">
                    
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_event" value="<?= $idEvent ?>">

                    <!-- Card Header Pilih Tiket -->
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-md space-y-5">
                        <div class="border-b border-slate-100 pb-4">
                            <h3 class="text-lg font-extrabold text-slate-900 tracking-tight">Pilih Kategori Tiket</h3>
                            <p class="text-xs text-slate-500 mt-1 font-normal">Tentukan jumlah tiket sesuai kuota panggung yang tersedia</p>
                        </div>

                        <!-- Ticket Categories List -->
                        <div class="space-y-3">
                            <?php foreach ($tickets as $t): 
                                $tId = (int)$t['id_tiket'];
                                $quota = (int)$t['kuota_tersedia'];
                                $isSoldOut = ($quota <= 0);
                            ?>
                                <div class="ticket-card p-4 rounded-2xl border border-slate-200 hover:border-slate-300 transition-colors bg-white <?= $isSoldOut ? 'opacity-50 bg-slate-50' : '' ?>"
                                     data-ticket-id="<?= $tId ?>"
                                     data-category="<?= htmlspecialchars($t['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                     data-price="<?= (float)$t['harga'] ?>"
                                     data-quota="<?= $quota ?>">
                                    
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 leading-snug">
                                                <?= htmlspecialchars($t['kategori'], ENT_QUOTES, 'UTF-8') ?>
                                            </h4>
                                            <?php if (!empty($t['deskripsi'])): ?>
                                                <p class="text-xs text-slate-500 mt-1 leading-relaxed font-normal">
                                                    <?= htmlspecialchars($t['deskripsi'], ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Badge Kuota -->
                                        <?php if ($isSoldOut): ?>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                                Habis
                                            </span>
                                        <?php elseif ($quota <= 50): ?>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                                Sisa <?= $quota ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                                                Sisa <?= $quota ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Price & Stepper Control -->
                                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400 block">Harga</span>
                                            <span class="text-sm font-extrabold text-slate-900">
                                                <?= formatRupiah($t['harga']) ?>
                                            </span>
                                        </div>

                                        <!-- Stepper - / + -->
                                        <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl border border-slate-200">
                                            <button type="button" 
                                                    class="btn-stepper w-7 h-7 rounded-lg bg-white hover:bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-sm transition-colors focus:outline-none shadow-2xs"
                                                    data-action="minus"
                                                    data-target="ticket-qty-<?= $tId ?>"
                                                    <?= $isSoldOut ? 'disabled' : '' ?>>
                                                -
                                            </button>
                                            
                                            <input type="number" 
                                                   id="ticket-qty-<?= $tId ?>"
                                                   name="tickets[<?= $tId ?>]" 
                                                   value="0" 
                                                   min="0" 
                                                   max="<?= $quota ?>"
                                                   class="ticket-qty-input w-9 text-center bg-transparent border-0 font-bold text-xs text-slate-900 focus:outline-none"
                                                   <?= $isSoldOut ? 'disabled' : '' ?>>

                                            <button type="button" 
                                                    class="btn-stepper w-7 h-7 rounded-lg bg-[#FF6082] hover:bg-[#E0486D] text-white flex items-center justify-center font-bold text-sm transition-colors focus:outline-none shadow-2xs"
                                                    data-action="plus"
                                                    data-target="ticket-qty-<?= $tId ?>"
                                                    <?= $isSoldOut ? 'disabled' : '' ?>>
                                                +
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Ringkasan Pemesanan -->
                        <div class="mt-4 pt-4 border-t border-slate-200 space-y-2.5">
                            <span class="text-xs font-semibold text-slate-700 block">Ringkasan Tiket Terpilih</span>
                            <div id="order-items-summary" class="space-y-1 bg-slate-50 p-3.5 rounded-2xl border border-slate-200/70 text-xs">
                                <p class="text-xs text-slate-400 italic text-center py-2 font-normal">Belum ada tiket yang dipilih</p>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1">
                                <span class="text-slate-500 font-medium">Total Tiket:</span>
                                <span id="summary-total-tickets" class="font-bold text-slate-900">0 Tiket</span>
                            </div>

                            <div class="flex items-center justify-between text-sm font-bold border-t border-slate-200 pt-3">
                                <span class="text-slate-900">Total Biaya:</span>
                                <span id="summary-total-price" class="text-lg font-extrabold text-slate-900">Rp 0</span>
                            </div>
                        </div>

                        <!-- Tombol Submit Pemesanan -->
                        <?php if ($isUserLoggedIn): ?>
                            <button type="submit" 
                                    id="btn-submit-order" 
                                    disabled
                                    class="w-full py-3.5 px-4 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#E0486D] transition-button opacity-50 cursor-not-allowed shadow-sm hover:shadow">
                                <span>Lanjut ke Pembayaran</span>
                            </button>
                        <?php else: ?>
                            <div class="space-y-2">
                                <button type="submit" 
                                        id="btn-submit-order" 
                                        disabled
                                        class="w-full py-3.5 px-4 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#E0486D] transition-button opacity-50 cursor-not-allowed shadow-sm hover:shadow">
                                    <span>Masuk untuk Memesan</span>
                                </button>
                                <p class="text-[11px] text-center text-slate-500 font-normal">
                                    Anda akan diarahkan ke halaman login terlebih dahulu.
                                </p>
                            </div>
                        <?php endif; ?>

                    </div>

                </form>

            </div>

        </div>

    </main>

    <!-- Modal Zoom Denah Panggung -->
    <div id="seating-modal" class="fixed inset-0 z-50 hidden bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4 sm:p-6">
        <div class="bg-[#070B14] rounded-3xl border border-slate-800 w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl">
            <div class="flex items-center justify-between p-4 sm:p-5 border-b border-slate-800">
                <div class="space-y-0.5">
                    <h3 class="text-base font-bold text-white font-sf">Denah Panggung & Kategori Kursi</h3>
                    <p class="text-xs text-slate-400"><?= htmlspecialchars($event['venue'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <button type="button" onclick="closeSeatingModal()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="modal-svg-target" class="p-4 sm:p-6 overflow-auto flex-1 flex items-center justify-center">
                <!-- Target Container -->
            </div>
        </div>
    </div>

    <!-- Sticky Mobile Action Bar (< lg) -->
    <div id="mobile-bottom-bar" class="fixed bottom-0 inset-x-0 bg-white/95 backdrop-blur-xl border-t border-slate-200/90 px-4 py-3 z-40 lg:hidden shadow-[0_-4px_20px_rgba(0,0,0,0.06)] flex items-center justify-between gap-4 transition-all duration-300">
        <div class="space-y-0.5">
            <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-400">Harga Tiket Mulai</span>
            <span class="text-base font-extrabold text-slate-900 font-sf">
                <?= $minPrice > 0 ? formatRupiah($minPrice) : 'Segera Hadir' ?>
            </span>
        </div>
        <button type="button" 
                onclick="scrollToBooking()" 
                class="px-5 py-2.5 rounded-xl bg-[#FF6082] hover:bg-[#E0486D] text-white font-semibold text-xs transition-button shadow-sm cursor-pointer">
            Pilih Tiket
        </button>
    </div>

    <!-- Footer Component -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Animation Libraries: GSAP, ScrollTrigger, Lenis, Motion -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
    <script src="<?= asset('js/animations.js') ?>"></script>

    <!-- Stepper & Form Interactivity Script -->
    <script src="<?= asset('js/ticket_booking.js') ?>"></script>
</body>
</html>
