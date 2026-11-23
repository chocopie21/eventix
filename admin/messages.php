<?php
/**
 * EVENTIX - Admin Panel: Kotak Masuk Pesan & Kontak
 * Human-Crafted Editorial Minimalist
 */

require_once __DIR__ . '/../config/app.php';

// 1. Wajib role Admin
requireAdmin('login.php');

$filterStatus = trim($_GET['status'] ?? 'semua');
$searchQuery  = trim($_GET['q'] ?? '');

try {
    $whereClauses = [];
    $params = [];

    if ($filterStatus === 'belum_dibaca') {
        $whereClauses[] = "status = 'belum_dibaca'";
    } elseif ($filterStatus === 'dibaca') {
        $whereClauses[] = "status = 'dibaca'";
    }

    if (!empty($searchQuery)) {
        $whereClauses[] = "(nama LIKE :q OR email LIKE :q OR subjek LIKE :q OR pesan LIKE :q)";
        $params[':q'] = "%{$searchQuery}%";
    }

    $whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";

    $sql = "SELECT id_pesan, nama, email, subjek, pesan, status, created_at 
            FROM pesan_kontak 
            {$whereSql} 
            ORDER BY created_at DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total unread
    $totalUnread = (int)$pdo->query("SELECT COUNT(*) FROM pesan_kontak WHERE status = 'belum_dibaca'")->fetchColumn();
    $totalAll    = (int)$pdo->query("SELECT COUNT(*) FROM pesan_kontak")->fetchColumn();
    $totalRead   = (int)$pdo->query("SELECT COUNT(*) FROM pesan_kontak WHERE status = 'dibaca'")->fetchColumn();

} catch (PDOException $e) {
    error_log("[Admin Messages Error] " . $e->getMessage());
    $messages = [];
    $totalUnread = 0;
    $totalAll = 0;
    $totalRead = 0;
}

