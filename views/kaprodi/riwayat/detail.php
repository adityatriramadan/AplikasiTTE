<?php
$surat      = $data['surat'] ?? [];
$tt         = $data['tanda_tangan'] ?? [];
$distribusi = $data['distribusi'] ?? [];
$suratId    = (int)($surat['id'] ?? 0);
$isiData    = json_decode($surat['isi_data'] ?? '{}', true) ?? [];
?>

<div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="margin:0 0 4px;">Detail Surat Ditandatangani</h2>
        <span style="display:inline-block;padding:4px 12px;border-radius:999px;background:#1f7a4d22;color:#1f7a4d;font-size:13px;font-weight:600;border:1px solid #1f7a4d44;">✅ Ditandatangani</span>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php if (!empty($tt['pdf_path'])): ?>
        <a class="btn ok" href="<?= BASE_URL ?>/public/pdf/<?= htmlspecialchars($tt['pdf_path'], ENT_QUOTES, 'UTF-8') ?>" target="_blank">📄 Unduh PDF</a>
        <a class="btn secondary" href="<?= BASE_URL ?>/verifikasi?id=<?= $suratId ?>" target="_blank">🔍 Verifikasi Publik</a>
        <?php endif; ?>
        <a class="btn secondary" href="<?= BASE_URL ?>/kaprodi/riwayat">← Kembali</a>
    </div>
</div>

<div class="grid cols-2">
    <div class="card">
        <h3>Informasi Surat</h3>
        <table style="width:100%;border-collapse:collapse;">
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;width:45%;">Nomor Surat</td>
                <td style="padding:10px 0;"><strong><?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></td>
            </tr>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Jenis</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($surat['nama_jenis'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Perihal</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($surat['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Tanggal Surat</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($surat['tanggal_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr>
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Dibuat Oleh</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($surat['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        </table>
    </div>

    <div class="card">
        <h3 style="color:var(--ok);">🔏 Detail Tanda Tangan Digital</h3>
        <table style="width:100%;border-collapse:collapse;">
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;width:45%;">Ditandatangani</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($tt['nama_kaprodi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Waktu</td>
                <td style="padding:10px 0;"><?= !empty($tt['timestamp_tandatangan']) ? date('d M Y H:i:s', (int)$tt['timestamp_tandatangan']) : '-' ?></td>
            </tr>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Hash SHA-256</td>
                <td style="padding:10px 0;font-family:monospace;font-size:11px;word-break:break-all;color:var(--muted);"><?= htmlspecialchars($tt['hash_sha256'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php if (!empty($tt['qr_code_url'])): ?>
            <tr>
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">URL Verifikasi</td>
                <td style="padding:10px 0;font-size:12px;word-break:break-all;">
                    <a href="<?= htmlspecialchars($tt['qr_code_url'], ENT_QUOTES, 'UTF-8') ?>" target="_blank"><?= htmlspecialchars($tt['qr_code_url'], ENT_QUOTES, 'UTF-8') ?></a>
                </td>
            </tr>
            <?php endif; ?>
        </table>

        <?php if (!empty($tt['pdf_path'])): ?>
        <div style="margin-top:14px;">
            <?php $qrFile = BASE_PATH . '/public/qr/qr_' . $suratId . '.png'; ?>
            <?php if (file_exists($qrFile)): ?>
            <p style="font-weight:600;margin:0 0 8px;color:var(--muted);font-size:13px;">QR Code Verifikasi:</p>
            <img src="<?= BASE_URL ?>/public/qr/qr_<?= $suratId ?>.png" alt="QR Code" style="width:120px;height:120px;border:1px solid var(--line);border-radius:8px;padding:6px;">
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>