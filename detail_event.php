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
                        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-[1.08] tracking-tight">
                            <?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                        </h1>
                        <p class="text-base sm:text-lg text-slate-600 font-medium pt-1">
                            Artis Utama: <span class="font-bold text-slate-900"><?= htmlspecialchars($event['pengisi_acara']) ?></span>
                        </p>
                    </div>

                    <!-- Clean Editorial Metadata List (No Card-in-Card) -->
                    <div class="pt-4 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-6">
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

                <!-- 2. Syarat & Ketentuan Konser Musik -->
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

    <!-- Footer Component -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Animation Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="<?= asset('js/animations.js') ?>"></script>

    <!-- Stepper & Form Interactivity Script -->
    <script src="<?= asset('js/ticket_booking.js') ?>"></script>
</body>
</html>
