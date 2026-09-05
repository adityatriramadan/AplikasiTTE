<div class="alert success" style="margin-top:16px;padding:20px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:36px;">✅</span>
        <div>
            <strong style="font-size:16px;display:block;">DOKUMEN VALID</strong>
            <span>Tanda tangan digital dan hash dokumen cocok. Dokumen ini asli dan tidak dimodifikasi.</span>
        </div>
    </div>
</div>

<?php if (!empty($surat)): ?>
<div class="card" style="margin-top:16px;">
    <h3 style="margin-top:0;color:var(--ok);">🔏 Detail Tanda Tangan Digital</h3>
    <table style="width:100%;border-collapse:collapse;">
        <tr style="border-bottom:1px solid var(--line);">
            <td style="padding:10px 0;color:var(--muted);font-size:13px;width:45%;">Nomor Surat</td>
            <td style="padding:10px 0;"><strong><?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></td>
        </tr>
        <tr style="border-bottom:1px solid var(--line);">
            <td style="padding:10px 0;color:var(--muted);font-size:13px;">Perihal</td>
            <td style="padding:10px 0;"><?= htmlspecialchars($surat['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <?php if (!empty($tandaTangan)): ?>
        <tr style="border-bottom:1px solid var(--line);">
            <td style="padding:10px 0;color:var(--muted);font-size:13px;">Ditandatangani Oleh</td>
            <td style="padding:10px 0;"><?= htmlspecialchars($tandaTangan['nama_kaprodi'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
        </tr>
        <tr style="border-bottom:1px solid var(--line);">
            <td style="padding:10px 0;color:var(--muted);font-size:13px;">Waktu Tanda Tangan</td>
            <td style="padding:10px 0;"><?= date('d F Y H:i:s', (int)($tandaTangan['timestamp_tandatangan'] ?? 0)) ?> WIB</td>
        </tr>
        <tr>
            <td style="padding:10px 0;color:var(--muted);font-size:13px;">Hash SHA-256</td>
            <td style="padding:10px 0;font-family:monospace;font-size:11px;word-break:break-all;color:#667085;">
                <?= htmlspecialchars($tandaTangan['hash_sha256'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
            </td>
        </tr>
        <?php endif; ?>
    </table>
</div>
<?php endif; ?>