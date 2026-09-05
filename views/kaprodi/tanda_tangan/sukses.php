<?php $surat = $data['surat'] ?? []; $tt = $data['tanda_tangan'] ?? []; ?>

<div class="card" style="border-left:4px solid var(--ok);margin-bottom:16px;">
    <h2 style="margin-top:0;color:var(--ok);">Tanda Tangan Berhasil</h2>
    <?php if (!empty($data['success'])): ?>
        <div class="alert success"><?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <p>Surat <?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?> sudah diproses dan dapat dilanjutkan ke distribusi.</p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a class="btn" href="<?= BASE_URL ?>/kaprodi/riwayat/detail/<?= (int)($surat['id'] ?? 0) ?>">Lihat Riwayat</a>
        <a class="btn secondary" href="<?= BASE_URL ?>/kaprodi/antrian">Kembali ke Antrian</a>
    </div>
</div>

<div class="grid cols-2">
    <div class="card">
        <h3>Detail Tanda Tangan</h3>
        <p><strong>Hash SHA-256:</strong><br><?= htmlspecialchars($tt['hash_sha256'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Timestamp:</strong> <?= htmlspecialchars((string)($tt['timestamp_tandatangan'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Kaprodi:</strong> <?= htmlspecialchars($tt['nama_kaprodi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card">
        <h3>Dokumen</h3>
        <p><strong>PDF:</strong> <?= htmlspecialchars($tt['pdf_path'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>QR Verifikasi:</strong> <?= htmlspecialchars($tt['qr_code_url'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        <p class="muted">Silakan cek distribusi surat dari menu sekretaris setelah ini.</p>
    </div>
</div>