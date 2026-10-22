<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../dashboard.php");
    exit;
}

try {
    // Cek apakah kategori digunakan
    $check = $pdo->prepare("SELECT COUNT(*) FROM detail_jasa WHERE id_kategori_jasa = :id");
    $check->execute(['id' => $id]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        // Hapus detail_jasa terkait jika schema CASCADE atau hapus manual
        $delJasa = $pdo->prepare("DELETE FROM detail_jasa WHERE id_kategori_jasa = :id");
        $delJasa->execute(['id' => $id]);
    }

    $stmt = $pdo->prepare("DELETE FROM kategori_jasa WHERE id_kategori_jasa = :id");
    $stmt->execute(['id' => $id]);

    header("Location: ../dashboard.php?view=kategori&msg=" . urlencode("Kategori dan layanan terkait berhasil dihapus!"));
    exit;
} catch (PDOException $e) {
    header("Location: ../dashboard.php?view=kategori&err=" . urlencode("Gagal menghapus kategori: " . $e->getMessage()));
    exit;
}
?>
