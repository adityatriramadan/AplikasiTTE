<?php $dokumen = $data['dokumen'] ?? []; $tandaTangan = $data['tanda_tangan'] ?? null; ?>
<div class="card">
    <h2>Detail Dokumen</h2>
    <p><strong>Nomor:</strong> <?= htmlspecialchars($dokumen['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Perihal:</strong> <?= htmlspecialchars($dokumen['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Pengirim:</strong> <?= htmlspecialchars($dokumen['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Status Baca:</strong> <?= htmlspecialchars($dokumen['status_baca'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Catatan:</strong> <?= htmlspecialchars($dokumen['catatan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
</div>

<div class="grid cols-2">
    <div class="card">
        <h3>Informasi Tanda Tangan</h3>
        <?php if ($tandaTangan): ?>
            <p><strong>Kaprodi:</strong> <?= htmlspecialchars($tandaTangan['nama_kaprodi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Waktu:</strong> <?= htmlspecialchars(date('d-m-Y H:i', (int)$tandaTangan['timestamp_tandatangan']), ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Hash:</strong> <code><?= htmlspecialchars($tandaTangan['hash_sha256'], ENT_QUOTES, 'UTF-8') ?></code></p>
        <?php else: ?>
            <p class="muted">Dokumen belum memiliki tanda tangan digital.</p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Aksi</h3>
        <p><a class="btn" href="<?= BASE_URL ?>/dosen/verifikasi/<?= (int)($dokumen['id'] ?? 0) ?>">Verifikasi Dokumen</a></p>
        <?php if (!empty($tandaTangan['pdf_path'])): ?>
            <p><a class="btn secondary" href="<?= BASE_URL ?>/public/pdf/<?= htmlspecialchars($tandaTangan['pdf_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">Unduh PDF</a></p>
        <?php endif; ?>
    </div>
</div>