<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: ../dashboard.php");
    exit;
}

// Ambil detail jasa
$stmt = $pdo->prepare("SELECT * FROM detail_jasa WHERE id_detail_jasa = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$jasa = $stmt->fetch();

if (!$jasa) {
    header("Location: ../dashboard.php?err=" . urlencode("Data jasa tidak ditemukan"));
    exit;
}

// Ambil kategori untuk dropdown
$stmtKat = $pdo->query("SELECT * FROM kategori_jasa ORDER BY nama_kategori ASC");
$kategori_list = $stmtKat->fetchAll();

// Ambil portofolio gambar pertama jika ada
$stmtPort = $pdo->prepare("SELECT * FROM portofolio WHERE id_detail_jasa = :id ORDER BY id_portofolio ASC LIMIT 1");
$stmtPort->execute(['id' => $id]);
$portofolio = $stmtPort->fetch();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Layanan Jasa — sekilasrv</title>
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
                <h1 class="title">Edit Layanan Jasa</h1>
                <p class="subtitle">Perbarui informasi dan spesifikasi penawaran jasa</p>
            </div>

            <form action="proses_edit.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_detail_jasa" value="<?= $jasa['id_detail_jasa'] ?>">

                <div class="form-grid">
                    <div class="form-group span-2">
                        <label for="nama_jasa">Nama Layanan Jasa <span class="required">*</span></label>
                        <input 
                            type="text" 
                            id="nama_jasa" 
                            name="nama_jasa" 
                            value="<?= htmlspecialchars($jasa['nama_jasa']) ?>" 
                            required>
                    </div>

                    <div class="form-group">
                        <label for="id_kategori_jasa">Kategori Jasa <span class="required">*</span></label>
                        <select id="id_kategori_jasa" name="id_kategori_jasa" required>
                            <?php foreach ($kategori_list as $kat): ?>
                                <option value="<?= $kat['id_kategori_jasa'] ?>" <?= $kat['id_kategori_jasa'] == $jasa['id_kategori_jasa'] ? 'selected' : '' ?>>
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
                            value="<?= $jasa['harga'] ?>" 
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
                            required><?= htmlspecialchars($jasa['deskripsi']) ?></textarea>
                    </div>

                    <div class="form-group span-2">
                        <label for="gambar">Ganti Foto Portofolio (Opsional)</label>
                        <?php if ($portofolio && file_exists(__DIR__ . '/../../uploads/' . $portofolio['gambar'])): ?>
                            <div class="current-img-preview">
                                <img src="../../uploads/<?= htmlspecialchars($portofolio['gambar']) ?>" alt="Preview">
                                <span>Foto saat ini</span>
                            </div>
                        <?php endif; ?>
                        <div class="file-upload-box">
                            <input 
                                type="file" 
                                id="gambar" 
                                name="gambar" 
                                accept="image/jpeg,image/png,image/webp,image/jpg">
                            <small class="help-text">Biarkan kosong jika tidak ingin mengubah foto portofolio.</small>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan Perubahan</button>
                    <a href="../dashboard.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
