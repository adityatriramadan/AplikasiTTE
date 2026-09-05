<div class="card">
    <h2>Surat Masuk</h2>
    <p><a class="btn" href="<?= BASE_URL ?>/sekretaris/surat-masuk/tambah">Input Surat Masuk</a></p>
    <table class="table">
        <thead><tr><th>Nomor</th><th>Pengirim</th><th>Perihal</th><th>Aksi</th></tr></thead>
        <tbody>
            <?php foreach (($data['surat_masuk'] ?? []) as $surat): ?>
                <tr>
                    <td><?= htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['pengirim'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><a class="btn secondary" href="<?= BASE_URL ?>/sekretaris/surat-masuk/disposisi/<?= (int)$surat['id'] ?>">Disposisi</a></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($data['surat_masuk'])): ?><tr><td colspan="4" class="muted">Belum ada surat masuk.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>