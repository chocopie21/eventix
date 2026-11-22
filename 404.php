<?php
/**
 * EVENTIX - Halaman Error 404 (Not Found)
 * Human-Crafted Editorial Minimalist
 */
require_once __DIR__ . '/config/app.php';
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Tidak Ditemukan | EVENTIX</title>

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

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="bg-[#FAFBFD] text-slate-900 antialiased flex flex-col min-h-screen font-sf">

    <!-- Navbar -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="flex-grow flex items-center justify-center px-4 py-16">
        <div class="max-w-md w-full text-center space-y-6">
            
            <!-- Graphic 404 Badge -->
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-xl bg-[#FF6082] text-white">
                <span class="text-2xl font-bold font-display tracking-tight">
                    404
                </span>
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">
                    Halaman Tidak Ditemukan
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Halaman atau tautan konser yang Anda cari mungkin telah usai, dipindahkan, atau alamat URL yang dimasukkan salah.
                </p>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="<?= url('index.php') ?>" class="w-full sm:w-auto px-5 py-2.5 rounded-lg bg-[#FF6082] hover:bg-[#e54b6d] text-white font-medium text-xs transition-colors shadow-xs">
                    Kembali ke Beranda
                </a>
                <a href="<?= url('index.php#katalog') ?>" class="w-full sm:w-auto px-5 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 font-medium text-xs transition-colors">
                    Lihat Katalog Konser
                </a>
            </div>

        </div>
    </main>

    <!-- Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

</body>
</html>
