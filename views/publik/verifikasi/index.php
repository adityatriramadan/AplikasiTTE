<?php $surat = $data['surat'] ?? null; $hasil = $data['hasil'] ?? null; $error = $data['error'] ?? null; $tandaTangan = $data['tanda_tangan'] ?? null; ?>
<div class="brand">
    <div>
        <h1>Verifikasi Dokumen</h1>
        <div class="muted">Pemeriksaan keaslian dokumen melalui QR Code</div>
    </div>
    <span class="badge">Publik</span>
</div>

<div class="grid cols-2">
    <div class="card">
        <form method="get" action="<?= BASE_URL ?>/verifikasi">
            <div class="field">
                <label for="id">ID Dokumen</label>
                <input type="number" id="id" name="id" value="<?= htmlspecialchars((string)($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Masukkan ID dokumen">
            </div>
            <button class="btn" type="submit">Verifikasi</button>
        </form>
        <?php if ($error): ?>
            <div class="alert error" style="margin-top:16px;"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($hasil === true): ?>
            <?php include BASE_PATH . '/views/publik/verifikasi/valid.php'; ?>
        <?php elseif ($hasil === false): ?>
            <?php include BASE_PATH . '/views/publik/verifikasi/tidak_valid.php'; ?>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Informasi Dokumen</h3>
        <?php if ($surat): ?>
            <p><strong>Nomor:</strong> <?= htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Perihal:</strong> <?= htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Status:</strong> <?= htmlspecialchars($surat['status'], ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Template:</strong> <?= htmlspecialchars($surat['nama_jenis'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
            <?php if ($tandaTangan): ?>
                <p><strong>Penandatangan:</strong> <?= htmlspecialchars($tandaTangan['nama_kaprodi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>
        <?php else: ?>
            <p class="muted">Masukkan ID dokumen untuk melihat hasil verifikasi.</p>
        <?php endif; ?>
    </div>
</div>
</div>
</body>
</html>