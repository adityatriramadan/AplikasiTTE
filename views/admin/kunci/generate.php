<div class="card">
    <h2>Generate Kunci RSA</h2>
    <form method="post" action="<?= BASE_URL ?>/admin/kunci/proses">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid cols-2">
            <div class="field">
                <label>Kaprodi</label>
                <select name="kaprodi_id" required>
                    <option value="">Pilih Kaprodi</option>
                    <?php foreach (($data['kaprodi_list'] ?? []) as $kaprodi): ?>
                        <option value="<?= (int)$kaprodi['id'] ?>"><?= htmlspecialchars($kaprodi['nama'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>PIN</label><input type="password" name="pin" required></div>
            <div class="field"><label>Konfirmasi PIN</label><input type="password" name="pin_konfirmasi" required></div>
        </div>
        <button class="btn" type="submit">Generate</button>
    </form>
</div>