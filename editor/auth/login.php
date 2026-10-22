<?php
session_start();
// Jika sudah login, langsung alihkan ke dashboard editor
if (isset($_SESSION['editor_logged_in']) && $_SESSION['editor_logged_in'] === true) {
    header("Location: ../dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Editor — sekilasrv</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../tampilan/login.css">
</head>

<body>
    <main class="login-container">
        <div class="login-card">
            <!-- Brand Mark -->
            <div class="login-brand">
                <div class="logo-mark">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                        <rect width="24" height="24" rx="6" fill="#0B3528" />
                        <path d="M7 12L12 7L17 12L12 17L7 12Z" fill="#D2E823" />
                        <circle cx="12" cy="12" r="2.5" fill="#0B3528" />
                    </svg>
                </div>
                <span class="brand-title">sekilasrv</span>
            </div>

            <div class="pill-badge">
                <span class="pill-icon">✍️</span>
                <span class="pill-text">PORTAL EDITOR & KREATOR</span>
            </div>

            <h1 class="title">Masuk ke Dashboard</h1>
            <p class="subtitle">Kelola penawaran jasa editing dan portofolio Anda.</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    PIN salah. Silakan periksa kembali kode akses Anda.
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['logged_out'])): ?>
                <div class="info-message">
                    Anda berhasil logout dari sesi editor.
                </div>
            <?php endif; ?>

            <form action="proses_login.php" method="POST">
                <div class="form-group">
                    <label for="pin">PIN Akses Editor</label>
                    <input
                        type="password"
                        id="pin"
                        name="pin"
                        placeholder="Masukkan PIN (cth: 123456)"
                        inputmode="numeric"
                        pattern="[0-9]+"
                        autocomplete="current-password"
                        required
                        autofocus>
                </div>

                <button type="submit" class="login-btn">Masuk Sekarang</button>
            </form>

            <div class="demo-hint">
                <small>💡 PIN uji coba bawaan: <strong>123456</strong> atau <strong>080421</strong></small>
            </div>

            <a href="../../beranda/beranda.php" class="back-link">
                ← Kembali ke Beranda
            </a>
        </div>
    </main>
</body>

</html>