<?php
/**
 * EVENTIX - Halaman Detail Konser & Pemesanan Tiket
 * Human-Crafted Editorial Minimalist
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
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

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Flash Messages -->
        <?php if ($flashError): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-800 text-xs sm:text-sm">
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
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-800 text-xs sm:text-sm">
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
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="<?= url('index.php') ?>" class="hover:text-slate-900 transition-colors">Beranda</a>
            <span>/</span>
            <a href="<?= url('index.php#katalog') ?>" class="hover:text-slate-900 transition-colors">Katalog Konser</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold truncate max-w-xs sm:max-w-md"><?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?></span>
        </nav>

        <!-- Concert Spotlight Banner (Clean Architectural Header - No Eyebrows) -->
        <div class="rounded-2xl bg-slate-900 text-white p-6 sm:p-8 lg:p-10 mb-8 border border-slate-800">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6 lg:gap-8">
                
                <!-- Poster Image -->
                <div class="w-48 sm:w-56 md:w-60 aspect-[3/4] rounded-xl overflow-hidden bg-slate-800 border border-slate-700 shadow-md shrink-0">
                    <img src="<?= asset('uploads/posters/' . ($event['poster'] ?? 'default.jpg')) ?>" 
                         alt="<?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?>"
                         class="w-full h-full object-cover"
                         onerror="this.src='<?= asset('uploads/posters/default.jpg') ?>'">
                </div>

                <!-- Event Header Info -->
                <div class="flex-1 text-center md:text-left space-y-3">
                    <h1 class="text-2xl sm:text-4xl font-bold font-display text-white leading-tight">
                        <?= htmlspecialchars($event['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-300 font-medium">
                        <?= htmlspecialchars($event['pengisi_acara']) ?> • Genre <?= htmlspecialchars($event['genre_musik']) ?>
                    </p>

                    <!-- Key Event Details Grid -->
                    <div class="pt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm text-slate-300">
                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/70 border border-slate-700/60">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <span class="text-[11px] text-slate-400 block">Jadwal tanggal</span>
                                <span class="font-semibold text-white"><?= formatTanggalIndo($event['tanggal_event']) ?></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/70 border border-slate-700/60">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <span class="text-[11px] text-slate-400 block">Waktu mulai</span>
                                <span class="font-semibold text-white"><?= formatJam($event['jam_event']) ?> WIB</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/70 border border-slate-700/60">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="truncate">
                                <span class="text-[11px] text-slate-400 block">Lokasi venue</span>
                                <span class="font-semibold text-white truncate block"><?= htmlspecialchars($event['venue'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-800/70 border border-slate-700/60">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <div>
                                <span class="text-[11px] text-slate-400 block">Jaminan tiket</span>
                                <span class="font-semibold text-white">Tiket Resmi Promotor</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- 2 Columns Layout: Left Content & Right Sticky Booking Panel -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Concert Information, Setlist, Guidelines -->
            <div class="lg:col-span-7 xl:col-span-8 space-y-6">

                <!-- 1. Deskripsi Konser -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-3">
                    <h2 class="text-base font-bold font-heading text-slate-900">
                        Tentang Konser Ini
                    </h2>
                    <div class="text-sm text-slate-600 leading-relaxed space-y-3">
                        <p><?= nl2br(htmlspecialchars($event['deskripsi'], ENT_QUOTES, 'UTF-8')) ?></p>
                    </div>
                </div>

                <!-- 2. Setlist Lagu -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-bold font-heading text-slate-900">
                            Daftar Lagu Konser
                        </h2>
                        <span class="text-xs text-slate-500 font-medium">
                            <?= count($setlist) ?> Lagu
                        </span>
                    </div>

                    <?php if (!empty($setlist)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                            <?php foreach ($setlist as $index => $song): ?>
                                <div class="flex items-center gap-3 p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                    <span class="w-6 h-6 rounded bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center shrink-0">
                                        <?= $index + 1 ?>
                                    </span>
                                    <span class="text-xs font-semibold text-slate-800 truncate">
                                        <?= htmlspecialchars($song, ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs text-slate-400 italic">Daftar lagu resmi akan diumumkan mendekati hari pelaksanaan konser.</p>
                    <?php endif; ?>
                </div>

                <!-- 3. Syarat & Ketentuan Konser Musik -->
                <div class="bg-white rounded-xl p-6 border border-slate-200 space-y-3">
                    <h2 class="text-base font-bold font-heading text-slate-900">
                        Ketentuan Masuk Venue & Penukaran Gelang
                    </h2>
                    <ul class="text-xs text-slate-600 space-y-2 list-disc list-inside leading-relaxed">
                        <li>E-ticket resmi dengan barcode QR unik wajib ditunjukkan melalui smartphone saat penukaran gelang wristband di lokasi acara.</li>
                        <li>Penonton di bawah umur 12 tahun wajib didampingi oleh orang tua atau wali dewasa.</li>
                        <li>Dilarang membawa senjata tajam, kembang api, laser pointer, botol kaca, serta kamera profesional tanpa izin media resmi.</li>
                        <li>Tiket yang telah diverifikasi lunas tidak dapat dibatalkan atau dikembalikan, kecuali pembatalan resmi dari pihak penyelenggara.</li>
                    </ul>
                </div>

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
                    <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs space-y-4">
                        <div class="border-b border-slate-100 pb-3">
                            <h3 class="text-base font-bold font-heading text-slate-900">Pilih Kategori Tiket</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Tentukan jumlah tiket sesuai kuota yang tersedia</p>
                        </div>

                        <!-- Ticket Categories List -->
                        <div class="space-y-3">
                            <?php foreach ($tickets as $t): 
                                $tId = (int)$t['id_tiket'];
                                $quota = (int)$t['kuota_tersedia'];
                                $isSoldOut = ($quota <= 0);
                            ?>
                                <div class="ticket-card p-3.5 rounded-lg border border-slate-200 hover:border-slate-300 transition-colors bg-white <?= $isSoldOut ? 'opacity-60 bg-slate-50' : '' ?>"
                                     data-ticket-id="<?= $tId ?>"
                                     data-category="<?= htmlspecialchars($t['kategori'], ENT_QUOTES, 'UTF-8') ?>"
                                     data-price="<?= (float)$t['harga'] ?>"
                                     data-quota="<?= $quota ?>">
                                    
                                    <div class="flex items-start justify-between gap-2">
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 leading-snug">
                                                <?= htmlspecialchars($t['kategori'], ENT_QUOTES, 'UTF-8') ?>
                                            </h4>
                                            <?php if (!empty($t['deskripsi'])): ?>
                                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                                    <?= htmlspecialchars($t['deskripsi'], ENT_QUOTES, 'UTF-8') ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Badge Kuota -->
                                        <?php if ($isSoldOut): ?>
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 shrink-0">
                                                Habis
                                            </span>
                                        <?php elseif ($quota <= 50): ?>
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-800 border border-amber-200 shrink-0">
                                                Sisa <?= $quota ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                                                Sisa <?= $quota ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Price & Stepper Control -->
                                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                                        <div>
                                            <span class="text-[11px] text-slate-500 block">Harga satuan</span>
                                            <span class="text-sm font-bold text-slate-900">
                                                <?= formatRupiah($t['harga']) ?>
                                            </span>
                                        </div>

                                        <!-- Stepper - / + -->
                                        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg border border-slate-200">
                                            <button type="button" 
                                                    class="btn-stepper w-7 h-7 rounded bg-white hover:bg-slate-200 text-slate-800 flex items-center justify-center font-bold text-sm transition-colors focus:outline-none"
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
                                                    class="btn-stepper w-7 h-7 rounded bg-[#FF6082] hover:bg-[#e54b6d] text-white flex items-center justify-center font-bold text-sm transition-colors focus:outline-none"
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
                        <div class="mt-4 pt-4 border-t border-slate-200 space-y-2">
                            <span class="text-xs font-semibold text-slate-700 block">Ringkasan Tiket Terpilih</span>
                            <div id="order-items-summary" class="space-y-1 bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs">
                                <p class="text-xs text-slate-400 italic text-center py-2">Belum ada tiket yang dipilih</p>
                            </div>

                            <div class="flex items-center justify-between text-xs pt-1">
                                <span class="text-slate-500">Total Tiket:</span>
                                <span id="summary-total-tickets" class="font-bold text-slate-900">0 Tiket</span>
                            </div>

                            <div class="flex items-center justify-between text-sm font-bold border-t border-slate-200 pt-2">
                                <span class="text-slate-900">Total Biaya:</span>
                                <span id="summary-total-price" class="text-base font-bold text-slate-900">Rp 0</span>
                            </div>
                        </div>

                        <!-- Tombol Submit Pemesanan -->
                        <?php if ($isUserLoggedIn): ?>
                            <button type="submit" 
                                    id="btn-submit-order" 
                                    disabled
                                    class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg text-xs sm:text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] transition-colors opacity-50 cursor-not-allowed shadow-xs">
                                <span>Lanjut ke Pembayaran</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        <?php else: ?>
                            <div class="space-y-2">
                                <button type="submit" 
                                        id="btn-submit-order" 
                                        disabled
                                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-lg text-xs sm:text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] transition-colors opacity-50 cursor-not-allowed shadow-xs">
                                    <span>Masuk untuk Memesan</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                                <p class="text-[11px] text-center text-slate-500">
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

    <!-- Ticket Booking Interactive Script -->
    <script src="<?= asset('js/ticket_booking.js') ?>"></script>
</body>
</html>
