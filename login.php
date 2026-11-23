<?php
/**
 * EVENTIX - Halaman Masuk / Login
 * Modern Minimalist Design
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
 */

require_once __DIR__ . '/config/app.php';

// Jika pengguna sudah login, alihkan langsung ke halaman yang relevan
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/index.php' : 'index.php');
}

$oldEmail = $_SESSION['old_login_email'] ?? '';
unset($_SESSION['old_login_email']);

$successMessage = getFlash('success');
$errorMessage = getFlash('error');
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun | EVENTIX</title>
    <meta name="description" content="Masuk ke akun EVENTIX Anda untuk memesan tiket konser musik resmi di Indonesia.">

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
<body class="min-h-full flex flex-col justify-between bg-[#FAFBFD] text-slate-900 antialiased font-sf">

    <!-- Top Navigation Simple Bar -->
    <header class="w-full glass-header py-4 px-4 sm:px-8">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="<?= url('index.php') ?>" class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-[#FF6082] flex items-center justify-center text-white shadow-xs">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <span class="text-xl font-extrabold tracking-tight text-slate-900 font-sans">
                    EVENTIX
                </span>
            </a>

            <a href="<?= url('index.php') ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-950 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-[0_4px_24px_rgba(15,23,42,0.05)] border border-slate-200/80 p-7 sm:p-9 space-y-6">
            
            <!-- Heading -->
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Masuk ke Akun
                </h1>
                <p class="mt-1 text-xs text-slate-500">
                    Gunakan email dan kata sandi yang telah Anda daftarkan.
                </p>
            </div>

            <!-- Flash Message Alert -->
            <?php if ($successMessage): ?>
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-emerald-800 text-xs">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage): ?>
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-rose-800 text-xs">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Login -->
            <form action="<?= url('actions/auth_login.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="<?= htmlspecialchars($oldEmail, ENT_QUOTES, 'UTF-8') ?>" 
                           placeholder="nama@email.com" 
                           required
                           autocomplete="email"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                </div>

                <!-- Password Input -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-slate-700">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400 hover:text-slate-800 cursor-pointer font-medium" onclick="alert('Silakan hubungi administrator untuk mereset kata sandi akun Anda.')">
                            Lupa sandi?
                        </span>
                    </div>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="••••••••" 
                               required
                               autocomplete="current-password"
                               class="w-full pl-3.5 pr-10 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                        <button type="button" 
                                id="toggle-password" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                            <svg id="eye-open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg id="eye-closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Checkbox Ingat Saya -->
                <div class="flex items-center">
                    <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-[#0F172A] border-slate-300 rounded-md cursor-pointer focus:ring-[#FF6082]">
                    <label for="remember-me" class="ml-2 block text-xs text-slate-600 cursor-pointer">
                        Tetap masuk di perangkat ini
                    </label>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" 
                        class="w-full py-3.5 px-4 rounded-xl text-xs sm:text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#E0486D] transition-button focus:outline-none shadow-sm hover:shadow">
                    Masuk
                </button>
            </form>

            <!-- Switch to Register Link -->
            <p class="text-center text-xs text-slate-500 pt-2">
                Belum memiliki akun EVENTIX? 
                <a href="<?= url('register.php') ?>" class="font-semibold text-[#FF6082] hover:underline">
                    Daftar Sekarang
                </a>
            </p>

        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="py-5 text-center text-xs text-slate-400 border-t border-slate-200/80">
        &copy; <?= date('Y') ?> EVENTIX Indonesia. All rights reserved.
    </footer>

    <!-- Script Password Toggle -->
    <script>
        const toggleBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', () => {
                const isPassword = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeOpen.classList.toggle('hidden', isPassword);
                eyeClosed.classList.toggle('hidden', !isPassword);
            });
        }
    </script>
</body>
</html>
