<?php
/**
 * EVENTIX - Admin Sidebar Component
 * Human-Crafted Editorial Minimalist
 */

$user = currentUser();
$currentFile = basename($_SERVER['SCRIPT_NAME'] ?? '');
?>
<!-- Admin Sidebar Backdrop (Mobile & Tablet) -->
<div id="admin-sidebar-backdrop" 
     class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"
     aria-hidden="true"></div>

<!-- Sidebar Navigation -->
<aside id="admin-sidebar" 
       class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-950 text-slate-300 flex flex-col shrink-0 min-h-screen border-r border-slate-800 transform -translate-x-full lg:translate-x-0 lg:static transition-transform duration-200 ease-in-out shadow-xl lg:shadow-none">
    
    <!-- Admin Header / Brand -->
    <div class="h-16 px-5 sm:px-6 flex items-center justify-between border-b border-slate-800 bg-slate-950">
        <a href="<?= url('admin/index.php') ?>" class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-md bg-[#FF6082] flex items-center justify-center text-white shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                </svg>
            </div>
            <div>
                <span class="text-base font-bold tracking-tight text-white block leading-none font-display">
                    EVENTIX
                </span>
                <span class="text-[11px] text-slate-400 block mt-1 font-medium">Panel Admin</span>
            </div>
        </a>

        <!-- Close Sidebar Button (Mobile/Tablet Only) -->
        <button type="button" 
                id="admin-sidebar-close" 
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors focus:outline-none"
                aria-label="Tutup Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Logged in Admin Profile Card -->
    <div class="px-5 py-4 border-b border-slate-800/80 bg-slate-900/50">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-slate-800 text-slate-200 font-bold flex items-center justify-center text-xs border border-slate-700">
                <?= htmlspecialchars(mb_substr($user['nama'] ?? 'A', 0, 1), ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="min-w-0 flex-1">
                <span class="text-xs font-semibold text-white block truncate"><?= htmlspecialchars($user['nama'] ?? 'Administrator', ENT_QUOTES, 'UTF-8') ?></span>
                <span class="text-[11px] text-slate-400 block truncate"><?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <nav class="flex-1 px-3 py-4 space-y-1 text-xs">
        
        <div class="px-3 pb-2 text-[11px] font-semibold text-slate-400">
            Menu utama
        </div>

        <!-- 1. Kelola Konser Musik -->
        <a href="<?= url('admin/index.php') ?>" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= ($currentFile === 'index.php') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' ?>">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
            <span>Kelola Konser</span>
        </a>

        <!-- 2. Kelola Kategori Tiket -->
        <a href="<?= url('admin/tickets.php') ?>" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= ($currentFile === 'tickets.php') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' ?>">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
            </svg>
            <span>Kelola Tiket</span>
        </a>

        <!-- 3. Kelola Pesanan & Verifikasi -->
        <a href="<?= url('admin/orders.php') ?>" 
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-colors <?= ($currentFile === 'orders.php') ? 'bg-slate-800 text-white font-semibold' : 'text-slate-400 hover:text-white hover:bg-slate-900' ?>">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>Verifikasi Pesanan</span>
        </a>

        <div class="pt-6 px-3 pb-2 text-[11px] font-semibold text-slate-400">
            Situs publik
        </div>

        <!-- 4. Kembali ke Website Beranda -->
        <a href="<?= url('index.php') ?>" target="_blank"
           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span>Buka Beranda</span>
        </a>

    </nav>

    <!-- Logout Button -->
    <div class="p-3 border-t border-slate-800">
        <a href="<?= url('actions/auth_logout.php') ?>" 
           class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-xs font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Keluar Sesi</span>
        </a>
    </div>

</aside>

<!-- Admin Mobile Sidebar Controller -->
<script>
    (function() {
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('admin-sidebar');
            const backdrop = document.getElementById('admin-sidebar-backdrop');

            if (e.target.closest('#admin-sidebar-open')) {
                sidebar?.classList.remove('-translate-x-full');
                backdrop?.classList.remove('hidden');
                document.body.classList.add('overflow-hidden', 'lg:overflow-auto');
            } else if (e.target.closest('#admin-sidebar-close') || e.target === backdrop) {
                sidebar?.classList.add('-translate-x-full');
                backdrop?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        });

        // Close drawer automatically when window resizes to desktop breakpoint
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                document.getElementById('admin-sidebar')?.classList.add('-translate-x-full');
                document.getElementById('admin-sidebar-backdrop')?.classList.add('hidden');
                document.body.classList.remove('overflow-hidden', 'lg:overflow-auto');
            }
        });
    })();
</script>
