<?php
/**
 * EVENTIX - Footer Component
 * Human-Crafted Editorial Minimalist
 */
?>
<footer class="bg-white border-t border-slate-200 pt-12 pb-8 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 pb-10 border-b border-slate-100">
            
            <!-- Col 1: Brand Info -->
            <div class="space-y-3">
                <a href="<?= url('index.php') ?>" class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-[#FF6082] flex items-center justify-center text-white">
                        <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                        </svg>
                    </div>
                    <span class="text-lg font-black tracking-tight text-slate-950 font-display">
                        EVENTIX
                    </span>
                </a>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Platform tiket konser musik resmi di Indonesia. Menyediakan akses langsung ke tiket panggung tanpa perantara dan anti-calo.
                </p>
            </div>

            <!-- Col 2: Navigasi Cepat -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900">Navigasi Utama</h4>
                <ul class="text-xs text-slate-600 space-y-2">
                    <li><a href="<?= url('index.php') ?>" class="hover:text-slate-950 transition-colors">Beranda</a></li>
                    <li><a href="<?= url('index.php#katalog') ?>" class="hover:text-slate-950 transition-colors">Katalog Konser Musik</a></li>
                    <li><a href="<?= url('about.php') ?>" class="hover:text-slate-950 transition-colors">Tentang Kami</a></li>
                    <li><a href="<?= url('faq.php') ?>" class="hover:text-slate-950 transition-colors">Pusat Bantuan & FAQ</a></li>
                    <li><a href="<?= url('contact.php') ?>" class="hover:text-slate-950 transition-colors">Hubungi Kami</a></li>
                </ul>
            </div>

            <!-- Col 3: Layanan & Jaminan -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900">Jaminan Tiket</h4>
                <ul class="text-xs text-slate-600 space-y-2">
                    <li>Garansi 100% tiket resmi promotor</li>
                    <li>E-Ticket dengan kode QR unik</li>
                    <li>Verifikasi transfer instan</li>
                    <li>Privasi data pembeli terenkripsi</li>
                </ul>
            </div>

            <!-- Col 4: Dukungan Pelanggan -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900">Bantuan Penonton</h4>
                <div class="text-xs space-y-1.5 text-slate-600">
                    <p class="text-slate-900 font-semibold">Email: support@eventix.id</p>
                    <p class="text-slate-900 font-semibold">WhatsApp: +62 812-9876-5432</p>
                    <p class="text-slate-500 text-[11px]">Setiap hari: 09.00 sampai 21.00 WIB</p>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; <?= date('Y') ?> EVENTIX Indonesia. Hak cipta dilindungi.</p>
            <div class="flex items-center gap-4 text-xs">
                <a href="<?= url('faq.php') ?>" class="hover:text-slate-900 transition-colors">Ketentuan Layanan</a>
                <a href="<?= url('faq.php') ?>" class="hover:text-slate-900 transition-colors">Kebijakan Privasi</a>
                <a href="<?= url('about.php') ?>" class="hover:text-slate-900 transition-colors">Keamanan Tiket</a>
            </div>
        </div>

    </div>
</footer>
