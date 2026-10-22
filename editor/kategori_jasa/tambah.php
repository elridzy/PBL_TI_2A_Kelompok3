<?php
require_once __DIR__ . '/../auth/auth_check.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kategori Jasa — sekilasrv</title>
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
                <h1 class="title">Tambah Kategori Jasa</h1>
                <p class="subtitle">Buat kelompok kategori baru untuk layanan editing</p>
            </div>

            <form action="proses_tambah.php" method="POST">
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="nama_kategori" 
                        name="nama_kategori" 
                        placeholder="Contoh: Motion Graphics, 3D Modeling" 
                        required 
                        autofocus>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan Kategori</button>
                    <a href="../dashboard.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>
</body>

</html>
