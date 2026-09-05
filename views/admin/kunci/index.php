<div class="card">
    <h2>Kunci RSA Kaprodi</h2>
    <p><a class="btn" href="<?= BASE_URL ?>/admin/kunci/generate">Generate Kunci Baru</a></p>
    <?php if (!empty($data['success'])): ?><div class="alert success"><?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if (!empty($data['error'])): ?><div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <table class="table">
        <thead><tr><th>Kaprodi</th><th>Aktif</th><th>Created</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach (($data['kunci_list'] ?? []) as $kunci): ?>
                <tr>
                    <td><?= htmlspecialchars($kunci['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= !empty($kunci['is_aktif']) ? 'Ya' : 'Tidak' ?></td>
                    <td><?= htmlspecialchars($kunci['created_at'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a class="btn secondary" href="<?= BASE_URL ?>/admin/kunci/nonaktifkan/<?= (int)$kunci['id'] ?>">Nonaktifkan</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['kunci_list'])): ?><tr><td colspan="4" class="muted">Belum ada kunci.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>