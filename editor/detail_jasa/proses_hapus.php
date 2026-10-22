<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: ../dashboard.php");
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Ambil nama file portofolio terkait untuk dihapus fisiknya dari uploads/
    $stmtPort = $pdo->prepare("SELECT gambar FROM portofolio WHERE id_detail_jasa = :id");
    $stmtPort->execute(['id' => $id]);
    $files = $stmtPort->fetchAll();

    foreach ($files as $f) {
        $filePath = __DIR__ . '/../../uploads/' . $f['gambar'];
        if (file_exists($filePath) && is_file($filePath)) {
            unlink($filePath);
        }
    }

    // 2. Hapus dari tabel portofolio
    $delPort = $pdo->prepare("DELETE FROM portofolio WHERE id_detail_jasa = :id");
    $delPort->execute(['id' => $id]);

    // 3. Hapus dari tabel detail_jasa
    $delJasa = $pdo->prepare("DELETE FROM detail_jasa WHERE id_detail_jasa = :id");
    $delJasa->execute(['id' => $id]);

    $pdo->commit();
    header("Location: ../dashboard.php?view=jasa&msg=" . urlencode("Layanan jasa berhasil dihapus!"));
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    header("Location: ../dashboard.php?view=jasa&err=" . urlencode("Gagal menghapus jasa: " . $e->getMessage()));
    exit;
}
?>
