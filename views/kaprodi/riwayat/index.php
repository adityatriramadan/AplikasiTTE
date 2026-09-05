<div class="card">
    <h2>Riwayat Tanda Tangan</h2>
    <form method="get" action="<?= BASE_URL ?>/kaprodi/riwayat">
        <div class="field"><label>Cari</label><input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nomor surat atau perihal"></div>
        <button class="btn" type="submit">Cari</button>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>Nomor</th><th>Perihal</th><th>Waktu</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach (($data['riwayat'] ?? []) as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($item['perihal'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y H:i', (int)$item['timestamp_tandatangan']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a class="btn secondary" href="<?= BASE_URL ?>/kaprodi/riwayat/detail/<?= (int)$item['surat_id'] ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['riwayat'])): ?><tr><td colspan="4" class="muted">Belum ada riwayat.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>