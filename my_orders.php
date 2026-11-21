<?php
/**
 * EVENTIX - Halaman Riwayat & Status Pesanan User
 * Human-Crafted Editorial Minimalist
 */

require_once __DIR__ . '/config/app.php';

// 1. Wajib Login
requireLogin('login.php');

$user = currentUser();
$idPengguna = (int)$user['id_pengguna'];

// Filter status dari query param (?status=...)
$filterStatus = trim($_GET['status'] ?? 'semua');

// 2. Ambil data seluruh pesanan milik pengguna
try {
    $sql = "SELECT 
                p.*,
                pay.metode AS metode_bayar,
                pay.bukti_bayar,
                pay.tanggal AS tanggal_bayar,
                pay.status AS status_pembayaran,
                e.id_event,
                e.nama_event,
                e.tanggal_event,
                e.jam_event,
                e.venue,
                e.poster,
                e.pengisi_acara,
                k.genre_musik
            FROM pesanan p
            LEFT JOIN pembayaran pay ON p.id_pesanan = pay.id_pesanan
            LEFT JOIN (
                SELECT dp.id_pesanan, MIN(t.id_event) AS id_event
                FROM detail_pesanan dp
                JOIN tiket t ON dp.id_tiket = t.id_tiket
                GROUP BY dp.id_pesanan
            ) o_ev ON p.id_pesanan = o_ev.id_pesanan
            LEFT JOIN event e ON o_ev.id_event = e.id_event
            LEFT JOIN konser k ON e.id_event = k.id_event
            WHERE p.id_pengguna = :id_pengguna";

    if ($filterStatus !== 'semua' && in_array($filterStatus, ['menunggu', 'menunggu_verifikasi', 'lunas', 'ditolak'])) {
        $sql .= " AND p.status = :status";
    }

    $sql .= " ORDER BY p.tanggal DESC";

    $stmtOrders = $pdo->prepare($sql);
    $params = [':id_pengguna' => $idPengguna];
    if ($filterStatus !== 'semua' && in_array($filterStatus, ['menunggu', 'menunggu_verifikasi', 'lunas', 'ditolak'])) {
        $params[':status'] = $filterStatus;
    }
    $stmtOrders->execute($params);
    $orders = $stmtOrders->fetchAll(PDO::FETCH_ASSOC);

    // Ambil semua detail tiket untuk pesanan pengguna ini
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
    error_log("[My Orders Error] " . $e->getMessage());
    $orders = [];
    $orderDetailsMap = [];
}

