<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if (empty($nama_kategori)) {
        header("Location: tambah.php?err=" . urlencode("Nama kategori tidak boleh kosong"));
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO kategori_jasa (nama_kategori) VALUES (:nama)");
        $stmt->execute(['nama' => $nama_kategori]);

        header("Location: ../dashboard.php?view=kategori&msg=" . urlencode("Kategori '{$nama_kategori}' berhasil ditambahkan!"));
        exit;
    } catch (PDOException $e) {
        header("Location: ../dashboard.php?view=kategori&err=" . urlencode("Gagal menambah kategori: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../dashboard.php?view=kategori");
    exit;
}
?>
