<?php
/**
 * EVENTIX - Halaman Tentang Kami (About Us)
 * Human-Crafted Editorial Minimalist
 */
require_once __DIR__ . '/config/app.php';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami | EVENTIX</title>
    <meta name="description" content="EVENTIX adalah platform resmi pemesanan tiket konser musik Indonesia dengan jaminan keaslian tiket 100% dan teknologi anti-calo.">

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

    <!-- Navbar -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        
        <!-- Header Hero Minimalist -->
        <div class="text-center space-y-4 max-w-2xl mx-auto">
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold font-heading tracking-tight text-slate-900">
                Membuka Gerbang Panggung Konser Impian Anda
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                EVENTIX didirikan untuk merevolusi pengalaman menonton konser musik di Indonesia: adil, transparan, dan bebas dari praktik calo yang merugikan penikmat musik.
            </p>
        </div>

        <!-- 3 Pilar Nilai Utama -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold font-heading text-slate-900">Tiket Resmi 100%</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap tiket yang terjual terhubung langsung dengan alokasi resmi dari promotor konser. Tidak ada tiket pihak ketiga atau calo tidak terverifikasi.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold font-heading text-slate-900">Proteksi Rebutan Tiket</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Teknologi database dengan <em>Pessimistic Row Locking</em> mencegah kuota minus atau overselling, memastikan semua penonton memiliki kesempatan yang adil.
                </p>
            </div>

            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold font-heading text-slate-900">Verifikasi Terpercaya</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap pembayaran diverifikasi secara teliti oleh admin, menerbitkan E-Ticket QR Code unik yang siap ditukarkan menjadi wristband di lokasi acara.
                </p>
            </div>

        </div>

        <!-- Cerita & Visi Kami -->
        <div class="bg-white rounded-xl p-8 sm:p-10 border border-slate-200 shadow-xs space-y-6">
            <h2 class="text-xl sm:text-2xl font-bold font-heading text-slate-900">
                Dedikasi untuk Ekosistem Musik Indonesia
            </h2>
            <div class="text-xs sm:text-sm text-slate-600 space-y-4 leading-relaxed">
                <p>
                    Konser musik bukan sekadar hiburan—itu adalah momen berharga di mana puluhan ribu orang bernyanyi bersama, merayakan karya musisi favorit, dan menciptakan kenangan berharga.
                </p>
                <p>
                    EVENTIX hadir menjembatani musisi legendaris tanah air—seperti Sheila on 7, Dewa 19, Hindia, Tulus, Bernadya, dan Perunggu—dengan para penikmat musik melalui sistem digital yang transparan dan dapat diandalkan.
                </p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-6 text-xs text-slate-500">
                    <div>
                        <span class="block text-xl font-bold text-slate-900 font-heading">100%</span>
                        <span>Garansi Resmi</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <span class="block text-xl font-bold text-slate-900 font-heading">24 Jam</span>
                        <span>Verifikasi Pembayaran</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <span class="block text-xl font-bold text-slate-900 font-heading">0 Biaya</span>
                        <span>Tersembunyi</span>
                    </div>
                </div>

                <a href="<?= url('index.php#katalog') ?>" class="px-5 py-2.5 rounded-lg bg-[#FF6082] hover:bg-[#e54b6d] text-white font-medium text-xs transition-colors shadow-xs">
                    Jelajahi Konser
                </a>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
