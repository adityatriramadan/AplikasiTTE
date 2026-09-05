<div class="card">
    <h2>Log Aktivitas</h2>
    <form method="get" action="<?= BASE_URL ?>/admin/log">
        <div class="field">
            <label>Cari</label>
            <input type="text" name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nama user atau keterangan">
        </div>
        <button class="btn" type="submit">Cari</button>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>Waktu</th><th>User</th><th>Aksi</th><th>Keterangan</th></tr></thead>
        <tbody>
            <?php foreach (($data['logs'] ?? []) as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($log['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($log['aksi'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($log['keterangan'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['logs'])): ?><tr><td colspan="4" class="muted">Belum ada log.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>