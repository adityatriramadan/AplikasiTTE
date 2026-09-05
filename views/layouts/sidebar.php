<?php $currentUser = $data['current_user'] ?? Auth::user(); $role = $currentUser['role'] ?? ''; ?>
<aside class="sidebar">
    <div class="user-box">
        <strong><?= htmlspecialchars($currentUser['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong><br>
        <span class="muted"><?= htmlspecialchars($currentUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></span><br>
        <span class="badge"><?= htmlspecialchars(ucfirst($role), ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <nav class="nav">
        <?php if ($role === 'admin'): ?>
            <a href="<?= BASE_URL ?>/admin/dashboard">Dashboard</a>
            <a href="<?= BASE_URL ?>/admin/users">User</a>
            <a href="<?= BASE_URL ?>/admin/kunci">Kunci RSA</a>
            <a href="<?= BASE_URL ?>/admin/template">Template Surat</a>
            <a href="<?= BASE_URL ?>/admin/log">Log Aktivitas</a>
        <?php elseif ($role === 'kaprodi'): ?>
            <a href="<?= BASE_URL ?>/kaprodi/dashboard">Dashboard</a>
            <a href="<?= BASE_URL ?>/kaprodi/antrian">Antrian Tanda Tangan</a>
            <a href="<?= BASE_URL ?>/kaprodi/riwayat">Riwayat</a>
        <?php elseif ($role === 'sekretaris'): ?>
            <a href="<?= BASE_URL ?>/sekretaris/dashboard">Dashboard</a>
            <a href="<?= BASE_URL ?>/sekretaris/buat-surat">Buat Surat</a>
            <a href="<?= BASE_URL ?>/sekretaris/surat-keluar/daftar">Surat Keluar</a>
            <a href="<?= BASE_URL ?>/sekretaris/surat-masuk">Surat Masuk</a>
            <a href="<?= BASE_URL ?>/sekretaris/arsip/keluar">Arsip Keluar</a>
            <a href="<?= BASE_URL ?>/sekretaris/arsip/masuk">Arsip Masuk</a>
        <?php elseif ($role === 'dosen'): ?>
            <a href="<?= BASE_URL ?>/dosen/dashboard">Dashboard</a>
            <a href="<?= BASE_URL ?>/dosen/dokumen">Dokumen Saya</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/logout">Logout</a>
    </nav>
</aside>
<main class="content">
    <div class="container">