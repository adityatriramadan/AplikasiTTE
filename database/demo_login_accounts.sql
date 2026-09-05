-- ============================================================
-- Demo Login Accounts for E-Office
-- Password for all accounts: Admin123!
-- Safe to import multiple times because it uses INSERT IGNORE.
-- ============================================================

USE `eoffice_unpam`;

INSERT IGNORE INTO `users` (`nama`, `nip`, `jabatan`, `role`, `email`, `password`, `status`) VALUES
('Administrator Sistem', 'admin001', 'Administrator', 'admin', 'admin@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Dr. Hendra Kusuma, M.Kom', 'kaprodi001', 'Ketua Program Studi', 'kaprodi', 'kaprodi@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Siti Rahayu, S.Kom', 'sekretaris001', 'Sekretaris TU', 'sekretaris', 'sekretaris@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Budi Santoso, S.Kom., M.T', 'dosen001', 'Dosen Tetap', 'dosen', 'budi@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif'),
('Dewi Lestari, M.Kom', 'dosen002', 'Dosen Tetap', 'dosen', 'dewi@ti.unpam.ac.id', '$2y$12$cy5jb0BpzAvu8QVP.kNV5.FhbLinEPs8jdOdQB3sl./BzK8rckm8u', 'aktif');
