<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id_kategori_jasa'] ?? 0);
    $nama_kategori = trim($_POST['nama_kategori'] ?? '');

    if ($id <= 0 || empty($nama_kategori)) {
        header("Location: ../dashboard.php?err=" . urlencode("Data kategori tidak lengkap"));
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE kategori_jasa SET nama_kategori = :nama WHERE id_kategori_jasa = :id");
        $stmt->execute(['nama' => $nama_kategori, 'id' => $id]);

        header("Location: ../dashboard.php?view=kategori&msg=" . urlencode("Kategori berhasil diperbarui!"));
        exit;
    } catch (PDOException $e) {
        header("Location: ../dashboard.php?view=kategori&err=" . urlencode("Gagal memperbarui kategori: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../dashboard.php?view=kategori");
    exit;
}
?>
