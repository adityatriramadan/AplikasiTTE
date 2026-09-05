<div class="card">
    <h2>Template Surat</h2>
    <p><a class="btn" href="<?= BASE_URL ?>/admin/template/tambah">Tambah Template</a></p>
    <?php if (!empty($data['success'])): ?><div class="alert success"><?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <table class="table">
        <thead><tr><th>Kode</th><th>Nama</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach (($data['templates'] ?? []) as $template): ?>
                <tr>
                    <td><?= htmlspecialchars($template['kode_jenis'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($template['nama_jenis'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($template['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <a class="btn secondary" href="<?= BASE_URL ?>/admin/template/edit/<?= (int)$template['id'] ?>">Edit</a>
                        <a class="btn secondary" href="<?= BASE_URL ?>/admin/template/toggle/<?= (int)$template['id'] ?>">Toggle</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['templates'])): ?><tr><td colspan="4" class="muted">Belum ada template.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>