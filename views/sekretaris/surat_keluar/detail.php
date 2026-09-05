<?php
$surat       = $data['surat']       ?? [];
$tt          = $data['tanda_tangan'] ?? null;
$distribusi  = $data['distribusi']  ?? [];
$suratId     = (int)($surat['id']   ?? 0);
$status      = $surat['status']     ?? '';

$statusLabel = [
    'draft'          => ['label' => 'Draft',            'color' => '#667085'],
    'menunggu'       => ['label' => 'Menunggu Tanda Tangan', 'color' => '#b45309'],
    'ditandatangani' => ['label' => 'Ditandatangani',   'color' => '#1f7a4d'],
    'ditolak'        => ['label' => 'Ditolak',          'color' => '#b42318'],
    'didistribusikan'=> ['label' => 'Didistribusikan',  'color' => '#0f4c81'],
];
$statusInfo  = $statusLabel[$status] ?? ['label' => ucfirst($status), 'color' => '#667085'];
?>

<?php if (!empty($data['success'])): ?>
<div class="alert success"><?= htmlspecialchars($data['success'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($data['error'])): ?>
<div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 4px;">Detail Surat Keluar</h2>
            <span style="display:inline-block;padding:4px 12px;border-radius:999px;background:<?= htmlspecialchars($statusInfo['color'], ENT_QUOTES, 'UTF-8') ?>22;color:<?= htmlspecialchars($statusInfo['color'], ENT_QUOTES, 'UTF-8') ?>;font-size:13px;font-weight:600;border:1px solid <?= htmlspecialchars($statusInfo['color'], ENT_QUOTES, 'UTF-8') ?>44;">
                <?= htmlspecialchars($statusInfo['label'], ENT_QUOTES, 'UTF-8') ?>
            </span>
        </div>
        <?php if ($status === 'draft' || $status === 'ditolak'): ?>
        <form method="post" action="<?= BASE_URL ?>/sekretaris/ajukan-surat" onsubmit="return confirm('Ajukan surat ini ke Kaprodi untuk ditandatangani?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="surat_id"   value="<?= $suratId ?>">
            <button class="btn ok" type="submit" style="font-size:15px;padding:11px 22px;">📤 Ajukan ke Kaprodi</button>
        </form>
        <?php elseif ($status === 'menunggu'): ?>
        <span style="color:#b45309;font-style:italic;">⏳ Menunggu tanda tangan Kaprodi…</span>
        <?php elseif ($status === 'ditandatangani' && !empty($tt['pdf_path'])): ?>
        <a class="btn ok" href="<?= BASE_URL ?>/public/pdf/<?= htmlspecialchars($tt['pdf_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank" style="font-size:15px;padding:11px 22px;">📄 Unduh PDF</a>
        <?php endif; ?>
    </div>
</div>

<div class="grid cols-2">
    <div class="card">
        <h3>Informasi Surat</h3>
        <table class="table" style="border:0;">
            <tr><td style="color:var(--muted);width:40%;border:0;">Nomor Surat</td><td style="border:0;"><strong><?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></td></tr>
            <tr><td style="color:var(--muted);border:0;">Perihal</td><td style="border:0;"><?= htmlspecialchars($surat['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><td style="color:var(--muted);border:0;">Jenis Template</td><td style="border:0;"><?= htmlspecialchars($surat['nama_jenis'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><td style="color:var(--muted);border:0;">Tanggal Surat</td><td style="border:0;"><?= htmlspecialchars($surat['tanggal_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
            <tr><td style="color:var(--muted);border:0;">Pembuat</td><td style="border:0;"><?= htmlspecialchars($surat['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php if (!empty($surat['penerima_nama'])): ?>
            <tr><td style="color:var(--muted);border:0;">Penerima</td><td style="border:0;"><?= htmlspecialchars($surat['penerima_nama'], ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php endif; ?>
            <?php if ($status === 'ditolak' && !empty($surat['alasan_tolak'])): ?>
            <tr><td style="color:var(--danger);border:0;">Alasan Tolak</td><td style="border:0;color:var(--danger);"><?= htmlspecialchars($surat['alasan_tolak'], ENT_QUOTES, 'UTF-8') ?></td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="card">
        <h3>Tanda Tangan Digital</h3>
        <?php if ($tt): ?>
            <table class="table" style="border:0;">
                <tr><td style="color:var(--muted);width:40%;border:0;">Ditandatangani</td><td style="border:0;"><?= htmlspecialchars($tt['nama_kaprodi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td></tr>
                <tr><td style="color:var(--muted);border:0;">Waktu</td><td style="border:0;"><?= date('d M Y H:i', (int)($tt['timestamp_tandatangan'] ?? 0)) ?></td></tr>
                <tr><td style="color:var(--muted);border:0;">Hash SHA-256</td><td style="border:0;font-family:monospace;font-size:11px;word-break:break-all;"><?= htmlspecialchars(substr($tt['hash_sha256'] ?? '', 0, 32) . '…', ENT_QUOTES, 'UTF-8') ?></td></tr>
            </table>
            <?php if (!empty($tt['pdf_path'])): ?>
            <div style="margin-top:14px;">
                <a class="btn ok" href="<?= BASE_URL ?>/public/pdf/<?= htmlspecialchars($tt['pdf_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">📄 Unduh PDF</a>
                <a class="btn secondary" href="<?= BASE_URL ?>/verifikasi?id=<?= $suratId ?>" target="_blank" style="margin-left:8px;">🔍 Verifikasi</a>
            </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="muted" style="margin:0;">Belum ditandatangani.</p>
        <?php endif; ?>
    </div>
</div>

<?php if ($status === 'ditandatangani' || $status === 'didistribusikan'): ?>
<div class="card">
    <h3>Distribusi Surat</h3>
    <?php if (!empty($distribusi)): ?>
    <table class="table">
        <thead><tr><th>Penerima</th><th>Tanggal Kirim</th><th>Status Baca</th></tr></thead>
        <tbody>
            <?php foreach ($distribusi as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nama_penerima_internal'] ?? $item['penerima_eksternal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($item['tanggal_kirim'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?php if ($item['status_baca'] === 'dibaca'): ?>
                    <span style="color:var(--ok);">✓ Dibaca</span>
                    <?php else: ?>
                    <span class="muted">Belum dibaca</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <p class="muted" style="margin:0;">Belum ada distribusi.</p>
    <?php endif; ?>
    <?php if ($status === 'ditandatangani'): ?>
    <div style="margin-top:18px;padding-top:14px;border-top:1px solid var(--line);">
        <h4 style="margin:0 0 12px;">Distribusikan Surat</h4>
        <form method="post" action="<?= BASE_URL ?>/sekretaris/distribusi">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="surat_id"   value="<?= $suratId ?>">
            <div class="grid cols-2">
                <div class="field">
                    <label>Penerima Eksternal (nama, opsional)</label>
                    <input type="text" name="penerima_eksternal" placeholder="Nama instansi / orang luar">
                </div>
                <div class="field">
                    <label>Penerima Internal — ID User (pisahkan koma)</label>
                    <input type="text" name="penerima_internal[]" placeholder="Contoh: 4,5">
                </div>
            </div>
            <button class="btn" type="submit">📨 Distribusikan</button>
        </form>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>