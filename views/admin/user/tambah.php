<div class="card">
    <h2>Tambah User</h2>
    <?php if (!empty($data['error'])): ?><div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post" action="<?= BASE_URL ?>/admin/users/simpan">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <div class="grid cols-2">
            <div class="field"><label>Nama</label><input name="nama" required></div>
            <div class="field"><label>NIP</label><input name="nip" required></div>
            <div class="field"><label>Jabatan</label><input name="jabatan" required></div>
            <div class="field"><label>Role</label>
                <select name="role" required>
                    <option value="admin">Admin</option>
                    <option value="kaprodi">Kaprodi</option>
                    <option value="sekretaris">Sekretaris</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>
            <div class="field"><label>Email</label><input type="email" name="email" required></div>
            <div class="field"><label>Password</label><input type="password" name="password" required></div>
        </div>
        <button class="btn" type="submit">Simpan</button>
    </form>
</div>