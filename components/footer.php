<?php
/**
 * EVENTIX - Footer Component
 * Full Redesign: SF Pro Display & Hallmark Curated Tone
 */
?>
<footer class="bg-white border-t border-slate-200/90 pt-16 pb-12 mt-auto font-sf">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-14 border-b border-slate-100">
            
            <!-- Col 1: Brand Masthead (5 Cols) -->
            <div class="md:col-span-5 space-y-4">
                <a href="<?= url('index.php') ?>" class="flex items-center gap-2.5">
                    <span class="w-2.5 h-6 bg-[#FF6082] rounded-full inline-block"></span>
                    <span class="text-2xl font-extrabold tracking-tighter text-slate-900 uppercase">
                        EVENTIX
                    </span>
                </a>
                <p class="text-sm text-slate-500 leading-relaxed max-w-sm font-normal">
                    Platform tiket pertunjukan konser musik live di Indonesia. Alokasi kuota resmi dari promotor, transaksi transparan, dan E-Ticket terverifikasi instan.
                </p>
                <div class="pt-2 text-xs text-slate-400">
                    Jakarta • Bandung • Surabaya • Bali
                </div>
            </div>

            <!-- Col 2: Navigasi (2 Cols) -->
            <div class="md:col-span-2 space-y-3">
                <p class="text-xs font-bold text-slate-900 tracking-normal">Navigasi</p>
                <ul class="text-xs text-slate-600 space-y-2.5 font-normal">
                    <li><a href="<?= url('index.php') ?>" class="hover:text-slate-950 transition-colors">Beranda</a></li>
                    <li><a href="<?= url('index.php#katalog') ?>" class="hover:text-slate-950 transition-colors">Katalog Konser</a></li>
                    <li><a href="<?= url('about.php') ?>" class="hover:text-slate-950 transition-colors">Tentang Kami</a></li>
                    <li><a href="<?= url('faq.php') ?>" class="hover:text-slate-950 transition-colors">Pusat Bantuan</a></li>
                    <li><a href="<?= url('contact.php') ?>" class="hover:text-slate-950 transition-colors">Hubungi Kami</a></li>
                </ul>
            </div>

            <!-- Col 3: Ketentuan & Tiket (2 Cols) -->
            <div class="md:col-span-2 space-y-3">
                <p class="text-xs font-bold text-slate-900 tracking-normal">Keamanan</p>
                <ul class="text-xs text-slate-600 space-y-2.5 font-normal">
                    <li><a href="<?= url('about.php') ?>" class="hover:text-slate-950 transition-colors">Alokasi Promotor</a></li>
                    <li><a href="<?= url('faq.php') ?>" class="hover:text-slate-950 transition-colors">E-Ticket QR Code</a></li>
                    <li><a href="<?= url('faq.php') ?>" class="hover:text-slate-950 transition-colors">Ketentuan Non-Refund</a></li>
                    <li><a href="<?= url('contact.php') ?>" class="hover:text-slate-950 transition-colors">Kemitraan Promotor</a></li>
                </ul>
            </div>

            <!-- Col 4: Dukungan Penonton (3 Cols) -->
            <div class="md:col-span-3 space-y-3">
                <p class="text-xs font-bold text-slate-900 tracking-normal">Layanan Pengguna</p>
                <div class="text-xs space-y-2 text-slate-600">
                    <p class="text-slate-900 font-semibold">support@eventix.id</p>
                    <p class="text-slate-900 font-semibold">+62 812-9876-5432</p>
                    <p class="text-slate-400 text-[11px] pt-1">Senin – Minggu, 09:00 – 21:00 WIB</p>
                </div>
            </div>

        </div>

        <!-- Bottom Copyright -->
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400 font-normal">
            <p>&copy; <?= date('Y') ?> EVENTIX Indonesia. Hak cipta dilindungi undang-undang.</p>
            <div class="flex items-center gap-6 text-xs text-slate-400">
                <a href="<?= url('faq.php') ?>" class="hover:text-slate-800 transition-colors">Ketentuan Layanan</a>
                <a href="<?= url('faq.php') ?>" class="hover:text-slate-800 transition-colors">Kebijakan Privasi</a>
                <a href="<?= url('about.php') ?>" class="hover:text-slate-800 transition-colors">Panduan Wristband</a>
            </div>
        </div>

    </div>
</footer>
