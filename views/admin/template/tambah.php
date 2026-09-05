<div class="card">
    <h2>Tambah Template</h2>
    <form method="post" action="<?= BASE_URL ?>/admin/template/simpan">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid cols-2">
            <div class="field"><label>Kode Jenis</label><input name="kode_jenis" maxlength="10" required></div>
            <div class="field"><label>Nama Jenis</label><input name="nama_jenis" required></div>
            <div class="field"><label>Format Nomor</label><input name="format_nomor" required></div>
            <div class="field"><label>Field Dinamis (JSON)</label><textarea name="field_dinamis" rows="6">[]</textarea></div>
            <div class="field" style="grid-column: 1 / -1;"><label>Konten Template</label><textarea name="konten_template" rows="12" required></textarea></div>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
</div>