<?php
/**
 * EVENTIX - Footer Component
 * Modern Minimalist Design
 * 2 Primary Colors: #FF6082 (Punch Pink) & #0F172A (Deep Slate Noir)
 */
?>
<footer class="bg-white border-t border-slate-200/80 pt-14 pb-10 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 pb-12 border-b border-slate-100">
            
            <!-- Col 1: Brand Info -->
            <div class="space-y-4">
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
                <p class="text-xs text-slate-500 leading-relaxed max-w-sm">
                    Platform tiket konser musik resmi di Indonesia. Akses langsung ke tiket panggung tanpa calo dan dengan verifikasi instan.
                </p>
                <div class="flex items-center gap-2 pt-1 text-xs text-slate-600 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Layanan Sistem Normal</span>
                </div>
            </div>

            <!-- Col 2: Navigasi Cepat -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900 tracking-tight">Navigasi Utama</h4>
                <ul class="text-xs text-slate-600 space-y-2.5">
                    <li><a href="<?= url('index.php') ?>" class="hover:text-slate-950 transition-colors">Beranda</a></li>
                    <li><a href="<?= url('index.php#katalog') ?>" class="hover:text-slate-950 transition-colors">Katalog Konser Musik</a></li>
                    <li><a href="<?= url('about.php') ?>" class="hover:text-slate-950 transition-colors">Tentang Kami</a></li>
                    <li><a href="<?= url('faq.php') ?>" class="hover:text-slate-950 transition-colors">Pusat Bantuan & FAQ</a></li>
                    <li><a href="<?= url('contact.php') ?>" class="hover:text-slate-950 transition-colors">Hubungi Kami</a></li>
                </ul>
            </div>

            <!-- Col 3: Layanan & Jaminan -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900 tracking-tight">Jaminan Layanan</h4>
                <ul class="text-xs text-slate-600 space-y-2.5">
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>100% tiket resmi promotor</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>E-Ticket QR code terenkripsi</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Konfirmasi pembayaran instan</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Proteksi transaksi anti-calo</span>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Dukungan Pelanggan -->
            <div class="space-y-3">
                <h4 class="text-xs font-bold text-slate-900 tracking-tight">Dukungan Penonton</h4>
                <div class="text-xs space-y-2 text-slate-600">
                    <p class="text-slate-900 font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#FF6082]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <span>support@eventix.id</span>
                    </p>
                    <p class="text-slate-900 font-semibold flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span>+62 812-9876-5432</span>
                    </p>
                    <p class="text-slate-400 text-[11px] pt-1">Operasional setiap hari: 09.00 - 21.00 WIB</p>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; <?= date('Y') ?> EVENTIX Indonesia. Hak cipta dilindungi.</p>
            <div class="flex items-center gap-5 text-xs text-slate-500">
                <a href="<?= url('faq.php') ?>" class="hover:text-slate-900 transition-colors">Ketentuan Layanan</a>
                <a href="<?= url('faq.php') ?>" class="hover:text-slate-900 transition-colors">Kebijakan Privasi</a>
                <a href="<?= url('about.php') ?>" class="hover:text-slate-900 transition-colors">Keamanan Tiket</a>
            </div>
        </div>

    </div>
</footer>
