<div class="card">
    <h2>Manajemen User</h2>
    <p><a class="btn" href="<?= BASE_URL ?>/admin/users/tambah">Tambah User</a></p>
    <?php if (!empty($data['success'])): ?><div class="alert success"><?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <table class="table">
        <thead><tr><th>Nama</th><th>NIP</th><th>Jabatan</th><th>Role</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach (($data['users'] ?? []) as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['nama'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['nip'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['jabatan'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a class="btn secondary" href="<?= BASE_URL ?>/admin/users/edit/<?= (int)$user['id'] ?>">Edit</a>
                        <a class="btn secondary" href="<?= BASE_URL ?>/admin/users/toggle/<?= (int)$user['id'] ?>">Toggle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['users'])): ?><tr><td colspan="6" class="muted">Belum ada user.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>