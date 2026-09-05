<?php $surat = $data['surat'] ?? null; ?>
<div class="card">
    <h2>Hasil Verifikasi</h2>
    <?php if (!empty($data['error'])): ?>
        <div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php elseif ($data['hasil'] === true): ?>
        <div class="alert success">Dokumen valid.</div>
    <?php elseif ($data['hasil'] === false): ?>
        <div class="alert error">Dokumen tidak valid.</div>
    <?php else: ?>
        <div class="alert">Belum ada hasil verifikasi.</div>
    <?php endif; ?>
    <p><strong>Nomor:</strong> <?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Perihal:</strong> <?= htmlspecialchars($surat['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    <p><a class="btn secondary" href="<?= BASE_URL ?>/dosen/dokumen/detail/<?= (int)($surat['id'] ?? 0) ?>">Kembali</a></p>
</div>