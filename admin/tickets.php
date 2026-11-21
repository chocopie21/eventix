<?php
/**
 * EVENTIX - Admin Panel: Kelola Kategori & Kuota Tiket
 * Human-Crafted Editorial Minimalist
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

$selectedEventId = filter_input(INPUT_GET, 'id_event', FILTER_VALIDATE_INT) ?: 0;
$editTicketId = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT) ?: 0;
$editTicket = null;

try {
    // 2. Ambil seluruh daftar konser untuk dropdown
    $stmtEvents = $pdo->query("SELECT id_event, nama_event, tanggal_event, venue FROM event ORDER BY tanggal_event ASC");
    $allEvents = $stmtEvents->fetchAll(PDO::FETCH_ASSOC);

    // Jika id_event belum ditentukan di URL, pilih konser pertama secara otomatis
    if ($selectedEventId <= 0 && !empty($allEvents)) {
        $selectedEventId = (int)$allEvents[0]['id_event'];
    }

    // Ambil info konser terpilih
    $selectedEvent = null;
    foreach ($allEvents as $ev) {
        if ((int)$ev['id_event'] === $selectedEventId) {
            $selectedEvent = $ev;
            break;
        }
    }

    // 3. Ambil data tiket jika mode edit aktif
    if ($editTicketId > 0) {
        $stmtEdit = $pdo->prepare("SELECT * FROM tiket WHERE id_tiket = :id LIMIT 1");
        $stmtEdit->execute([':id' => $editTicketId]);
        $editTicket = $stmtEdit->fetch(PDO::FETCH_ASSOC);
        if ($editTicket) {
            $selectedEventId = (int)$editTicket['id_event'];
        }
    }

    // 4. Ambil daftar tiket untuk konser yang sedang dipilih
    $tickets = [];
    if ($selectedEventId > 0) {
        $stmtTickets = $pdo->prepare("SELECT * FROM tiket WHERE id_event = :id ORDER BY harga DESC");
        $stmtTickets->execute([':id' => $selectedEventId]);
        $tickets = $stmtTickets->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    error_log("[Admin Tickets Error] " . $e->getMessage());
    $allEvents = [];
    $tickets = [];
}

$flashSuccess = getFlash('success');
$flashError = getFlash('error');
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori & Kuota Tiket | EVENTIX Admin</title>

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
        <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 sticky top-0 z-30 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Hamburger Button for Mobile / Tablet -->
                <button type="button" 
                        id="admin-sidebar-open" 
                        class="lg:hidden p-2 -ml-1 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none shrink-0" 
                        aria-label="Buka Menu Admin">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 font-heading truncate">Manajemen Kategori & Kuota Tiket</h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden sm:block">Atur harga, kuota tempat, dan fasilitas kategori tiket konser</p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                <a href="#form-tiket" class="inline-flex items-center gap-1.5 px-3 sm:px-3.5 py-2 rounded-lg bg-[#FF6082] text-white font-medium text-xs hover:bg-[#e54b6d] transition-colors shadow-xs">
                    <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span class="hidden sm:inline">Tambah Kategori Baru</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
            </div>
        </header>

        <!-- Body Content -->
        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">
            
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

            <!-- DROPDOWN FILTER PILIH KONSER MUSIK -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 text-slate-800 flex items-center justify-center shrink-0 border border-slate-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 block">Konser yang dikelola:</span>
                        <h2 class="text-base font-bold text-slate-900 font-heading">
                            <?= htmlspecialchars($selectedEvent['nama_event'] ?? 'Pilih Konser', ENT_QUOTES, 'UTF-8') ?>
                        </h2>
                    </div>
                </div>

                <!-- Dropdown Form -->
                <form method="GET" action="<?= url('admin/tickets.php') ?>" class="flex items-center gap-2">
                    <label for="id_event" class="text-xs font-semibold text-slate-700 whitespace-nowrap">Ganti konser:</label>
                    <select id="id_event" 
                            name="id_event" 
                            onchange="this.form.submit()" 
                            class="px-3.5 py-2 bg-white border border-slate-300 focus:border-slate-900 rounded-lg text-xs font-medium text-slate-900 focus:outline-none">
                        <?php foreach ($allEvents as $ev): ?>
                            <option value="<?= $ev['id_event'] ?>" <?= ((int)$ev['id_event'] === $selectedEventId) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ev['nama_event'], ENT_QUOTES, 'UTF-8') ?> (<?= date('d/m/Y', strtotime($ev['tanggal_event'])) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- FORM TAMBAH / EDIT KATEGORI TIKET -->
            <div id="form-tiket" class="bg-white rounded-xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-base font-bold font-heading text-slate-900">
                            <?= $editTicket ? 'Edit Kategori: ' . htmlspecialchars($editTicket['kategori'], ENT_QUOTES, 'UTF-8') : 'Tambah Kategori Tiket untuk Konser Ini' ?>
                        </h3>
                    </div>

                    <?php if ($editTicket): ?>
                        <a href="<?= url('admin/tickets.php?id_event=' . $selectedEventId) ?>" class="text-xs font-medium text-slate-600 hover:text-slate-900 underline underline-offset-2">
                            Batal edit
                        </a>
                    <?php endif; ?>
                </div>

                <form action="<?= url('actions/admin_ticket_save.php') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_tiket" value="<?= $editTicket['id_tiket'] ?? 0 ?>">
                    <input type="hidden" name="id_event" value="<?= $selectedEventId ?>">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        
                        <!-- Nama Kategori -->
                        <div>
                            <label for="kategori" class="block text-xs font-semibold text-slate-800 mb-1">
                                Nama kategori <span class="text-rose-600">*</span>
                            </label>
                            <input type="text" 
                                   id="kategori" 
                                   name="kategori" 
                                   value="<?= htmlspecialchars($editTicket['kategori'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                                   placeholder="Contoh: VIP Standing, Festival, Tribune" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>

                        <!-- Harga Tiket (Rp) -->
                        <div>
                            <label for="harga" class="block text-xs font-semibold text-slate-800 mb-1">
                                Harga satuan (Rp) <span class="text-rose-600">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-xs font-medium text-slate-500">Rp</span>
                                <input type="number" 
                                       id="harga" 
                                       name="harga" 
                                       min="0" 
                                       step="1000"
                                       value="<?= $editTicket ? (float)$editTicket['harga'] : '' ?>" 
                                       placeholder="1250000" 
                                       required
                                       class="w-full pl-10 pr-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                            </div>
                        </div>

                        <!-- Kuota Tiket -->
                        <div>
                            <label for="kuota_tersedia" class="block text-xs font-semibold text-slate-800 mb-1">
                                Kuota tersedia <span class="text-rose-600">*</span>
                            </label>
                            <input type="number" 
                                   id="kuota_tersedia" 
                                   name="kuota_tersedia" 
                                   min="0" 
                                   value="<?= $editTicket ? (int)$editTicket['kuota_tersedia'] : '' ?>" 
                                   placeholder="500" 
                                   required
                                   class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                        </div>

                    </div>

                    <!-- Deskripsi / Fasilitas Tiket -->
                    <div>
                        <label for="deskripsi" class="block text-xs font-semibold text-slate-800 mb-1">
                            Deskripsi & fasilitas kategori
                        </label>
                        <input type="text" 
                               id="deskripsi" 
                               name="deskripsi" 
                               value="<?= htmlspecialchars($editTicket['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?>" 
                               placeholder="Contoh: Barisan depan panggung, exclusive merchandise kit & wristband" 
                               class="w-full px-3.5 py-2.5 bg-white border border-slate-300 focus:border-slate-900 focus:ring-1 focus:ring-slate-900 rounded-lg text-sm text-slate-900 placeholder-slate-400 focus:outline-none transition-colors">
                    </div>

                    <!-- Tombol Simpan -->
                    <div class="pt-2 flex items-center justify-end gap-3">
                        <?php if ($editTicket): ?>
                            <a href="<?= url('admin/tickets.php?id_event=' . $selectedEventId) ?>" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-xs font-medium text-slate-700 transition-colors">
                                Batal
                            </a>
                        <?php endif; ?>

                        <button type="submit" 
                                class="px-5 py-2.5 rounded-lg text-xs font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] shadow-xs transition-colors">
                            <?= $editTicket ? 'Perbarui Kategori' : 'Simpan Kategori Tiket' ?>
                        </button>
                    </div>

                </form>
            </div>

            <!-- TABEL DAFTAR KATEGORI TIKET -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold font-heading text-slate-900">Kategori Tiket Konser Ini</h3>
                        <p class="text-xs text-slate-500">Total <?= count($tickets) ?> kategori tiket aktif untuk konser yang dipilih</p>
                    </div>
                </div>

                <div class="w-full overflow-x-auto lg:overflow-hidden">
                    <table class="w-full min-w-[620px] lg:min-w-0 lg:table-fixed text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-3 w-[22%]">Nama kategori</th>
                                <th class="py-3 px-3 w-[38%]">Fasilitas / Deskripsi</th>
                                <th class="py-3 px-3 w-[16%]">Harga tiket</th>
                                <th class="py-3 px-3 w-[12%]">Sisa kuota</th>
                                <th class="py-3 px-3 w-[12%] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($tickets)): ?>
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-400 italic">Belum ada kategori tiket untuk konser ini. Silakan tambahkan pada formulir di atas.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($tickets as $t): 
                                    $idT = (int)$t['id_tiket'];
                                    $quota = (int)$t['kuota_tersedia'];
                                ?>
                                    <tr class="hover:bg-slate-50/75 transition-colors">
                                        
                                        <!-- Nama Kategori -->
                                        <td class="py-3 px-3 font-semibold text-slate-900 text-sm truncate">
                                            <?= htmlspecialchars($t['kategori'], ENT_QUOTES, 'UTF-8') ?>
                                        </td>

                                        <!-- Deskripsi -->
                                        <td class="py-3 px-3 text-slate-600 truncate" title="<?= htmlspecialchars($t['deskripsi'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <?= !empty($t['deskripsi']) ? htmlspecialchars($t['deskripsi'], ENT_QUOTES, 'UTF-8') : '-' ?>
                                        </td>

                                        <!-- Harga -->
                                        <td class="py-3 px-3 whitespace-nowrap font-bold text-slate-900 text-sm">
                                            <?= formatRupiah($t['harga']) ?>
                                        </td>

                                        <!-- Kuota -->
                                        <td class="py-3 px-3 whitespace-nowrap">
                                            <?php if ($quota <= 0): ?>
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Habis
                                                </span>
                                            <?php elseif ($quota <= 50): ?>
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <?= number_format($quota, 0, ',', '.') ?> tersisa
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <?= number_format($quota, 0, ',', '.') ?> tiket
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi Buttons -->
                                        <td class="py-3 px-3 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                
                                                <!-- Tombol Edit -->
                                                <a href="<?= url('admin/tickets.php?id_event=' . $selectedEventId . '&edit=' . $idT . '#form-tiket') ?>" 
                                                   class="p-1.5 rounded border border-slate-300 hover:bg-slate-100 text-slate-700 transition-colors"
                                                   title="Edit Kategori">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <!-- Tombol Hapus Form -->
                                                <form action="<?= url('actions/admin_ticket_delete.php') ?>" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori tiket \'<?= htmlspecialchars(addslashes($t['kategori']), ENT_QUOTES, 'UTF-8') ?>\'?');" 
                                                      class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_tiket" value="<?= $idT ?>">
                                                    <input type="hidden" name="id_event" value="<?= $selectedEventId ?>">
                                                    <button type="submit" 
                                                            class="p-1.5 rounded border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 transition-colors"
                                                            title="Hapus Kategori">
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
