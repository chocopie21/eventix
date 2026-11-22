<?php
/**
 * EVENTIX - Halaman Kontak & Kemitraan Promotor
 * Full Redesign: SF Pro Display & Hallmark Editorial Craft
 */
require_once __DIR__ . '/config/app.php';

$sentSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (csrf_verify()) {
        $sentSuccess = true;
        setFlash('success', 'Pesan Anda telah berhasil terkirim! Tim EVENTIX akan merespons melalui email/WhatsApp dalam 1x24 jam kerja.');
    } else {
        setFlash('error', 'Sesi keamanan kedaluwarsa. Silakan coba kembali.');
    }
}

$flashSuccess = getFlash('success');
$flashError = getFlash('error');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami & Kemitraan | EVENTIX</title>
    <meta name="description" content="Hubungi tim layanan pelanggan dan kemitraan promotor konser musik EVENTIX Indonesia.">

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

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-12">
        
        <!-- Header (No Eyebrow Pill) -->
        <header class="space-y-4 max-w-3xl">
            <p class="text-xs font-semibold text-[#FF6082] tracking-normal uppercase">
                Komunikasi & Kemitraan
            </p>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.08] gsap-words-reveal">
                Hubungi Tim EVENTIX
            </h1>
            <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed">
                Pertanyaan seputar tiket penonton atau diskusi integrasi tiket resmi untuk promotor konser musik.
            </p>
        </header>

        <!-- Flash Messages -->
        <?php if ($flashSuccess): ?>
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-start">
            
            <!-- Left: Kontak Detail (5 Cols) -->
            <div class="md:col-span-5 space-y-4">
                
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Surat Elektronik</span>
                    <h3 class="text-base font-bold text-slate-900">support@eventix.id</h3>
                    <p class="text-xs text-slate-500 font-normal leading-relaxed">
                        Pertanyaan penonton, kendala e-ticket, dan korespondensi promotor.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Layanan Cepat</span>
                    <h3 class="text-base font-bold text-slate-900">+62 812-9876-5432</h3>
                    <p class="text-xs text-slate-500 font-normal leading-relaxed">
                        WhatsApp Helpdesk aktif setiap hari pada jam operasional 09:00 – 21:00 WIB.
                    </p>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-2xs space-y-2">
                    <span class="text-xs uppercase font-bold tracking-wider text-slate-400 block">Alamat Kantor</span>
                    <h3 class="text-sm font-bold text-slate-900">Gedung Cyber 2 Lantai 15</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-normal">
                        Jl. H.R. Rasuna Said Blok X-5 No. 13, Kuningan Timur, Setiabudi, Jakarta Selatan 12950.
                    </p>
                </div>

            </div>

            <!-- Right: Formulir Pesan & Kemitraan (7 Cols) -->
            <div class="md:col-span-7 bg-white p-8 rounded-3xl border border-slate-200/90 shadow-xs space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 tracking-tight">Kirim Pesan / Pengajuan Promotor</h2>
                    <p class="text-xs text-slate-500 mt-1 font-normal">Tuliskan kebutuhan Anda secara ringkas. Tim kami akan merespons dalam 1x24 jam.</p>
                </div>

                <form action="contact.php" method="POST" class="space-y-4">
                    <?= csrf_field() ?>

                    <div class="space-y-1.5">
                        <label for="nama" class="block text-xs font-semibold text-slate-700">Nama Lengkap</label>
                        <input type="text" 
                               id="nama" 
                               name="nama" 
                               required 
                               placeholder="Nama Anda atau perwakilan promotor"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#FF6082] transition-colors">
                    </div>

                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-semibold text-slate-700">Alamat Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               required 
                               placeholder="nama@domain.com"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#FF6082] transition-colors">
                    </div>

                    <div class="space-y-1.5">
                        <label for="subjek" class="block text-xs font-semibold text-slate-700">Subjek Keperluan</label>
                        <select id="subjek" 
                                name="subjek" 
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 focus:outline-none focus:border-[#FF6082] transition-colors">
                            <option value="pertanyaan_tiket">Pertanyaan Pemesanan / E-Ticket</option>
                            <option value="kemitraan_promotor">Kemitraan Penjualan Tiket Promotor</option>
                            <option value="kendala_pembayaran">Kendala Transaksi Pembayaran</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label for="pesan" class="block text-xs font-semibold text-slate-700">Isi Pesan</label>
                        <textarea id="pesan" 
                                  name="pesan" 
                                  rows="4" 
                                  required 
                                  placeholder="Jelaskan detail pertanyaan atau rencana konser musik Anda..."
                                  class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#FF6082] transition-colors"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-3.5 px-4 rounded-xl text-xs font-semibold text-white bg-[#FF6082] hover:bg-[#E0486D] transition-button shadow-sm hover:shadow">
                        Kirim Pesan
                    </button>
                </form>
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
</body>
</html>
