<?php
$surat = $data['surat'] ?? [];
$isiData = json_decode($surat['isi_data'] ?? '{}', true) ?? [];
?>

<?php if (!empty($data['error'])): ?>
<div class="alert error"><?= htmlspecialchars($data['error'], ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="margin:0 0 4px;">Review Surat — Sebelum Tanda Tangan</h2>
        <span class="muted">Periksa detail surat dengan seksama sebelum menandatangani secara digital</span>
    </div>
    <a class="btn secondary" href="<?= BASE_URL ?>/kaprodi/antrian">← Kembali ke Antrian</a>
</div>

<div class="grid cols-2">
    <!-- INFORMASI SURAT -->
    <div class="card">
        <h3>Informasi Surat</h3>
        <table style="width:100%;border-collapse:collapse;">
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);width:45%;font-size:13px;">Nomor Surat</td>
                <td style="padding:10px 0;"><strong><?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong></td>
            </tr>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Jenis Surat</td>
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
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Pembuat</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($surat['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php if (!empty($surat['penerima_nama'])): ?>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;">Penerima</td>
                <td style="padding:10px 0;"><?= htmlspecialchars($surat['penerima_nama'], ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
            <?php endif; ?>
            <?php foreach ($isiData as $key => $val): ?>
            <tr style="border-bottom:1px solid var(--line);">
                <td style="padding:10px 0;color:var(--muted);font-size:13px;"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key)), ENT_QUOTES, 'UTF-8') ?></td>
                <td style="padding:10px 0;"><?= nl2br(htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8')) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- AKSI TANDA TANGAN -->
    <div>
        <!-- Form Tanda Tangan -->
        <div class="card" style="border-left:4px solid var(--ok);margin-bottom:16px;">
            <h3 style="color:var(--ok);margin-top:0;">✅ Setujui & Lanjutkan</h3>
            <p style="color:var(--muted);font-size:14px;margin-top:0;">
                Setelah detail surat benar, lanjutkan ke halaman input PIN untuk memproses tanda tangan digital.
            </p>
            <a class="btn ok" href="<?= BASE_URL ?>/kaprodi/input-pin/<?= (int)($surat['id'] ?? 0) ?>" style="width:100%;text-align:center;font-size:15px;padding:14px;">
                🔏 Lanjut ke Input PIN
            </a>
        </div>

        <!-- Form Tolak -->
        <div class="card" style="border-left:4px solid var(--danger);">
            <h3 style="color:var(--danger);margin-top:0;">❌ Tolak Surat</h3>
            <p style="color:var(--muted);font-size:14px;margin-top:0;">
                Jika ada kesalahan, tolak surat dan berikan alasan yang jelas kepada Sekretaris.
            </p>
            <form method="post" action="<?= BASE_URL ?>/kaprodi/tolak" onsubmit="return confirm('Tolak surat ini?')">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($data['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="surat_id"   value="<?= (int)($surat['id'] ?? 0) ?>">
                <div class="field">
                    <label for="alasan_tolak">Alasan Penolakan</label>
                    <textarea id="alasan_tolak" name="alasan" rows="4" required
                              placeholder="Tuliskan alasan penolakan surat ini…"></textarea>
                </div>
                <button class="btn danger" type="submit" style="width:100%;font-size:15px;padding:12px;">
                    Tolak Surat Ini
                </button>
            </form>
        </div>
    </div>
</div>

<!-- PREVIEW KONTEN SURAT -->
<?php if (!empty($surat['konten_template'])): ?>
<div class="card">
    <h3>Preview Konten Surat</h3>
    <div style="border:1px solid var(--line);border-radius:12px;padding:24px;background:#fafbfd;max-height:500px;overflow-y:auto;">
        <?php
        // Render template dengan data surat
        $konten = $surat['konten_template'];
        foreach ($isiData as $key => $val) {
            $konten = str_replace('{{' . $key . '}}', htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8'), $konten);
        }
        $konten = str_replace('{{nomor_surat}}',  htmlspecialchars($surat['nomor_surat'] ?? '', ENT_QUOTES, 'UTF-8'), $konten);
        $konten = str_replace('{{tanggal_surat}}', htmlspecialchars($surat['tanggal_surat'] ?? '', ENT_QUOTES, 'UTF-8'), $konten);
        $konten = str_replace('{{perihal}}',       htmlspecialchars($surat['perihal'] ?? '', ENT_QUOTES, 'UTF-8'), $konten);
        echo $konten;
        ?>
    </div>
</div>
<?php endif; ?>