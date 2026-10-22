<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id_detail_jasa'] ?? 0);
    $id_kategori_jasa = (int)($_POST['id_kategori_jasa'] ?? 0);
    $nama_jasa = trim($_POST['nama_jasa'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);

    if ($id <= 0 || $id_kategori_jasa <= 0 || empty($nama_jasa) || empty($deskripsi) || $harga < 0) {
        header("Location: ../dashboard.php?err=" . urlencode("Data tidak lengkap untuk diperbarui"));
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Update detail_jasa
        $stmt = $pdo->prepare("
            UPDATE detail_jasa 
            SET id_kategori_jasa = :id_kat, nama_jasa = :nama, deskripsi = :deskripsi, harga = :harga
            WHERE id_detail_jasa = :id
        ");
        $stmt->execute([
            'id_kat' => $id_kategori_jasa,
            'nama' => $nama_jasa,
            'deskripsi' => $deskripsi,
            'harga' => $harga,
            'id' => $id
        ]);

        // 2. Jika ada file upload gambar baru
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['gambar']['tmp_name'];
            $fileName = $_FILES['gambar']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = __DIR__ . '/../../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = 'jasa_' . $id . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Cek apakah sudah ada portofolio sebelumnya
                    $checkPort = $pdo->prepare("SELECT id_portofolio, gambar FROM portofolio WHERE id_detail_jasa = :id LIMIT 1");
                    $checkPort->execute(['id' => $id]);
                    $existingPort = $checkPort->fetch();

                    if ($existingPort) {
                        // Hapus file lama jika ada
                        $oldFile = $uploadDir . $existingPort['gambar'];
                        if (file_exists($oldFile) && is_file($oldFile)) {
                            unlink($oldFile);
                        }

                        $updatePort = $pdo->prepare("UPDATE portofolio SET gambar = :gambar WHERE id_portofolio = :id_p");
                        $updatePort->execute(['gambar' => $newFileName, 'id_p' => $existingPort['id_portofolio']]);
                    } else {
                        $insertPort = $pdo->prepare("INSERT INTO portofolio (id_detail_jasa, gambar) VALUES (:id, :gambar)");
                        $insertPort->execute(['id' => $id, 'gambar' => $newFileName]);
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: ../dashboard.php?view=jasa&msg=" . urlencode("Layanan jasa '{$nama_jasa}' berhasil diperbarui!"));
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        header("Location: ../dashboard.php?view=jasa&err=" . urlencode("Gagal memperbarui jasa: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../dashboard.php?view=jasa");
    exit;
}
?>