$flashSuccess = getFlash('success');
$flashError   = getFlash('error');
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Masuk & Kemitraan | EVENTIX Admin</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"SF Pro Display"', 'Plus Jakarta Sans', 'system-ui', 'sans-serif'],
                        heading: ['"SF Pro Display"', 'Plus Jakarta Sans', 'sans-serif'],
                        display: ['"SF Pro Display"', 'Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="h-full flex bg-slate-100 text-slate-900 antialiased font-sf">

    <!-- Admin Sidebar Component -->
    <?php include __DIR__ . '/../components/admin_sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- Top Navigation Header -->
        <header class="h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 sticky top-0 z-30 gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <!-- Hamburger Button for Mobile / Tablet -->
                <button type="button" 
                        id="admin-sidebar-open" 
                        class="lg:hidden p-2 -ml-1 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none shrink-0" 
                        aria-label="Buka Menu Admin">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <div class="min-w-0">
                    <h1 class="text-sm sm:text-base font-extrabold text-slate-900 tracking-tight truncate">Kotak Masuk Pesan & Kemitraan</h1>
                    <p class="text-[11px] sm:text-xs text-slate-500 truncate hidden sm:block">Tinjau pertanyaan penonton dan pengajuan kerja sama promotor konser</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold <?= $totalUnread > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-slate-100 text-slate-600' ?>">
                    <span class="w-2 h-2 rounded-full <?= $totalUnread > 0 ? 'bg-rose-500 animate-pulse' : 'bg-slate-400' ?>"></span>
                    <span><?= $totalUnread ?> Pesan Baru</span>
                </span>
            </div>
        </header>

        <!-- Body Content -->
        <main class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <!-- Flash Messages -->
            <?php if ($flashSuccess): ?>
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-2.5 text-emerald-800 text-xs shadow-2xs">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <?php if ($flashError): ?>
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-start gap-2.5 text-rose-800 text-xs shadow-2xs">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <!-- Filter & Search Controls -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 text-xs">
                    <a href="<?= url('admin/messages.php') ?>" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition-all <?= ($filterStatus === 'semua') ? 'bg-[#FF6082] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                        Semua (<?= $totalAll ?>)
                    </a>
                    <a href="<?= url('admin/messages.php?status=belum_dibaca') ?>" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition-all <?= ($filterStatus === 'belum_dibaca') ? 'bg-[#FF6082] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                        Belum Dibaca (<?= $totalUnread ?>)
                    </a>
                    <a href="<?= url('admin/messages.php?status=dibaca') ?>" 
                       class="px-3.5 py-2 rounded-xl font-semibold transition-all <?= ($filterStatus === 'dibaca') ? 'bg-[#FF6082] text-white shadow-xs' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                        Sudah Dibaca (<?= $totalRead ?>)
                    </a>
                </div>

                <!-- Search Input -->
                <form action="<?= url('admin/messages.php') ?>" method="GET" class="relative max-w-xs w-full">
                    <?php if ($filterStatus !== 'semua'): ?>
                        <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                    <input type="text" 
                           name="q" 
                           value="<?= htmlspecialchars($searchQuery, ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="Cari nama, email, subjek..."
                           class="w-full pl-9 pr-3.5 py-2 rounded-xl border border-slate-200 bg-white text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#FF6082] shadow-2xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </form>
            </div>

            <!-- Tabel Daftar Pesan -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Surat Masuk</h3>
                        <p class="text-xs text-slate-500">Total <?= count($messages) ?> pesan ditampilkan</p>
                    </div>
                </div>

                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold">
                                <th class="py-3.5 px-4 w-[16%]">Waktu Kirim</th>
                                <th class="py-3.5 px-4 w-[22%]">Pengirim</th>
                                <th class="py-3.5 px-4 w-[24%]">Subjek</th>
                                <th class="py-3.5 px-4 w-[18%]">Cuplikan Pesan</th>
                                <th class="py-3.5 px-4 w-[10%]">Status</th>
                                <th class="py-3.5 px-4 w-[10%] text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($messages)): ?>
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                        Tidak ada pesan masuk yang sesuai kriteria pencarian.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($messages as $m): 
                                    $idM = (int)$m['id_pesan'];
                                    $isUnread = ($m['status'] === 'belum_dibaca');
                                ?>
                                    <tr class="hover:bg-slate-50/70 transition-colors <?= $isUnread ? 'bg-amber-50/20 font-medium' : '' ?>">
                                        
                                        <!-- Waktu Kirim -->
                                        <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">
                                            <span class="block font-semibold text-slate-700"><?= date('d/m/Y', strtotime($m['created_at'])) ?></span>
                                            <span class="text-[11px] text-slate-400"><?= date('H:i', strtotime($m['created_at'])) ?> WIB</span>
                                        </td>

                                        <!-- Pengirim -->
                                        <td class="py-3.5 px-4">
                                            <div class="font-bold text-slate-900 line-clamp-1"><?= htmlspecialchars($m['nama'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <a href="mailto:<?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>?subject=Re:%20<?= urlencode($m['subjek']) ?>" 
                                               class="text-[11px] text-[#FF6082] hover:underline line-clamp-1">
                                                <?= htmlspecialchars($m['email'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </td>

                                        <!-- Subjek -->
                                        <td class="py-3.5 px-4">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200 mb-1">
                                                <?= htmlspecialchars($m['subjek'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </td>

                                        <!-- Cuplikan & Tombol Baca -->
                                        <td class="py-3.5 px-4 text-slate-600">
                                            <p class="line-clamp-2 text-xs leading-relaxed"><?= htmlspecialchars($m['pesan'], ENT_QUOTES, 'UTF-8') ?></p>
                                            <button type="button" 
                                                    onclick="openMessageModal(<?= htmlspecialchars(json_encode($m), ENT_QUOTES, 'UTF-8') ?>)"
                                                    class="text-[11px] font-semibold text-slate-800 hover:text-[#FF6082] mt-1 inline-flex items-center gap-1 cursor-pointer">
                                                <span>Baca Lengkap</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            </button>
                                        </td>

                                        <!-- Status -->
                                        <td class="py-3.5 px-4 whitespace-nowrap">
                                            <?php if ($isUnread): ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                                    <span>Baru</span>
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                    <span>Dibaca</span>
                                                </span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Aksi -->
                                        <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                            <div class="inline-flex items-center justify-end gap-1.5">
                                                
                                                <!-- Toggle Baca / Belum Dibaca -->
                                                <form action="<?= url('actions/admin_message_status.php') ?>" method="POST" class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_pesan" value="<?= $idM ?>">
                                                    <input type="hidden" name="status" value="<?= $isUnread ? 'dibaca' : 'belum_dibaca' ?>">
                                                    <button type="submit" 
                                                            class="p-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-600 transition-colors" 
                                                            title="<?= $isUnread ? 'Tandai sudah dibaca' : 'Tandai belum dibaca' ?>">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                <!-- Hapus Pesan -->
                                                <form action="<?= url('actions/admin_message_delete.php') ?>" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan dari <?= htmlspecialchars($m['nama'], ENT_QUOTES, 'UTF-8') ?>?');"
                                                      class="inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="id_pesan" value="<?= $idM ?>">
                                                    <button type="submit" 
                                                            class="p-1.5 rounded-lg border border-slate-200 text-rose-600 hover:bg-rose-50 hover:border-rose-300 transition-colors" 
                                                            title="Hapus Pesan">
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

    <!-- MODAL BACA LENGKAP PESAN -->
    <div id="message-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs hidden transition-opacity">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-xl relative border border-slate-200">
            <button type="button" onclick="closeMessageModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div>
                <span id="modal-msg-time" class="text-[11px] text-slate-400 block mb-1">Tanggal & Waktu</span>
                <h3 id="modal-msg-subject" class="text-base font-bold text-slate-900">Subjek Pesan</h3>
                <div class="mt-1 flex items-center gap-2 text-xs text-slate-600">
                    <span id="modal-msg-sender" class="font-semibold text-slate-900">Nama Pengirim</span>
                    <span>•</span>
                    <span id="modal-msg-email" class="font-mono text-slate-500">email@example.com</span>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-800 leading-relaxed max-h-60 overflow-y-auto whitespace-pre-wrap" id="modal-msg-body">
                Isi pesan...
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeMessageModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Tutup
                </button>
                <a id="modal-msg-reply" href="#" class="px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#FF6082] hover:bg-[#e54b6d] transition-colors shadow-xs flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>Balas via Email</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Script Modal -->
    <script>
        function openMessageModal(data) {
            document.getElementById('modal-msg-time').textContent = data.created_at;
            document.getElementById('modal-msg-subject').textContent = data.subjek;
            document.getElementById('modal-msg-sender').textContent = data.nama;
            document.getElementById('modal-msg-email').textContent = data.email;
            document.getElementById('modal-msg-body').textContent = data.pesan;
            document.getElementById('modal-msg-reply').href = 'mailto:' + encodeURIComponent(data.email) + '?subject=Re: ' + encodeURIComponent(data.subjek);
            document.getElementById('message-modal').classList.remove('hidden');
        }

        function closeMessageModal() {
            document.getElementById('message-modal').classList.add('hidden');
        }

        document.getElementById('message-modal')?.addEventListener('click', function(e) {
            if (e.target === this) closeMessageModal();
        });
    </script>

</body>
</html>
