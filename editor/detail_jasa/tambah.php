<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

// Ambil semua kategori untuk dropdown
$stmtKat = $pdo->query("SELECT * FROM kategori_jasa ORDER BY nama_kategori ASC");
$kategori_list = $stmtKat->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Layanan Jasa — sekilasrv</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tampilan/detail_jasa.css">
</head>

<body>
    <main class="form-container">
        <div class="form-card">
            <div class="card-header">
                <a href="../dashboard.php" class="back-link">← Kembali ke Dashboard</a>
                <h1 class="title">Tambah Layanan Jasa Editing</h1>
                <p class="subtitle">Publikasikan penawaran jasa baru beserta portofolionya</p>
            </div>

            <form action="proses_tambah.php" method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group span-2">
                        <label for="nama_jasa">Nama Layanan Jasa <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="nama_jasa" 
                            name="nama_jasa" 
                            placeholder="Contoh: Editing Video YouTube Sinematik & Vlogging" 
                            required 
                            autofocus>
                    </div>

                    <div class="form-group">
                        <label for="id_kategori_jasa">Kategori Jasa <span class="required">*</span></label>
                        <select id="id_kategori_jasa" name="id_kategori_jasa" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php foreach ($kategori_list as $kat): ?>
                                <option value="<?= $kat['id_kategori_jasa'] ?>">
                                    <?= htmlspecialchars($kat['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="harga">Harga Mulai Dari (Rp) <span class="required">*</span></label>
                        <input 
                            type="number" 
                            id="harga" 
                            name="harga" 
                            placeholder="Contoh: 150000" 
                            min="0" 
                            step="1000" 
                            required>
                    </div>

                    <div class="form-group span-2">
                        <label for="deskripsi">Deskripsi Lengkap Jasa <span class="required">*</span></label>
                        <textarea 
                            id="deskripsi" 
                            name="deskripsi" 
                            rows="5" 
                            placeholder="Jelaskan apa saja yang didapatkan klien, durasi, jumlah revisi, dan keunggulan jasa Anda..." 
                            required></textarea>
                    </div>

                    <div class="form-group span-2">
                        <label for="gambar">Foto / Gambar Portofolio (Opsional)</label>
                        <div class="file-upload-box">
                            <input 
                                type="file" 
                                id="gambar" 
                                name="gambar" 
                                accept="image/jpeg,image/png,image/webp,image/jpg">
                            <small class="help-text">Format didukung: JPG, PNG, WEBP. Maksimal ukuran 5MB.</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan & Publikasikan Jasa</button>
                    <a href="../dashboard.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
