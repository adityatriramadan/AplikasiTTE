-- ============================================================
-- E-Office Sistem Tanda Tangan Digital
-- Prodi Teknik Informatika — Universitas Pamulang
-- Schema Database Lengkap + Data Awal
-- ============================================================

CREATE DATABASE IF NOT EXISTS `eoffice_unpam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `eoffice_unpam`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Tabel users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(150) NOT NULL,
  `nip`        VARCHAR(30)  NOT NULL UNIQUE,
  `jabatan`    VARCHAR(100) NOT NULL,
  `role`       ENUM('admin','kaprodi','sekretaris','dosen') NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
  `status`     ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel kunci_rsa
-- ----------------------------
DROP TABLE IF EXISTS `kunci_rsa`;
CREATE TABLE `kunci_rsa` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`               INT UNSIGNED NOT NULL COMMENT 'FK ke users (Kaprodi)',
  `public_key`            TEXT NOT NULL COMMENT 'Public key PEM',
  `private_key_encrypted` TEXT NOT NULL COMMENT 'Private key terenkripsi AES-256 + PIN',
  `is_aktif`              TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel template_surat
-- ----------------------------
DROP TABLE IF EXISTS `template_surat`;
CREATE TABLE `template_surat` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jenis`       VARCHAR(10)  NOT NULL UNIQUE COMMENT 'ST, SK, SU, ND, BA, SP, DS',
  `nama_jenis`       VARCHAR(100) NOT NULL,
  `konten_template`  TEXT NOT NULL COMMENT 'Template HTML dengan placeholder {{variable}}',
  `format_nomor`     VARCHAR(100) NOT NULL COMMENT 'Contoh: {urut}/ST-TI.UNPAM/{bulan_romawi}/{tahun}',
  `field_dinamis`    JSON NOT NULL COMMENT 'Daftar field yang harus diisi user',
  `status`           ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel surat
-- ----------------------------
DROP TABLE IF EXISTS `surat`;
CREATE TABLE `surat` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_surat`         VARCHAR(100) NOT NULL UNIQUE,
  `template_id`         INT UNSIGNED NOT NULL COMMENT 'FK ke template_surat',
  `perihal`             VARCHAR(255) NOT NULL,
  `isi_data`            JSON NOT NULL COMMENT 'Data dinamis sesuai template',
  `pembuat_id`          INT UNSIGNED NOT NULL COMMENT 'FK ke users (Sekretaris)',
  `penerima_nama`       VARCHAR(150) DEFAULT NULL,
  `penerima_jabatan`    VARCHAR(100) DEFAULT NULL,
  `penerima_instansi`   VARCHAR(200) DEFAULT NULL,
  `status`              ENUM('draft','menunggu','ditandatangani','ditolak','didistribusikan')
                        NOT NULL DEFAULT 'draft',
  `alasan_tolak`        TEXT DEFAULT NULL,
  `tanggal_surat`       DATE NOT NULL,
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`template_id`) REFERENCES `template_surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`pembuat_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel tanda_tangan
-- ----------------------------
DROP TABLE IF EXISTS `tanda_tangan`;
CREATE TABLE `tanda_tangan` (
  `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `surat_id`               INT UNSIGNED NOT NULL UNIQUE COMMENT 'FK ke surat',
  `kaprodi_id`             INT UNSIGNED NOT NULL COMMENT 'FK ke users (Kaprodi)',
  `hash_sha256`            CHAR(64) NOT NULL COMMENT 'SHA-256 hash PDF final (hex)',
  `signature_rsa`          TEXT NOT NULL COMMENT 'Digital signature base64',
  `public_key_snapshot`    TEXT NOT NULL COMMENT 'Snapshot public key saat penandatanganan',
  `timestamp_tandatangan`  INT UNSIGNED NOT NULL COMMENT 'Unix timestamp UTC',
  `pdf_path`               VARCHAR(255) DEFAULT NULL COMMENT 'Nama file PDF di STORAGE_PDF',
  `qr_code_url`            VARCHAR(500) DEFAULT NULL COMMENT 'URL verifikasi publik',
  `created_at`             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`surat_id`)   REFERENCES `surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`kaprodi_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel distribusi
-- ----------------------------
DROP TABLE IF EXISTS `distribusi`;
CREATE TABLE `distribusi` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `surat_id`             INT UNSIGNED NOT NULL COMMENT 'FK ke surat',
  `penerima_id`          INT UNSIGNED DEFAULT NULL COMMENT 'FK ke users jika internal',
  `penerima_eksternal`   VARCHAR(200) DEFAULT NULL COMMENT 'Nama penerima jika eksternal',
  `tanggal_kirim`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_baca`          ENUM('belum','dibaca') NOT NULL DEFAULT 'belum',
  `dibaca_pada`          TIMESTAMP DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`surat_id`)    REFERENCES `surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`penerima_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel notifikasi
