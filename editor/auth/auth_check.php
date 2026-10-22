<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['editor_logged_in']) || $_SESSION['editor_logged_in'] !== true) {
    // Cari path ke auth/login.php berdasarkan kedalaman folder
    $login_path = file_exists(__DIR__ . '/login.php') ? __DIR__ . '/login.php' : '';
    // Buat URL relatif yang tepat
    $current_dir = dirname($_SERVER['SCRIPT_NAME']);
    if ($current_dir === '/editor' || substr($current_dir, -7) === '/editor') {
        header("Location: auth/login.php");
    } else {
        header("Location: ../auth/login.php");
    }
    exit;
}
?>
