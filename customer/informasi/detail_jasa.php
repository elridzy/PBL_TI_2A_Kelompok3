<?php
require_once __DIR__ . '/../../database/koneksi_psql.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: kategori_jasa.php");
    exit;
}

// Ambil detail jasa
$stmt = $pdo->prepare("
    SELECT dj.*, kj.nama_kategori, e.no_wa
    FROM detail_jasa dj
    JOIN kategori_jasa kj ON dj.id_kategori_jasa = kj.id_kategori_jasa
    JOIN editor e ON dj.id_editor = e.id_editor
    WHERE dj.id_detail_jasa = :id
    LIMIT 1
");
$stmt->execute(['id' => $id]);
$jasa = $stmt->fetch();

if (!$jasa) {
    header("Location: kategori_jasa.php");
    exit;
}

// Ambil semua portofolio gambar untuk jasa ini
$stmtPort = $pdo->prepare("SELECT * FROM portofolio WHERE id_detail_jasa = :id ORDER BY id_portofolio ASC");
$stmtPort->execute(['id' => $id]);
$portofolio_list = $stmtPort->fetchAll();

// Ambil jasa terkait dalam kategori yang sama
$stmtRelated = $pdo->prepare("
    SELECT dj.*, kj.nama_kategori, p.gambar
    FROM detail_jasa dj
    JOIN kategori_jasa kj ON dj.id_kategori_jasa = kj.id_kategori_jasa
    LEFT JOIN (
        SELECT DISTINCT ON (id_detail_jasa) id_detail_jasa, gambar 
        FROM portofolio 
        ORDER BY id_detail_jasa, id_portofolio ASC
    ) p ON dj.id_detail_jasa = p.id_detail_jasa
    WHERE dj.id_kategori_jasa = :id_kat AND dj.id_detail_jasa != :id
    ORDER BY dj.id_detail_jasa DESC
    LIMIT 3
");
$stmtRelated->execute(['id_kat' => $jasa['id_kategori_jasa'], 'id' => $id]);
$related_list = $stmtRelated->fetchAll();

// Format WhatsApp URL
$wa_clean = preg_replace('/[^0-9]/', '', $jasa['no_wa']);
if (strpos($wa_clean, '0') === 0) {
    $wa_clean = '62' . substr($wa_clean, 1);
}
$wa_message = "Halo Editor sekilasrv, saya tertarik untuk memesan jasa editing: *" . $jasa['nama_jasa'] . "* (Rp " . number_format($jasa['harga'], 0, ',', '.') . "). Mohon info ketersediaan dan detailnya.";
$wa_url = "https://wa.me/" . $wa_clean . "?text=" . urlencode($wa_message);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($jasa['nama_jasa']) ?> — sekilasrv</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tampilan/detail_jasa.css">
</head>

<body>
    <!-- Top Navbar -->
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
                <a href="kategori_jasa.php" class="btn btn-login">← Kembali ke Katalog</a>
                <a href="<?= $wa_url ?>" target="_blank" class="btn btn-primary-header">Chat Editor di WA</a>
            </div>
        </div>
    </header>

    <main class="main-wrapper">
        <!-- Breadcrumb -->
        <nav class="breadcrumb">
            <a href="../../beranda/beranda.php">Beranda</a>
            <span class="separator">/</span>
            <a href="kategori_jasa.php">Katalog Jasa</a>
            <span class="separator">/</span>
            <a href="kategori_jasa.php?kategori=<?= $jasa['id_kategori_jasa'] ?>"><?= htmlspecialchars($jasa['nama_kategori']) ?></a>
            <span class="separator">/</span>
            <span class="current"><?= htmlspecialchars($jasa['nama_jasa']) ?></span>
        </nav>

        <div class="detail-layout">
            <!-- Left Column: Media & Description -->
            <div class="detail-main">
                <!-- Gallery Showcase -->
                <div class="gallery-card">
                    <div class="main-image-container">
                        <?php 
                        $first_img = !empty($portofolio_list) ? $portofolio_list[0]['gambar'] : '';
                        $img_path = !empty($first_img) ? '../../uploads/' . $first_img : '';
                        if (!empty($first_img) && file_exists(__DIR__ . '/' . $img_path)): 
                        ?>
                            <img id="mainImage" src="<?= htmlspecialchars($img_path) ?>" alt="<?= htmlspecialchars($jasa['nama_jasa']) ?>">
                        <?php else: ?>
                            <div class="media-placeholder">
                                <div class="media-icon">🎬</div>
                                <span>Preview Portofolio Karya Jasa</span>
                            </div>
                        <?php endif; ?>
                        <span class="badge-category"><?= htmlspecialchars($jasa['nama_kategori']) ?></span>
                    </div>

                    <?php if (count($portofolio_list) > 1): ?>
                        <div class="thumbnail-list">
                            <?php foreach ($portofolio_list as $idx => $p): ?>
                                <?php $thumb_path = '../../uploads/' . $p['gambar']; ?>
                                <?php if (file_exists(__DIR__ . '/' . $thumb_path)): ?>
                                    <button type="button" class="thumb-btn <?= $idx === 0 ? 'active' : '' ?>" onclick="changeImage('<?= htmlspecialchars($thumb_path) ?>', this)">
                                        <img src="<?= htmlspecialchars($thumb_path) ?>" alt="Portofolio <?= $idx + 1 ?>">
                                    </button>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Detail Content Card -->
                <div class="info-card">
                    <h1 class="service-title"><?= htmlspecialchars($jasa['nama_jasa']) ?></h1>
                    
                    <div class="meta-row">
                        <span class="meta-item">
                            📁 Kategori: <strong><?= htmlspecialchars($jasa['nama_kategori']) ?></strong>
                        </span>
                        <span class="meta-item">
                            📅 Diupload: <strong><?= date('d M Y', strtotime($jasa['tanggal_upload'])) ?></strong>
                        </span>
                    </div>

                    <div class="desc-section">
                        <h3>Rincian & Cakupan Layanan</h3>
                        <div class="desc-text">
                            <?= nl2br(htmlspecialchars($jasa['deskripsi'])) ?>
                        </div>
                    </div>

                    <div class="features-grid">
                        <div class="feature-box">
                            <span class="feature-icon">⚡</span>
                            <div>
                                <strong>Pengerjaan Tepat Waktu</strong>
                                <p>Sesuai kesepakatan jadwal Anda</p>
                            </div>
                        </div>
                        <div class="feature-box">
                            <span class="feature-icon">🔄</span>
                            <div>
                                <strong>Jaminan Revisi</strong>
                                <p>Menjamin kecocokan gaya visual</p>
                            </div>
                        </div>
                        <div class="feature-box">
                            <span class="feature-icon">💬</span>
                            <div>
                                <strong>Konsultasi WhatsApp</strong>
                                <p>Bebas diskusi konsep sebelum order</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sticky Pricing & Action -->
            <div class="detail-sidebar">
                <div class="sticky-action-card">
                    <div class="card-tag">TARIF JASA TERTERA</div>
                    <div class="price-wrap">
                        <span class="price-prefix">Mulai Dari</span>
                        <h2 class="price-total">Rp <?= number_format($jasa['harga'], 0, ',', '.') ?></h2>
                    </div>

                    <div class="action-divider"></div>

                    <div class="editor-info-box">
                        <div class="editor-avatar">✍️</div>
                        <div class="editor-text">
                            <strong>Editor Terdaftar</strong>
                            <span>Kontak: <?= htmlspecialchars($jasa['no_wa']) ?></span>
                        </div>
                    </div>

                    <button type="button" class="btn-brief-generator" onclick="openBriefModal()">
                        <span class="btn-flash">⚡</span>
                        <span>Susun Brief Pesanan Rapi</span>
                    </button>

                    <a href="<?= $wa_url ?>" target="_blank" rel="noopener noreferrer" class="btn-whatsapp">
                        <span>Chat Editor Langsung</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>

                    <p class="wa-note">
                        💡 Gunakan <strong>Brief Otomatis</strong> untuk menyiapkan pesan rapi berisi link bahan, deadline, dan preferensi sebelum chat ke WhatsApp.
                    </p>

                    <div class="card-guarantee">
                        <div class="guarantee-item">✓ File Master Resolusi Tinggi (1080p / 4K)</div>
                        <div class="guarantee-item">✓ Bebas Konsultasi Ide & Referensi</div>
                        <div class="guarantee-item">✓ Transaksi Langsung Tanpa Potongan Platform</div>
                    </div>

                    <a href="kategori_jasa.php" class="btn-back">
                        ← Kembali ke Katalog Jasa
                    </a>
                </div>
            </div>
        </div>

        <!-- Related Services -->
        <?php if (!empty($related_list)): ?>
            <section class="related-section">
                <h3 class="related-title">Layanan Terkait dalam Kategori <span><?= htmlspecialchars($jasa['nama_kategori']) ?></span></h3>
                <div class="related-grid">
                    <?php foreach ($related_list as $rel): ?>
                        <div class="related-card">
                            <div class="rel-thumb">
                                <?php 
                                $rimg = !empty($rel['gambar']) ? '../../uploads/' . $rel['gambar'] : '';
                                if (!empty($rel['gambar']) && file_exists(__DIR__ . '/' . $rimg)): 
                                ?>
                                    <img src="<?= htmlspecialchars($rimg) ?>" alt="<?= htmlspecialchars($rel['nama_jasa']) ?>">
                                <?php else: ?>
                                    <div class="rel-placeholder">🎬</div>
                                <?php endif; ?>
                            </div>
                            <div class="rel-body">
                                <h4><a href="detail_jasa.php?id=<?= $rel['id_detail_jasa'] ?>"><?= htmlspecialchars($rel['nama_jasa']) ?></a></h4>
                                <span class="rel-price">Rp <?= number_format($rel['harga'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
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

    <!-- Modal Generator Brief Pesanan (In-Page Zero Jump) -->
    <div id="modalBriefDetail" class="brief-modal-overlay" onclick="handleModalBackdropClick(event)">
        <div class="brief-modal-container">
            <!-- Modal Header -->
            <div class="brief-modal-header">
                <div class="header-titles">
                    <span class="modal-badge">⚡ GENERATOR BRIEF CEPAT</span>
                    <h2 class="modal-service-title"><?= htmlspecialchars($jasa['nama_jasa']) ?></h2>
                    <p class="modal-service-meta">Tarif Tertera: <strong>Rp <?= number_format($jasa['harga'], 0, ',', '.') ?></strong> • Editor WA: <strong><?= htmlspecialchars($jasa['no_wa']) ?></strong></p>
                </div>
                <button type="button" class="btn-close-modal" onclick="closeBriefModal()" title="Tutup Modal">&times;</button>
            </div>

            <!-- Modal Grid -->
            <div class="brief-modal-body">
                <!-- Left: Form Input -->
                <div class="modal-form-col">
                    <div class="form-group-modal">
                        <label class="modal-label">Nama Anda / Brand <span class="req">*</span></label>
                        <input type="text" id="dtlClientName" class="modal-input" placeholder="Misal: Rian Pratama (Studio Sembilan)" value="Rian Pratama" oninput="updateDetailBrief()">
                    </div>

                    <div class="form-group-modal">
                        <label class="modal-label">Format / Durasi Konten</label>
                        <div class="modal-pills-row">
                            <label class="m-pill">
                                <input type="radio" name="dtlFormat" value="Vertical 9:16 (Shorts/Reels/TikTok 15-60s)" checked onchange="updateDetailBrief()">
                                <span>Vertical (15-60s)</span>
                            </label>
                            <label class="m-pill">
                                <input type="radio" name="dtlFormat" value="Landscape 16:9 (YouTube 5-15 Menit)" onchange="updateDetailBrief()">
                                <span>Landscape (5-15m)</span>
                            </label>
                            <label class="m-pill">
                                <input type="radio" name="dtlFormat" value="Desain Visual / Banner / Feed" onchange="updateDetailBrief()">
                                <span>Desain Visual</span>
                            </label>
                            <label class="m-pill">
                                <input type="radio" name="dtlFormat" value="Custom / Longform" onchange="updateDetailBrief()">
                                <span>Custom</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-modal">
                        <label class="modal-label">Target Deadline Pengerjaan</label>
                        <div class="modal-pills-row">
                            <label class="m-pill">
                                <input type="radio" name="dtlDeadline" value="< 24 Jam (Prioritas Kilat)" onchange="updateDetailBrief()">
                                <span>⚡ < 24 Jam (Kilat)</span>
                            </label>
                            <label class="m-pill">
                                <input type="radio" name="dtlDeadline" value="2 - 3 Hari (Standar)" checked onchange="updateDetailBrief()">
                                <span>2 - 3 Hari (Standar)</span>
                            </label>
                            <label class="m-pill">
                                <input type="radio" name="dtlDeadline" value="Fleksibel (~1 Minggu)" onchange="updateDetailBrief()">
                                <span>Fleksibel (~1 Minggu)</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-modal">
                        <label class="modal-label">Link File Mentah / Contoh Referensi Gaya</label>
                        <input type="text" id="dtlRawLink" class="modal-input" placeholder="https://drive.google.com/drive/folders/..." value="https://drive.google.com/drive/folders/footage-project" oninput="updateDetailBrief()">
                        <span class="m-hint">Sertakan link folder Google Drive, Dropbox, atau video referensi.</span>
                    </div>

                    <div class="form-group-modal">
                        <label class="modal-label">Preferensi Tambahan</label>
                        <div class="modal-checks-grid">
                            <label class="m-check">
                                <input type="checkbox" id="dtlPrefSubtitle" checked onchange="updateDetailBrief()">
                                <span>Subtitle Dinamis / Pop-up Text</span>
                            </label>
                            <label class="m-check">
                                <input type="checkbox" id="dtlPrefSound" checked onchange="updateDetailBrief()">
                                <span>Sound FX & Musik Bebas Copyright</span>
                            </label>
                            <label class="m-check">
                                <input type="checkbox" id="dtlPrefColor" checked onchange="updateDetailBrief()">
                                <span>Color Grading Sinematik</span>
                            </label>
                            <label class="m-check">
                                <input type="checkbox" id="dtlPrefThumb" onchange="updateDetailBrief()">
                                <span>Desain Cover / Thumbnail</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group-modal">
                        <label class="modal-label">Catatan Tambahan untuk Editor (Opsional)</label>
                        <textarea id="dtlNotes" class="modal-textarea" rows="2" placeholder="Misal: Pacing cepat, cut jeda nafas, sound effect pop-up subtitle." oninput="updateDetailBrief()">Tolong fokuskan pacing cepat di 3 detik awal hook, subtitle dinamis warna lime/kuning.</textarea>
                    </div>
                </div>

                <!-- Right: WhatsApp Chat Simulation Preview -->
                <div class="modal-preview-col">
                    <div class="chat-preview-box">
                        <div class="chat-bar-top">
                            <div class="c-avatar">✍️</div>
                            <div class="c-info">
                                <strong>Editor sekilasrv</strong>
                                <span>Online • <?= htmlspecialchars($jasa['no_wa']) ?></span>
                            </div>
                        </div>

                        <div class="chat-screen">
                            <div class="c-bubble-incoming">
                                Halo! Saya editor untuk layanan <strong><?= htmlspecialchars($jasa['nama_jasa']) ?></strong>. Silakan kirimkan detail pesanan Anda ya 😊
                            </div>

                            <div class="c-bubble-outgoing">
                                <div id="dtlLivePreviewText" class="c-brief-text">
                                    <!-- Dynamic text populated by JS -->
                                </div>
                                <div class="c-meta">
                                    <span>Baru saja</span>
                                    <span class="c-check">✓✓</span>
                                </div>
                            </div>
                        </div>

                        <div class="chat-bar-bottom">
                            <button type="button" class="btn-send-dtl-wa" onclick="sendDetailBriefToWhatsApp()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.04 14.69 2 12.04 2M12.05 3.67C14.25 3.67 16.31 4.53 17.87 6.09C19.42 7.65 20.28 9.72 20.28 11.92C20.28 16.46 16.58 20.16 12.04 20.16C10.66 20.16 9.3 19.79 8.12 19.09L7.84 18.92L4.72 19.74L5.55 16.7L5.36 16.4C4.55 15.11 4.12 13.53 4.12 11.91C4.12 7.37 7.82 3.67 12.05 3.67Z"/>
                                </svg>
                                <span>Kirim ke WhatsApp Sekarang</span>
                            </button>

                            <div class="chat-sub-actions">
                                <button type="button" class="btn-copy-dtl" id="btnCopyDtl" onclick="copyDetailBriefText()">
                                    <span id="dtlCopyIcon">📋</span> <span id="dtlCopyLabel">Salin Teks Brief</span>
                                </button>
                                <button type="button" class="btn-close-dtl" onclick="closeBriefModal()">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const SERVICE_NAME = <?= json_encode($jasa['nama_jasa']) ?>;
        const SERVICE_PRICE = <?= json_encode('Rp ' . number_format($jasa['harga'], 0, ',', '.')) ?>;
        const EDITOR_WA = <?= json_encode($jasa['no_wa']) ?>;

        function changeImage(src, btn) {
            const mainImg = document.getElementById('mainImage');
            if (mainImg) {
                mainImg.src = src;
            }
            document.querySelectorAll('.thumb-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        function openBriefModal() {
            const modal = document.getElementById('modalBriefDetail');
            if (modal) {
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
                updateDetailBrief();
            }
        }

        function closeBriefModal() {
            const modal = document.getElementById('modalBriefDetail');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }

        function handleModalBackdropClick(e) {
            if (e.target && e.target.classList.contains('brief-modal-overlay')) {
                closeBriefModal();
            }
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeBriefModal();
            }
        });

        function getDetailBriefData() {
            const clientName = document.getElementById('dtlClientName')?.value.trim() || 'Klien sekilasrv';
            const formatRadio = document.querySelector('input[name="dtlFormat"]:checked');
            const format = formatRadio ? formatRadio.value : 'Vertical 9:16 (15-60s)';

            const deadlineRadio = document.querySelector('input[name="dtlDeadline"]:checked');
            const deadline = deadlineRadio ? deadlineRadio.value : '2 - 3 Hari';

            const rawLink = document.getElementById('dtlRawLink')?.value.trim() || '- (Akan dikirim via chat)';

            const prefs = [];
            if (document.getElementById('dtlPrefSubtitle')?.checked) prefs.push('Subtitle Dinamis / Pop-up Text');
            if (document.getElementById('dtlPrefSound')?.checked) prefs.push('Sound FX & BGM Bebas Hak Cipta');
            if (document.getElementById('dtlPrefColor')?.checked) prefs.push('Color Grading Sinematik');
            if (document.getElementById('dtlPrefThumb')?.checked) prefs.push('Desain Thumbnail Menarik');

            const notes = document.getElementById('dtlNotes')?.value.trim() || 'Tidak ada catatan tambahan.';

            return { clientName, format, deadline, rawLink, prefs, notes };
        }

        function buildDetailBriefText(data) {
            let t = `Halo Editor sekilasrv, saya ingin order jasa editing:\n\n`;
            t += `📋 *DETAIL BRIEF PESANAN*\n`;
            t += `• *Nama Klien / Brand*: ${data.clientName}\n`;
            t += `• *Layanan*: ${SERVICE_NAME} (${SERVICE_PRICE})\n`;
            t += `• *Format Konten*: ${data.format}\n`;
            t += `• *Target Deadline*: ${data.deadline}\n`;
            t += `• *Link Bahan / Referensi*: ${data.rawLink}\n\n`;

            if (data.prefs.length > 0) {
                t += `✨ *PREFERENSI TAMBAHAN*\n`;
                data.prefs.forEach(p => {
                    t += `• ✓ ${p}\n`;
                });
                t += `\n`;
            }

            t += `📝 *CATATAN KHUSUS*\n`;
            t += `${data.notes}\n\n`;
            t += `Apakah ada slot pengerjaan yang tersedia? Terima kasih! 🙏`;
            return t;
        }

        function updateDetailBrief() {
            const data = getDetailBriefData();
            const container = document.getElementById('dtlLivePreviewText');
            if (!container) return;

            let h = `<strong>Halo Editor sekilasrv</strong>, saya ingin order jasa editing:<br><br>`;
            h += `<span class="pv-tag">📋 <strong>DETAIL BRIEF PESANAN</strong></span><br>`;
            h += `• <strong>Klien:</strong> <span class="pv-val">${escapeHtml(data.clientName)}</span><br>`;
            h += `• <strong>Layanan:</strong> <span class="pv-val">${escapeHtml(SERVICE_NAME)} (${escapeHtml(SERVICE_PRICE)})</span><br>`;
            h += `• <strong>Format:</strong> <span class="pv-val">${escapeHtml(data.format)}</span><br>`;
            h += `• <strong>Deadline:</strong> <span class="pv-val">${escapeHtml(data.deadline)}</span><br>`;
            h += `• <strong>Bahan:</strong> <span class="pv-val pv-link">${escapeHtml(data.rawLink)}</span><br><br>`;

            if (data.prefs.length > 0) {
                h += `<span class="pv-tag">✨ <strong>PREFERENSI</strong></span><br>`;
                data.prefs.forEach(p => {
                    h += `• <span class="pv-chip">✓ ${escapeHtml(p)}</span><br>`;
                });
                h += `<br>`;
            }

            h += `<span class="pv-tag">📝 <strong>CATATAN KHUSUS</strong></span><br>`;
            h += `<em>"${escapeHtml(data.notes)}"</em><br><br>`;
            h += `Apakah ada slot pengerjaan yang tersedia? Terima kasih! 🙏`;

            container.innerHTML = h;
        }

        function escapeHtml(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }

        function sendDetailBriefToWhatsApp() {
            const data = getDetailBriefData();
            const text = buildDetailBriefText(data);
            const cleanWa = EDITOR_WA.replace(/\D/g, '');
            const url = `https://wa.me/${cleanWa}?text=${encodeURIComponent(text)}`;
            window.open(url, '_blank');
        }

        function copyDetailBriefText() {
            const data = getDetailBriefData();
            const text = buildDetailBriefText(data);
            navigator.clipboard.writeText(text).then(() => {
                const btn = document.getElementById('btnCopyDtl');
                const label = document.getElementById('dtlCopyLabel');
                const icon = document.getElementById('dtlCopyIcon');
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
            }).catch(e => {
                alert('Teks brief: \n\n' + text);
            });
        }
    </script>
</body>

</html>
