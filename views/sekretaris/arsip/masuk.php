<?php $arsip = $data['arsip'] ?? []; ?>
<div class="card">
    <h2>Arsip Surat Masuk</h2>
    <form method="get" action="<?= BASE_URL ?>/sekretaris/arsip/masuk">
        <div class="field">
            <label for="search">Cari</label>
            <input type="text" id="search" name="search" value="<?= htmlspecialchars($data['search'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Nomor surat, pengirim, atau perihal">
        </div>
        <button class="btn" type="submit">Cari</button>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Nomor</th>
                <th>Pengirim</th>
                <th>Perihal</th>
                <th>Tanggal Terima</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arsip as $surat): ?>
                <tr>
                    <td><?= htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['pengirim'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($surat['tanggal_terima'], ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$arsip): ?>
                <tr><td colspan="4" class="muted">Arsip kosong.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>