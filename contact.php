<?php
/**
 * EVENTIX - Halaman Kontak & Kemitraan Promotor
 * Human-Crafted Editorial Minimalist
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
    <main class="flex-grow max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
        
        <!-- Header Minimalist -->
        <div class="text-center space-y-3 max-w-xl mx-auto">
            <h1 class="text-3xl sm:text-4xl font-bold font-heading text-slate-900">
                Ada yang Bisa Kami Bantu?
            </h1>
            <p class="text-xs sm:text-sm text-slate-600">
                Punya pertanyaan seputar tiket atau ingin mendaftarkan event konser musik Anda di platform EVENTIX? Kami siap berdiskusi.
            </p>
        </div>

        <!-- Flash Messages -->
        <?php if ($flashSuccess): ?>
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-emerald-800 text-xs sm:text-sm max-w-2xl mx-auto">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-rose-800 text-xs sm:text-sm max-w-2xl mx-auto">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
            
            <!-- Left: Kontak Detail Cards (5 Cols) -->
            <div class="md:col-span-5 space-y-4">
                
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Email Resmi</h3>
                    <p class="text-xs text-slate-500">Pertanyaan umum, penawaran promotor, atau kendala transaksi.</p>
                    <p class="text-xs font-semibold text-slate-900 font-mono">support@eventix.id</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">WhatsApp CS (Chat Only)</h3>
                    <p class="text-xs text-slate-500">Layanan pesan cepat untuk verifikasi mendesak.</p>
                    <p class="text-xs font-semibold text-slate-900 font-mono">+62 812-9876-5432</p>
                </div>

                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-2">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center font-bold">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 font-heading">Kantor Operasional</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Gedung Cyber 2 Lantai 15, Jl. H.R. Rasuna Said, Jakarta Selatan 12950, Indonesia.
                    </p>
                </div>

            </div>

            <!-- Right: Formulir Pesan & Kemitraan (7 Cols) -->
            <div class="md:col-span-7 bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-xs space-y-5">
                <div>
                    <h2 class="text-base font-bold font-heading text-slate-900">Kirim Pesan / Kemitraan Promotor</h2>
                    <p class="text-xs text-slate-500 mt-1">Kami akan menghubungi Anda kembali dalam kurun waktu 1x24 jam.</p>
                </div>

                <form action="<?= url('contact.php') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="contact-name" class="block text-xs font-semibold text-slate-800 mb-1">
                                Nama lengkap <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" id="contact-name" name="name" required placeholder="Budi Santoso"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label for="contact-email" class="block text-xs font-semibold text-slate-800 mb-1">
                                Alamat email <span class="text-rose-600">*</span>
                            </label>
                            <input type="email" id="contact-email" name="email" required placeholder="budi@email.com"
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="contact-subject" class="block text-xs font-semibold text-slate-800 mb-1">
                            Tujuan pesan <span class="text-rose-600">*</span>
                        </label>
                        <select id="contact-subject" name="subject" required
                                class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 focus:outline-none transition-colors">
                            <option value="pertanyaan">Pertanyaan Pembelian Tiket</option>
                            <option value="promotor">Kemitraan Promotor / Pendaftaran Konser Baru</option>
                            <option value="verifikasi">Kendala Verifikasi Pembayaran</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label for="contact-message" class="block text-xs font-semibold text-slate-800 mb-1">
                            Pesan Anda <span class="text-rose-600">*</span>
                        </label>
                        <textarea id="contact-message" name="message" rows="4" required placeholder="Tuliskan detail pertanyaan atau informasi yang ingin Anda sampaikan..."
                                  class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors"></textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-lg text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] shadow-xs transition-colors">
                        Kirim Pesan
                    </button>
                </form>
            </div>

        </div>

    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
