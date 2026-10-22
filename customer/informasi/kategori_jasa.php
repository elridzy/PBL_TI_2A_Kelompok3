<?php
require_once __DIR__ . '/../../database/koneksi_psql.php';

// Ambil semua kategori untuk filter tab
$stmtKat = $pdo->query("SELECT * FROM kategori_jasa ORDER BY nama_kategori ASC");
$kategori_list = $stmtKat->fetchAll();

// Parameter filter
$kategori_aktif = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

// Query daftar jasa
$query = "
    SELECT dj.*, kj.nama_kategori, e.no_wa, p.gambar
    FROM detail_jasa dj
    JOIN kategori_jasa kj ON dj.id_kategori_jasa = kj.id_kategori_jasa
    JOIN editor e ON dj.id_editor = e.id_editor
    LEFT JOIN (
        SELECT DISTINCT ON (id_detail_jasa) id_detail_jasa, gambar 
        FROM portofolio 
        ORDER BY id_detail_jasa, id_portofolio ASC
    ) p ON dj.id_detail_jasa = p.id_detail_jasa
    WHERE 1=1
";

$params = [];

if ($kategori_aktif > 0) {
    $query .= " AND dj.id_kategori_jasa = :id_kat";
    $params['id_kat'] = $kategori_aktif;
}

if (!empty($search)) {
    $query .= " AND (dj.nama_jasa ILIKE :search OR dj.deskripsi ILIKE :search)";
    $params['search'] = '%' . $search . '%';
}

$query .= " ORDER BY dj.tanggal_upload DESC, dj.id_detail_jasa DESC";

$stmtJasa = $pdo->prepare($query);
$stmtJasa->execute($params);
$jasa_list = $stmtJasa->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Jasa Editing — sekilasrv</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tampilan/kategori_jasa.css">
</head>

