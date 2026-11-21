<?php
/**
 * EVENTIX - Admin Panel: Kelola Konser Musik
 * Human-Crafted Editorial Minimalist
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

$editId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT) ?: 0;
$editEvent = null;

try {
    // 2. Query 4 KPI Ringkasan Data
    $totalKonser = (int)$pdo->query("SELECT COUNT(*) FROM event")->fetchColumn();
    $totalTiketKategori = (int)$pdo->query("SELECT COUNT(*) FROM tiket")->fetchColumn();
    $totalPesanan = (int)$pdo->query("SELECT COUNT(*) FROM pesanan")->fetchColumn();
    $totalPendapatan = (float)$pdo->query("SELECT COALESCE(SUM(harga), 0) FROM pesanan WHERE status = 'lunas'")->fetchColumn();

    // 3. Ambil data konser jika mode edit aktif
    if ($editId > 0) {
        $stmtEdit = $pdo->prepare("SELECT e.*, k.daftar_lagu, k.genre_musik 
                                   FROM event e 
                                   LEFT JOIN konser k ON e.id_event = k.id_event 
                                   WHERE e.id_event = :id LIMIT 1");
        $stmtEdit->execute([':id' => $editId]);
        $editEvent = $stmtEdit->fetch(PDO::FETCH_ASSOC);
    }

    // 4. Ambil seluruh daftar konser untuk tabel
    $sqlConcerts = "SELECT 
                        e.*, 
                        k.genre_musik, 
                        k.daftar_lagu, 
                        COUNT(t.id_tiket) AS total_kategori,
                        COALESCE(SUM(t.kuota_tersedia), 0) AS total_kuota,
                        COALESCE(MIN(t.harga), 0) AS harga_terendah
                    FROM event e
                    LEFT JOIN konser k ON e.id_event = k.id_event
                    LEFT JOIN tiket t ON e.id_event = t.id_event
                    GROUP BY e.id_event
                    ORDER BY e.tanggal_event ASC";
    $concerts = $pdo->query($sqlConcerts)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log("[Admin Index Error] " . $e->getMessage());
    $totalKonser = 0;
    $totalTiketKategori = 0;
    $totalPesanan = 0;
    $totalPendapatan = 0;
    $concerts = [];
}

$flashSuccess = getFlash('success');
$flashError = getFlash('error');
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Konser Musik | EVENTIX Admin</title>

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
<body class="h-full flex bg-slate-100 text-slate-900 antialiased">

    <!-- Admin Sidebar Component -->
    <?php include __DIR__ . '/../components/admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Navigation Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-6 sm:px-8 flex items-center justify-between shrink-0 sticky top-0 z-20">
            <div>
                <h1 class="text-base font-bold text-slate-900 font-heading">Manajemen Konser Musik</h1>
                <p class="text-xs text-slate-500">Atur jadwal, informasi artis, poster, dan katalog konser</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="#form-konser" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#FF6082] text-white font-medium text-xs hover:bg-[#e54b6d] transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Konser Baru</span>
                </a>
            </div>
        </header>

        <!-- Body Content -->
        <main class="p-6 sm:p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <!-- Flash Messages -->
            <?php if ($flashSuccess): ?>
                <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-emerald-800 text-xs">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-rose-800 text-xs">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- 4 SUMMARY KPI CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: Total Konser -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-1.5">
                    <div class="flex items-center justify-between text-slate-500">
                        <span class="text-xs font-semibold">Konser aktif</span>
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-heading">
                        <?= $totalKonser ?> <span class="text-xs font-normal text-slate-500">Event</span>
                    </div>
                    <p class="text-xs text-slate-500">Tersedia di katalog</p>
                </div>

                <!-- Card 2: Kategori Tiket -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-1.5">
                    <div class="flex items-center justify-between text-slate-500">
                        <span class="text-xs font-semibold">Kategori tiket</span>
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-heading">
                        <?= $totalTiketKategori ?> <span class="text-xs font-normal text-slate-500">Kategori</span>
                    </div>
                    <p class="text-xs text-slate-500">VIP, Festival, Tribune</p>
                </div>

                <!-- Card 3: Total Transaksi -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-1.5">
                    <div class="flex items-center justify-between text-slate-500">
                        <span class="text-xs font-semibold">Total pesanan</span>
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-heading">
                        <?= $totalPesanan ?> <span class="text-xs font-normal text-slate-500">Transaksi</span>
                    </div>
                    <p class="text-xs text-slate-500">Masuk ke sistem</p>
                </div>

                <!-- Card 4: Pendapatan Lunas -->
                <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-xs space-y-1.5">
                    <div class="flex items-center justify-between text-slate-500">
                        <span class="text-xs font-semibold">Pendapatan lunas</span>
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="text-2xl font-bold text-slate-900 font-heading truncate" title="<?= formatRupiah($totalPendapatan) ?>">
                        <?= formatRupiah($totalPendapatan) ?>
                    </div>
                    <p class="text-xs text-slate-500">Pesanan terverifikasi</p>
                </div>

            </div>

            <!-- FORM TAMBAH / EDIT KONSER MUSIK -->
            <div id="form-konser" class="bg-white rounded-xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h2 class="text-lg font-bold font-heading text-slate-900">
                            <?= $editEvent ? 'Edit Data Konser: ' . htmlspecialchars($editEvent['nama_event'], ENT_QUOTES, 'UTF-8') : 'Tambah Konser Musik Baru' ?>
                        </h2>
                    </div>

                    <?php if ($editEvent): ?>
                        <a href="<?= url('admin/index.php') ?>" class="text-xs font-medium text-slate-600 hover:text-slate-900 underline underline-offset-2">
                            Batal edit
                        </a>
                    <?php endif; ?>
                </div>

                <form action="<?= url('actions/admin_event_save.php') ?>" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      class="space-y-4">
                    
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_event" value="<?= $editEvent['id_event'] ?? 0 ?>">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Nama Event Konser -->
                        <div>
                            <label for="nama_event" class="block text-xs font-semibold text-slate-800 mb-1">
                                Nama konser musik <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="nama_event" 
                                   name="nama_event" 
                                   value="<?= htmlspecialchars($editEvent['nama_event'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   placeholder="Contoh: Sheila on 7: Tunggu Aku Di Jakarta 2026" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>

                        <!-- Pengisi Acara / Artis -->
                        <div>
                            <label for="pengisi_acara" class="block text-xs font-semibold text-slate-800 mb-1">
                                Pengisi acara / Bintang tamu <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="pengisi_acara" 
                                   name="pengisi_acara" 
                                   value="<?= htmlspecialchars($editEvent['pengisi_acara'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   placeholder="Contoh: Sheila on 7, Perunggu" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>

                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        <!-- Genre Musik -->
                        <div>
                            <label for="genre_musik" class="block text-xs font-semibold text-slate-800 mb-1">
                                Genre musik <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="genre_musik" 
                                   name="genre_musik" 
                                   value="<?= htmlspecialchars($editEvent['genre_musik'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   placeholder="Contoh: Pop Rock, Indie, Rock" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>

                        <!-- Tanggal Event -->
                        <div>
                            <label for="tanggal_event" class="block text-xs font-semibold text-slate-800 mb-1">
                                Tanggal pelaksanaan <span class="text-rose-600">*</span>
                            </label>
                            <input type="date" 
                                   id="tanggal_event" 
                                   name="tanggal_event" 
                                   value="<?= htmlspecialchars($editEvent['tanggal_event'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 focus:outline-none transition-colors">
                        </div>

                        <!-- Jam Mulai -->
                        <div>
                            <label for="jam_event" class="block text-xs font-semibold text-slate-800 mb-1">
                                Waktu mulai (WIB) <span class="text-rose-600">*</span>
                            </label>
                            <input type="time" 
                                   id="jam_event" 
                                   name="jam_event" 
                                   value="<?= htmlspecialchars($editEvent['jam_event'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 focus:outline-none transition-colors">
                        </div>

                    </div>

                    <!-- Venue / Lokasi Konser -->
                    <div>
                        <label for="venue" class="block text-xs font-semibold text-slate-800 mb-1">
                            Lokasi venue / Tempat acara <span class="text-rose-600">*</span>
                        </label>
                        <input type="text" 
                               id="venue" 
                               name="venue" 
                               value="<?= htmlspecialchars($editEvent['venue'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="Contoh: Stadion Madya Gelora Bung Karno, Senayan, Jakarta" 
                               required
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                    </div>

                    <!-- Daftar Lagu / Setlist -->
                    <div>
                        <label for="daftar_lagu" class="block text-xs font-semibold text-slate-800 mb-1">
                            Daftar lagu (setlist prediksi - pisahkan dengan tanda koma)
                        </label>
                        <input type="text" 
                               id="daftar_lagu" 
                               name="daftar_lagu" 
                               value="<?= htmlspecialchars($editEvent['daftar_lagu'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="Contoh: Dan, Sephia, Sahabat Sejati, Pejantan Tangguh, Melompat Lebih Tinggi" 
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                    </div>

                    <!-- Deskripsi Konser & Upload Poster (2 Kolom) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <!-- Deskripsi -->
                        <div>
                            <label for="deskripsi" class="block text-xs font-semibold text-slate-800 mb-1">
                                Deskripsi konser & informasi acara
                            </label>
                            <textarea id="deskripsi" 
                                      name="deskripsi" 
                                      rows="4" 
                                      placeholder="Tuliskan latar belakang konser, ketentuan panggung, dan informasi penting lainnya..."
                                      class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors"><?= htmlspecialchars($editEvent['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <!-- Upload File Poster -->
                        <div>
                            <label for="poster" class="block text-xs font-semibold text-slate-800 mb-1">
                                File poster konser <span class="text-slate-400 font-normal">(JPG, PNG, WEBP maks 3MB)</span>
                            </label>
                            
                            <?php if (!empty($editEvent['poster'])): ?>
                                <div class="flex items-center gap-3 mb-2 p-2 bg-slate-50 rounded-lg border border-slate-200">
                                    <img src="<?= asset('uploads/posters/' . $editEvent['poster']) ?>" alt="Poster" class="w-10 h-12 object-cover rounded">
                                    <div class="text-xs text-slate-600 min-w-0">
                                        <span class="block font-medium text-slate-900">Poster saat ini:</span>
                                        <span class="truncate block text-slate-500"><?= htmlspecialchars($editEvent['poster'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <input type="file" 
                                   id="poster" 
                                   name="poster" 
                                   accept="image/jpeg,image/png,image/webp"
                                   class="w-full px-3 py-2 bg-white border border-slate-300 focus:border-slate-900 rounded-lg text-xs text-slate-600 focus:outline-none file:mr-3 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white cursor-pointer">
                            <p class="text-xs text-slate-500 mt-1">Kosongkan jika tidak ingin mengubah poster saat edit.</p>
                        </div>

                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-2 flex items-center justify-end gap-3">
                        <?php if ($editEvent): ?>
                            <a href="<?= url('admin/index.php') ?>" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-xs font-medium text-slate-700 transition-colors">
                                Batal
                            </a>
                        <?php endif; ?>

                        <button type="submit" 
                                class="px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] shadow-xs transition-colors">
                            <?= $editEvent ? 'Perbarui Data Konser' : 'Simpan & Publikasikan Konser' ?>
                        </button>
                    </div>

                </form>
            </div>

            <!-- TABEL DAFTAR KONSER MUSIK -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold font-heading text-slate-900">Daftar Seluruh Konser Musik</h3>
                        <p class="text-xs text-slate-500">Total <?= count($concerts) ?> konser terdaftar dalam database</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-4">Poster</th>
                                <th class="py-3 px-4">Nama konser & artis</th>
                                <th class="py-3 px-4">Genre</th>
                                <th class="py-3 px-4">Jadwal & waktu</th>
                                <th class="py-3 px-4">Lokasi venue</th>
                                <th class="py-3 px-4">Kategori & sisa kuota</th>
                                <th class="py-3 px-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($concerts)): ?>
                                <tr>
                                    <td colspan="7" class="py-8 text-center text-slate-400 italic">Belum ada data konser di sistem.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($concerts as $c): 
                                    $idC = (int)$c['id_event'];
                                ?>
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        
                                        <!-- Poster Thumbnail -->
                                        <td class="py-3 px-4">
                                            <img src="<?= asset('uploads/posters/' . ($c['poster'] ?? 'default.jpg')) ?>" 
                                                 alt="Poster" 
                                                 class="w-10 h-14 object-cover rounded border border-slate-200"
                                                 onerror="this.src='https://images.unsplash.com/photo-1470225620780-dba8ba36b745?auto=format&fit=crop&w=150&q=80'">
                                        </td>

                                        <!-- Nama & Artis -->
                                        <td class="py-3 px-4 max-w-xs">
                                            <span class="font-semibold text-slate-900 block truncate" title="<?= htmlspecialchars($c['nama_event'], ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($c['nama_event'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <span class="text-slate-500 text-xs block truncate">
                                                <?= htmlspecialchars($c['pengisi_acara'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <!-- Genre -->
                                        <td class="py-3 px-4">
                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                                <?= htmlspecialchars($c['genre_musik'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <!-- Tanggal & Jam -->
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <span class="font-medium text-slate-900 block"><?= formatTanggalIndo($c['tanggal_event'], false) ?></span>
                                            <span class="text-slate-500 text-xs block"><?= formatJam($c['jam_event']) ?></span>
                                        </td>

                                        <!-- Venue -->
                                        <td class="py-3 px-4 max-w-xs truncate text-slate-600" title="<?= htmlspecialchars($c['venue'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars($c['venue'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <!-- Kategori Tiket & Kuota -->
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <span class="font-semibold text-slate-900 block"><?= $c['total_kategori'] ?> Kategori</span>
                                            <span class="text-xs text-blue-600 font-medium block">Sisa <?= number_format($c['total_kuota'], 0, ',', '.') ?> tiket</span>
                                        </td>

                                        <!-- Aksi Buttons -->
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5">
                                                
                                                <!-- Tombol Kelola Tiket -->
                                                <a href="<?= url('admin/tickets.php?id_event=' . $idC) ?>" 
                                                   class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-xs flex items-center gap-1 transition-colors"
                                                   title="Atur Kategori & Kuota Tiket">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                                    </svg>
                                                    <span>Tiket</span>
                                                </a>

                                                <!-- Tombol Edit -->
                                                <a href="<?= url('admin/index.php?edit=' . $idC . '#form-konser') ?>" 
                                                   class="p-1.5 rounded border border-slate-300 hover:bg-slate-100 text-slate-700 transition-colors"
                                                   title="Edit Data Konser">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <!-- Tombol Hapus Form -->
                                                <form action="<?= url('actions/admin_event_delete.php') ?>" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus konser \'<?= htmlspecialchars(addslashes($c['nama_event']), ENT_QUOTES, 'UTF-8') ?>\' beserta seluruh tiketnya? Tindakan ini tidak dapat dibatalkan.');" 
                                                      class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_event" value="<?= $idC ?>">
                                                    <button type="submit" 
                                                            class="p-1.5 rounded border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors"
                                                            title="Hapus Konser">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </main>

    </div>

</body>
</html>