$flashSuccess = getFlash('success');
$flashError = getFlash('error');
$flashInfo = getFlash('info');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan Tiket | EVENTIX</title>

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
<body class="bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">

    <!-- Navbar Component -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Main Container -->
    <main class="flex-grow max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumb & Header Title -->
        <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                    <a href="<?= url('index.php') ?>" class="hover:text-slate-900 transition-colors">Beranda</a>
                    <span>/</span>
                    <span class="text-slate-900 font-semibold">Riwayat Pesanan</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-bold font-heading text-slate-900">
                    Tiket & Riwayat Pesanan
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Daftar pesanan tiket dan akses E-Ticket konser resmi Anda.
                </p>
            </div>

            <a href="<?= url('index.php#katalog') ?>" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-white border border-slate-300 hover:border-slate-400 text-xs font-semibold text-slate-800 transition-colors self-start sm:self-auto">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Beli Tiket Lain</span>
            </a>
        </div>

        <!-- Flash Messages -->
        <?php if ($flashSuccess): ?>
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3 text-emerald-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($flashError): ?>
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-start gap-3 text-rose-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($flashInfo): ?>
            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3 text-blue-800 text-xs sm:text-sm">
                <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span><?= htmlspecialchars($flashInfo, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <!-- Filter Tabs: Semua / Menunggu / Menunggu Verifikasi / Lunas / Ditolak -->
        <div class="flex items-center gap-2 overflow-x-auto pb-3 mb-6 no-scrollbar">
            <?php
            $tabs = [
                'semua'                => 'Semua Pesanan',
                'menunggu'             => 'Menunggu Pembayaran',
                'menunggu_verifikasi'  => 'Sedang Diverifikasi',
                'lunas'                => 'Lunas (Siap Masuk)',
                'ditolak'              => 'Ditolak'
            ];
            foreach ($tabs as $key => $label):
                $isActive = ($filterStatus === $key);
            ?>
                <a href="<?= url('my_orders.php' . ($key !== 'semua' ? '?status=' . $key : '')) ?>" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors border <?= $isActive ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-300' ?>">
                    <?= $label ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Orders List -->
        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-xl p-12 border border-slate-200 text-center space-y-3 my-4">
                <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 font-heading">Belum Ada Pesanan Tiket</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">
                    <?php if ($filterStatus !== 'semua'): ?>
                        Tidak ada transaksi dengan status "<?= htmlspecialchars($tabs[$filterStatus] ?? $filterStatus, ENT_QUOTES, 'UTF-8') ?>".
                    <?php else: ?>
                        Anda belum pernah memesan tiket konser di EVENTIX. Jelajahi katalog konser dan pesan tiket artis favorit Anda.
                    <?php endif; ?>
                </p>
                <div class="pt-2">
                    <a href="<?= url('index.php#katalog') ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold text-xs hover:bg-slate-800 transition-colors">
                        <span>Lihat Jadwal Konser</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($orders as $order): 
                    $idPesanan = (int)$order['id_pesanan'];
                    $items = $orderDetailsMap[$idPesanan] ?? [];
                    $status = $order['status'];
                ?>
                    <div class="ticket-stub overflow-hidden">
                        
                        <!-- Order Top Bar -->
                        <div class="bg-slate-50 px-5 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                                    #EVX-<?= $idPesanan ?>
                                </span>
                                <span class="text-slate-500 text-[11px]">
                                    <?= formatTanggalIndo(date('Y-m-d', strtotime($order['tanggal']))) ?> • <?= date('H:i', strtotime($order['tanggal'])) ?> WIB
                                </span>
                            </div>

                            <!-- Status Badge -->
                            <div>
                                <?php if ($status === 'menunggu'): ?>
                                    <span class="badge-clean bg-amber-50 text-amber-800 border-amber-300 text-[11px]">
                                        Menunggu Pembayaran
                                    </span>
                                <?php elseif ($status === 'menunggu_verifikasi'): ?>
                                    <span class="badge-clean bg-blue-50 text-blue-800 border-blue-300 text-[11px]">
                                        Sedang Diverifikasi Admin
                                    </span>
                                <?php elseif ($status === 'lunas'): ?>
                                    <span class="badge-clean bg-emerald-50 text-emerald-800 border-emerald-300 text-[11px]">
                                        <svg class="w-3 h-3 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Lunas (E-Ticket Aktif)
                                    </span>
                                <?php else: ?>
                                    <span class="badge-clean bg-rose-50 text-rose-800 border-rose-300 text-[11px]">
                                        Ditolak
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Order Card Body -->
                        <div class="p-5 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                            
                            <!-- Left: Event Poster & Details -->
                            <div class="flex items-start gap-4 flex-1 min-w-0">
                                <img src="<?= asset('uploads/posters/' . ($order['poster'] ?? 'default.jpg')) ?>" 
                                     alt="<?= htmlspecialchars($order['nama_event'] ?? 'Konser', ENT_QUOTES, 'UTF-8') ?>"
                                     class="w-16 h-22 object-cover rounded-lg border border-slate-200 shadow-2xs shrink-0"
                                     onerror="this.src='<?= asset('uploads/posters/default.jpg') ?>'">

                                <div class="space-y-1 min-w-0 flex-1">
                                    <span class="badge-clean bg-slate-100 text-slate-800 border-slate-200 text-[10px]">
                                        <?= htmlspecialchars($order['genre_musik'] ?? 'Konser', ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                    <h4 class="text-base font-bold text-slate-900 font-heading truncate">
                                        <?= htmlspecialchars($order['nama_event'] ?? 'Konser Musik', ENT_QUOTES, 'UTF-8') ?>
                                    </h4>
                                    <p class="text-xs text-slate-600 truncate">
                                        <?= htmlspecialchars($order['venue'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Jadwal: <?= !empty($order['tanggal_event']) ? formatTanggalIndo($order['tanggal_event']) : '-' ?> • <?= !empty($order['jam_event']) ? formatJam($order['jam_event']) : '' ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Center: Ticket List Summary -->
                            <div class="w-full md:w-56 text-xs border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-5 space-y-1.5">
                                <span class="text-xs font-semibold text-slate-700 block">Kategori Tiket:</span>
                                <div class="space-y-1">
                                    <?php foreach ($items as $it): ?>
                                        <div class="flex items-center justify-between text-slate-700">
                                            <span class="truncate"><?= htmlspecialchars($it['kategori'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <span class="text-slate-500 font-mono text-xs"><?= $it['jumlah'] ?>x</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Right: Total Price & Actions -->
                            <div class="w-full md:w-48 flex md:flex-col items-center md:items-end justify-between border-t md:border-t-0 md:border-l border-slate-200 pt-3 md:pt-0 md:pl-5 gap-2.5">
                                <div class="text-left md:text-right">
                                    <span class="text-[10px] text-slate-400 block font-medium">Total Biaya</span>
                                    <span class="text-base font-bold text-slate-900">
                                        <?= formatRupiah($order['harga']) ?>
                                    </span>
                                </div>

                                <div>
                                    <?php if ($status === 'menunggu'): ?>
                                        <a href="<?= url('checkout.php?id_pesanan=' . $idPesanan) ?>" 
                                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] transition-colors shadow-xs">
                                            <span>Bayar Sekarang</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    <?php elseif ($status === 'menunggu_verifikasi'): ?>
                                        <a href="<?= url('checkout.php?id_pesanan=' . $idPesanan) ?>" 
                                           class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 transition-colors">
                                            <span>Lihat Bukti Bayar</span>
                                        </a>
                                    <?php elseif ($status === 'lunas'): ?>
                                        <button type="button" 
                                                onclick="openTicketModal('<?= $idPesanan ?>', '<?= htmlspecialchars($order['nama_event'] ?? '', ENT_QUOTES, 'UTF-8') ?>', '<?= htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8') ?>', '<?= !empty($order['tanggal_event']) ? formatTanggalIndo($order['tanggal_event']) : '-' ?>', '<?= htmlspecialchars($order['venue'] ?? '', ENT_QUOTES, 'UTF-8') ?>')"
                                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                            </svg>
                                            <span>Buka E-Ticket</span>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-xs text-rose-600 font-medium">Batal</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </main>

    <!-- Modal E-Ticket QR Code (Crisp Monochrome Ticket Design) -->
    <div id="ticket-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/70 backdrop-blur-xs hidden transition-opacity">
        <div class="bg-white rounded-xl max-w-sm w-full p-6 text-center space-y-4 shadow-xl relative border border-slate-200">
            <button type="button" onclick="closeTicketModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 focus:outline-none p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div>
                <span class="badge-clean bg-emerald-50 text-emerald-800 border-emerald-300 text-[10px] mb-2">
                    Verified E-Ticket
                </span>
                <h3 id="modal-event-title" class="text-base font-bold text-slate-900 font-heading">Konser Musik</h3>
                <p id="modal-order-id" class="text-xs font-mono font-bold text-slate-600">#EVX-000</p>
            </div>

            <!-- Crisp Clean QR Code -->
            <div class="p-4 bg-slate-50 rounded-lg inline-block mx-auto border border-slate-200">
                <svg class="w-36 h-36 mx-auto text-slate-900" viewBox="0 0 100 100" fill="currentColor">
                    <rect x="0" y="0" width="30" height="30" rx="2" fill="#0f172a" />
                    <rect x="5" y="5" width="20" height="20" rx="1" fill="white" />
                    <rect x="10" y="10" width="10" height="10" fill="#0f172a" />

                    <rect x="70" y="0" width="30" height="30" rx="2" fill="#0f172a" />
                    <rect x="75" y="5" width="20" height="20" rx="1" fill="white" />
                    <rect x="80" y="10" width="10" height="10" fill="#0f172a" />

                    <rect x="0" y="70" width="30" height="30" rx="2" fill="#0f172a" />
                    <rect x="5" y="75" width="20" height="20" rx="1" fill="white" />
                    <rect x="10" y="80" width="10" height="10" fill="#0f172a" />

                    <rect x="35" y="10" width="8" height="8" fill="#0f172a" />
                    <rect x="50" y="15" width="6" height="12" fill="#0f172a" />
                    <rect x="40" y="35" width="20" height="20" fill="#0f172a" />
                    <rect x="45" y="40" width="10" height="10" fill="white" />
                    <rect x="15" y="45" width="12" height="6" fill="#0f172a" />
                    <rect x="75" y="45" width="10" height="8" fill="#0f172a" />
                    <rect x="35" y="75" width="14" height="14" fill="#0f172a" />
                    <rect x="65" y="75" width="8" height="12" fill="#0f172a" />
                    <rect x="80" y="70" width="12" height="18" fill="#0f172a" />
                </svg>
                <span class="block text-[10px] text-slate-500 mt-2 font-mono">Tunjukkan di pintu venue</span>
            </div>

            <div class="text-xs text-slate-600 space-y-1 text-left bg-slate-50 p-3.5 rounded-lg border border-slate-200">
                <p><span class="text-slate-400">Pemegang Tiket:</span> <strong id="modal-user-name" class="text-slate-900">Nama</strong></p>
                <p><span class="text-slate-400">Jadwal:</span> <span id="modal-date" class="text-slate-900">-</span></p>
                <p><span class="text-slate-400">Lokasi:</span> <span id="modal-venue" class="truncate block text-slate-900">-</span></p>
            </div>

            <button type="button" onclick="closeTicketModal()" class="w-full py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-colors">
                Tutup E-Ticket
            </button>
        </div>
    </div>

    <!-- Footer Component -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
        function openTicketModal(id, title, user, date, venue) {
            document.getElementById('modal-order-id').textContent = '#EVX-' + id;
            document.getElementById('modal-event-title').textContent = title;
            document.getElementById('modal-user-name').textContent = user;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-venue').textContent = venue;
            document.getElementById('ticket-modal').classList.remove('hidden');
        }

        function closeTicketModal() {
            document.getElementById('ticket-modal').classList.add('hidden');
        }
    </script>
</body>
</html>
