<div class="card">
    <h2>Edit User</h2>
    <form method="post" action="<?= BASE_URL ?>/admin/users/update/<?= (int)($data['user']['id'] ?? 0) ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid cols-2">
            <div class="field"><label>Nama</label><input name="nama" value="<?= htmlspecialchars($data['user']['nama'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="field"><label>Jabatan</label><input name="jabatan" value="<?= htmlspecialchars($data['user']['jabatan'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="field"><label>Role</label>
                <select name="role" required>
                    <?php foreach (['admin','kaprodi','sekretaris','dosen'] as $role): ?>
                        <option value="<?= $role ?>" <?= (($data['user']['role'] ?? '') === $role) ? 'selected' : '' ?>><?= ucfirst($role) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($data['user']['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required></div>
            <div class="field"><label>Status</label>
                <select name="status" required>
                    <option value="aktif" <?= (($data['user']['status'] ?? '') === 'aktif') ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= (($data['user']['status'] ?? '') === 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="field"><label>Password Baru (opsional)</label><input type="password" name="password"></div>
        </div>
        <button class="btn" type="submit">Update</button>
    </form>
</div>