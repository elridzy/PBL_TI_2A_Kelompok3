-- ==============================================================
-- DATABASE SCHEMA & SAMPLE DATA: sekilasrv
-- Sistem Promosi Jasa Editing
-- Database: PostgreSQL
-- ==============================================================

-- 1. Hapus tabel jika sudah ada sebelumnya (urutan child ke parent)
DROP TABLE IF EXISTS portofolio CASCADE;
DROP TABLE IF EXISTS detail_jasa CASCADE;
DROP TABLE IF EXISTS kategori_jasa CASCADE;
DROP TABLE IF EXISTS editor CASCADE;

-- 2. Buat tabel editor
CREATE TABLE editor (
    id_editor SERIAL PRIMARY KEY,
    pin VARCHAR(255) NOT NULL,
    no_wa VARCHAR(20) NOT NULL
);

-- 3. Buat tabel kategori_jasa
CREATE TABLE kategori_jasa (
    id_kategori_jasa SERIAL PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- 4. Buat tabel detail_jasa
CREATE TABLE detail_jasa (
    id_detail_jasa SERIAL PRIMARY KEY,
    id_kategori_jasa INTEGER NOT NULL,
    id_editor INTEGER NOT NULL,
    nama_jasa VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    harga INTEGER NOT NULL,
    tanggal_upload DATE NOT NULL DEFAULT CURRENT_DATE,

    FOREIGN KEY (id_kategori_jasa)
        REFERENCES kategori_jasa(id_kategori_jasa)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    FOREIGN KEY (id_editor)
        REFERENCES editor(id_editor)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- 5. Buat tabel portofolio
CREATE TABLE portofolio (
    id_portofolio SERIAL PRIMARY KEY,
    id_detail_jasa INTEGER NOT NULL,
    gambar VARCHAR(255) NOT NULL,

    FOREIGN KEY (id_detail_jasa)
        REFERENCES detail_jasa(id_detail_jasa)
        ON UPDATE CASCADE
        ON DELETE CASCADE
);

-- ==============================================================
-- SEED DATA (DATA AWAL UNTUK PENGUJIAN)
-- ==============================================================

-- Data Editor (PIN default: 123456 dan 080421)
INSERT INTO editor (id_editor, pin, no_wa) VALUES
(1, '123456', '6281234567890'),
(2, '080421', '6289876543210');

-- Data Kategori Jasa
INSERT INTO kategori_jasa (id_kategori_jasa, nama_kategori) VALUES
(1, 'Video Editing'),
(2, 'Desain Grafis'),
(3, 'Color Grading'),
(4, 'Motion Graphics'),
(5, 'Foto & Retouching');

-- Data Detail Jasa
INSERT INTO detail_jasa (id_detail_jasa, id_kategori_jasa, id_editor, nama_jasa, deskripsi, harga, tanggal_upload) VALUES
(1, 1, 1, 'Editing Video Reels & TikTok Engagement Tinggi', 'Jasa editing video vertikal durasi 15-60 detik dengan subtitle dinamis bergaya Alex Hormozi, sound design immersif, dan visual effect kekinian untuk meningkatkan watch-time serta konversi bisnis Anda.', 75000, CURRENT_DATE),
(2, 1, 1, 'Editing Video YouTube Sinematik & Vlogging', 'Layanan editing konten YouTube berdurasi 5-15 menit. Meliputi pacing yang dinamis, b-roll sync, background music bebas copyright, transisi smooth, dan thumbnail gratis untuk video pertama Anda.', 250000, CURRENT_DATE),
(3, 2, 1, 'Desain Logo & Brand Identity Modern Minimalis', 'Paket desain logo profesional lengkap dengan panduan brand guideline (warna, tipografi), file vector resolusi tinggi (AI, SVG, PDF, PNG transparan), serta mock up 3D siap pakai.', 350000, CURRENT_DATE),
(4, 3, 2, 'Color Grading Sinematik Film & Music Video', 'Pewarnaan video tingkat profesional menggunakan DaVinci Resolve. Menghasilkan mood sinematik (Teal & Orange, Moody Dark, Warm Vintage) untuk klip musik, wedding cinematic, atau iklan promosi.', 200000, CURRENT_DATE),
(5, 4, 1, 'Motion Graphic Intro & Outro Channel Animasi', 'Pembuatan animasi bumper intro/outro 2D/3D berdurasi 5-10 detik yang elegan, modern, dan catchy untuk branding channel YouTube atau materi promosi media sosial.', 150000, CURRENT_DATE);

-- Data Portofolio (Gambar referensi awal)
INSERT INTO portofolio (id_portofolio, id_detail_jasa, gambar) VALUES
(1, 1, 'sample_reels.jpg'),
(2, 2, 'sample_youtube.jpg'),
(3, 3, 'sample_logo.jpg'),
(4, 4, 'sample_colorgrade.jpg'),
(5, 5, 'sample_motion.jpg');

-- Sinkronisasi sequence SERIAL setelah insert data dengan ID manual
SELECT setval('editor_id_editor_seq', (SELECT MAX(id_editor) FROM editor));
SELECT setval('kategori_jasa_id_kategori_jasa_seq', (SELECT MAX(id_kategori_jasa) FROM kategori_jasa));
SELECT setval('detail_jasa_id_detail_jasa_seq', (SELECT MAX(id_detail_jasa) FROM detail_jasa));
SELECT setval('portofolio_id_portofolio_seq', (SELECT MAX(id_portofolio) FROM portofolio));
