<?php
/**
 * EVENTIX - Halaman Pusat Bantuan & FAQ
 * Full Redesign: SF Pro Display & Hallmark Editorial Craft
 */
require_once __DIR__ . '/config/app.php';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Bantuan & FAQ | EVENTIX</title>
    <meta name="description" content="Pertanyaan umum dan panduan pemesanan tiket konser musik resmi di EVENTIX Indonesia.">

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
                Pusat Bantuan
            </p>
            <h1 class="text-3xl sm:text-5xl font-extrabold text-slate-900 tracking-tight leading-[1.08] gsap-words-reveal">
                Pertanyaan yang Sering Diajukan
            </h1>
            <p class="text-sm sm:text-base text-slate-600 font-normal leading-relaxed">
                Panduan lengkap seputar pembelian tiket, konfirmasi pembayaran instan, dan penukaran wristband di lokasi konser.
            </p>
        </header>

        <!-- FAQ Editorial Accordion List (Clean Hairline Borders, No Number Boxes) -->
        <section class="bg-white rounded-3xl border border-slate-200/90 shadow-xs divide-y divide-slate-100 overflow-hidden">
            
            <!-- Item 1 -->
            <article class="p-6 sm:p-8 space-y-3">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    Bagaimana cara memesan tiket konser di EVENTIX?
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    Pilih konser musik yang ingin ditonton di halaman Beranda atau Katalog, buka detail event, tentukan kategori tiket dan jumlah tiket dengan tombol stepper (+), lalu klik <strong>Lanjut ke Pembayaran</strong>. Anda akan diarahkan ke halaman checkout untuk memilih metode pembayaran transfer bank atau QRIS.
                </p>
            </article>

            <!-- Item 2 -->
            <article class="p-6 sm:p-8 space-y-3">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    Metode pembayaran apa saja yang didukung?
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    Kami mendukung transfer bank resmi (BCA, Mandiri, BNI) serta QRIS All E-Wallet (GoPay, OVO, DANA, LinkAja, ShopeePay). Pembayaran diverifikasi langsung oleh sistem tanpa perlu repot mengunggah foto bukti fisik.
                </p>
            </article>

            <!-- Item 3 -->
            <article class="p-6 sm:p-8 space-y-3">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    Kapan E-Ticket saya akan terbit?
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    Setelah Anda mengonfirmasi pembayaran di halaman checkout, E-Ticket resmi ber-QR Code unik akan langsung aktif seketika di menu <strong>Tiket Saya</strong> tanpa waktu tunggu.
                </p>
            </article>

            <!-- Item 4 -->
            <article class="p-6 sm:p-8 space-y-3">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    Bagaimana cara penukaran gelang wristband di lokasi acara?
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    Tunjukkan E-Ticket digital berbarcode QR Code langsung dari layar ponsel Anda kepada staf penukaran di ticket box venue, beserta kartu identitas resmi (KTP/SIM/Paspor) yang sesuai dengan nama pada akun pemesan.
                </p>
            </article>

            <!-- Item 5 -->
            <article class="p-6 sm:p-8 space-y-3">
                <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    Apakah tiket yang sudah dibeli bisa dibatalkan atau refund?
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal">
                    Sesuai dengan ketentuan standar promotor konser, tiket yang telah lunas bersifat <em>non-refundable</em> (tidak dapat dikembalikan), kecuali terjadi pembatalan atau perubahan jadwal resmi dari pihak promotor penyelenggara acara.
                </p>
            </article>

        </section>

        <!-- Callout Hubungi CS -->
        <section class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/90 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 shadow-xs">
            <div class="space-y-1.5">
                <h3 class="text-lg font-bold text-slate-900 tracking-tight">Masih Memerlukan Bantuan?</h3>
                <p class="text-xs sm:text-sm text-slate-500 font-normal">
                    Tim dukungan penonton kami siap membantu menjawab pertanyaan seputar transaksi Anda.
                </p>
            </div>
            <a href="<?= url('contact.php') ?>" class="px-6 py-3 rounded-xl bg-[#0F172A] hover:bg-[#1E293B] text-white font-semibold text-xs transition-colors shrink-0 whitespace-nowrap">
                Hubungi Tim Dukungan
            </a>
        </section>

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