-- ----------------------------
DROP TABLE IF EXISTS `notifikasi`;
CREATE TABLE `notifikasi` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL COMMENT 'FK ke users penerima notifikasi',
  `pesan`      VARCHAR(500) NOT NULL,
  `url`        VARCHAR(500) DEFAULT NULL,
  `is_dibaca`  TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel log_aktivitas
-- ----------------------------
DROP TABLE IF EXISTS `log_aktivitas`;
CREATE TABLE `log_aktivitas` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL COMMENT 'FK ke users',
  `aksi`       VARCHAR(100) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel nomor_surat_counter
-- ----------------------------
DROP TABLE IF EXISTS `nomor_surat_counter`;
CREATE TABLE `nomor_surat_counter` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jenis`  VARCHAR(10)  NOT NULL,
  `tahun`       YEAR NOT NULL,
  `counter`     INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jenis_tahun` (`kode_jenis`, `tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel surat_masuk
-- ----------------------------
DROP TABLE IF EXISTS `surat_masuk`;
CREATE TABLE `surat_masuk` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_surat`     VARCHAR(100) NOT NULL,
  `pengirim`        VARCHAR(200) NOT NULL,
  `perihal`         VARCHAR(255) NOT NULL,
  `tanggal_surat`   DATE NOT NULL,
  `tanggal_terima`  DATE NOT NULL,
  `file_path`       VARCHAR(255) DEFAULT NULL,
  `catatan`         TEXT DEFAULT NULL,
  `status_disposisi` ENUM('belum','sudah') NOT NULL DEFAULT 'belum',
  `penerima_disposisi` VARCHAR(200) DEFAULT NULL,
  `catatan_disposisi`  TEXT DEFAULT NULL,
  `input_oleh`      INT UNSIGNED NOT NULL COMMENT 'FK ke users (Sekretaris)',
  `created_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`input_oleh`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- DATA AWAL (SEED)
-- Password default semua user: Admin123!
-- Hash bcrypt dari "Admin123!" (cost=12)
-- ============================================================

INSERT INTO `users` (`nama`, `nip`, `jabatan`, `role`, `email`, `password`, `status`) VALUES
('Administrator Sistem', 'admin001', 'Administrator', 'admin', 'admin@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Dr. Hendra Kusuma, M.Kom', 'kaprodi001', 'Ketua Program Studi', 'kaprodi', 'kaprodi@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Siti Rahayu, S.Kom', 'sekretaris001', 'Sekretaris TU', 'sekretaris', 'sekretaris@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Budi Santoso, S.Kom., M.T', 'dosen001', 'Dosen Tetap', 'dosen', 'budi@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Dewi Lestari, M.Kom', 'dosen002', 'Dosen Tetap', 'dosen', 'dewi@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif');

-- Template Surat Tugas (ST)
INSERT INTO `template_surat` (`kode_jenis`, `nama_jenis`, `konten_template`, `format_nomor`, `field_dinamis`) VALUES
('ST', 'Surat Tugas', 
'<div style="font-family: Times New Roman, serif; font-size: 12pt; line-height: 1.5;">
<div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
<img src="" style="height: 60px;" alt="Logo UNPAM">
<h2 style="margin: 5px 0;">UNIVERSITAS PAMULANG</h2>
<h3 style="margin: 0;">PROGRAM STUDI TEKNIK INFORMATIKA</h3>
<p style="margin: 2px 0; font-size: 10pt;">Jl. Surya Kencana No.1, Pamulang, Tangerang Selatan, Banten 15417</p>
</div>
<h3 style="text-align: center; text-decoration: underline;">SURAT TUGAS</h3>
<p style="text-align: center;">Nomor: {{nomor_surat}}</p>
<br>
<p>Yang bertanda tangan di bawah ini, Ketua Program Studi Teknik Informatika Universitas Pamulang, dengan ini menugaskan:</p>
<table style="width: 100%; margin: 10px 0;">
  <tr><td style="width: 30%;">Nama</td><td>: <strong>{{nama_pegawai}}</strong></td></tr>
  <tr><td>NIP</td><td>: {{nip_pegawai}}</td></tr>
  <tr><td>Jabatan</td><td>: {{jabatan_pegawai}}</td></tr>
</table>
<p>Untuk melaksanakan tugas:</p>
<table style="width: 100%; margin: 10px 0;">
  <tr><td style="width: 30%;">Kegiatan</td><td>: {{kegiatan}}</td></tr>
  <tr><td>Tempat</td><td>: {{tempat}}</td></tr>
  <tr><td>Tanggal</td><td>: {{tanggal_kegiatan}}</td></tr>
</table>
<p>Demikian surat tugas ini dibuat untuk dapat dilaksanakan dengan sebaik-baiknya.</p>
<br>
<div style="text-align: right; margin-right: 30px;">
<p>Tangerang Selatan, {{tanggal_surat}}</p>
<p>Ketua Program Studi Teknik Informatika,</p>
<br><br><br>
<p><strong>Dr. Hendra Kusuma, M.Kom</strong></p>
<p>NIP. kaprodi001</p>
</div>
</div>',
'{urut}/ST-TI.UNPAM/{bulan_romawi}/{tahun}',
'[{"name":"nama_pegawai","label":"Nama Pegawai","type":"text","required":true},{"name":"nip_pegawai","label":"NIP Pegawai","type":"text","required":true},{"name":"jabatan_pegawai","label":"Jabatan","type":"text","required":true},{"name":"kegiatan","label":"Kegiatan/Tugas","type":"textarea","required":true},{"name":"tempat","label":"Tempat Pelaksanaan","type":"text","required":true},{"name":"tanggal_kegiatan","label":"Tanggal Kegiatan","type":"text","required":true}]');

-- Template Surat Keterangan (SK)
INSERT INTO `template_surat` (`kode_jenis`, `nama_jenis`, `konten_template`, `format_nomor`, `field_dinamis`) VALUES
('SK', 'Surat Keterangan',
'<div style="font-family: Times New Roman, serif; font-size: 12pt; line-height: 1.5;">
<div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
<h2 style="margin: 5px 0;">UNIVERSITAS PAMULANG</h2>
<h3 style="margin: 0;">PROGRAM STUDI TEKNIK INFORMATIKA</h3>
</div>
<h3 style="text-align: center; text-decoration: underline;">SURAT KETERANGAN</h3>
<p style="text-align: center;">Nomor: {{nomor_surat}}</p>
<br>
<p>Yang bertanda tangan di bawah ini menerangkan bahwa:</p>
<table style="width: 100%; margin: 10px 0;">
  <tr><td style="width: 30%;">Nama</td><td>: <strong>{{nama_yang_diterangkan}}</strong></td></tr>
  <tr><td>NIM/NIP</td><td>: {{nim_nip}}</td></tr>
  <tr><td>Program Studi</td><td>: Teknik Informatika</td></tr>
  <tr><td>Status</td><td>: {{status_yang_diterangkan}}</td></tr>
</table>
<p>{{isi_keterangan}}</p>
<p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
<br>
<div style="text-align: right; margin-right: 30px;">
<p>Tangerang Selatan, {{tanggal_surat}}</p>
<p>Ketua Program Studi Teknik Informatika,</p>
<br><br><br>
<p><strong>Dr. Hendra Kusuma, M.Kom</strong></p>
<p>NIP. kaprodi001</p>
</div>
</div>',
'{urut}/SK-TI.UNPAM/{bulan_romawi}/{tahun}',
'[{"name":"nama_yang_diterangkan","label":"Nama Yang Diterangkan","type":"text","required":true},{"name":"nim_nip","label":"NIM/NIP","type":"text","required":true},{"name":"status_yang_diterangkan","label":"Status (Mahasiswa/Dosen/dll)","type":"text","required":true},{"name":"isi_keterangan","label":"Isi Keterangan","type":"textarea","required":true}]');

-- Template Surat Undangan (SU)
INSERT INTO `template_surat` (`kode_jenis`, `nama_jenis`, `konten_template`, `format_nomor`, `field_dinamis`) VALUES
('SU', 'Surat Undangan',
'<div style="font-family: Times New Roman, serif; font-size: 12pt; line-height: 1.5;">
<div style="text-align: center; border-bottom: 3px double #000; padding-bottom: 10px; margin-bottom: 20px;">
<h2 style="margin: 5px 0;">UNIVERSITAS PAMULANG</h2>
<h3 style="margin: 0;">PROGRAM STUDI TEKNIK INFORMATIKA</h3>
</div>
<h3 style="text-align: center; text-decoration: underline;">SURAT UNDANGAN</h3>
<p style="text-align: center;">Nomor: {{nomor_surat}}</p>
<table style="width: 100%; margin: 10px 0;">
  <tr><td style="width: 20%;">Kepada Yth.</td><td>: {{nama_penerima}}</td></tr>
  <tr><td>Di</td><td>: Tempat</td></tr>
</table>
<p>Dengan hormat,</p>
<p>Sehubungan dengan {{keperluan}}, kami mengundang Bapak/Ibu untuk hadir pada:</p>
<table style="width: 100%; margin: 10px 0;">
  <tr><td style="width: 20%;">Hari/Tanggal</td><td>: {{hari_tanggal}}</td></tr>
  <tr><td>Waktu</td><td>: {{waktu}}</td></tr>
  <tr><td>Tempat</td><td>: {{tempat_acara}}</td></tr>
  <tr><td>Agenda</td><td>: {{agenda}}</td></tr>
</table>
<p>Demikian undangan ini kami sampaikan, atas kehadiran dan partisipasi Bapak/Ibu kami ucapkan terima kasih.</p>
<br>
<div style="text-align: right; margin-right: 30px;">
<p>Tangerang Selatan, {{tanggal_surat}}</p>
<p>Ketua Program Studi Teknik Informatika,</p>
<br><br><br>
<p><strong>Dr. Hendra Kusuma, M.Kom</strong></p>
<p>NIP. kaprodi001</p>
</div>
</div>',
'{urut}/SU-TI.UNPAM/{bulan_romawi}/{tahun}',
'[{"name":"nama_penerima","label":"Nama Penerima Undangan","type":"text","required":true},{"name":"keperluan","label":"Keperluan/Perihal","type":"text","required":true},{"name":"hari_tanggal","label":"Hari dan Tanggal Acara","type":"text","required":true},{"name":"waktu","label":"Waktu","type":"text","required":true},{"name":"tempat_acara","label":"Tempat Acara","type":"text","required":true},{"name":"agenda","label":"Agenda","type":"textarea","required":true}]');
