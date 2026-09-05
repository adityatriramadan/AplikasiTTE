<div class="card">
    <h2>Daftar Surat Keluar</h2>
    <form method="get" action="<?= BASE_URL ?>/sekretaris/surat-keluar/daftar">
        <div class="field"><label>Cari</label><input name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nomor surat atau perihal"></div>
        <button class="btn" type="submit">Cari</button>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead><tr><th>Nomor</th><th>Perihal</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach (($data['surat_list'] ?? []) as $surat): ?>
                <tr>
                    <td><?= htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['status'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a class="btn secondary" href="<?= BASE_URL ?>/sekretaris/surat-keluar/detail/<?= (int)$surat['id'] ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['surat_list'])): ?><tr><td colspan="4" class="muted">Belum ada surat.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>