<body>
    <!-- Top Navbar (Clause Header) -->
    <header class="site-header">
        <div class="header-container">
            <a href="../../beranda/beranda.php" class="brand-logo">
                <div class="logo-mark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <rect width="24" height="24" rx="6" fill="#0B3528" />
                        <path d="M7 12L12 7L17 12L12 17L7 12Z" fill="#D2E823" />
                        <circle cx="12" cy="12" r="2.5" fill="#0B3528" />
                    </svg>
                </div>
                <span class="brand-title">sekilasrv</span>
            </a>

            <nav class="nav-links">
                <a href="../../beranda/beranda.php" class="nav-item">Beranda</a>
                <a href="kategori_jasa.php" class="nav-item active">Katalog Jasa</a>
                <a href="../../beranda/beranda.php#cara-kerja" class="nav-item">Cara Kerja</a>
            </nav>

            <div class="header-actions">
                <a href="../../editor/auth/login.php" class="btn btn-login">Login Editor</a>
                <a href="../../beranda/beranda.php" class="btn btn-primary-header">← Beranda</a>
            </div>
        </div>
    </header>

    <main class="main-wrapper">
        <!-- Catalog Hero -->
        <section class="catalog-hero">
            <div class="pill-badge">
                <span class="pill-icon">✦</span>
                <span class="pill-text">DIREKTORI RESMI KREATOR & EDITOR</span>
            </div>

            <h1 class="hero-headline">
                Pilih <span class="highlight-text">layanan</span> editing terbaik<br>
                sesuai konsep konten Anda
            </h1>

            <p class="hero-subtitle">
                Temukan editor profesional untuk video pendek, YouTube, color grading, dan desain grafis. Hubungi langsung lewat WhatsApp tanpa biaya platform tambahan.
            </p>

            <!-- Search Bar -->
            <form action="kategori_jasa.php" method="GET" class="search-form">
                <?php if ($kategori_aktif > 0): ?>
                    <input type="hidden" name="kategori" value="<?= $kategori_aktif ?>">
                <?php endif; ?>
                <div class="search-input-group">
                    <span class="search-icon">🔍</span>
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Cari nama layanan atau kata kunci (cth: Reels, Sinematik, Logo)..." 
                        value="<?= htmlspecialchars($search) ?>"
                        autocomplete="off">
                    <button type="submit" class="search-btn">Cari Jasa</button>
                    <?php if (!empty($search)): ?>
                        <a href="kategori_jasa.php<?= $kategori_aktif > 0 ? '?kategori=' . $kategori_aktif : '' ?>" class="reset-search" title="Hapus pencarian">✕</a>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <!-- Category Filter Pills -->
        <section class="category-filter-section">
            <div class="category-pills">
                <a href="kategori_jasa.php<?= !empty($search) ? '?q=' . urlencode($search) : '' ?>" 
                   class="pill <?= ($kategori_aktif === 0) ? 'active' : '' ?>">
                    Semua Kategori
                </a>
                <?php foreach ($kategori_list as $kat): ?>
                    <a href="kategori_jasa.php?kategori=<?= $kat['id_kategori_jasa'] ?><?= !empty($search) ? '&q=' . urlencode($search) : '' ?>" 
                       class="pill <?= ($kategori_aktif === (int)$kat['id_kategori_jasa']) ? 'active' : '' ?>">
                        <?= htmlspecialchars($kat['nama_kategori']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Services Grid Section -->
        <section class="services-section">
            <div class="section-header">
                <h2 class="section-title">
                    <?php 
                    if ($kategori_aktif > 0) {
                        $currentKat = array_filter($kategori_list, fn($k) => (int)$k['id_kategori_jasa'] === $kategori_aktif);
                        $currentKatName = $currentKat ? reset($currentKat)['nama_kategori'] : 'Kategori';
                        echo 'Kategori: <span>' . htmlspecialchars($currentKatName) . '</span>';
                    } else {
                        echo 'Semua <span>Layanan Jasa</span>';
                    }
                    ?>
                </h2>
                <span class="total-badge"><?= count($jasa_list) ?> Layanan Ditemukan</span>
            </div>

            <?php if (empty($jasa_list)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📂</div>
                    <h3>Belum Ada Jasa yang Ditemukan</h3>
                    <p>Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    <a href="kategori_jasa.php" class="btn btn-primary-header">Lihat Semua Kategori</a>
                </div>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($jasa_list as $jasa): ?>
                        <article class="service-card">
                            <div class="card-thumb">
                                <?php 
                                $img_path = !empty($jasa['gambar']) ? '../../uploads/' . $jasa['gambar'] : '';
                                if (!empty($jasa['gambar']) && file_exists(__DIR__ . '/' . $img_path)): 
                                ?>
                                    <img src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($jasa['nama_jasa']) ?>">
                                <?php else: ?>
                                    <div class="thumb-placeholder">
                                        <div class="placeholder-icon">🎬</div>
                                        <span><?= htmlspecialchars($jasa['nama_kategori']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <span class="category-badge"><?= htmlspecialchars($jasa['nama_kategori']) ?></span>
                            </div>

                            <div class="card-content">
                                <h3 class="service-title">
                                    <a href="detail_jasa.php?id=<?= $jasa['id_detail_jasa'] ?>">
                                        <?= htmlspecialchars($jasa['nama_jasa']) ?>
                                    </a>
                                </h3>
                                <p class="service-desc">
                                    <?= htmlspecialchars(mb_strimwidth($jasa['deskripsi'], 0, 115, "...")) ?>
                                </p>
                                
                                <div class="card-footer">
                                    <div class="price-box">
                                        <span class="price-label">Mulai Dari</span>
                                        <span class="price-amount">Rp <?= number_format($jasa['harga'], 0, ',', '.') ?></span>
                                    </div>
                                    <a href="detail_jasa.php?id=<?= $jasa['id_detail_jasa'] ?>" class="btn-detail">
                                        Lihat Detail →
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="content-container">
            <div class="footer-bottom-row">
                <span>© <?= date('Y') ?> <strong>sekilasrv</strong> • Solusi Direktori Jasa Editing Indonesia</span>
                <span class="footer-note">PBL Semester 3</span>
            </div>
        </div>
    </footer>
</body>

</html>
