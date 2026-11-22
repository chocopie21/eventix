<?php
/**
 * EVENTIX - Halaman Registrasi / Daftar Akun Baru
 * Modern Minimalist Design
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
 */

require_once __DIR__ . '/config/app.php';

// Jika pengguna sudah login, alihkan langsung
if (isLoggedIn()) {
    redirect(isAdmin() ? 'admin/index.php' : 'index.php');
}

$old = $_SESSION['old_register'] ?? [];
unset($_SESSION['old_register']);

$errorMessage = getFlash('error');
$successMessage = getFlash('success');
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru | EVENTIX</title>
    <meta name="description" content="Daftar akun EVENTIX baru untuk kemudahan beli tiket konser musik dan kelola e-ticket Anda.">

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
<body class="min-h-full flex flex-col justify-between bg-slate-50 text-slate-900 antialiased">

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
        <div class="w-full max-w-xl bg-white rounded-3xl shadow-[0_4px_24px_rgba(15,23,42,0.05)] border border-slate-200/80 p-7 sm:p-9 space-y-6">
            
            <!-- Title -->
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    Buat Akun Baru
                </h1>
                <p class="mt-1 text-xs text-slate-500">
                    Lengkapi data diri untuk memesan tiket konser resmi dan mengelola pesanan.
                </p>
            </div>

            <!-- Flash Message Alert -->
            <?php if ($errorMessage): ?>
                <div class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-rose-800 text-xs">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($successMessage): ?>
                <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-emerald-800 text-xs">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- Form Register -->
            <form action="<?= url('actions/auth_register.php') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <!-- Nama Lengkap -->
                <div>
                    <label for="nama" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Nama lengkap <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" 
                           id="nama" 
                           name="nama" 
                           value="<?= htmlspecialchars($old['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                           placeholder="Sesuai kartu identitas (KTP / SIM)" 
                           required
                           autocomplete="name"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                </div>

                <!-- 2 Kolom: Email & No Telepon -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Alamat email <span class="text-rose-600">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="nama@email.com" 
                               required
                               autocomplete="email"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                    </div>

                    <!-- No Telepon / WhatsApp -->
                    <div>
                        <label for="no_telepon" class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Nomor WhatsApp / HP <span class="text-rose-600">*</span>
                        </label>
                        <input type="tel" 
                               id="no_telepon" 
                               name="no_telepon" 
                               value="<?= htmlspecialchars($old['no_telepon'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="0812xxxxxxxx" 
                               required
                               autocomplete="tel"
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                    </div>
                </div>

                <!-- Alamat Domisili -->
                <div>
                    <label for="alamat" class="block text-xs font-semibold text-slate-800 mb-1.5">
                        Kota asal / Alamat domisili <span class="text-slate-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text" 
                           id="alamat" 
                           name="alamat" 
                           value="<?= htmlspecialchars($old['alamat'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                           placeholder="Contoh: Jakarta Selatan, DKI Jakarta" 
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                </div>

                <!-- 2 Kolom: Kata Sandi & Konfirmasi Kata Sandi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Kata sandi <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Minimal 6 karakter" 
                                   required
                                   minlength="6"
                                   autocomplete="new-password"
                                   class="w-full px-3.5 pr-10 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                            <button type="button" 
                                    class="toggle-pass-btn absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none"
                                    data-target="password"
                                    aria-label="Tampilkan sandi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirm" class="block text-xs font-semibold text-slate-800 mb-1.5">
                            Ulangi kata sandi <span class="text-rose-600">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   id="password_confirm" 
                                   name="password_confirm" 
                                   placeholder="Ulangi kata sandi" 
                                   required
                                   minlength="6"
                                   autocomplete="new-password"
                                   class="w-full px-3.5 pr-10 py-2.5 bg-white border border-slate-200/90 focus:border-[#FF6082] focus:ring-2 focus:ring-[#FF6082]/15 rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-all">
                            <button type="button" 
                                    class="toggle-pass-btn absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-700 focus:outline-none"
                                    data-target="password_confirm"
                                    aria-label="Tampilkan sandi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Persetujuan -->
                <div class="flex items-start pt-1">
                    <input id="terms" name="terms" type="checkbox" required class="h-4 w-4 mt-0.5 text-[#0F172A] focus:ring-[#FF6082] border-slate-300 rounded cursor-pointer">
                    <label for="terms" class="ml-2 block text-xs text-slate-600 leading-normal cursor-pointer">
                        Saya menyetujui ketentuan layanan dan kebijakan privasi pembelian tiket konser EVENTIX.
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] shadow-xs hover:shadow-sm transition-all focus:outline-none">
                    <span>Daftar Akun</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </button>
            </form>

            <!-- Switch to Login Link -->
            <p class="pt-2 text-center text-xs text-slate-600 border-t border-slate-100">
                Sudah memiliki akun? 
                <a href="<?= url('login.php') ?>" class="font-semibold text-[#FF6082] hover:underline underline-offset-2">
                    Masuk di sini
                </a>
            </p>

        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="py-5 text-center text-xs text-slate-400 border-t border-slate-200/80">
        &copy; <?= date('Y') ?> EVENTIX Indonesia. Hak cipta dilindungi.
    </footer>

    <!-- Script Toggle Password Visibility -->
    <script>
        document.querySelectorAll('.toggle-pass-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const targetInput = document.getElementById(targetId);
                if (targetInput) {
                    const isPassword = targetInput.getAttribute('type') === 'password';
                    targetInput.setAttribute('type', isPassword ? 'text' : 'password');
                    btn.classList.toggle('text-slate-900', isPassword);
                    btn.classList.toggle('text-slate-400', !isPassword);
                }
            });
        });
    </script>
</body>
</html>
