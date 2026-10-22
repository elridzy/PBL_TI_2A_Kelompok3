<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: ../dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM kategori_jasa WHERE id_kategori_jasa = :id LIMIT 1");
$stmt->execute(['id' => $id]);
$kat = $stmt->fetch();

if (!$kat) {
    header("Location: ../dashboard.php?err=" . urlencode("Kategori tidak ditemukan"));
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori Jasa — sekilasrv</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tampilan/kategori_jasa.css">
</head>

<body>
    <main class="form-container">
        <div class="form-card">
            <div class="card-header">
                <a href="../dashboard.php" class="back-link">← Kembali ke Dashboard</a>
                <h1 class="title">Edit Kategori Jasa</h1>
                <p class="subtitle">Perbarui nama kategori layanan editing</p>
            </div>

            <form action="proses_edit.php" method="POST">
                <input type="hidden" name="id_kategori_jasa" value="<?= $kat['id_kategori_jasa'] ?>">

                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="nama_kategori" 
                        name="nama_kategori" 
                        value="<?= htmlspecialchars($kat['nama_kategori']) ?>" 
                        required 
                        autofocus>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Perbarui Kategori</button>
                    <a href="../dashboard.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
