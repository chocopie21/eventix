<?php
/**
 * EVENTIX - Halaman Pusat Bantuan & FAQ
 * Modern Minimalist Design
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
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
                        sans: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        heading: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        display: ['Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-4xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-10">
        
        <!-- Header Minimalist -->
        <div class="text-center space-y-3 max-w-xl mx-auto">
            <span class="px-3.5 py-1 rounded-full text-xs font-semibold bg-[#fff1f4] text-[#c5294e] border border-[#fed7e2] inline-block">
                Pusat Bantuan
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                Pertanyaan yang Sering Diajukan
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 font-normal">
                Temukan jawaban seputar tata cara pembelian tiket, upload bukti bayar, dan akses e-ticket konser.
            </p>
        </div>

        <!-- FAQ Categories & Accordion List -->
        <div class="space-y-4">
            
            <!-- Item 1 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-2">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-bold shrink-0">1</span>
                    Bagaimana cara memesan tiket konser di EVENTIX?
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 pl-10 leading-relaxed font-normal">
                    Pilih konser musik yang ingin ditonton di halaman Beranda atau Katalog, klik detail event, tentukan kategori tiket dan jumlah tiket dengan tombol stepper (+), lalu klik <strong>Lanjut ke Pembayaran</strong>. Anda akan diarahkan ke halaman checkout untuk melakukan transfer bank.
                </p>
            </div>

            <!-- Item 2 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-2">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-bold shrink-0">2</span>
                    Metode pembayaran apa saja yang didukung?
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 pl-10 leading-relaxed font-normal">
                    Kami mendukung transfer bank resmi ke rekening PT EVENTIX INDONESIA (BCA, Mandiri, BNI, BRI) serta QRIS All E-Wallet (Gopay, OVO, Dana, LinkAja, ShopeePay). Setelah transfer, unggah foto bukti transfer di halaman checkout.
                </p>
            </div>

            <!-- Item 3 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-2">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-bold shrink-0">3</span>
                    Kapan E-Ticket saya akan terbit?
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 pl-10 leading-relaxed font-normal">
                    Setelah Anda mengunggah bukti transfer, tim verifikator kami akan memeriksa keaslian mutasi dalam waktu maksimal 1x24 jam. Begitu diverifikasi berstatus <strong>Lunas</strong>, E-Ticket resmi berbarcode QR Code unik akan otomatis aktif di menu <strong>Tiket & Pesanan Saya</strong>.
                </p>
            </div>

            <!-- Item 4 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-2">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-bold shrink-0">4</span>
                    Bagaimana cara penukaran gelang wristband di lokasi acara?
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 pl-10 leading-relaxed font-normal">
                    Tunjukkan E-Ticket digital berbarcode QR Code langsung dari layar ponsel Anda kepada staf penukaran di ticket box venue, beserta kartu identitas (KTP/SIM/Paspor) yang sesuai dengan nama pada akun pemesan.
                </p>
            </div>

            <!-- Item 5 -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-2xs space-y-2">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-3">
                    <span class="w-7 h-7 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center text-xs font-bold shrink-0">5</span>
                    Apakah tiket yang sudah dibeli bisa dibatalkan atau refund?
                </h3>
                <p class="text-xs sm:text-sm text-slate-600 pl-10 leading-relaxed font-normal">
                    Sesuai dengan ketentuan promotor konser, tiket yang telah lunas bersifat <em>non-refundable</em> (tidak dapat dikembalikan), kecuali terjadi pembatalan atau perubahan jadwal resmi dari pihak penyelenggara konser.
                </p>
            </div>

        </div>

        <!-- Callout Hubungi CS -->
        <div class="bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 text-center space-y-3 shadow-2xs">
            <h3 class="text-lg font-bold text-slate-900">Masih Memiliki Pertanyaan Lain?</h3>
            <p class="text-xs sm:text-sm text-slate-600 max-w-md mx-auto font-normal">
                Tim layanan pelanggan kami siap membantu Anda menyelesaikan kendala pemesanan atau verifikasi tiket.
            </p>
            <div class="pt-2">
                <a href="<?= url('contact.php') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#FF6082] hover:bg-[#e54b6d] text-white font-semibold text-xs transition-all shadow-xs hover:shadow-sm">
                    <span>Hubungi Tim Dukungan</span>
                </a>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
