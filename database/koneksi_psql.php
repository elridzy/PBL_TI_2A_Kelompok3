<?php
// Konfigurasi Database PostgreSQL
$host = getenv('DB_HOST') ?: "localhost";
$port = getenv('DB_PORT') ?: "5432";
$dbname = getenv('DB_NAME') ?: "db_sekilasrv";
$user = getenv('DB_USER') ?: "postgres";
$password = getenv('DB_PASS') ?: "aditya"; // Ganti jika password PostgreSQL Anda berbeda (misal: postgres / root / admin)

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    $pdo = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    // Tampilkan pesan error ramah jika belum terkoneksi
    $error_msg = htmlspecialchars($e->getMessage());
    echo <<<HTML
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Database Connection Error - sekilasrv</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #171A21;
                color: #ffffff;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                margin: 0;
                padding: 20px;
                box-sizing: border-box;
            }
            .error-box {
                background-color: #222;
                border: 1px solid #ff4d4d;
                border-radius: 16px;
                max-width: 600px;
                padding: 30px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            }
            h2 { color: #ff4d4d; margin-top: 0; }
            p { line-height: 1.6; color: #ccc; }
            code {
                background: #111;
                color: #ffb86c;
                padding: 3px 6px;
                border-radius: 4px;
                font-size: 14px;
            }
            .tips {
                background: #1c2333;
                border-left: 4px solid #1877F2;
                padding: 12px 16px;
                margin: 20px 0;
                border-radius: 4px;
                font-size: 14px;
            }
            .tips ul { margin: 8px 0 0 20px; padding: 0; }
        </style>
    </head>
    <body>
        <div class="error-box">
            <h2>Gagal Terhubung ke Database PostgreSQL</h2>
            <p><strong>Pesan Error:</strong> <code>$error_msg</code></p>
            <div class="tips">
                <strong>Langkah Perbaikan:</strong>
                <ul>
                    <li>Buka file <code>database/koneksi_psql.php</code></li>
                    <li>Sesuaikan <code>\$password</code> dengan password PostgreSQL lokal Anda.</li>
                    <li>Pastikan database <code>db_sekilasrv</code> sudah dibuat dan script <code>database/schema.sql</code> sudah di-import.</li>
                    <li>Pastikan service PostgreSQL sedang berjalan di port <code>$port</code>.</li>
                </ul>
            </div>
            <p style="font-size: 13px; color: #888;">Silakan refresh halaman setelah konfigurasi disesuaikan.</p>
        </div>
    </body>
    </html>
HTML;
    exit;
}
?>