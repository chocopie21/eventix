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

            <!-- Opsi Daftar dengan Akun Pihak Ketiga (Google & Apple) -->
            <div class="space-y-2.5">
                <!-- Tombol Google -->
                <button type="button" 
                        onclick="openSsoModal('google')"
                        class="w-full flex items-center justify-center gap-3 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/90 shadow-2xs hover:shadow-xs transition-all focus:outline-none">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Daftar dengan Google</span>
                </button>

                <!-- Tombol Apple -->
                <button type="button" 
                        onclick="openSsoModal('apple')"
                        class="w-full flex items-center justify-center gap-3 py-2.5 px-4 rounded-xl text-xs sm:text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/90 shadow-2xs hover:shadow-xs transition-all focus:outline-none">
                    <svg class="w-4 h-4 fill-current text-slate-900" viewBox="0 0 170 170">
                        <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.08-7.7-7.85-12.04-14.32-6.55-9.84-11.66-21.2-15.34-34.09-3.68-12.89-5.52-24.81-5.52-35.77 0-14.07 3.51-26.04 10.53-35.91 7.02-9.87 16.03-14.93 27.02-15.19 4.35 0 9.28 1.15 14.79 3.45 5.51 2.3 9.4 3.51 11.67 3.63 1.9.12 5.92-1.21 12.04-3.99 6.13-2.78 11.45-3.98 15.96-3.61 14.52.88 25.4 6.33 32.65 16.34-12.87 7.81-19.12 18.52-18.75 32.13.38 10.45 4.38 19.34 12 26.68 3.79 3.69 8.13 6.52 13.02 8.5-2.6 7.62-5.72 15.34-9.36 23.16zM119.22 33.15c0-7.23 2.65-13.9 7.95-20.02 5.3-6.12 11.83-9.85 19.59-11.2-.13 1.01-.2 2.09-.2 3.24 0 7.03-2.75 13.84-8.25 20.43-5.5 6.59-12.18 10.37-20.05 11.34.13-1.22.96-3.79.96-3.79z"/>
                    </svg>
                    <span>Daftar dengan Apple</span>
                </button>
            </div>

            <!-- Divider -->
            <div class="relative flex items-center my-3">
                <div class="flex-grow border-t border-slate-200/90"></div>
                <span class="flex-shrink mx-3 text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider">atau daftar dengan formulir</span>
                <div class="flex-grow border-t border-slate-200/90"></div>
            </div>

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

    <!-- Modal SSO / Pemilih Akun Pihak Ketiga (Google & Apple) -->
    <div id="ssoModal" class="fixed inset-0 z-50 hidden bg-slate-950/40 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 transform transition-all animate-in fade-in duration-200">
            <!-- Modal Header -->
            <div class="text-center mb-5">
                <div id="ssoProviderIcon" class="w-10 h-10 mx-auto mb-3 flex items-center justify-center">
                    <!-- Icon disuntikkan secara dinamis -->
                </div>
                <h3 id="ssoModalTitle" class="text-base font-extrabold text-slate-900 tracking-tight">
                    Daftar dengan Akun Google
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    untuk melanjutkan ke <span class="font-bold text-slate-800">EVENTIX</span>
                </p>
            </div>

            <!-- Daftar Akun Rekomendasi Cepat -->
            <div class="space-y-2 mb-3.5" id="ssoAccountList">
                <!-- Disuntikkan via JS -->
            </div>

            <!-- Form Input Akun Kustom / Baru -->
            <div id="ssoCustomAccountBox" class="hidden mb-3.5 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80 space-y-2.5">
                <span class="text-[11px] font-bold text-slate-700 block">Ketik Akun Baru:</span>
                <input type="text" id="customSsoName" placeholder="Nama Lengkap" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-[#FF6082]">
                <input type="email" id="customSsoEmail" placeholder="nama@email.com" class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-900 focus:outline-none focus:border-[#FF6082]">
                <button type="button" onclick="submitCustomSso()" class="w-full py-2 bg-[#FF6082] hover:bg-[#e54b6d] text-white text-xs font-semibold rounded-xl transition-colors">
                    Lanjutkan Mendaftar
                </button>
            </div>

            <!-- Tombol Tambah Akun Lain -->
            <button type="button" id="btnToggleCustomSso" onclick="toggleCustomSso()" class="w-full py-2.5 px-3 text-center text-xs font-semibold text-slate-600 hover:text-slate-900 hover:bg-slate-50 rounded-xl transition-colors flex items-center justify-center gap-1.5 mb-4 border border-dashed border-slate-200">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Gunakan akun lain</span>
            </button>

            <!-- Catatan Privasi -->
            <p class="text-[10px] text-slate-400 text-center leading-relaxed mb-4">
                Dengan melanjutkan, penyedia membagikan nama & email Anda secara aman ke EVENTIX sesuai kebijakan privasi.
            </p>

            <!-- Tombol Batal -->
            <button type="button" onclick="closeSsoModal()" class="w-full py-2.5 text-center text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors border-t border-slate-100">
                Batal
            </button>
        </div>
    </div>

    <!-- Hidden Form untuk Submit SSO ke actions/auth_oauth.php -->
    <form id="ssoForm" action="<?= url('actions/auth_oauth.php') ?>" method="POST" class="hidden">
        <?= csrf_field() ?>
        <input type="hidden" name="provider" id="ssoFormProvider" value="google">
        <input type="hidden" name="sso_name" id="ssoFormName" value="">
        <input type="hidden" name="sso_email" id="ssoFormEmail" value="">
    </form>

    <!-- Script Toggle Password Visibility & SSO -->
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

        // ============================================================
        // SISTEM SSO (GOOGLE & APPLE)
        // ============================================================
        let currentSsoProvider = 'google';

        const googleSvg = `<svg class="w-9 h-9" viewBox="0 0 24 24">
            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
        </svg>`;

        const appleSvg = `<svg class="w-8 h-8 fill-current text-slate-900" viewBox="0 0 170 170">
            <path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.08-7.7-7.85-12.04-14.32-6.55-9.84-11.66-21.2-15.34-34.09-3.68-12.89-5.52-24.81-5.52-35.77 0-14.07 3.51-26.04 10.53-35.91 7.02-9.87 16.03-14.93 27.02-15.19 4.35 0 9.28 1.15 14.79 3.45 5.51 2.3 9.4 3.51 11.67 3.63 1.9.12 5.92-1.21 12.04-3.99 6.13-2.78 11.45-3.98 15.96-3.61 14.52.88 25.4 6.33 32.65 16.34-12.87 7.81-19.12 18.52-18.75 32.13.38 10.45 4.38 19.34 12 26.68 3.79 3.69 8.13 6.52 13.02 8.5-2.6 7.62-5.72 15.34-9.36 23.16zM119.22 33.15c0-7.23 2.65-13.9 7.95-20.02 5.3-6.12 11.83-9.85 19.59-11.2-.13 1.01-.2 2.09-.2 3.24 0 7.03-2.75 13.84-8.25 20.43-5.5 6.59-12.18 10.37-20.05 11.34.13-1.22.96-3.79.96-3.79z"/>
        </svg>`;

        function openSsoModal(provider) {
            currentSsoProvider = provider;
            document.getElementById('ssoFormProvider').value = provider;
            const modal = document.getElementById('ssoModal');
            const iconContainer = document.getElementById('ssoProviderIcon');
            const titleEl = document.getElementById('ssoModalTitle');
            const listContainer = document.getElementById('ssoAccountList');
            const customBox = document.getElementById('ssoCustomAccountBox');
            customBox.classList.add('hidden');

            if (provider === 'google') {
                iconContainer.innerHTML = googleSvg;
                titleEl.textContent = 'Daftar / Masuk Akun Google';
                listContainer.innerHTML = `
                    <button type="button" onclick="selectSsoAccount('Ahmad Nafi', 'mnafi151@gmail.com', this)" class="w-full flex items-center gap-3 p-3 rounded-2xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-left transition-all group">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            A
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="block text-xs font-bold text-slate-900 truncate">Ahmad Nafi</span>
                            <span class="block text-[11px] text-slate-500 truncate">mnafi151@gmail.com</span>
                        </div>
                        <span class="text-[10px] text-blue-600 bg-blue-50 font-semibold px-2 py-0.5 rounded-full shrink-0">Pilih</span>
                    </button>

                    <button type="button" onclick="selectSsoAccount('Budi Santoso', 'budi@gmail.com', this)" class="w-full flex items-center gap-3 p-3 rounded-2xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-left transition-all group">
                        <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            B
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="block text-xs font-bold text-slate-900 truncate">Budi Santoso</span>
                            <span class="block text-[11px] text-slate-500 truncate">budi@gmail.com</span>
                        </div>
                        <span class="text-[10px] text-emerald-600 bg-emerald-50 font-semibold px-2 py-0.5 rounded-full shrink-0">Pilih</span>
                    </button>
                `;
            } else {
                iconContainer.innerHTML = appleSvg;
                titleEl.textContent = 'Daftar / Masuk dengan Apple ID';
                listContainer.innerHTML = `
                    <button type="button" onclick="selectSsoAccount('Ahmad Nafi', 'ahmad.nafi@icloud.com', this)" class="w-full flex items-center gap-3 p-3 rounded-2xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50 text-left transition-all group">
                        <div class="w-9 h-9 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="block text-xs font-bold text-slate-900 truncate">Ahmad Nafi</span>
                            <span class="block text-[11px] text-slate-500 truncate">ahmad.nafi@icloud.com</span>
                        </div>
                        <span class="text-[10px] text-slate-700 bg-slate-100 font-semibold px-2 py-0.5 rounded-full shrink-0">Apple ID</span>
                    </button>
                `;
            }

            modal.classList.remove('hidden');
        }

        function closeSsoModal() {
            document.getElementById('ssoModal').classList.add('hidden');
        }

        function toggleCustomSso() {
            const customBox = document.getElementById('ssoCustomAccountBox');
            customBox.classList.toggle('hidden');
            if (!customBox.classList.contains('hidden')) {
                document.getElementById('customSsoName').focus();
            }
        }

        function selectSsoAccount(name, email, btnEl) {
            if (btnEl) {
                btnEl.classList.add('opacity-60', 'pointer-events-none');
                btnEl.innerHTML = `<div class="w-full text-center py-2 text-xs font-bold text-slate-700 flex items-center justify-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-[#FF6082]" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Menghubungkan ke ${currentSsoProvider === 'google' ? 'Google' : 'Apple'}...</span>
                </div>`;
            }
            document.getElementById('ssoFormName').value = name;
            document.getElementById('ssoFormEmail').value = email;
            document.getElementById('ssoFormProvider').value = currentSsoProvider;
            setTimeout(() => {
                document.getElementById('ssoForm').submit();
            }, 300);
        }

        function submitCustomSso() {
            const name = document.getElementById('customSsoName').value.trim();
            const email = document.getElementById('customSsoEmail').value.trim();
            if (!email || !email.includes('@')) {
                alert('Silakan masukkan alamat email yang valid.');
                return;
            }
            selectSsoAccount(name || email.split('@')[0], email, null);
        }

        // Tutup modal jika klik di luar area modal
        document.getElementById('ssoModal')?.addEventListener('click', (e) => {
            if (e.target.id === 'ssoModal') {
                closeSsoModal();
            }
        });
    </script>
</body>
</html>
