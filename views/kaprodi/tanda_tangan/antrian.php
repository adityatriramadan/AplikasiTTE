<?php
$surat_menunggu = $data['surat_menunggu'] ?? [];
$total = count($surat_menunggu);
?>

<?php if (!empty($data['success'])): ?>
<div class="alert success"><?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($data['error'])): ?>
<div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="margin:0 0 4px;">Antrian Tanda Tangan</h2>
        <span class="muted">Surat yang menunggu persetujuan dan tanda tangan digital Anda</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px;">
        <span style="background:<?= $total > 0 ? '#b4531822' : '#1f7a4d22' ?>;color:<?= $total > 0 ? '#b45318' : '#1f7a4d' ?>;padding:8px 16px;border-radius:999px;font-weight:700;font-size:18px;border:1px solid <?= $total > 0 ? '#b4531844' : '#1f7a4d44' ?>;">
            <?= $total ?> surat
        </span>
    </div>
</div>

<div class="card">
    <?php if (empty($surat_menunggu)): ?>
    <div style="text-align:center;padding:40px;color:var(--muted);">
        <div style="font-size:48px;margin-bottom:12px;">✅</div>
        <p style="margin:0;font-size:16px;font-weight:600;">Tidak ada surat menunggu tanda tangan.</p>
        <p style="margin:8px 0 0;font-size:14px;">Semua surat sudah diproses.</p>
    </div>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Nomor Surat</th>
                <th>Perihal</th>
                <th>Pembuat</th>
                <th>Tanggal Surat</th>
                <th>Diterima</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($surat_menunggu as $surat): ?>
            <tr>
                <td><code style="font-size:12px;"><?= htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8') ?></code></td>
                <td><?= htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($surat['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($surat['tanggal_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td style="color:var(--muted);font-size:13px;"><?= htmlspecialchars(substr($surat['created_at'] ?? '-', 0, 16), ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <a class="btn secondary" href="<?= BASE_URL ?>/kaprodi/review/<?= (int)$surat['id'] ?>" style="font-size:13px;">
                        📋 Review &amp; Tanda Tangan
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>