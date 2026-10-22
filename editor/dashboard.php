<?php
require_once __DIR__ . '/auth/auth_check.php';
require_once __DIR__ . '/../database/koneksi_psql.php';

$editor_id = $_SESSION['editor_id'] ?? 1;
$editor_wa = $_SESSION['editor_wa'] ?? '6281234567890';

// Ambil statistik real-time
$countJasa = (int)$pdo->query("SELECT COUNT(*) FROM detail_jasa")->fetchColumn();
$countKategori = (int)$pdo->query("SELECT COUNT(*) FROM kategori_jasa")->fetchColumn();
$countPortofolio = (int)$pdo->query("SELECT COUNT(*) FROM portofolio")->fetchColumn();

// Ambil daftar jasa
$stmtJasa = $pdo->prepare("
    SELECT dj.*, kj.nama_kategori, p.gambar
    FROM detail_jasa dj
    JOIN kategori_jasa kj ON dj.id_kategori_jasa = kj.id_kategori_jasa
    LEFT JOIN (
        SELECT DISTINCT ON (id_detail_jasa) id_detail_jasa, gambar 
        FROM portofolio 
        ORDER BY id_detail_jasa, id_portofolio ASC
    ) p ON dj.id_detail_jasa = p.id_detail_jasa
    ORDER BY dj.id_detail_jasa DESC
");
$stmtJasa->execute();
$jasa_list = $stmtJasa->fetchAll();

// Ambil daftar kategori beserta jumlah jasa
$stmtKat = $pdo->query("
    SELECT kj.*, COUNT(dj.id_detail_jasa) as total_jasa
    FROM kategori_jasa kj
    LEFT JOIN detail_jasa dj ON kj.id_kategori_jasa = dj.id_kategori_jasa
    GROUP BY kj.id_kategori_jasa, kj.nama_kategori
    ORDER BY kj.nama_kategori ASC
");
$kategori_list = $stmtKat->fetchAll();

// View Navigasi Aktif (overview | jasa | kategori)
$active_view = $_GET['view'] ?? 'overview';
if (!in_array($active_view, ['overview', 'jasa', 'kategori'])) {
    $active_view = 'overview';
}

// Notifikasi pesan
$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donezo Dashboard — sekilasrv</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="tampilan/dashboard.css">
</head>

<body>
    <div class="app-layout">
        <!-- Left Sidebar (Donezo Style) -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-symbol">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="#0B3528" stroke-width="2.5"/>
                        <path d="M12 7V17M7 12H17" stroke="#D2E823" stroke-width="2.5" stroke-linecap="round"/>
                    </svg>
                </div>
                <span class="brand-text">sekilasrv</span>
            </div>

            <div class="sidebar-section">
                <span class="sidebar-group-label">MENU</span>
                <nav class="sidebar-nav">
                    <a href="?view=overview" class="nav-btn <?= $active_view === 'overview' ? 'active' : '' ?>" id="navOverview" onclick="switchView('overview', event)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="3" width="7" height="7" rx="1.5"></rect>
                            <rect x="14" y="14" width="7" height="7" rx="1.5"></rect>
                            <rect x="3" y="14" width="7" height="7" rx="1.5"></rect>
                        </svg>
                        <span>Dashboard</span>
                    </a>
                    <a href="?view=jasa" class="nav-btn <?= $active_view === 'jasa' ? 'active' : '' ?>" id="navJasa" onclick="switchView('jasa', event)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="23 7 16 12 23 17 23 7"></polygon>
                            <rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect>
                        </svg>
                        <span>Kelola Jasa</span>
                        <span class="nav-badge"><?= $countJasa ?></span>
                    </a>
                    <a href="?view=kategori" class="nav-btn <?= $active_view === 'kategori' ? 'active' : '' ?>" id="navKategori" onclick="switchView('kategori', event)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                        </svg>
                        <span>Kategori</span>
                        <span class="nav-badge"><?= $countKategori ?></span>
                    </a>
                    <a href="../customer/informasi/kategori_jasa.php" target="_blank" class="nav-btn">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <span>Lihat Web Live</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-section">
                <span class="sidebar-group-label">GENERAL</span>
                <nav class="sidebar-nav">
                    <a href="#" class="nav-btn" onclick="openModal('modalTambahKategori'); return false;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Kategori Baru</span>
                    </a>
                    <a href="auth/logout.php" class="nav-btn text-danger">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        <span>Keluar / Logout</span>
                    </a>
                </nav>
            </div>

            <!-- Bottom Mini Card (Donezo Download App Card style) -->
            <div class="sidebar-bottom-widget">
                <div class="widget-icon">⚡</div>
                <h4>Katalog Publik Aktif</h4>
                <p>Jasa Anda tampil langsung untuk calon klien di website.</p>
                <a href="../customer/informasi/kategori_jasa.php" target="_blank" class="btn-widget">Buka Katalog</a>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <div class="main-content">
            <!-- Top Navbar (Donezo Search & Profile Bar) -->
            <header class="top-header">
                <div class="search-box">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2.2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" id="quickSearchInput" placeholder="Cari jasa atau kategori..." onkeyup="filterTable()">
                    <span class="search-key">⌘F</span>
                </div>

                <div class="header-user-actions">
                    <button class="icon-circle-btn" title="Pesan WhatsApp">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </button>
                    <button class="icon-circle-btn" title="Notifikasi">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="notif-dot"></span>
                    </button>

                    <!-- User Profile Dropdown / Pill -->
                    <div class="user-profile-pill">
                        <div class="profile-avatar">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=80&q=80" alt="Editor">
                        </div>
                        <div class="profile-info">
                            <strong>Editor #<?= htmlspecialchars($editor_id) ?></strong>
                            <span><?= htmlspecialchars($editor_wa) ?></span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content Container -->
            <div class="dashboard-scrollable">
                <!-- Notifications Toast -->
                <?php if (!empty($msg)): ?>
                    <div class="alert-toast success">
                        <span>✅ <?= htmlspecialchars($msg) ?></span>
                        <button onclick="this.parentElement.remove()">✕</button>
                    </div>
                <?php endif; ?>
                <?php if (!empty($err)): ?>
                    <div class="alert-toast danger">
                        <span>⚠️ <?= htmlspecialchars($err) ?></span>
                        <button onclick="this.parentElement.remove()">✕</button>
                    </div>
                <?php endif; ?>

                <!-- ==============================================================
                     VIEW 1: OVERVIEW (RINGKASAN DASHBOARD)
                     ============================================================== -->
                <div id="viewOverview" class="dashboard-view <?= $active_view === 'overview' ? 'active' : '' ?>">
                    <!-- Page Title & Top CTA Buttons (Donezo Style) -->
                    <div class="page-title-row">
                        <div>
                            <h1 class="page-title">Ringkasan Dashboard</h1>
                            <p class="page-sub">Pantau performa layanan, aktivitas kategori, dan statistik akun editor Anda.</p>
                        </div>
                        <div class="page-actions">
                            <button class="btn btn-primary" onclick="openModal('modalTambahJasa')">
                                + Tambah Jasa
                            </button>
                            <button class="btn btn-white" onclick="openModal('modalTambahKategori')">
                                + Tambah Kategori
                            </button>
                        </div>
                    </div>

                    <!-- 4 Top Stat Cards (Donezo 4-Card Row Style) -->
                    <div class="stat-cards-row">
                        <!-- Card 1: Dark Forest Green Card -->
                        <div class="stat-card card-dark-green" onclick="switchView('jasa')" style="cursor: pointer;" title="Klik untuk kelola jasa">
                            <div class="stat-card-top">
                                <span class="stat-title">Total Layanan Jasa</span>
                                <div class="stat-arrow-btn">↗</div>
                            </div>
                            <div class="stat-big-number"><?= $countJasa ?></div>
                            <div class="stat-growth-tag">
                                <span>⚡</span> Tampil Aktif di Katalog
                            </div>
                        </div>

                        <!-- Card 2: White Card -->
                        <div class="stat-card card-white" onclick="switchView('kategori')" style="cursor: pointer;" title="Klik untuk kelola kategori">
                            <div class="stat-card-top">
                                <span class="stat-title">Kategori Jasa</span>
                                <div class="stat-arrow-btn">↗</div>
                            </div>
                            <div class="stat-big-number"><?= $countKategori ?></div>
                            <div class="stat-growth-tag tag-gray">
                                <span>📁</span> <?= $countKategori ?> Klasifikasi Bidang
                            </div>
                        </div>

                        <!-- Card 3: White Card -->
                        <div class="stat-card card-white" onclick="switchView('jasa')" style="cursor: pointer;" title="Klik untuk lihat portofolio">
                            <div class="stat-card-top">
                                <span class="stat-title">Portofolio Karya</span>
                                <div class="stat-arrow-btn">↗</div>
                            </div>
                            <div class="stat-big-number"><?= $countPortofolio ?></div>
                            <div class="stat-growth-tag tag-gray">
                                <span>🖼️</span> Karya Terverifikasi
                            </div>
                        </div>

                        <!-- Card 4: White Card -->
                        <div class="stat-card card-white">
                            <div class="stat-card-top">
                                <span class="stat-title">Status WhatsApp</span>
                                <div class="stat-arrow-btn">↗</div>
                            </div>
                            <div class="stat-big-number">100%</div>
                            <div class="stat-growth-tag tag-green">
                                <span>●</span> Siap Terima Order
                            </div>
                        </div>
                    </div>

                    <!-- Middle Widgets Row (Donezo Analytics + Reminder + Tasks) -->
                    <div class="widgets-grid-row">
                        <!-- Widget 1: Project Analytics Chart (Rock-Solid Donezo Bar Chart) -->
                        <div class="widget-box widget-chart">
                            <div class="widget-header">
                                <div>
                                    <h3>Aktivitas Layanan & Trafik</h3>
                                    <p class="widget-desc-sub">Statistik interaksi klien 7 hari terakhir</p>
                                </div>
                                <span class="widget-sub-badge">Mingguan</span>
                            </div>

                            <!-- Bulletproof Donezo Chart with Dedicated Track Containers -->
                            <div class="analytics-chart-container">
                                <div class="chart-y-axis">
                                    <span>100%</span>
                                    <span>50%</span>
                                    <span>0%</span>
                                </div>
                                <div class="chart-main-area">
                                    <div class="chart-grid-lines">
                                        <div class="grid-line"></div>
                                        <div class="grid-line"></div>
                                        <div class="grid-line"></div>
                                    </div>
                                    <div class="chart-bars-track-wrap">
                                        <div class="chart-col">
                                            <div class="bar-track">
                                                <div class="bar-fill stripe" style="height: 42%;" title="Senin: 42%"></div>
                                            </div>
                                            <span class="col-day">Sen</span>
                                        </div>
                                        <div class="chart-col">
                                            <div class="bar-track">
                                                <div class="bar-fill green" style="height: 72%;" title="Selasa: 72%"></div>
                                            </div>
                                            <span class="col-day">Sel</span>
                                        </div>
                                        <div class="chart-col highlight-col">
                                            <div class="bar-track">
                                                <div class="bar-fill mint" style="height: 85%;" title="Rabu (Puncak): 85%">
                                                    <span class="chart-tooltip">85% Puncak</span>
                                                </div>
                                            </div>
                                            <span class="col-day">Rab</span>
                                        </div>
                                        <div class="chart-col">
                                            <div class="bar-track">
                                                <div class="bar-fill dark" style="height: 94%;" title="Kamis: 94%"></div>
                                            </div>
                                            <span class="col-day">Kam</span>
                                        </div>
                                        <div class="chart-col">
                                            <div class="bar-track">
                                                <div class="bar-fill stripe" style="height: 58%;" title="Jumat: 58%"></div>
                                            </div>
                                            <span class="col-day">Jum</span>
                                        </div>
                                        <div class="chart-col">
                                            <div class="bar-track">
                                                <div class="bar-fill green" style="height: 68%;" title="Sabtu: 68%"></div>
                                            </div>
                                            <span class="col-day">Sab</span>
                                        </div>
                                        <div class="chart-col">
                                            <div class="bar-track">
                                                <div class="bar-fill stripe" style="height: 48%;" title="Minggu: 48%"></div>
                                            </div>
                                            <span class="col-day">Min</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Widget 2: Order Notification / Reminders -->
                        <div class="widget-box widget-reminders">
                            <div class="widget-header">
                                <h3>Reminder Klien</h3>
                                <span class="widget-sub-badge">Live</span>
                            </div>
                            <div class="reminder-body">
                                <h4>Diskusi Konsep Video TikTok</h4>
                                <p class="reminder-time">WhatsApp • 14.00 - 16.00 WIB</p>
                                <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $editor_wa) ?>" target="_blank" class="btn-meeting">
                                    <span class="meet-icon">💬</span>
                                    <span>Buka Chat WhatsApp</span>
                                </a>
                            </div>
                        </div>

                        <!-- Widget 3: Quick Projects Mini List -->
                        <div class="widget-box widget-projects-list">
                            <div class="widget-header">
                                <h3>Layanan Teratas</h3>
                                <button class="btn-mini-add" onclick="switchView('jasa')">Lihat Semua →</button>
                            </div>
                            <div class="mini-task-list">
                                <?php foreach (array_slice($jasa_list, 0, 3) as $topJasa): ?>
                                    <div class="mini-task-item">
                                        <div class="task-icon-dot">🎬</div>
                                        <div class="task-info">
                                            <strong><?= htmlspecialchars(mb_strimwidth($topJasa['nama_jasa'], 0, 24, '...')) ?></strong>
                                            <span>Rp <?= number_format($topJasa['harga'], 0, ',', '.') ?></span>
                                        </div>
                                        <span class="task-status-pill"><?= htmlspecialchars($topJasa['nama_kategori']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Split Row: Progress Gauge + Shortcut Box -->
                    <div class="bottom-split-row">
                        <!-- Gauge Progress Card (Donezo 85% style) -->
                        <div class="widget-box widget-gauge">
                            <div class="widget-header">
                                <h3>Kelengkapan Profil Jasa</h3>
                            </div>
                            <div class="gauge-center">
                                <div class="gauge-circle">
                                    <svg width="140" height="90" viewBox="0 0 140 90">
                                        <path d="M 15 80 A 55 55 0 0 1 125 80" fill="none" stroke="#E5EAE2" stroke-width="14" stroke-linecap="round"/>
                                        <path d="M 15 80 A 55 55 0 0 1 105 35" fill="none" stroke="#0B3528" stroke-width="14" stroke-linecap="round"/>
                                    </svg>
                                    <div class="gauge-number-wrap">
                                        <span class="gauge-num">85%</span>
                                        <span class="gauge-label">Portofolio Siap</span>
                                    </div>
                                </div>
                            </div>
                            <div class="gauge-legend">
                                <span><strong class="dot green"></strong> Aktif</span>
                                <span><strong class="dot mint"></strong> Draft</span>
                                <span><strong class="dot gray"></strong> Portofolio</span>
                            </div>
                        </div>

                        <!-- Shortcut Quick Actions Box -->
                        <div class="widget-box widget-shortcuts">
                            <div class="widget-header">
                                <h3>Pintasan & Manajemen Cepat</h3>
                            </div>
                            <div class="shortcuts-grid">
                                <div class="shortcut-card" onclick="openModal('modalTambahJasa')">
                                    <div class="sc-icon">🎬</div>
                                    <div class="sc-content">
                                        <strong>Tambah Jasa Baru</strong>
                                        <span>Terbitkan paket editing baru ke katalog customer</span>
                                    </div>
                                    <span class="sc-arrow">+</span>
                                </div>
                                <div class="shortcut-card" onclick="openModal('modalTambahKategori')">
                                    <div class="sc-icon">📁</div>
                                    <div class="sc-content">
                                        <strong>Tambah Kategori</strong>
                                        <span>Buat klasifikasi bidang baru</span>
                                    </div>
                                    <span class="sc-arrow">+</span>
                                </div>
                                <a href="../customer/informasi/kategori_jasa.php" target="_blank" class="shortcut-card sc-live">
                                    <div class="sc-icon">🌐</div>
                                    <div class="sc-content">
                                        <strong>Lihat Tampilan Katalog</strong>
                                        <span>Buka katalog publik yang dilihat calon klien</span>
                                    </div>
                                    <span class="sc-arrow">↗</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==============================================================
                     VIEW 2: KELOLA JASA EDITING
                     ============================================================== -->
                <div id="viewJasa" class="dashboard-view <?= $active_view === 'jasa' ? 'active' : '' ?>">
                    <div class="page-title-row">
                        <div>
                            <h1 class="page-title">Kelola Jasa Editing</h1>
                            <p class="page-sub">Kelola daftar layanan jasa, ubah tarif, upload portofolio, dan perbarui deskripsi.</p>
                        </div>
                        <div class="page-actions">
                            <div class="table-search-box">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="jasaSearchInput" placeholder="Cari nama jasa / kategori..." onkeyup="filterJasaTable()">
                            </div>
                            <button class="btn btn-primary" onclick="openModal('modalTambahJasa')">
                                + Tambah Jasa Baru
                            </button>
                        </div>
                    </div>

                    <!-- Full-Width Donezo Data Table for Services -->
                    <div class="table-card-full">
                        <div class="table-responsive">
                            <table class="data-table" id="tableJasaList">
                                <thead>
                                    <tr>
                                        <th style="width: 70px;">Media</th>
                                        <th>Nama Layanan Jasa</th>
                                        <th>Kategori</th>
                                        <th>Tarif Tertera</th>
                                        <th>Tanggal Upload</th>
                                        <th style="text-align: right; width: 170px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($jasa_list)): ?>
                                        <tr>
                                            <td colspan="6" class="td-empty">Belum ada layanan jasa. Klik tombol <strong>+ Tambah Jasa Baru</strong> di atas.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($jasa_list as $row): ?>
                                            <tr>
                                                <td class="td-thumb">
                                                    <?php 
                                                    $tpath = !empty($row['gambar']) ? '../uploads/' . $row['gambar'] : '';
                                                    if (!empty($row['gambar']) && file_exists(__DIR__ . '/' . $tpath)): 
                                                    ?>
                                                        <img src="<?= htmlspecialchars($tpath) ?>" alt="thumb">
                                                    <?php else: ?>
                                                        <div class="mini-thumb">🎬</div>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong class="row-title"><?= htmlspecialchars($row['nama_jasa']) ?></strong>
                                                    <p class="row-snippet"><?= htmlspecialchars(mb_strimwidth($row['deskripsi'], 0, 70, '...')) ?></p>
                                                </td>
                                                <td>
                                                    <span class="pill-kategori"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                                                </td>
                                                <td>
                                                    <span class="price-text">Rp <?= number_format($row['harga'], 0, ',', '.') ?></span>
                                                </td>
                                                <td class="td-date"><?= date('d M Y', strtotime($row['tanggal_upload'])) ?></td>
                                                <td class="td-actions">
                                                    <button 
                                                        class="btn-edit"
                                                        data-id="<?= $row['id_detail_jasa'] ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_jasa'], ENT_QUOTES) ?>"
                                                        data-kategori="<?= $row['id_kategori_jasa'] ?>"
                                                        data-harga="<?= $row['harga'] ?>"
                                                        data-deskripsi="<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES) ?>"
                                                        data-gambar="<?= htmlspecialchars($row['gambar'] ?? '', ENT_QUOTES) ?>"
                                                        onclick="editJasaModal(this)">
                                                        ✏️ Edit
                                                    </button>
                                                    <button 
                                                        class="btn-delete"
                                                        data-id="<?= $row['id_detail_jasa'] ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama_jasa'], ENT_QUOTES) ?>"
                                                        onclick="confirmDeleteJasa(this)">
                                                        🗑️ Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ==============================================================
                     VIEW 3: KELOLA KATEGORI JASA
                     ============================================================== -->
                <div id="viewKategori" class="dashboard-view <?= $active_view === 'kategori' ? 'active' : '' ?>">
                    <div class="page-title-row">
                        <div>
                            <h1 class="page-title">Kelola Kategori Jasa</h1>
                            <p class="page-sub">Kelompokkan layanan editing Anda ke dalam spesialisasi yang mudah ditemukan customer.</p>
                        </div>
                        <div class="page-actions">
                            <div class="table-search-box">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#6B7280" stroke-width="2.2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                <input type="text" id="katSearchInput" placeholder="Cari nama kategori..." onkeyup="filterKatTable()">
                            </div>
                            <button class="btn btn-primary" onclick="openModal('modalTambahKategori')">
                                + Tambah Kategori Baru
                            </button>
                        </div>
                    </div>

                    <!-- Full-Width Donezo Data Table for Categories -->
                    <div class="table-card-full">
                        <div class="table-responsive">
                            <table class="data-table" id="tableKatList">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">ID</th>
                                        <th>Nama Kategori</th>
                                        <th>Total Layanan Terdaftar</th>
                                        <th style="text-align: right; width: 170px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($kategori_list)): ?>
                                        <tr>
                                            <td colspan="4" class="td-empty">Belum ada kategori. Klik <strong>+ Tambah Kategori Baru</strong> di atas.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($kategori_list as $kat): ?>
                                            <tr>
                                                <td><span class="id-badge">#<?= $kat['id_kategori_jasa'] ?></span></td>
                                                <td><strong class="row-title"><?= htmlspecialchars($kat['nama_kategori']) ?></strong></td>
                                                <td><span class="count-tag"><?= $kat['total_jasa'] ?> layanan aktif</span></td>
                                                <td class="td-actions">
                                                    <button 
                                                        class="btn-edit"
                                                        data-id="<?= $kat['id_kategori_jasa'] ?>"
                                                        data-nama="<?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES) ?>"
                                                        onclick="editKategoriModal(this)">
                                                        ✏️ Edit
                                                    </button>
                                                    <button 
                                                        class="btn-delete"
                                                        data-id="<?= $kat['id_kategori_jasa'] ?>"
                                                        data-nama="<?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES) ?>"
                                                        onclick="confirmDeleteKategori(this)">
                                                        🗑️ Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         MODAL 1: Tambah Jasa Baru (Zero Page Jump!)
         ============================================================== -->
    <div id="modalTambahJasa" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Tambah Layanan Jasa Baru</h3>
                    <p class="modal-sub">Data akan langsung tersimpan dan tampil di katalog customer.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modalTambahJasa')">✕</button>
            </div>
            <form action="detail_jasa/proses_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_jasa">Nama Layanan Jasa <span class="req">*</span></label>
                        <input type="text" id="nama_jasa" name="nama_jasa" placeholder="Contoh: Editing Video Reels Instagram & TikTok" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-half">
                            <label for="id_kategori_jasa">Kategori <span class="req">*</span></label>
                            <select id="id_kategori_jasa" name="id_kategori_jasa" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($kategori_list as $k): ?>
                                    <option value="<?= $k['id_kategori_jasa'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-half">
                            <label for="harga">Harga Mulai (Rp) <span class="req">*</span></label>
                            <input type="number" id="harga" name="harga" placeholder="Contoh: 150000" min="0" step="1000" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi Lengkap Jasa <span class="req">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan benefit, format video, jumlah revisi, dan keunggulan Anda..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="gambar">Foto / Gambar Portofolio (Opsional)</label>
                        <div class="file-box">
                            <input type="file" id="gambar" name="gambar" accept="image/jpeg,image/png,image/webp,image/jpg">
                            <small class="file-hint">Format: JPG, PNG, WEBP. Maks 5MB.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" onclick="closeModal('modalTambahJasa')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Terbitkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================================
         MODAL 2: Edit Jasa (Zero Page Jump!)
         ============================================================== -->
    <div id="modalEditJasa" class="modal-overlay">
        <div class="modal-card">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Edit Layanan Jasa</h3>
                    <p class="modal-sub">Perbarui informasi layanan jasa dan portofolio Anda.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modalEditJasa')">✕</button>
            </div>
            <form action="detail_jasa/proses_edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id_detail_jasa" name="id_detail_jasa" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_jasa">Nama Layanan Jasa <span class="req">*</span></label>
                        <input type="text" id="edit_nama_jasa" name="nama_jasa" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-half">
                            <label for="edit_id_kategori_jasa">Kategori <span class="req">*</span></label>
                            <select id="edit_id_kategori_jasa" name="id_kategori_jasa" required>
                                <?php foreach ($kategori_list as $k): ?>
                                    <option value="<?= $k['id_kategori_jasa'] ?>"><?= htmlspecialchars($k['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group col-half">
                            <label for="edit_harga">Harga Mulai (Rp) <span class="req">*</span></label>
                            <input type="number" id="edit_harga" name="harga" min="0" step="1000" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_deskripsi">Deskripsi Lengkap Jasa <span class="req">*</span></label>
                        <textarea id="edit_deskripsi" name="deskripsi" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="edit_gambar">Ganti Foto Portofolio (Biarkan kosong jika tetap)</label>
                        <div class="file-box">
                            <input type="file" id="edit_gambar" name="gambar" accept="image/jpeg,image/png,image/webp,image/jpg">
                            <small class="file-hint">Pilih gambar baru hanya jika ingin mengganti portofolio lama.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" onclick="closeModal('modalEditJasa')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================================
         MODAL 3: Konfirmasi Hapus Jasa
         ============================================================== -->
    <div id="modalHapusJasa" class="modal-overlay">
        <div class="modal-card modal-card-sm">
            <div class="modal-header">
                <h3 class="modal-title text-danger">Hapus Layanan Jasa?</h3>
                <button class="modal-close" onclick="closeModal('modalHapusJasa')">✕</button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus jasa <strong id="hapus_nama_jasa"></strong>?</p>
                <p class="text-muted" style="font-size: 13px; margin-top: 8px;">Tindakan ini permanen dan file gambar portofolio terkait akan dihapus.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" onclick="closeModal('modalHapusJasa')">Batal</button>
                <a id="btnConfirmDeleteJasa" href="#" class="btn btn-danger">Hapus Permanen</a>
            </div>
        </div>
    </div>

    <!-- ==============================================================
         MODAL 4: Tambah Kategori
         ============================================================== -->
    <div id="modalTambahKategori" class="modal-overlay">
        <div class="modal-card modal-card-sm">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Tambah Kategori Baru</h3>
                    <p class="modal-sub">Kelompokkan layanan editing Anda.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modalTambahKategori')">✕</button>
            </div>
            <form action="kategori_jasa/proses_tambah.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama_kategori">Nama Kategori <span class="req">*</span></label>
                        <input type="text" id="nama_kategori" name="nama_kategori" placeholder="Contoh: Motion Graphics" required autofocus>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" onclick="closeModal('modalTambahKategori')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================================
         MODAL 5: Edit Kategori
         ============================================================== -->
    <div id="modalEditKategori" class="modal-overlay">
        <div class="modal-card modal-card-sm">
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Edit Kategori Jasa</h3>
                    <p class="modal-sub">Perbarui nama kategori.</p>
                </div>
                <button class="modal-close" onclick="closeModal('modalEditKategori')">✕</button>
            </div>
            <form action="kategori_jasa/proses_edit.php" method="POST">
                <input type="hidden" id="edit_id_kategori_jasa" name="id_kategori_jasa" value="">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_nama_kategori">Nama Kategori <span class="req">*</span></label>
                        <input type="text" id="edit_nama_kategori" name="nama_kategori" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" onclick="closeModal('modalEditKategori')">Batal</button>
                    <button type="submit" class="btn btn-primary">Perbarui Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==============================================================
         MODAL 6: Konfirmasi Hapus Kategori
         ============================================================== -->
    <div id="modalHapusKategori" class="modal-overlay">
        <div class="modal-card modal-card-sm">
            <div class="modal-header">
                <h3 class="modal-title text-danger">Hapus Kategori?</h3>
                <button class="modal-close" onclick="closeModal('modalHapusKategori')">✕</button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus kategori <strong id="hapus_nama_kategori"></strong>?</p>
                <p class="text-muted" style="font-size: 13px; margin-top: 8px;">Perhatian: Layanan jasa yang menggunakan kategori ini juga dapat terpengaruh.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" onclick="closeModal('modalHapusKategori')">Batal</button>
                <a id="btnConfirmDeleteKat" href="#" class="btn btn-danger">Hapus Kategori</a>
            </div>
        </div>
    </div>

    <!-- JavaScript Modal Handlers & Tab Switcher (No Page Jumps!) -->
    <script>
        // Modal Open / Close Functions
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        // Close modal when clicking outside of card
        window.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });

        // Edit Jasa Modal Populate
        function editJasaModal(btn) {
            document.getElementById('edit_id_detail_jasa').value = btn.dataset.id;
            document.getElementById('edit_nama_jasa').value = btn.dataset.nama;
            document.getElementById('edit_id_kategori_jasa').value = btn.dataset.kategori;
            document.getElementById('edit_harga').value = btn.dataset.harga;
            document.getElementById('edit_deskripsi').value = btn.dataset.deskripsi;
            openModal('modalEditJasa');
        }

        // Confirm Delete Jasa
        function confirmDeleteJasa(btn) {
            const id = btn.dataset.id;
            const nama = btn.dataset.nama;
            document.getElementById('hapus_nama_jasa').textContent = '"' + nama + '"';
            document.getElementById('btnConfirmDeleteJasa').href = 'detail_jasa/proses_hapus.php?id=' + id;
            openModal('modalHapusJasa');
        }

        // Edit Kategori Modal Populate
        function editKategoriModal(btn) {
            document.getElementById('edit_id_kategori_jasa').value = btn.dataset.id;
            document.getElementById('edit_nama_kategori').value = btn.dataset.nama;
            openModal('modalEditKategori');
        }

        // Confirm Delete Kategori
        function confirmDeleteKategori(btn) {
            const id = btn.dataset.id;
            const nama = btn.dataset.nama;
            document.getElementById('hapus_nama_kategori').textContent = '"' + nama + '"';
            document.getElementById('btnConfirmDeleteKat').href = 'kategori_jasa/proses_hapus.php?id=' + id;
            openModal('modalHapusKategori');
        }

        // Switch View (Overview | Jasa | Kategori) - True In-App Navigation
        function switchView(viewName, e) {
            if (e) e.preventDefault();
            
            // 1. Hide all views, show targeted view
            document.querySelectorAll('.dashboard-view').forEach(v => v.classList.remove('active'));
            const targetView = document.getElementById('view' + viewName.charAt(0).toUpperCase() + viewName.slice(1));
            if (targetView) {
                targetView.classList.add('active');
            }

            // 2. Update active state on sidebar
            document.querySelectorAll('.sidebar-nav .nav-btn').forEach(btn => btn.classList.remove('active'));
            const targetNav = document.getElementById('nav' + viewName.charAt(0).toUpperCase() + viewName.slice(1));
            if (targetNav) {
                targetNav.classList.add('active');
            }

            // 3. Update browser URL history without reload
            const url = new URL(window.location);
            url.searchParams.set('view', viewName);
            window.history.pushState({}, '', url);

            // 4. Scroll to top of content canvas
            const scrollContainer = document.querySelector('.dashboard-scrollable');
            if (scrollContainer) {
                scrollContainer.scrollTop = 0;
            }
        }

        // Live Search Filter for Jasa Table
        function filterJasaTable() {
            const query = (document.getElementById('jasaSearchInput')?.value || '').toLowerCase();
            const rows = document.querySelectorAll('#tableJasaList tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Live Search Filter for Kategori Table
        function filterKatTable() {
            const query = (document.getElementById('katSearchInput')?.value || '').toLowerCase();
            const rows = document.querySelectorAll('#tableKatList tbody tr');
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        // Topbar Global Search Handler
        function filterTable() {
            const query = (document.getElementById('quickSearchInput')?.value || '').toLowerCase();
            if (!query) return;

            // Jika sedang di overview, alihkan ke view Jasa untuk menampilkan pencarian
            const currentActiveView = document.querySelector('.dashboard-view.active');
            if (currentActiveView && currentActiveView.id === 'viewOverview') {
                switchView('jasa');
                const jasaInput = document.getElementById('jasaSearchInput');
                if (jasaInput) {
                    jasaInput.value = query;
                }
            }
            filterJasaTable();
        }
    </script>
</body>

</html>
