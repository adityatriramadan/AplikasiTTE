<?php $surat = $data['surat'] ?? []; ?>

<?php if (!empty($data['error'])): ?>
<div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card" style="margin-bottom:16px;">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 4px;">Input PIN — Tanda Tangan Digital</h2>
            <div class="muted">Surat siap diproses: <?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <a class="btn secondary" href="<?= BASE_URL ?>/kaprodi/review/<?= (int)($surat['id'] ?? 0) ?>">← Kembali ke Review</a>
    </div>
</div>

<div class="grid cols-2">
    <div class="card">
        <h3>Ringkasan Surat</h3>
        <p><strong>Nomor:</strong> <?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Perihal:</strong> <?= htmlspecialchars($surat['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Tanggal:</strong> <?= htmlspecialchars($surat['tanggal_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Pembuat:</strong> <?= htmlspecialchars($surat['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card" style="border-left:4px solid var(--ok);">
        <h3 style="color:var(--ok);margin-top:0;">Masukkan PIN Kunci RSA</h3>
        <p class="muted">PIN dipakai untuk membuka private key terenkripsi dan membuat tanda tangan digital.</p>
        <form method="post" action="<?= BASE_URL ?>/kaprodi/tanda-tangan" onsubmit="return confirm('Anda yakin ingin menandatangani surat ini secara digital?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="surat_id" value="<?= (int)($surat['id'] ?? 0) ?>">
            <div class="field">
                <label for="pin_sign">PIN Kunci RSA</label>
                <input type="password" id="pin_sign" name="pin" required autocomplete="off" placeholder="Masukkan PIN rahasia Anda" style="font-size:16px;letter-spacing:3px;">
            </div>
            <button class="btn ok" type="submit" style="width:100%;font-size:15px;padding:14px;">🔏 Proses Tanda Tangan</button>
        </form>
    </div>
</div>