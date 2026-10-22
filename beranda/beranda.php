<?php
// Koneksi opsional untuk mengambil jumlah data jika database aktif
$total_jasa = 5;
$total_kategori = 5;
$services_list = [];
$categories_list = [];

$koneksi_file = __DIR__ . '/../database/koneksi_psql.php';
if (file_exists($koneksi_file)) {
    try {
        require_once $koneksi_file;
        if (isset($pdo)) {
            $total_jasa = (int)$pdo->query("SELECT COUNT(*) FROM detail_jasa")->fetchColumn();
            $total_kategori = (int)$pdo->query("SELECT COUNT(*) FROM kategori_jasa")->fetchColumn();
            
            $services_list = $pdo->query("SELECT d.id_detail_jasa, d.nama_jasa, d.harga, k.nama_kategori, e.no_wa 
                                          FROM detail_jasa d 
                                          JOIN kategori_jasa k ON d.id_kategori_jasa = k.id_kategori_jasa 
                                          JOIN editor e ON d.id_editor = e.id_editor 
                                          ORDER BY d.id_detail_jasa ASC")->fetchAll(PDO::FETCH_ASSOC);
                                          
            $categories_list = $pdo->query("SELECT id_kategori_jasa, nama_kategori FROM kategori_jasa ORDER BY id_kategori_jasa ASC")->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (Exception $e) {
        // Fallback default
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sekilasrv — Platform Promosi & Direktori Jasa Editing</title>
    <meta name="description" content="Satu platform untuk menemukan editor video, motion graphics, dan desainer grafis pilihan. Terhubung langsung via WhatsApp tanpa biaya perantara.">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="beranda.css">
</head>

<body>
    <!-- Top Navigation Bar (Clause style) -->
    <header class="site-header" id="mainHeader">
        <div class="header-container">
            <!-- Brand Logo -->
            <a href="beranda.php" class="brand-logo">
                <div class="logo-mark">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <rect width="24" height="24" rx="6" fill="#0B3528" />
                        <path d="M7 12L12 7L17 12L12 17L7 12Z" fill="#D2E823" />
                        <circle cx="12" cy="12" r="2.5" fill="#0B3528" />
                    </svg>
                </div>
                <span class="brand-title">sekilasrv</span>
            </a>

            <!-- Nav Links (Scrollspy enabled) -->
            <nav class="nav-links">
                <a href="#fitur" class="nav-item">Fitur Layanan</a>
                <a href="#kategori" class="nav-item">Kategori Jasa</a>
                <a href="#cara-kerja" class="nav-item">Cara Kerja</a>
                <a href="#order-generator" class="nav-item">Buat Brief WA</a>
                <a href="#peran" class="nav-item">Pilih Peran</a>
            </nav>

            <!-- Nav Actions -->
            <div class="header-actions">
                <a href="../editor/auth/login.php" class="btn btn-login">Login Editor</a>
                <a href="../customer/informasi/kategori_jasa.php" class="btn btn-primary-header">Mulai Sekarang</a>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section" id="hero">
            <div class="hero-bg-grid"></div>

            <div class="hero-container">
                <!-- Floating Avatars with Pointers (Clause Signature Style) -->
                <div class="floating-avatar avatar-top-left" title="Sarah - Content Creator">
                    <div class="avatar-ring ring-purple">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=120&auto=format&fit=crop&q=80" alt="Sarah">
                    </div>
                    <div class="avatar-pointer pointer-tl">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#0B3528"><path d="M3 3l7.07 16.97 2.51-7.39 7.39-2.51L3 3z"/></svg>
                    </div>
                </div>

                <div class="floating-avatar avatar-top-right" title="Aris - Video Editor">
                    <div class="avatar-ring ring-blue">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=120&auto=format&fit=crop&q=80" alt="Aris">
                    </div>
                    <div class="avatar-pointer pointer-tr">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#0B3528"><path d="M21 3l-16.97 7.07 7.39 2.51 2.51 7.39L21 3z"/></svg>
                    </div>
                </div>

                <div class="floating-avatar avatar-bottom-left" title="Nadia - YouTuber">
                    <div class="avatar-ring ring-teal">
                        <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?w=120&auto=format&fit=crop&q=80" alt="Nadia">
                    </div>
                    <div class="avatar-pointer pointer-bl">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#0B3528"><path d="M3 21l16.97-7.07-7.39-2.51-2.51-7.39L3 21z"/></svg>
                    </div>
                </div>

                <div class="floating-avatar avatar-bottom-right" title="Dimas - Motion Designer">
                    <div class="avatar-ring ring-yellow">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=120&auto=format&fit=crop&q=80" alt="Dimas">
                    </div>
                    <div class="avatar-pointer pointer-br">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#0B3528"><path d="M21 21l-7.07-16.97-2.51 7.39-7.39 2.51L21 21z"/></svg>
                    </div>
                </div>

                <!-- Hero Center Badge -->
                <div class="pill-badge">
                    <span class="pill-icon">⚡</span>
                    <span class="pill-text">PLATFORM JASA EDITING TERPERCAYA</span>
                </div>

                <!-- Main Headline -->
                <h1 class="hero-headline">
                    Satu tempat untuk <span class="highlight-text">temukan</span><br>
                    editor dan selesaikan konten
                </h1>

                <!-- Subtitle -->
                <p class="hero-subtitle">
                    sekilasrv menghubungkan kreator, pebisnis, dan YouTuber dengan editor profesional. Diskusikan konsep secara transparan dan pesan langsung via WhatsApp tanpa biaya perantara.
                </p>

                <!-- Hero CTA Buttons -->
                <div class="hero-button-group">
                    <a href="../customer/informasi/kategori_jasa.php" class="btn btn-hero-dark">
                        Cari Jasa Sekarang
                    </a>
                    <a href="#order-generator" class="btn btn-hero-lime">
                        ⚡ Buat Brief Order WA
                    </a>
                    <a href="../editor/auth/login.php" class="btn btn-hero-light">
                        Portal Editor
                    </a>
                </div>
            </div>

            <!-- Partner / Compatible Tools Strip -->
            <div class="partners-strip">
                <span class="partners-label">Didukung format & software standar industri:</span>
                <div class="partners-logos">
                    <span class="partner-name">Premiere Pro</span>
                    <span class="partner-dot">•</span>
                    <span class="partner-name">DaVinci Resolve</span>
                    <span class="partner-dot">•</span>
                    <span class="partner-name">After Effects</span>
                    <span class="partner-dot">•</span>
                    <span class="partner-name">Photoshop</span>
                    <span class="partner-dot">•</span>
                    <span class="partner-name">CapCut</span>
                    <span class="partner-dot">•</span>
                    <span class="partner-name">Figma</span>
                </div>
            </div>
        </section>

        <!-- Features Showcase Section (Clause Style Clean Cards) -->
        <section id="fitur" class="features-section">
            <div class="content-container">
                <div class="section-badge-center reveal-on-scroll">
                    <span class="section-tag">✦ FITUR UNGGULAN</span>
                </div>
                <h2 class="section-title reveal-on-scroll">
                    Alur kerja ringkas untuk memastikan<br>
                    semua kebutuhan konten terpenuhi
                </h2>
                <p class="section-description reveal-on-scroll">
                    Maksimalkan konsistensi publikasi video dan visual Anda dengan sistem direktori jasa yang terstruktur dan mudah digunakan.
                </p>

                <!-- Feature Cards Container -->
                <div class="feature-cards-wrap">
                    <!-- Card 1: Wide Dashboard Card -->
                    <div class="feature-card-wide reveal-on-scroll">
                        <div class="card-left">
                            <h3 class="card-heading">Katalog & Direktori Jasa</h3>
                            <p class="card-body-text">
                                Temukan ragam spesialisasi editing mulai dari konten vertikal TikTok/Reels, video YouTube sinematik, hingga identitas logo visual dengan harga tertera transparan.
                            </p>
                            <a href="../customer/informasi/kategori_jasa.php" class="btn btn-card-green">
                                Eksplorasi Semua
                            </a>
                        </div>

                        <!-- Card Right: Clean Dashboard Mockup -->
                        <div class="card-right-preview">
                            <div class="inner-mockup-panel">
                                <div class="panel-header">
                                    <div class="panel-title-wrap">
                                        <span class="panel-label">Kategori Pilihan</span>
                                        <strong class="panel-current">Video Reels & YouTube</strong>
                                    </div>
                                    <div class="panel-avatars">
                                        <div class="mini-avatar av1" title="Sarah"></div>
                                        <div class="mini-avatar av2" title="Aris"></div>
                                        <div class="mini-avatar av3" title="Dimas"></div>
                                    </div>
                                </div>

                                <!-- Mini Bar Chart Mockup (Matching Clause Design) -->
                                <div class="chart-bars">
                                    <div class="bar-col"><div class="bar-fill" style="height: 38%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 65%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 30%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 48%;"></div></div>
                                    <div class="bar-col highlight"><div class="bar-fill-high" style="height: 94%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 42%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 72%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 55%;"></div></div>
                                    <div class="bar-col"><div class="bar-fill" style="height: 35%;"></div></div>
                                </div>
                                <div class="chart-labels">
                                    <span>TikTok</span>
                                    <span>Reels</span>
                                    <span>Vlog</span>
                                    <span>Podcast</span>
                                    <span class="active-label">Sinematik</span>
                                    <span>Logo</span>
                                    <span>Motion</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Row of 2 Cards Below -->
                    <div class="feature-cards-row">
                        <!-- Card 2: Filter & Preferensi -->
                        <div class="feature-card-half reveal-on-scroll">
                            <div class="card-header-simple">
                                <h3>Filter Spesifikasi & Software</h3>
                                <p>Saring layanan berdasarkan kebutuhan teknis dan gaya visual Anda.</p>
                            </div>

                            <div class="toggle-list-mockup">
                                <div class="toggle-row">
                                    <span>Termasuk Subtitle Dinamis</span>
                                    <label class="switch"><input type="checkbox" checked disabled><span class="slider"></span></label>
                                </div>
                                <div class="toggle-row">
                                    <span>Gratis Desain Thumbnail</span>
                                    <label class="switch"><input type="checkbox" checked disabled><span class="slider"></span></label>
                                </div>
                                <div class="toggle-row">
                                    <span>Color Grading DaVinci Resolve</span>
                                    <label class="switch"><input type="checkbox" checked disabled><span class="slider"></span></label>
                                </div>
                                <div class="toggle-row">
                                    <span>Pengerjaan Kilat (< 24 Jam)</span>
                                    <label class="switch"><input type="checkbox" disabled><span class="slider"></span></label>
                                </div>
                            </div>
                        </div>

                        <!-- Card 3: Direct WA Chat & Task Management -->
                        <div class="feature-card-half reveal-on-scroll">
                            <div class="card-header-simple">
                                <h3>Komunikasi Langsung & Negosiasi</h3>
                                <p>Hubungi editor langsung di WhatsApp tanpa pihak ketiga.</p>
                            </div>

                            <div class="activity-feed-mockup">
                                <div class="activity-item">
                                    <div class="act-avatar av-user1"></div>
                                    <div class="act-content">
                                        <div class="act-top">
                                            <strong>Rian (Klien)</strong>
                                            <span class="act-time">Baru saja</span>
                                        </div>
                                        <p class="act-bubble">"Halo kak, bisa edit video TikTok 60 detik sebelum hari Jumat? Bahan sudah siap di Google Drive."</p>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="act-avatar av-user2"></div>
                                    <div class="act-content">
                                        <div class="act-top">
                                            <strong>Editor sekilasrv</strong>
                                            <span class="act-time">1 mnt lalu</span>
                                        </div>
                                        <div class="act-attachment">
                                            <span class="file-icon">📄</span>
                                            <span>Draft_Final_Reels_v1.mp4 (1080x1920)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Dark Forest Green Section ("Don't replace, Integrate" Style) -->
        <section id="kategori" class="dark-banner-section">
            <div class="content-container">
                <div class="dark-banner-box reveal-on-scroll">
                    <span class="dark-tag">✦ SPESIALISASI KREATIF</span>
                    <h2 class="dark-title">Layanan Lengkap untuk Segala Format</h2>
                    <p class="dark-desc">
                        Dari konten media sosial berkecepatan tinggi hingga produksi video korporat beresolusi 4K, temukan keahlian yang tepat.
                    </p>

                    <!-- App / Category Icon Badges Grid -->
                    <div class="software-badges-grid">
                        <a href="../customer/informasi/kategori_jasa.php?kategori=1" class="soft-badge">
                            <span class="badge-icon">🎬</span>
                            <span class="badge-title">Video Editing</span>
                        </a>
                        <a href="../customer/informasi/kategori_jasa.php?kategori=2" class="soft-badge">
                            <span class="badge-icon">🎨</span>
                            <span class="badge-title">Desain Grafis</span>
                        </a>
                        <a href="../customer/informasi/kategori_jasa.php?kategori=3" class="soft-badge">
                            <span class="badge-icon">🎞️</span>
                            <span class="badge-title">Color Grading</span>
                        </a>
                        <a href="../customer/informasi/kategori_jasa.php?kategori=4" class="soft-badge">
                            <span class="badge-icon">✨</span>
                            <span class="badge-title">Motion Graphics</span>
                        </a>
                        <a href="../customer/informasi/kategori_jasa.php?kategori=5" class="soft-badge">
                            <span class="badge-icon">📸</span>
                            <span class="badge-title">Foto Retouch</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cara Kerja Section (Clear 3-Step Timeline) -->
        <section id="cara-kerja" class="workflow-section">
            <div class="content-container">
                <div class="section-badge-center reveal-on-scroll">
                    <span class="section-tag">✦ ALUR KERJA TERSTRUKTUR</span>
                </div>
                <h2 class="section-title reveal-on-scroll">
                    3 Langkah Praktis dari Ide<br>Hingga Konten Siap Tayang
                </h2>
                <p class="section-description reveal-on-scroll">
                    Tidak ada birokrasi berbelit atau formulir rumit. Hubungkan visi kreatif Anda langsung ke editor dalam 3 langkah mudah.
                </p>

                <div class="workflow-steps-grid">
                    <div class="workflow-step-card reveal-on-scroll">
                        <div class="step-num-badge">01</div>
                        <div class="step-icon-wrap">🔍</div>
                        <h3 class="step-heading">Pilih Jasa & Cek Portofolio</h3>
                        <p class="step-desc">
                            Jelajahi kategori video editing, motion graphics, atau desain. Periksa sampel hasil karya nyata dan patokan tarif tertera yang jelas.
                        </p>
                        <a href="../customer/informasi/kategori_jasa.php" class="step-link">Lihat Katalog →</a>
                    </div>

                    <div class="workflow-step-card highlight-step reveal-on-scroll">
                        <div class="step-num-badge badge-lime">02</div>
                        <div class="step-icon-wrap">⚡</div>
                        <h3 class="step-heading">Susun Brief Rapi Otomatis</h3>
                        <p class="step-desc">
                            Gunakan generator brief di bawah. Tentukan format video, link bahan mentah, referensi visual, dan preferensi subtitle dalam satu format rapi.
                        </p>
                        <a href="#order-generator" class="step-link">Coba Generator ↓</a>
                    </div>

                    <div class="workflow-step-card reveal-on-scroll">
                        <div class="step-num-badge">03</div>
                        <div class="step-icon-wrap">💬</div>
                        <h3 class="step-heading">Kirim ke WA & Kolaborasi</h3>
                        <p class="step-desc">
                            Pesan terkirim langsung ke nomor WhatsApp editor terkait. Diskusikan revisi secara transparan tanpa potongan komisi pihak ketiga.
                        </p>
                        <span class="step-tag-pill">100% Direct WA</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- WhatsApp Order Generator Section -->
        <section id="order-generator" class="generator-section">
            <div class="content-container">
                <div class="generator-header-box reveal-on-scroll">
                    <div class="section-badge-center">
                        <span class="section-tag tag-lime">⚡ GENERATOR BRIEF CEPAT</span>
                    </div>
                    <h2 class="section-title">
                        Susun Brief & Kirim Pesanan ke WhatsApp
                    </h2>
                    <p class="section-description">
                        Hindari salah paham dan revisi berulang. Isi formulir interaktif di bawah, preview pesan WhatsApp Anda secara live, lalu kirim langsung ke editor dengan sekali klik!
                    </p>
                </div>

                <div class="generator-layout reveal-on-scroll">
                    <!-- Left: Interactive Form -->
                    <div class="generator-form-card">
                        <div class="form-card-header">
                            <div class="card-icon-pill">📝</div>
                            <div>
                                <h3 class="form-title">Data Brief Kebutuhan</h3>
                                <p class="form-subtitle">Sesuaikan preferensi project konten Anda</p>
                            </div>
                        </div>

                        <form id="waBriefForm" onsubmit="event.preventDefault(); sendToWhatsApp();">
                            <!-- 1. Client Name -->
                            <div class="form-group">
                                <label for="clientName" class="form-label">Nama Anda / Brand <span class="required">*</span></label>
                                <input type="text" id="clientName" class="form-input" placeholder="Misal: Rian Pratama (Studio Sembilan)" value="Rian Pratama" required oninput="updateBriefPreview()">
                            </div>

                            <!-- 2. Select Service -->
                            <div class="form-group">
                                <label for="serviceSelect" class="form-label">Pilih Layanan Jasa <span class="required">*</span></label>
                                <select id="serviceSelect" class="form-select" onchange="onServiceChange()">
                                    <?php if (!empty($services_list)): ?>
                                        <?php foreach ($services_list as $srv): ?>
                                            <option value="<?= htmlspecialchars($srv['nama_jasa']) ?>" 
                                                    data-price="<?= number_format($srv['harga'], 0, ',', '.') ?>" 
                                                    data-category="<?= htmlspecialchars($srv['nama_kategori']) ?>"
                                                    data-wa="<?= htmlspecialchars($srv['no_wa']) ?>">
                                                [<?= htmlspecialchars($srv['nama_kategori']) ?>] <?= htmlspecialchars($srv['nama_jasa']) ?> — Rp <?= number_format($srv['harga'], 0, ',', '.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="Custom / Kebutuhan Khusus" data-price="Sesuai Diskusi" data-category="Konsultasi Bebas" data-wa="6281234567890">
                                            Lainnya / Konsultasi Project Khusus
                                        </option>
                                    <?php else: ?>
                                        <option value="Editing Video Reels & TikTok Engagement Tinggi" data-price="75.000" data-category="Video Editing" data-wa="6281234567890">Editing Video Reels & TikTok Engagement Tinggi — Rp 75.000</option>
                                        <option value="Editing Video YouTube Sinematik & Vlogging" data-price="250.000" data-category="Video Editing" data-wa="6281234567890">Editing Video YouTube Sinematik & Vlogging — Rp 250.000</option>
                                        <option value="Desain Logo & Brand Identity Modern Minimalis" data-price="350.000" data-category="Desain Grafis" data-wa="6281234567890">Desain Logo & Brand Identity Modern Minimalis — Rp 350.000</option>
                                    <?php endif; ?>
                                </select>
                                <div class="service-meta-hint">
                                    <span id="serviceCategoryBadge" class="hint-badge">Video Editing</span>
                                    <span id="servicePriceBadge" class="hint-price">Mulai Rp 75.000</span>
                                </div>
                            </div>

                            <!-- 3. Content Format / Duration -->
                            <div class="form-group">
                                <label class="form-label">Format / Durasi Konten</label>
                                <div class="pill-radio-group">
                                    <label class="pill-radio">
                                        <input type="radio" name="contentFormat" value="Shorts / Reels / TikTok (15-60 Detik)" checked onchange="updateBriefPreview()">
                                        <span>Vertical (15-60s)</span>
                                    </label>
                                    <label class="pill-radio">
                                        <input type="radio" name="contentFormat" value="YouTube Landscape (5-15 Menit)" onchange="updateBriefPreview()">
                                        <span>Landscape (5-15m)</span>
                                    </label>
                                    <label class="pill-radio">
                                        <input type="radio" name="contentFormat" value="Desain Visual / Banner / Feed" onchange="updateBriefPreview()">
                                        <span>Desain Visual</span>
                                    </label>
                                    <label class="pill-radio">
                                        <input type="radio" name="contentFormat" value="Custom / Longform (> 15 Menit)" onchange="updateBriefPreview()">
                                        <span>Longform / Lainnya</span>
                                    </label>
                                </div>
                            </div>

                            <!-- 4. Deadline -->
                            <div class="form-group">
                                <label class="form-label">Target Waktu Selesai</label>
                                <div class="pill-radio-group">
                                    <label class="pill-radio">
                                        <input type="radio" name="deadline" value="< 24 Jam (Prioritas Kilat)" onchange="updateBriefPreview()">
                                        <span>⚡ < 24 Jam (Kilat)</span>
                                    </label>
                                    <label class="pill-radio">
                                        <input type="radio" name="deadline" value="2 - 3 Hari (Standar)" checked onchange="updateBriefPreview()">
                                        <span>2 - 3 Hari (Standar)</span>
                                    </label>
                                    <label class="pill-radio">
                                        <input type="radio" name="deadline" value="Fleksibel / Santai (~1 Minggu)" onchange="updateBriefPreview()">
                                        <span>Fleksibel (~1 Minggu)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- 5. Link Raw Files / Reference -->
                            <div class="form-group">
                                <label for="rawLink" class="form-label">Link File Mentah / Contoh Referensi</label>
                                <input type="text" id="rawLink" class="form-input" placeholder="https://drive.google.com/... atau link YouTube" value="https://drive.google.com/drive/folders/sample-footage" oninput="updateBriefPreview()">
                                <span class="input-help">Sertakan link Google Drive, Dropbox, atau link video referensi gaya yang disukai.</span>
                            </div>

                            <!-- 6. Features / Styles Checkboxes -->
                            <div class="form-group">
                                <label class="form-label">Preferensi Tambahan</label>
                                <div class="checkbox-options-grid">
                                    <label class="check-item">
                                        <input type="checkbox" id="prefSubtitle" checked onchange="updateBriefPreview()">
                                        <span>Subtitle Dinamis / Pop-up Text</span>
                                    </label>
                                    <label class="check-item">
                                        <input type="checkbox" id="prefSound" checked onchange="updateBriefPreview()">
                                        <span>Sound FX & Musik Bebas Copyright</span>
                                    </label>
                                    <label class="check-item">
                                        <input type="checkbox" id="prefColor" checked onchange="updateBriefPreview()">
                                        <span>Color Grading Sinematik</span>
                                    </label>
                                    <label class="check-item">
                                        <input type="checkbox" id="prefThumb" onchange="updateBriefPreview()">
                                        <span>Desain Thumbnail Sekaligus</span>
                                    </label>
                                </div>
                            </div>

                            <!-- 7. Custom Notes -->
                            <div class="form-group">
                                <label for="customNotes" class="form-label">Catatan Khusus untuk Editor (Opsional)</label>
                                <textarea id="customNotes" class="form-textarea" rows="2" placeholder="Misal: Tolong potong bagian jeda bernafas, buat pacing cepat di 3 detik awal hook." oninput="updateBriefPreview()">Tolong fokuskan hook di 3 detik awal, sound effect pop-up subtitle mirip gaya Alex Hormozi ya kak.</textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Right: WhatsApp Phone Simulation Live Preview -->
                    <div class="generator-preview-card">
                        <div class="preview-sticky-wrap">
                            <div class="phone-mockup">
                                <!-- Phone Header -->
                                <div class="phone-header">
                                    <div class="phone-back">←</div>
                                    <div class="phone-avatar">
                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=80&q=80" alt="Editor">
                                    </div>
                                    <div class="phone-user-info">
                                        <strong class="phone-username">Editor sekilasrv</strong>
                                        <span class="phone-status">Online • <span id="targetWaDisplay">+62 812-3456-7890</span></span>
                                    </div>
                                    <div class="phone-icons">
                                        <span>📹</span>
                                        <span>📞</span>
                                    </div>
                                </div>

                                <!-- Chat Body -->
                                <div class="phone-chat-body">
                                    <div class="chat-date-pill">HARI INI</div>

                                    <!-- Incoming Bot / Welcome Bubble -->
                                    <div class="chat-bubble bubble-incoming">
                                        <p>Halo! Selamat datang di sekilasrv. Silakan kirimkan brief pesanan Anda di bawah ya 😊</p>
                                        <span class="bubble-time">10:00</span>
                                    </div>

                                    <!-- Outgoing Dynamic Brief Bubble -->
                                    <div class="chat-bubble bubble-outgoing">
                                        <div id="liveWaPreviewText" class="brief-preview-content">
                                            <!-- Dynamically populated by JS -->
                                        </div>
                                        <div class="bubble-meta">
                                            <span class="bubble-time" id="previewCurrentTime">Baru saja</span>
                                            <span class="bubble-check">✓✓</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Footer -->
                                <div class="phone-action-footer">
                                    <button type="button" class="btn-wa-send" onclick="sendToWhatsApp()">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.16 12.04 20.16C10.66 20.16 9.3 19.79 8.12 19.09L7.84 18.92L4.72 19.74L5.55 16.7L5.36 16.4C4.55 15.11 4.12 13.53 4.12 11.91C4.12 7.37 7.82 3.67 12.05 3.67Z"/>
                                        </svg>
                                        <span>Kirim ke WhatsApp Sekarang</span>
                                    </button>

                                    <div class="secondary-actions-row">
                                        <button type="button" class="btn-copy-brief" id="btnCopyBrief" onclick="copyBriefText()">
                                            <span id="copyIcon">📋</span> <span id="copyLabel">Salin Teks Brief</span>
                                        </button>
                                        <button type="button" class="btn-reset-form" onclick="resetBriefForm()">
                                            ↺ Reset Form
                                        </button>
                                    </div>

                                    <div class="guarantee-footer-hint">
                                        🔒 Bebas biaya perantara. Chat langsung terhubung ke WhatsApp editor tanpa login akun klien.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Editorial Quote Testimonial (Clause Signature Style) -->
        <section class="quote-section">
            <div class="content-container">
                <div class="quote-wrapper reveal-on-scroll">
                    <div class="quote-mark">“</div>
                    <blockquote class="quote-text">
                        "sekilasrv sangat membantu channel kami memangkas waktu produksi. Kami bisa langsung berdiskusi dengan editor lewat WhatsApp, mencocokkan referensi video, dan menerima hasil jadi tepat waktu."
                    </blockquote>
                    <div class="quote-author">
                        <div class="author-photo">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=90&auto=format&fit=crop&q=80" alt="Sarah Amalia">
                        </div>
                        <div class="author-info">
                            <strong>Sarah Amalia</strong>
                            <span>Kreator Konten & Founder Studio Sembilan</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Counter Bar (Matching Clause 2021 / 50K+ / 1k+) -->
        <section class="stats-bar-section">
            <div class="content-container">
                <div class="stats-grid-row reveal-on-scroll">
                    <div class="stat-col">
                        <span class="stat-big-number">100%</span>
                        <span class="stat-sub-label">Direct WhatsApp Chat</span>
                    </div>
                    <div class="stat-col">
                        <span class="stat-big-number">0%</span>
                        <span class="stat-sub-label">Potongan Komisi Platform</span>
                    </div>
                    <div class="stat-col">
                        <span class="stat-big-number">PBL</span>
                        <span class="stat-sub-label">Semester 3 Inisiatif</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Role Selector Section (Customer vs Editor) -->
        <section id="peran" class="roles-selection-section">
            <div class="content-container">
                <div class="section-badge-center reveal-on-scroll">
                    <span class="section-tag">✦ PILIH PERAN ANDA</span>
                </div>
                <h2 class="section-title reveal-on-scroll">
                    Siap Memulai Hari Ini?
                </h2>
                <p class="section-description reveal-on-scroll">
                    Pilih akses yang sesuai dengan kebutuhan Anda di platform sekilasrv.
                </p>

                <div class="roles-duo-grid">
                    <!-- Customer Box -->
                    <div class="role-card-box reveal-on-scroll">
                        <span class="role-pill-user">Untuk Klien & Kreator</span>
                        <h3 class="role-title">Customer</h3>
                        <p class="role-summary">
                            Cari editor profesional untuk mengedit video YouTube, konten TikTok, reels Instagram, atau pembuatan logo dan materi promosi.
                        </p>
                        <ul class="role-perks">
                            <li>✓ Akses gratis ke seluruh katalog layanan</li>
                            <li>✓ Transparansi harga tanpa biaya tambahan</li>
                            <li>✓ Pesan langsung terhubung ke WhatsApp editor</li>
                        </ul>
                        <a href="../customer/informasi/kategori_jasa.php" class="btn btn-role-main">
                            Buka Katalog Jasa
                        </a>
                    </div>

                    <!-- Editor Box -->
                    <div class="role-card-box highlight-role reveal-on-scroll">
                        <span class="role-pill-editor">Untuk Freelancer & Editor</span>
                        <h3 class="role-title">Editor</h3>
                        <p class="role-summary">
                            Punya keahlian editing video atau desain grafis? Promosikan keahlian Anda, atur tarif sendiri, dan terima pesanan langsung.
                        </p>
                        <ul class="role-perks">
                            <li>✓ Dashboard kelola jasa dan kategori</li>
                            <li>✓ Upload portofolio karya terbaik</li>
                            <li>✓ Masuk praktis menggunakan PIN akses</li>
                        </ul>
                        <a href="../editor/auth/login.php" class="btn btn-role-highlight">
                            Masuk Portal Editor
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pre-Footer Banner (Clause Style Green Banner) -->
        <section class="cta-banner-section">
            <div class="content-container">
                <div class="cta-banner-green reveal-on-scroll">
                    <div class="banner-text">
                        <h2>Temukan editor yang tepat untuk project Anda sekarang</h2>
                    </div>
                    <div class="banner-buttons">
                        <a href="../customer/informasi/kategori_jasa.php" class="btn btn-banner-lime">Mulai Cari Jasa</a>
                        <a href="../editor/auth/login.php" class="btn btn-banner-white">Area Editor</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Floating Back to Top Button -->
    <button id="backToTopBtn" class="back-to-top-btn" title="Kembali ke atas" onclick="window.scrollTo({top: 0, behavior: 'smooth'});">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <polyline points="18 15 12 9 6 15"></polyline>
        </svg>
    </button>

    <!-- Clean Deep Forest Footer -->
    <footer class="main-footer">
        <div class="content-container">
            <div class="footer-top-row">
                <div class="footer-brand-col">
                    <div class="footer-logo">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                            <rect width="24" height="24" rx="6" fill="#144A39" />
                            <path d="M7 12L12 7L17 12L12 17L7 12Z" fill="#D2E823" />
                            <circle cx="12" cy="12" r="2.5" fill="#144A39" />
                        </svg>
                        <span>sekilasrv</span>
                    </div>
                    <p class="footer-bio">
                        Platform direktori dan promosi jasa editing video, grafis, dan motion kreatif.
                    </p>
                </div>

                <div class="footer-nav-col">
                    <strong class="col-title">Navigasi</strong>
                    <a href="../customer/informasi/kategori_jasa.php">Katalog Jasa</a>
                    <a href="../editor/auth/login.php">Portal Editor</a>
                    <a href="#fitur">Fitur Utama</a>
                    <a href="#peran">Pilih Peran</a>
                </div>

                <div class="footer-nav-col">
                    <strong class="col-title">Kategori</strong>
                    <a href="../customer/informasi/kategori_jasa.php?kategori=1">Video Editing</a>
                    <a href="../customer/informasi/kategori_jasa.php?kategori=2">Desain Grafis</a>
                    <a href="../customer/informasi/kategori_jasa.php?kategori=3">Color Grading</a>
                    <a href="../customer/informasi/kategori_jasa.php?kategori=4">Motion Graphics</a>
                </div>

                <div class="footer-nav-col">
                    <strong class="col-title">Tentang</strong>
                    <span>Project-Based Learning</span>
                    <span>Politeknik Negeri / PBL</span>
                    <span>Semester 3</span>
                </div>
            </div>

            <div class="footer-bottom-row">
                <span>© <?= date('Y') ?> sekilasrv. Seluruh hak cipta dilindungi.</span>
                <span class="footer-note">Didesain minimalis dan fungsional.</span>
            </div>
        </div>
    </footer>

    <!-- Interactive Scripts: Scroll Animations, Navbar State, Scrollspy & WhatsApp Generator -->
    <script>
        // Data Global Generator Brief
        let currentTargetWa = '6281234567890';

        function onServiceChange() {
            const select = document.getElementById('serviceSelect');
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            
            const price = opt.getAttribute('data-price') || 'Sesuai Diskusi';
            const cat = opt.getAttribute('data-category') || 'Layanan Editing';
            currentTargetWa = opt.getAttribute('data-wa') || '6281234567890';

            const catBadge = document.getElementById('serviceCategoryBadge');
            const priceBadge = document.getElementById('servicePriceBadge');
            const waDisplay = document.getElementById('targetWaDisplay');

            if (catBadge) catBadge.textContent = cat;
            if (priceBadge) priceBadge.textContent = (price.includes('Rp') || price.includes('Sesuai')) ? price : 'Mulai Rp ' + price;
            if (waDisplay) waDisplay.textContent = formatPhoneNumber(currentTargetWa);

            updateBriefPreview();
        }

        function formatPhoneNumber(num) {
            if (!num) return '+62 812-3456-7890';
            num = num.replace(/\D/g, '');
            if (num.startsWith('62')) {
                return '+62 ' + num.substring(2, 5) + '-' + num.substring(5, 9) + '-' + num.substring(9);
            }
            return '+' + num;
        }

        function getBriefData() {
            const clientName = document.getElementById('clientName')?.value.trim() || 'Klien sekilasrv';
            const serviceSelect = document.getElementById('serviceSelect');
            const selectedOpt = serviceSelect?.options[serviceSelect.selectedIndex];
            const serviceName = selectedOpt?.value || 'Editing Video / Konten';
            const price = selectedOpt?.getAttribute('data-price') || '';

            const formatRadio = document.querySelector('input[name="contentFormat"]:checked');
            const contentFormat = formatRadio ? formatRadio.value : 'Vertical (15-60s)';

            const deadlineRadio = document.querySelector('input[name="deadline"]:checked');
            const deadline = deadlineRadio ? deadlineRadio.value : '2 - 3 Hari';

            const rawLink = document.getElementById('rawLink')?.value.trim() || '- (Akan dikirim susulan via chat)';

            const prefs = [];
            if (document.getElementById('prefSubtitle')?.checked) prefs.push('Subtitle Dinamis / Pop-up Text');
            if (document.getElementById('prefSound')?.checked) prefs.push('Sound FX & BGM Bebas Hak Cipta');
            if (document.getElementById('prefColor')?.checked) prefs.push('Color Grading Sinematik');
            if (document.getElementById('prefThumb')?.checked) prefs.push('Desain Thumbnail Menarik');

            const customNotes = document.getElementById('customNotes')?.value.trim() || 'Tidak ada catatan tambahan.';

            return { clientName, serviceName, price, contentFormat, deadline, rawLink, prefs, customNotes };
        }

        function generateBriefText(data) {
            let text = `Halo Editor sekilasrv, saya ingin order / konsultasi jasa editing:\n\n`;
            text += `📋 *DETAIL BRIEF PROJECT*\n`;
            text += `• *Nama Klien / Brand*: ${data.clientName}\n`;
            text += `• *Layanan*: ${data.serviceName}\n`;
            text += `• *Format / Durasi*: ${data.contentFormat}\n`;
            text += `• *Target Deadline*: ${data.deadline}\n`;
            text += `• *Link Bahan / Referensi*: ${data.rawLink}\n\n`;

            if (data.prefs.length > 0) {
                text += `✨ *PREFERENSI TAMBAHAN*\n`;
                data.prefs.forEach(p => {
                    text += `• ✓ ${p}\n`;
                });
                text += `\n`;
            }

            text += `📝 *CATATAN KHUSUS*\n`;
            text += `${data.customNotes}\n\n`;
            text += `Apakah ada slot pengerjaan yang tersedia? Terima kasih! 🙏`;

            return text;
        }

        function updateBriefPreview() {
            const data = getBriefData();
            const previewContainer = document.getElementById('liveWaPreviewText');
            if (!previewContainer) return;

            let html = `<strong>Halo Editor sekilasrv</strong>, saya ingin order / konsultasi jasa editing:<br><br>`;
            html += `<span class="preview-section-title">📋 <strong>DETAIL BRIEF PROJECT</strong></span><br>`;
            html += `• <strong>Klien:</strong> <span class="pv-val">${escapeHtml(data.clientName)}</span><br>`;
            html += `• <strong>Layanan:</strong> <span class="pv-val">${escapeHtml(data.serviceName)}</span><br>`;
            html += `• <strong>Format:</strong> <span class="pv-val">${escapeHtml(data.contentFormat)}</span><br>`;
            html += `• <strong>Deadline:</strong> <span class="pv-val">${escapeHtml(data.deadline)}</span><br>`;
            html += `• <strong>Bahan:</strong> <span class="pv-val link-val">${escapeHtml(data.rawLink)}</span><br><br>`;

            if (data.prefs.length > 0) {
                html += `<span class="preview-section-title">✨ <strong>PREFERENSI</strong></span><br>`;
                data.prefs.forEach(p => {
                    html += `• <span class="pv-pill">✓ ${escapeHtml(p)}</span><br>`;
                });
                html += `<br>`;
            }

            html += `<span class="preview-section-title">📝 <strong>CATATAN KHUSUS</strong></span><br>`;
            html += `<em>"${escapeHtml(data.customNotes)}"</em><br><br>`;
            html += `Apakah ada slot pengerjaan yang tersedia? Terima kasih! 🙏`;

            previewContainer.innerHTML = html;
        }

        function escapeHtml(string) {
            return String(string).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function sendToWhatsApp() {
            const data = getBriefData();
            const text = generateBriefText(data);
            const cleanWa = currentTargetWa.replace(/\D/g, '');
            const waUrl = `https://wa.me/${cleanWa}?text=${encodeURIComponent(text)}`;
            window.open(waUrl, '_blank');
        }

        function copyBriefText() {
            const data = getBriefData();
            const text = generateBriefText(data);
            
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('btnCopyBrief');
                const label = document.getElementById('copyLabel');
                const icon = document.getElementById('copyIcon');
                
                if (btn && label && icon) {
                    btn.classList.add('copied');
                    label.textContent = '✓ Berhasil Disalin!';
                    icon.textContent = '✓';
                    
                    setTimeout(() => {
                        btn.classList.remove('copied');
                        label.textContent = 'Salin Teks Brief';
                        icon.textContent = '📋';
                    }, 2400);
                }
            }).catch(err => {
                alert('Teks brief: \n\n' + text);
            });
        }

        function resetBriefForm() {
            const form = document.getElementById('waBriefForm');
            if (form) {
                form.reset();
                onServiceChange();
                updateBriefPreview();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Inisialisasi generator
            onServiceChange();
            updateBriefPreview();

            // Jam realtime untuk preview bubble WhatsApp
            const timeEl = document.getElementById('previewCurrentTime');
            if (timeEl) {
                const now = new Date();
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                timeEl.textContent = `${hh}:${mm}`;
            }

            // 1. Scroll Reveal using IntersectionObserver
            const revealElements = document.querySelectorAll('.reveal-on-scroll');
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            obs.unobserve(entry.target);
                        }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

                revealElements.forEach(el => observer.observe(el));
            } else {
                revealElements.forEach(el => el.classList.add('is-revealed'));
            }

            // 2. Navbar Scroll Style & Back to Top Button Visibility
            const header = document.getElementById('mainHeader');
            const bttBtn = document.getElementById('backToTopBtn');

            window.addEventListener('scroll', function () {
                const scrollPos = window.scrollY || window.pageYOffset;

                if (scrollPos > 30) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }

                if (scrollPos > 400) {
                    bttBtn.classList.add('visible');
                } else {
                    bttBtn.classList.remove('visible');
                }
            });

            // 3. ScrollSpy for Navigation Items
            const sections = document.querySelectorAll('section[id]');
            const navItems = document.querySelectorAll('.nav-links .nav-item');

            window.addEventListener('scroll', function () {
                let current = '';
                sections.forEach(section => {
                    const sectionTop = section.offsetTop - 140;
                    if (window.pageYOffset >= sectionTop) {
                        current = section.getAttribute('id');
                    }
                });

                navItems.forEach(item => {
                    item.classList.remove('active');
                    if (item.getAttribute('href') === '#' + current) {
                        item.classList.add('active');
                    }
                });
            });
        });
    </script>
</body>

</html>