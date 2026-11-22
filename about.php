<?php
/**
 * EVENTIX - Halaman Tentang Kami (About Us)
 * Full Redesign: SF Pro Display & Hallmark Editorial Craft
 */
require_once __DIR__ . '/config/app.php';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | EVENTIX</title>
    <meta name="description" content="EVENTIX adalah platform tiket konser musik resmi di Indonesia dengan jaminan keaslian alokasi promotor dan sistem pemesanan transparan.">

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
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">
        
        <!-- Editorial Manifesto Header (No Eyebrow Pill) -->
        <header class="space-y-6 max-w-3xl">
            <p class="text-xs font-semibold text-[#FF6082] tracking-normal uppercase">
                Tentang Platform
            </p>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 leading-[1.08]">
                Mengembalikan Kemurnian Pengalaman Menonton Konser Live.
            </h1>
            <p class="text-base sm:text-lg text-slate-600 leading-relaxed font-normal">
                EVENTIX didirikan atas satu prinsip sederhana: tiket pertunjukan musik harus sampai ke tangan penikmat musik sejati secara adil, transparan, dan bebas dari distorsi calo.
            </p>
        </header>

        <!-- Editorial Split: Story & Architecture -->
        <section class="grid grid-cols-1 md:grid-cols-12 gap-10 pt-4 border-t border-slate-200/90 items-start">
            <div class="md:col-span-5 space-y-3">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Mengapa Sistem Tiket Konser Harus Berubah
                </h2>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Setiap tahun, ribuan penggemar musik dirugikan oleh tiket duplikat, markup harga tidak wajar, dan antrean yang dimanipulasi bot.
                </p>
            </div>
            <div class="md:col-span-7 space-y-6 text-sm text-slate-600 leading-relaxed">
                <p>
                    Bagi kami, konser bukan komoditas spekulasi. Itu adalah ruang sakral di mana lagu-lagu yang menemani keseharian Anda dinyanyikan bersama ribuan suara di bawah gemerlap panggung stadion dan festival hall.
                </p>
                <p>
                    Oleh karena itu, kami membangun infrastruktur reservasi tiket yang terhubung langsung dengan pihak promotor konser resmi di Indonesia—dari pertunjukan arena musisi tanah air hingga tur stadion artis internasional.
                </p>
            </div>
        </section>

        <!-- 3 Pilar Integritas Sistem (Asymmetric Editorial Cards, No Gimmick Icons) -->
        <section class="space-y-6">
            <div class="border-b border-slate-200/90 pb-4">
                <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                    Standar Operasional EVENTIX
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <article class="bg-white p-8 rounded-2xl border border-slate-200/90 shadow-xs space-y-3">
                    <span class="text-xs font-mono font-bold text-slate-400 block">01</span>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Alokasi Resmi Promotor</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-normal">
                        Seluruh kategori tiket dan kuota kursi yang terbit berasal langsung dari kontrak kerja sama promotor penyelenggara acara, tanpa perantara sekunder.
                    </p>
                </article>

                <article class="bg-white p-8 rounded-2xl border border-slate-200/90 shadow-xs space-y-3">
                    <span class="text-xs font-mono font-bold text-slate-400 block">02</span>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">Pencegahan Overselling</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-normal">
                        Database transaksional kami mengunci kuota secara <em>atomic</em> pada saat checkout dilakukan, menjamin tidak ada perebutan ganda atas satu nomor tiket.
                    </p>
                </article>

                <article class="bg-white p-8 rounded-2xl border border-slate-200/90 shadow-xs space-y-3">
                    <span class="text-xs font-mono font-bold text-slate-400 block">03</span>
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">E-Ticket QR Terverifikasi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed font-normal">
                        Begitu pembayaran terkonfirmasi, e-ticket berbarcode QR dinamis langsung terbit di akun Anda, siap dipindai di ticket box lokasi acara untuk penukaran wristband.
                    </p>
                </article>

            </div>
        </section>

        <!-- Call to Action Banner -->
        <section class="bg-[#0F172A] text-white rounded-3xl p-8 sm:p-12 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="space-y-2 max-w-xl">
                <h3 class="text-xl sm:text-2xl font-extrabold tracking-tight text-white font-sf">
                    Siap Mengamankan Tiket Konser Pilihan Anda?
                </h3>
                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                    Jelajahi jadwal pertunjukan tur dunia dan konser eksklusif yang sedang membuka alokasi tiket resmi.
                </p>
            </div>
            <a href="<?= url('index.php#katalog') ?>" class="px-6 py-3.5 rounded-xl bg-[#FF6082] hover:bg-[#E0486D] text-white font-semibold text-xs tracking-normal transition-button shadow-sm hover:shadow whitespace-nowrap">
                Buka Katalog Konser
            </a>
        </section>

    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
