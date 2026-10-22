<?php
session_start();
require_once __DIR__ . '/../../database/koneksi_psql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = trim($_POST['pin'] ?? '');

    if (empty($pin)) {
        header("Location: login.php?error=1");
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM editor WHERE pin = :pin LIMIT 1");
        $stmt->execute(['pin' => $pin]);
        $editor = $stmt->fetch();

        if ($editor) {
            $_SESSION['editor_logged_in'] = true;
            $_SESSION['editor_id'] = $editor['id_editor'];
            $_SESSION['editor_wa'] = $editor['no_wa'];
            
            header("Location: ../dashboard.php");
            exit;
        } else {
            header("Location: login.php?error=1");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: login.php?error=1");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>
