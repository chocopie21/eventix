<?php
/**
 * EVENTIX - Header & Navigation Bar
 * Human-Crafted Editorial Minimalist
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
 */
$isLoggedIn = isLoggedIn();
$user = currentUser();
$isAdminUser = isAdmin();
$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!-- Navbar Sticky Minimalist -->
<header class="sticky top-0 z-50 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16 gap-4">
            
            <!-- Left: Brand Logo -->
            <div class="flex items-center gap-8">
                <a href="<?= url('index.php') ?>" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-[#FF6082] flex items-center justify-center text-white">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 font-display">
                        EVENTIX
                    </span>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                    <a href="<?= url('index.php') ?>" 
                       class="hover:text-slate-950 transition-colors <?= $currentScript === 'index.php' ? 'text-slate-950 font-bold' : '' ?>">
                        Beranda
                    </a>
                    <a href="<?= url('index.php#katalog') ?>" 
                       class="hover:text-slate-950 transition-colors">
                        Katalog Konser
                    </a>
                    <a href="<?= url('about.php') ?>" 
                       class="hover:text-slate-950 transition-colors <?= $currentScript === 'about.php' ? 'text-slate-950 font-bold' : '' ?>">
                        Tentang Kami
                    </a>
                    <a href="<?= url('faq.php') ?>" 
                       class="hover:text-slate-950 transition-colors <?= $currentScript === 'faq.php' ? 'text-slate-950 font-bold' : '' ?>">
                        Bantuan
                    </a>
                    <a href="<?= url('contact.php') ?>" 
                       class="hover:text-slate-950 transition-colors <?= $currentScript === 'contact.php' ? 'text-slate-950 font-bold' : '' ?>">
                        Kontak
                    </a>
                </nav>
            </div>

            <!-- Right: Actions & User State -->
            <div class="hidden md:flex items-center gap-3">
                <?php if (!$isLoggedIn): ?>
                    <a href="<?= url('login.php') ?>" 
                       class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-slate-950 hover:bg-slate-100 rounded-lg transition-colors">
                        Masuk
                    </a>
                    <a href="<?= url('register.php') ?>" 
                       class="px-4 py-2 text-xs font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] rounded-lg transition-colors shadow-xs">
                        Daftar Akun
                    </a>
                <?php else: ?>
                    <div class="flex items-center gap-3">
                        <a href="<?= url('my_orders.php') ?>" 
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-800 bg-slate-100 border border-slate-300 hover:bg-slate-200 rounded-md transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Tiket Saya</span>
                        </a>

                        <?php if ($isAdminUser): ?>
                            <a href="<?= url('admin/index.php') ?>" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-900 bg-slate-100 border border-slate-300 hover:bg-slate-200 rounded-md transition-colors">
                                <span>Panel Admin</span>
                            </a>
                        <?php endif; ?>

                        <!-- User Profile Dropdown -->
                        <div class="relative group">
                            <button type="button" class="flex items-center gap-2 p-1 pl-2.5 rounded-lg border border-slate-200 bg-white hover:border-slate-300 transition-colors text-left">
                                <span class="text-xs font-bold text-slate-800 max-w-[110px] truncate">
                                    <?= htmlspecialchars($user['nama'] ?? 'User', ENT_QUOTES, 'UTF-8') ?>
                                </span>
                                <div class="w-6 h-6 rounded bg-slate-900 text-white font-bold flex items-center justify-center text-xs">
                                    <?= htmlspecialchars(mb_substr($user['nama'] ?? 'U', 0, 1), ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            </button>

                            <div class="absolute right-0 mt-1.5 w-52 rounded-xl bg-white border border-slate-200 shadow-lg py-2 hidden group-hover:block transition-all z-50">
                                <div class="px-4 py-2 border-b border-slate-100">
                                    <p class="text-xs font-bold text-slate-900 truncate"><?= htmlspecialchars($user['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                    <p class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                                </div>

                                <?php if ($isAdminUser): ?>
                                    <a href="<?= url('admin/index.php') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Kelola Konser & Tiket</span>
                                    </a>
                                <?php endif; ?>

                                <a href="<?= url('my_orders.php') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                    </svg>
                                    <span>Riwayat Pesanan</span>
                                </a>

                                <div class="border-t border-slate-100 my-1"></div>

                                <a href="<?= url('actions/auth_logout.php') ?>" class="flex items-center gap-2 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors">
                                    <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar</span>
                                </a>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile Hamburger Menu Button -->
            <div class="flex items-center md:hidden">
                <button type="button" 
                        id="mobile-menu-btn"
                        class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none"
                        aria-label="Toggle menu">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <!-- Mobile Drawer Menu -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-6 space-y-3">
        <nav class="flex flex-col space-y-2 text-sm font-medium">
            <a href="<?= url('index.php') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-50 <?= $currentScript === 'index.php' ? 'bg-slate-100 font-bold text-slate-900' : 'text-slate-600' ?>">Beranda</a>
            <a href="<?= url('index.php#katalog') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-600">Katalog Konser</a>
            <a href="<?= url('about.php') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-50 <?= $currentScript === 'about.php' ? 'bg-slate-100 font-bold text-slate-900' : 'text-slate-600' ?>">Tentang Kami</a>
            <a href="<?= url('faq.php') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-50 <?= $currentScript === 'faq.php' ? 'bg-slate-100 font-bold text-slate-900' : 'text-slate-600' ?>">Bantuan</a>
            <a href="<?= url('contact.php') ?>" class="px-3 py-2 rounded-lg hover:bg-slate-50 <?= $currentScript === 'contact.php' ? 'bg-slate-100 font-bold text-slate-900' : 'text-slate-600' ?>">Kontak</a>
        </nav>

        <div class="pt-3 border-t border-slate-200 flex flex-col gap-2">
            <?php if (!$isLoggedIn): ?>
                <a href="<?= url('login.php') ?>" class="w-full text-center py-2.5 rounded-lg border border-slate-300 text-sm font-semibold text-slate-700">Masuk</a>
                <a href="<?= url('register.php') ?>" class="w-full text-center py-2.5 rounded-lg bg-[#FF6082] text-sm font-semibold text-white">Daftar Akun</a>
            <?php else: ?>
                <a href="<?= url('my_orders.php') ?>" class="w-full text-center py-2.5 rounded-lg bg-slate-900 text-sm font-semibold text-white">Tiket & Pesanan Saya</a>
                <?php if ($isAdminUser): ?>
                    <a href="<?= url('admin/index.php') ?>" class="w-full text-center py-2.5 rounded-lg bg-slate-100 text-sm font-semibold text-slate-800">Panel Admin</a>
                <?php endif; ?>
                <a href="<?= url('actions/auth_logout.php') ?>" class="w-full text-center py-2.5 rounded-lg border border-rose-200 text-sm font-semibold text-rose-600">Keluar</a>
            <?php endif; ?>
        </div>
    </div>
</header>
<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        document.getElementById('mobile-menu')?.classList.toggle('hidden');
    });
</script>
