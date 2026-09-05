<?php $dokumenList = $data['dokumen'] ?? []; ?>
<div class="card">
    <h2>Dokumen Saya</h2>
    <p class="muted">Daftar surat yang telah didistribusikan ke akun dosen Anda.</p>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Perihal</th>
                <th>Pengirim</th>
                <th>Status Baca</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dokumenList as $dokumen): ?>
                <tr>
                    <td><?= htmlspecialchars($dokumen['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($dokumen['perihal'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($dokumen['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($dokumen['status_baca'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a class="btn secondary" href="<?= BASE_URL ?>/dosen/dokumen/detail/<?= (int)$dokumen['id'] ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$dokumenList): ?>
                <tr><td colspan="5" class="muted">Belum ada dokumen.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>