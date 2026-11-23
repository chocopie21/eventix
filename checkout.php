<?php
/**
 * EVENTIX - Halaman Konfirmasi Pesanan & Pembayaran
 * Modern Minimalist Design
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
 */

require_once __DIR__ . '/config/app.php';

// 1. Wajib Login
requireLogin('login.php');

$user = currentUser();
$idPengguna = (int)$user['id_pengguna'];
$idPesanan = filter_input(INPUT_GET, 'id_pesanan', FILTER_VALIDATE_INT);

if (!$idPesanan) {
    setFlash('error', 'Nomor pesanan tidak valid.');
    redirect('my_orders.php');
}

// 2. Ambil data pesanan dan event terkait
try {
    $stmtOrder = $pdo->prepare("SELECT 
                                    p.*, 
                                    e.id_event,
                                    e.nama_event,
                                    e.tanggal_event,
                                    e.jam_event,
                                    e.venue,
                                    e.poster,
                                    e.pengisi_acara,
                                    k.genre_musik
                                FROM pesanan p
                                INNER JOIN detail_pesanan dp ON p.id_pesanan = dp.id_pesanan
                                INNER JOIN tiket t ON dp.id_tiket = t.id_tiket
                                INNER JOIN event e ON t.id_event = e.id_event
                                INNER JOIN konser k ON e.id_event = k.id_event
                                WHERE p.id_pesanan = :id_pesanan AND p.id_pengguna = :id_pengguna
                                LIMIT 1");
    $stmtOrder->execute([
        ':id_pesanan'  => $idPesanan,
        ':id_pengguna' => $idPengguna
    ]);
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        setFlash('error', 'Pesanan tidak ditemukan atau Anda tidak berhak mengakses halaman ini.');
        redirect('my_orders.php');
    }

    // 3. Ambil rincian tiket yang dipesan
    $stmtDetails = $pdo->prepare("SELECT 
                                      dp.*, 
                                      t.kategori, 
                                      t.deskripsi AS deskripsi_tiket,
                                      t.harga AS harga_satuan 
                                  FROM detail_pesanan dp 
                                  INNER JOIN tiket t ON dp.id_tiket = t.id_tiket 
                                  WHERE dp.id_pesanan = :id_pesanan");
    $stmtDetails->execute([':id_pesanan' => $idPesanan]);
    $orderDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("[Checkout Query Error] " . $e->getMessage());
    setFlash('error', 'Terjadi kesalahan sistem saat memuat data pesanan.');
    redirect('my_orders.php');
}

$flashError = getFlash('error');
$flashSuccess = getFlash('success');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembayaran #EVX-<?= $idPesanan ?> | EVENTIX</title>

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

    <!-- Navbar -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500">
            <a href="<?= url('index.php') ?>" class="hover:text-slate-900 transition-colors">Beranda</a>
            <span>/</span>
            <a href="<?= url('my_orders.php') ?>" class="hover:text-slate-900 transition-colors">Riwayat Pesanan</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold">Konfirmasi #EVX-<?= $idPesanan ?></span>
        </nav>

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

        <!-- Order Status Header Banner -->
        <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <span class="text-xs font-mono font-bold px-2.5 py-0.5 rounded-md bg-slate-100 text-slate-800 border border-slate-200">
                        #EVX-<?= $order['id_pesanan'] ?>
                    </span>
                    <span class="text-xs text-slate-500 font-medium">
                        Dibuat: <?= formatTanggalIndo(date('Y-m-d', strtotime($order['tanggal']))) ?> • <?= date('H:i', strtotime($order['tanggal'])) ?> WIB
                    </span>
                </div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight gsap-words-reveal">
                    Konfirmasi Pesanan & Pembayaran
                </h1>
            </div>

            <!-- Status Badge -->
            <div>
                <?php if ($order['status'] === 'menunggu'): ?>
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-800 border border-amber-200 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Menunggu Pembayaran</span>
                    </span>
                <?php elseif ($order['status'] === 'lunas'): ?>
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>Lunas (E-Ticket Aktif)</span>
                    </span>
                <?php else: ?>
                    <span class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-800 border border-rose-200">
                        Dibatalkan
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Expiry Countdown Timer (Hanya tampil saat status menunggu) -->
        <?php if ($order['status'] === 'menunggu'): ?>
            <div id="countdown-card" class="bg-white rounded-2xl p-5 border border-amber-200/90 shadow-2xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Selesaikan Pembayaran Sebelum Batas Waktu Berakhir</p>
                            <p class="text-[11px] text-slate-500 font-normal">Kuota tiket Anda diamankan. Selesaikan transfer dan unggah bukti sebelum waktu habis.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 self-start sm:self-auto bg-amber-50/80 px-4 py-2 rounded-xl border border-amber-200/80 shrink-0">
                        <span class="text-[10px] uppercase font-bold tracking-wider text-amber-800">Sisa Waktu:</span>
                        <span id="countdown-timer" class="font-mono text-xl font-black text-[#FF6082] tracking-tight">15:00</span>
                    </div>
                </div>
                <!-- Progress Line -->
                <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                    <div id="countdown-progress-bar" class="bg-[#FF6082] h-full transition-all duration-1000 ease-linear rounded-full" style="width: 100%;"></div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Two Columns Layout: Summary & Payment Form -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: Rincian Event & Detail Tiket -->
            <div class="lg:col-span-6 space-y-6">
                
                <!-- Concert Brief Card -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs space-y-4">
                    <div class="flex items-start gap-4">
                        <img src="<?= asset('uploads/posters/' . ($order['poster'] ?? 'default.jpg')) ?>" 
                             alt="<?= htmlspecialchars($order['nama_event'], ENT_QUOTES, 'UTF-8') ?>"
                             class="w-20 h-28 object-cover rounded-xl border border-slate-200 shadow-xs shrink-0"
                             onerror="this.src='<?= asset('uploads/posters/default.jpg') ?>'">
                        <div class="space-y-1.5 flex-1 min-w-0">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-800 border border-slate-200 inline-block">
                                <?= htmlspecialchars($order['genre_musik'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <h2 class="text-base font-bold text-slate-900 line-clamp-2 leading-snug">
                                <?= htmlspecialchars($order['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                            </h2>
                            <p class="text-xs text-slate-500 font-medium line-clamp-1">
                                <?= htmlspecialchars($order['pengisi_acara'], ENT_QUOTES, 'UTF-8') ?>
                            </p>
                        </div>
                    </div>

                    <div class="pt-3.5 border-t border-slate-100 text-xs text-slate-600 space-y-2">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span><?= formatTanggalIndo($order['tanggal_event']) ?> • <?= formatJam($order['jam_event']) ?> WIB</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="truncate"><?= htmlspecialchars($order['venue'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Rincian Tiket yang Dipesan -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                        Rincian Tiket Pesanan
                    </h3>

                    <div class="divide-y divide-slate-100">
                        <?php foreach ($orderDetails as $item): ?>
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between text-xs">
                                <div>
                                    <span class="font-bold text-slate-900 block"><?= htmlspecialchars($item['kategori'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="text-slate-500 text-[11px]"><?= $item['jumlah'] ?> Tiket x <?= formatRupiah($item['harga_satuan']) ?></span>
                                </div>
                                <span class="font-extrabold text-slate-900"><?= formatRupiah($item['sub_total']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="pt-3 border-t border-slate-200/80 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Biaya Layanan Platform</span>
                            <span class="text-emerald-700 font-bold">GRATIS</span>
                        </div>
                        <div class="flex justify-between text-sm font-bold text-slate-900 pt-2.5 border-t border-slate-100">
                            <span>Total Pembayaran</span>
                            <span class="text-base text-slate-950 font-extrabold"><?= formatRupiah($order['harga']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Data Pemesan -->
                <div class="bg-slate-100/80 rounded-2xl p-5 border border-slate-200/80 text-xs text-slate-600 space-y-1.5">
                    <span class="font-bold text-slate-900 block">Informasi Data Pemesan:</span>
                    <p><span class="text-slate-500">Nama:</span> <strong class="text-slate-900"><?= htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                    <p><span class="text-slate-500">Email:</span> <strong class="text-slate-900"><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></strong></p>
                    <p><span class="text-slate-500">No. HP:</span> <strong class="text-slate-900"><?= htmlspecialchars($user['no_telepon'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></p>
                </div>

            </div>

            <!-- RIGHT COLUMN: Metode Pembayaran & Konfirmasi -->
            <div class="lg:col-span-6 space-y-6">

                <!-- Pembayaran QRIS Resmi -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Metode Pembayaran QRIS
                            </h3>
                            <span class="text-xs text-slate-500 font-medium">Scan & Bayar Otomatis Terverifikasi</span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>QRIS Aktif</span>
                        </span>
                    </div>

                    <!-- Kotak QRIS Interaktif -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50/80 border border-slate-200/90 text-center space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-900 text-white text-[11px] font-semibold shadow-xs">
                            <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span>QRIS Standar Pembayaran Nasional</span>
                        </div>

                        <!-- Gambar Poster QRIS Resmi -->
                        <div class="flex justify-center">
                            <div class="bg-white p-3 rounded-2xl shadow-sm border border-slate-200/90 max-w-[290px] w-full">
                                <img src="<?= asset('images/qris_payment.png') ?>" 
                                     alt="QRIS Gopay Merchant - NAFI DIGITAL & KREATIF" 
                                     class="w-full h-auto rounded-xl object-contain shadow-2xs">
                            </div>
                        </div>

                        <!-- Info Merchant & Panduan Pembayaran -->
                        <div class="space-y-1.5 pt-1">
                            <h4 class="text-sm font-bold text-slate-900 tracking-tight">NAFI, DIGITAL & KREATIF</h4>
                            <p class="text-xs font-mono text-slate-500 font-semibold">NMID: ID1025389679826</p>
                            <p class="text-[11px] text-slate-500 leading-relaxed max-w-sm mx-auto">
                                Buka aplikasi e-wallet atau mobile banking favorit Anda (<strong>GoPay, OVO, DANA, ShopeePay, BCA Mobile, Livin' Mandiri, BRImo, LinkAja</strong>), lalu arahkan kamera ke kode QR di atas.
                            </p>
                        </div>
                    </div>

                    <!-- Total yang Harus Dibayar -->
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-950 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-wider text-amber-700 block">Total yang Harus Dibayar</span>
                            <span class="text-base font-extrabold text-amber-950"><?= formatRupiah($order['harga']) ?></span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-100/80 text-amber-900 font-semibold text-[11px] border border-amber-200/60">
                            Bebas Biaya Admin
                        </span>
                    </div>
                </div>

                <!-- FORM KONFIRMASI PEMBAYARAN TANPA UPLOAD FILE -->
                <?php if ($order['status'] !== 'lunas'): ?>
                    <form action="<?= url('actions/payment_upload.php') ?>" 
                          method="POST" 
                          class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs space-y-4">
                        
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_pesanan" value="<?= $idPesanan ?>">
                        <input type="hidden" name="metode" id="input-metode" value="QRIS All E-Wallet">

                        <div>
                            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">
                                Konfirmasi Pembayaran
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Selesaikan pemesanan tiket Anda tanpa antre dan tanpa ribet.</p>
                        </div>

                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs text-slate-600">
                            <div class="flex items-center gap-2 text-emerald-700 font-semibold">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>E-Ticket QR Langsung Aktif</span>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-relaxed">
                                Setelah Anda menekan tombol konfirmasi di bawah, tiket resmi akan langsung diterbitkan secara digital dan dapat segera digunakan di pintu masuk konser.
                            </p>
                        </div>

                        <!-- Tombol Konfirmasi Bayar -->
                        <button type="submit" 
                                class="w-full flex items-center justify-center gap-2 py-3.5 px-4 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] transition-all shadow-xs hover:shadow-sm cursor-pointer">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Saya Sudah Scan & Bayar via QRIS</span>
                        </button>
                    </form>
                <?php else: ?>
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-2xs space-y-4 text-center">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">Pembayaran Terverifikasi Lunas</h3>
                        <p class="text-xs text-slate-500">E-Ticket Anda telah aktif dan dapat langsung ditunjukkan di venue konser.</p>
                        <a href="<?= url('my_orders.php') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#0F172A] text-white font-semibold text-xs hover:bg-slate-800 transition-all">
                            <span>Buka E-Ticket Saya</span>
                        </a>
                    </div>
                <?php endif; ?>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Animation Libraries: GSAP, ScrollTrigger, Lenis, Motion -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/motion@11.11.13/dist/motion.js"></script>
    <script src="<?= asset('js/animations.js') ?>"></script>

    <!-- Interactive Scripts -->
    <script>
        // Countdown Timer Logic (15 Menit = 900 Detik)
        (function() {
            const timerElem = document.getElementById('countdown-timer');
            const progressElem = document.getElementById('countdown-progress-bar');
            if (!timerElem) return;

            const orderId = <?= (int)$order['id_pesanan'] ?>;
            const storageKey = 'eventix_timer_order_' + orderId;
            const totalDuration = 15 * 60; // 15 menit

            let startTime = localStorage.getItem(storageKey);
            if (!startTime) {
                startTime = Date.now();
                localStorage.setItem(storageKey, startTime);
            } else {
                startTime = parseInt(startTime, 10);
            }

            function updateTimer() {
                const elapsed = Math.floor((Date.now() - startTime) / 1000);
                const remaining = totalDuration - elapsed;

                if (remaining <= 0) {
                    timerElem.textContent = '00:00';
                    if (progressElem) progressElem.style.width = '0%';
                    
                    const submitBtn = document.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.setAttribute('disabled', 'disabled');
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                    return;
                }

                const minutes = Math.floor(remaining / 60);
                const seconds = remaining % 60;
                timerElem.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

                if (progressElem) {
                    const percent = Math.max(0, (remaining / totalDuration) * 100);
                    progressElem.style.width = percent + '%';
                }

                setTimeout(updateTimer, 1000);
            }

            updateTimer();
        })();
    </script>
</body>
</html>
