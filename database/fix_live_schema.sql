USE `eoffice_unpam`;

CREATE TABLE IF NOT EXISTS `notifikasi` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL COMMENT 'FK ke users penerima notifikasi',
  `pesan`      VARCHAR(500) NOT NULL,
  `url`        VARCHAR(500) DEFAULT NULL,
  `is_dibaca`  TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `log_aktivitas` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL COMMENT 'FK ke users',
  `aksi`       VARCHAR(100) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `distribusi` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `surat_id`             INT UNSIGNED NOT NULL COMMENT 'FK ke surat',
  `penerima_id`          INT UNSIGNED DEFAULT NULL COMMENT 'FK ke users jika internal',
  `penerima_eksternal`   VARCHAR(200) DEFAULT NULL COMMENT 'Nama penerima jika eksternal',
  `tanggal_kirim`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_baca`          ENUM('belum','dibaca') NOT NULL DEFAULT 'belum',
  `dibaca_pada`          DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`surat_id`) REFERENCES `surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`penerima_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `nomor_surat_counter` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jenis`  VARCHAR(10)  NOT NULL,
  `tahun`       YEAR NOT NULL,
  `counter`     INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jenis_tahun` (`kode_jenis`, `tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `surat_masuk` (
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