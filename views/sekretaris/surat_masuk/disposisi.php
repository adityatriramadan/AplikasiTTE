<div class="card">
    <h2>Disposisi Surat Masuk</h2>
    <form method="post" action="<?= BASE_URL ?>/sekretaris/surat-masuk/disposisi/<?= (int)($data['surat_masuk']['id'] ?? 0) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="field"><label>Penerima Disposisi</label><input name="penerima_disposisi" value="<?= htmlspecialchars($data['surat_masuk']['penerima_disposisi'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></div>
        <div class="field"><label>Catatan Disposisi</label><textarea name="catatan_disposisi" rows="4"><?= htmlspecialchars($data['surat_masuk']['catatan_disposisi'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
        <button class="btn ok" type="submit">Simpan Disposisi</button>
    </form>
</div>