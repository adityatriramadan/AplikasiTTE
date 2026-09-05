<div class="card">
    <h2>Input Surat Masuk</h2>
    <?php if (!empty($data['error'])): ?><div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/sekretaris/surat-masuk/simpan">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid cols-2">
            <div class="field"><label>Nomor Surat</label><input name="nomor_surat" required></div>
            <div class="field"><label>Pengirim</label><input name="pengirim" required></div>
            <div class="field"><label>Perihal</label><input name="perihal" required></div>
            <div class="field"><label>Tanggal Surat</label><input type="date" name="tanggal_surat" required></div>
            <div class="field"><label>Tanggal Terima</label><input type="date" name="tanggal_terima" value="<?= date('Y-m-d') ?>"></div>
            <div class="field"><label>Catatan</label><textarea name="catatan" rows="4"></textarea></div>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
</div>