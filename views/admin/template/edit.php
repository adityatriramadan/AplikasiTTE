<div class="card">
    <h2>Edit Template</h2>
    <form method="post" action="<?= BASE_URL ?>/admin/template/update/<?= (int)($data['template']['id'] ?? 0) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid cols-2">
            <div class="field"><label>Nama Jenis</label><input name="nama_jenis" value="<?= htmlspecialchars($data['template']['nama_jenis'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="field"><label>Format Nomor</label><input name="format_nomor" value="<?= htmlspecialchars($data['template']['format_nomor'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="field"><label>Status</label>
                <select name="status" required>
                    <option value="aktif" <?= (($data['template']['status'] ?? '') === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= (($data['template']['status'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="field"><label>Field Dinamis (JSON)</label><textarea name="field_dinamis" rows="6"><?= htmlspecialchars($data['template']['field_dinamis'] ?? '[]', ENT_QUOTES, 'UTF-8') ?></textarea></div>
            <div class="field" style="grid-column: 1 / -1;"><label>Konten Template</label><textarea name="konten_template" rows="12" required><?= htmlspecialchars($data['template']['konten_template'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea></div>
        </div>
        <button class="btn" type="submit">Update</button>
    </form>
</div>