<?php
require_once __DIR__ . '/../auth/auth_check.php';
require_once __DIR__ . '/../../database/koneksi_psql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $editor_id = (int)($_SESSION['editor_id'] ?? 1);
    $id_kategori_jasa = (int)($_POST['id_kategori_jasa'] ?? 0);
    $nama_jasa = trim($_POST['nama_jasa'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga = (int)($_POST['harga'] ?? 0);

    if ($id_kategori_jasa <= 0 || empty($nama_jasa) || empty($deskripsi) || $harga < 0) {
        header("Location: tambah.php?err=" . urlencode("Mohon lengkapi semua field yang wajib diisi!"));
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Insert detail_jasa
        $stmt = $pdo->prepare("
            INSERT INTO detail_jasa (id_kategori_jasa, id_editor, nama_jasa, deskripsi, harga, tanggal_upload)
            VALUES (:id_kat, :id_editor, :nama, :deskripsi, :harga, CURRENT_DATE)
            RETURNING id_detail_jasa
        ");
        $stmt->execute([
            'id_kat' => $id_kategori_jasa,
            'id_editor' => $editor_id,
            'nama' => $nama_jasa,
            'deskripsi' => $deskripsi,
            'harga' => $harga
        ]);
        $id_detail_jasa = $stmt->fetchColumn();

        // 2. Handle upload portofolio gambar jika ada
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

                $newFileName = 'jasa_' . $id_detail_jasa . '_' . time() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $stmtPort = $pdo->prepare("
                        INSERT INTO portofolio (id_detail_jasa, gambar)
                        VALUES (:id_jasa, :gambar)
                    ");
                    $stmtPort->execute([
                        'id_jasa' => $id_detail_jasa,
                        'gambar' => $newFileName
                    ]);
                }
            }
        }

        $pdo->commit();
        header("Location: ../dashboard.php?view=jasa&msg=" . urlencode("Layanan jasa '{$nama_jasa}' berhasil ditambahkan!"));
        exit;
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        header("Location: ../dashboard.php?view=jasa&err=" . urlencode("Gagal menambahkan jasa: " . $e->getMessage()));
        exit;
    }
} else {
    header("Location: ../dashboard.php?view=jasa");
    exit;
}
?>
