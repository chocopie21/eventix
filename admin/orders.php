<?php
/**
 * EVENTIX - Admin Panel: Kelola Pesanan & Verifikasi Pembayaran
 * Human-Crafted Editorial Minimalist
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

$filterStatus = trim($_GET['status'] ?? 'semua');
$searchQuery  = trim($_GET['q'] ?? '');

try {
    // 2. Hitung jumlah pesanan per status untuk badge tab
    $countVerifikasi = (int)$pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'menunggu_verifikasi'")->fetchColumn();
    $countMenunggu   = (int)$pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'menunggu'")->fetchColumn();
    $countLunas      = (int)$pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'lunas'")->fetchColumn();
    $countDitolak    = (int)$pdo->query("SELECT COUNT(*) FROM pesanan WHERE status = 'ditolak'")->fetchColumn();

    // 3. Query Seluruh Pesanan dengan relasi Pengguna, Event, Konser, dan Pembayaran
    $sql = "SELECT 
                p.*,
                u.nama AS nama_pembeli,
                u.email AS email_pembeli,
                u.no_telepon AS telp_pembeli,
                pay.id_pembayaran,
                pay.metode AS metode_bayar,
                pay.bukti_bayar,
                pay.tanggal AS tanggal_bayar,
                pay.status AS status_pembayaran,
                e.id_event,
                e.nama_event,
                e.tanggal_event,
                e.venue,
                e.poster,
                k.genre_musik
            FROM pesanan p
            INNER JOIN pengguna u ON p.id_pengguna = u.id_pengguna
            LEFT JOIN pembayaran pay ON p.id_pesanan = pay.id_pesanan
            LEFT JOIN (
                SELECT dp.id_pesanan, MIN(t.id_event) AS id_event
                FROM detail_pesanan dp
                JOIN tiket t ON dp.id_tiket = t.id_tiket
                GROUP BY dp.id_pesanan
            ) o_ev ON p.id_pesanan = o_ev.id_pesanan
            LEFT JOIN event e ON o_ev.id_event = e.id_event
            LEFT JOIN konser k ON e.id_event = k.id_event
            WHERE 1=1";

    $params = [];

    // Filter status
    if ($filterStatus !== 'semua' && in_array($filterStatus, ['menunggu', 'menunggu_verifikasi', 'lunas', 'ditolak'], true)) {
        $sql .= " AND p.status = :status";
        $params[':status'] = $filterStatus;
    }

    // Filter pencarian
    if (!empty($searchQuery)) {
        $sql .= " AND (
            p.id_pesanan LIKE :q 
            OR u.nama LIKE :q 
            OR u.email LIKE :q 
            OR e.nama_event LIKE :q
        )";
        $params[':q'] = "%{$searchQuery}%";
    }

    // Urutkan: antrean verifikasi di paling atas, lalu tanggal terbaru
    $sql .= " ORDER BY (CASE WHEN p.status = 'menunggu_verifikasi' THEN 1 WHEN p.status = 'menunggu' THEN 2 ELSE 3 END), p.tanggal DESC";

    $stmtOrders = $pdo->prepare($sql);
    $stmtOrders->execute($params);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

    // Ambil rincian tiket untuk seluruh pesanan yang tampil
    $orderDetailsMap = [];
    if (!empty($orders)) {
        $orderIds = array_column($orders, 'id_pesanan');
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        
        $stmtDetails = $pdo->prepare("SELECT 
                                          dp.id_pesanan, 
                                          dp.jumlah, 
                                          dp.sub_total, 
                                          t.kategori, 
                                          t.harga AS harga_satuan 
                                      FROM detail_pesanan dp 
                                      JOIN tiket t ON dp.id_tiket = t.id_tiket 
                                      WHERE dp.id_pesanan IN ($placeholders)");
        $stmtDetails->execute($orderIds);
        $allDetails = $stmtDetails->fetchAll(PDO::FETCH_ASSOC);

        foreach ($allDetails as $d) {
            $orderDetailsMap[$d['id_pesanan']][] = $d;
        }
    }

} catch (PDOException $e) {
    error_log("[Admin Orders Error] " . $e->getMessage());
    $orders = [];
    $orderDetailsMap = [];
    $countVerifikasi = 0;
    $countMenunggu = 0;
    $countLunas = 0;
    $countDitolak = 0;
}

$flashSuccess = getFlash('success');
$flashError = getFlash('error');
$flashInfo = getFlash('info');
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan & Verifikasi Pembayaran | EVENTIX Admin</title>

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
                    <h1 class="text-sm sm:text-base font-bold text-slate-900 font-heading truncate">Verifikasi Pembayaran & Pesanan</h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden sm:block">Tinjau bukti transfer pembayaran, setujui pesanan, atau batalkan pesanan</p>
                </div>
            </div>

            <!-- Quick Action: Filter Antrean Verifikasi -->
            <div class="shrink-0">
                <a href="<?= url('admin/orders.php?status=menunggu_verifikasi') ?>" 
                   class="inline-flex items-center gap-1.5 sm:gap-2 px-2.5 sm:px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 text-xs font-semibold transition-colors">
                    <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0"></span>
                    <span class="hidden sm:inline">Perlu verifikasi: <?= $countVerifikasi ?> pesanan</span>
                    <span class="sm:hidden">Verifikasi (<?= $countVerifikasi ?>)</span>
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

            <?php if ($flashInfo): ?>
                <div class="p-4 rounded-lg bg-blue-50 border border-blue-200 flex items-start gap-2.5 text-blue-800 text-xs">
                    <svg class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($flashInfo, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- FILTER TABS & SEARCH BAR -->
            <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-xs space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    
                    <!-- Status Filter Tabs -->
                    <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                        <?php
                        $tabs = [
                            'semua'               => 'Semua Pesanan',
                            'menunggu_verifikasi' => "Perlu Verifikasi ({$countVerifikasi})",
                            'menunggu'            => "Menunggu Bayar ({$countMenunggu})",
                            'lunas'               => "Lunas ({$countLunas})",
                            'ditolak'             => "Ditolak ({$countDitolak})"
                        ];
                        foreach ($tabs as $k => $label):
                            $isActive = ($filterStatus === $k);
                        ?>
                            <a href="<?= url('admin/orders.php?status=' . $k . (!empty($searchQuery) ? '&q=' . urlencode($searchQuery) : '')) ?>" 
                               class="px-3 py-1.5 rounded-lg whitespace-nowrap font-medium transition-colors <?= $isActive ? 'bg-[#FF6082] text-white' : 'bg-slate-50 text-slate-600 hover:text-slate-900 border border-slate-200' ?>">
                                <?= $label ?>
                            </a>
                        <?php endforeach; ?>
                    </div>

                    <!-- Search Input Form -->
                    <form method="GET" action="<?= url('admin/orders.php') ?>" class="flex items-center gap-2 max-w-xs w-full">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus, ENT_QUOTES, 'UTF-8') ?>">
                        <div class="relative w-full">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" 
                                   name="q" 
                                   value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>" 
                                   placeholder="Cari ID, pembeli, konser..." 
                                   class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 focus:border-slate-900 rounded-lg text-xs text-slate-900 placeholder-slate-400 focus:outline-none">
                        </div>
                        <?php if (!empty($searchQuery)): ?>
                            <a href="<?= url('admin/orders.php?status=' . $filterStatus) ?>" class="p-1.5 text-slate-400 hover:text-slate-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </a>
                        <?php endif; ?>
                    </form>

                </div>
            </div>

            <!-- TABEL SELURUH PESANAN & VERIFIKASI -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold font-heading text-slate-900">Daftar Transaksi Pesanan Tiket</h3>
                        <p class="text-xs text-slate-500">Total <?= count($orders) ?> transaksi ditemukan</p>
                    </div>
                </div>

                <div class="w-full overflow-x-auto lg:overflow-hidden">
                    <table class="w-full min-w-[760px] lg:min-w-0 lg:table-fixed text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3 px-3 w-[12%]">ID & tanggal</th>
                                <th class="py-3 px-3 w-[16%]">Pembeli</th>
                                <th class="py-3 px-3 w-[22%]">Konser & tiket</th>
                                <th class="py-3 px-3 w-[13%]">Total</th>
                                <th class="py-3 px-3 w-[11%]">Bukti transfer</th>
                                <th class="py-3 px-3 w-[10%]">Status</th>
                                <th class="py-3 px-3 w-[16%] text-right">Aksi & Kelola</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" class="py-10 text-center text-slate-400 italic">
                                        Tidak ada transaksi pesanan yang sesuai dengan kriteria filter.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $o): 
                                    $idO = (int)$o['id_pesanan'];
                                    $items = $orderDetailsMap[$idO] ?? [];
                                    $st = $o['status'];
                                    $hasReceipt = !empty($o['bukti_bayar']);
                                    $receiptUrl = $hasReceipt ? asset('uploads/payments/' . $o['bukti_bayar']) : '';
                                ?>
                                    <tr class="hover:bg-slate-50/75 transition-colors <?= ($st === 'menunggu_verifikasi') ? 'bg-blue-50/40' : '' ?>">
                                        
                                        <!-- ID & Tanggal -->
                                        <td class="py-3 px-3 whitespace-nowrap min-w-0">
                                            <span class="font-mono font-semibold text-slate-900 block">#EVX-<?= $idO ?></span>
                                            <span class="text-xs text-slate-500 block"><?= date('d/m/Y H:i', strtotime($o['tanggal'])) ?></span>
                                        </td>

                                        <!-- Pembeli -->
                                        <td class="py-3 px-3 min-w-0">
                                            <span class="font-semibold text-slate-900 block truncate" title="<?= htmlspecialchars($o['nama_pembeli'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($o['nama_pembeli'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="text-xs text-slate-500 block truncate" title="<?= htmlspecialchars($o['email_pembeli'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($o['email_pembeli'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php if (!empty($o['telp_pembeli'])): ?>
                                                <span class="text-xs text-slate-400 block truncate"><?= htmlspecialchars($o['telp_pembeli'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Konser & Item Tiket -->
                                        <td class="py-3 px-3 min-w-0">
                                            <span class="font-semibold text-slate-900 block truncate" title="<?= htmlspecialchars($o['nama_event'] ?? '-', ENT_QUOTES, 'UTF-8') ?>">
                                                <?= htmlspecialchars($o['nama_event'] ?? 'Konser', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                            <div class="text-xs text-slate-500 space-y-0.5 mt-0.5">
                                                <?php foreach ($items as $it): ?>
                                                    <div class="truncate">
                                                        <span class="text-slate-700"><?= htmlspecialchars($it['kategori'], ENT_QUOTES, 'UTF-8') ?></span> 
                                                        <span class="text-slate-400 font-mono">(<?= $it['jumlah'] ?>x)</span>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>

                                        <!-- Total Biaya & Metode -->
                                        <td class="py-3 px-3 whitespace-nowrap min-w-0">
                                            <span class="font-bold text-slate-900 block text-sm">
                                                <?= formatRupiah($o['harga']) ?>
                                            </span>
                                            <span class="text-[11px] text-slate-500 block truncate">
                                                <?= htmlspecialchars($o['metode_bayar'] ?? 'Manual Transfer', ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <!-- Bukti Bayar Thumbnail -->
                                        <td class="py-3 px-3 whitespace-nowrap min-w-0">
                                            <?php if ($hasReceipt): ?>
                                                <button type="button" 
                                                        onclick="openReceiptModal('<?= $idO ?>', '<?= htmlspecialchars($o['nama_pembeli'], ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($receiptUrl, ENT_QUOTES, 'UTF-8') ?>', '<?= formatRupiah($o['harga']) ?>', '<?= htmlspecialchars($o['metode_bayar'] ?? 'Transfer', ENT_QUOTES, 'UTF-8') ?>')"
                                                        class="inline-flex items-center gap-1.5 p-1 rounded-lg border border-slate-200 hover:border-slate-400 bg-white group transition-colors">
                                                    <img src="<?= $receiptUrl ?>" alt="Bukti" class="w-7 h-7 rounded object-cover">
                                                    <span class="text-xs font-medium text-blue-600 pr-1 group-hover:underline">Buka bukti</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400 italic">Belum diunggah</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="py-3 px-3 whitespace-nowrap min-w-0">
                                            <?php if ($st === 'menunggu'): ?>
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                                    Menunggu bayar
                                                </span>
                                            <?php elseif ($st === 'menunggu_verifikasi'): ?>
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-blue-50 text-blue-800 border border-blue-200">
                                                    Perlu verifikasi
                                                </span>
                                            <?php elseif ($st === 'lunas'): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-[11px] font-medium bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Lunas
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-medium bg-rose-50 text-rose-800 border border-rose-200">
                                                    Ditolak
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi & Kelola: ACC / Tolak / Batalkan / Hapus -->
                                        <td class="py-3 px-3 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                
                                                <?php if ($st === 'menunggu_verifikasi' || $st === 'menunggu'): ?>
                                                    <!-- Tombol ACC / Konfirmasi Lunas -->
                                                    <form action="<?= url('actions/admin_payment_verify.php') ?>" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Konfirmasi bahwa pembayaran pesanan #EVX-<?= $idO ?> telah diterima secara sah?');"
                                                          class="inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id_pesanan" value="<?= $idO ?>">
                                                        <input type="hidden" name="action" value="acc">
                                                        <button type="submit" 
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition-colors shadow-2xs"
                                                                title="Konfirmasi Pembayaran Lunas">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            <span>ACC</span>
                                                        </button>
                                                    </form>

                                                    <!-- Tombol Tolak & Kembalikan Kuota -->
                                                    <form action="<?= url('actions/admin_payment_verify.php') ?>" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Tolak pesanan #EVX-<?= $idO ?>? Kuota tiket akan otomatis dikembalikan ke sistem.');"
                                                          class="inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id_pesanan" value="<?= $idO ?>">
                                                        <input type="hidden" name="action" value="tolak">
                                                        <button type="submit" 
                                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-semibold text-xs transition-colors"
                                                                title="Tolak Pesanan & Kembalikan Kuota">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            <span>Tolak</span>
                                                        </button>
                                                    </form>

                                                <?php elseif ($st === 'lunas'): ?>
                                                    <!-- Tombol Batalkan Pesanan Lunas & Kembalikan Kuota -->
                                                    <form action="<?= url('actions/admin_payment_verify.php') ?>" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Batalkan pesanan lunas #EVX-<?= $idO ?>? Tiket e-ticket akan dinonaktifkan dan kuota tiket akan dikembalikan ke sistem.');"
                                                          class="inline">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="id_pesanan" value="<?= $idO ?>">
                                                        <input type="hidden" name="action" value="tolak">
                                                        <button type="submit" 
                                                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-semibold text-xs transition-colors"
                                                                title="Batalkan Pesanan & Kembalikan Kuota">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            <span>Batalkan</span>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <!-- Tombol Hapus Permanen (Untuk Semua Status) -->
                                                <form action="<?= url('actions/admin_order_delete.php') ?>" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Hapus permanen transaksi #EVX-<?= $idO ?>? Jika belum dibatalkan, kuota tiket akan otomatis dikembalikan ke sistem.');"
                                                      class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_pesanan" value="<?= $idO ?>">
                                                    <button type="submit" 
                                                            class="p-1.5 rounded-lg border border-slate-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 transition-colors" 
                                                            title="Hapus Data Pesanan & Kembalikan Kuota">
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

    <!-- MODAL PRATINJAU BUKTI TRANSFER -->
    <div id="receipt-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
        <div class="bg-white rounded-xl max-w-lg w-full p-6 space-y-4 shadow-xl relative border border-slate-200">
            <button type="button" onclick="closeReceiptModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 focus:outline-none p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div>
                <h3 id="modal-receipt-order" class="text-base font-bold text-slate-900 font-heading">#EVX-000</h3>
                <p id="modal-receipt-info" class="text-xs text-slate-500">Nama Pembeli • Total</p>
            </div>

            <!-- Image Container -->
            <div class="p-2 bg-slate-50 rounded-lg border border-slate-200 max-h-[420px] overflow-auto flex items-center justify-center">
                <img id="modal-receipt-img" src="#" alt="Bukti Transfer" class="max-h-[380px] w-auto rounded object-contain">
            </div>

            <div class="flex items-center justify-between pt-2">
                <a id="modal-download-link" href="#" target="_blank" class="text-xs font-medium text-blue-600 hover:text-blue-700 underline underline-offset-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Buka Ukuran Asli</span>
                </a>

                <button type="button" onclick="closeReceiptModal()" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <script>
        function openReceiptModal(id, buyer, imgUrl, total, method) {
            document.getElementById('modal-receipt-order').textContent = 'Pesanan #EVX-' + id;
            document.getElementById('modal-receipt-info').textContent = buyer + ' • ' + total + ' (' + method + ')';
            document.getElementById('modal-receipt-img').src = imgUrl;
            document.getElementById('modal-download-link').href = imgUrl;
            document.getElementById('receipt-modal').classList.remove('hidden');
        }

        function closeReceiptModal() {
            document.getElementById('receipt-modal').classList.add('hidden');
        }
    </script>
</body>
</html>
