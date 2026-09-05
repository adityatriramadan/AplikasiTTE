<?php $templates = $data['templates'] ?? []; ?>

<div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
        <h2 style="margin:0 0 4px;">✏️ Buat Surat Baru</h2>
        <span class="muted">Pilih jenis surat yang akan dibuat — setiap template memiliki format dan nomor surat yang berbeda.</span>
    </div>
    <a href="<?= BASE_URL ?>/sekretaris/surat-keluar" class="btn secondary">← Kembali ke Daftar</a>
</div>

<?php if (empty($templates)): ?>
<div class="card" style="text-align:center;padding:60px 24px;">
    <div style="font-size:48px;margin-bottom:16px;">📋</div>
    <h3 style="margin:0 0 8px;color:var(--muted);">Belum Ada Template Aktif</h3>
    <p style="color:var(--muted);margin:0;">Hubungi Admin untuk menambahkan template surat terlebih dahulu.</p>
</div>
<?php else: ?>

<div style="margin-bottom:12px;color:var(--muted);font-size:14px;">
    <?= count($templates) ?> template tersedia — klik salah satu untuk mulai mengisi data surat
</div>

<div class="grid cols-3">
    <?php
    $templateIcons = [
        'ST'  => ['icon'=>'📨', 'desc'=>'Surat resmi dari instansi'],
        'SK'  => ['icon'=>'📜', 'desc'=>'Keputusan resmi Kaprodi'],
        'SU'  => ['icon'=>'📩', 'desc'=>'Surat untuk pihak internal/eksternal'],
        'ND'  => ['icon'=>'📋', 'desc'=>'Nota internal antar bagian'],
        'BA'  => ['icon'=>'📄', 'desc'=>'Dokumentasi kegiatan/rapat'],
        'SP'  => ['icon'=>'🏷️', 'desc'=>'Surat pengantar dokumen'],
        'DS'  => ['icon'=>'📊', 'desc'=>'Disposisi surat masuk'],
    ];
    $bgColors = ['#eff6ff','#ecfdf3','#fef9c3','#fef3f2','#f5f3ff','#fff7ed','#f0fdf4'];
    $borderColors = ['#bfdbfe','#bbf7d0','#fde68a','#fecaca','#ddd6fe','#fed7aa','#bbf7d0'];
    $textColors = ['#1d4ed8','#166534','#92400e','#991b1b','#6d28d9','#c2410c','#166534'];
    $i = 0;
    foreach ($templates as $t):
        $kode = $t['kode_jenis'] ?? 'ST';
        $tInfo = $templateIcons[$kode] ?? ['icon'=>'📄','desc'=>'Dokumen resmi'];
        $bg = $bgColors[$i % count($bgColors)];
        $border = $borderColors[$i % count($borderColors)];
        $txtColor = $textColors[$i % count($textColors)];
        $i++;
    ?>
    <a href="<?= BASE_URL ?>/sekretaris/isi-form/<?= (int)$t['id'] ?>" style="display:block;padding:24px;border:2px solid <?= $border ?>;background:<?= $bg ?>;border-radius:20px;text-decoration:none;color:var(--text);transition:transform .15s,box-shadow .15s;" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='0 12px 24px rgba(0,0,0,.1)';" onmouseout="this.style.transform='';this.style.boxShadow='';">
        <div style="font-size:40px;margin-bottom:14px;"><?= $tInfo['icon'] ?></div>
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
            <span style="background:<?= $border ?>;color:<?= $txtColor ?>;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:700;"><?= htmlspecialchars($kode, ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div style="font-weight:700;font-size:16px;margin-bottom:6px;"><?= htmlspecialchars($t['nama_jenis'], ENT_QUOTES, 'UTF-8') ?></div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:12px;"><?= $tInfo['desc'] ?></div>
        <div style="font-size:12px;color:var(--muted);border-top:1px solid <?= $border ?>;padding-top:10px;font-family:monospace;">
            Format: <?= htmlspecialchars($t['format_nomor'] ?? '-', ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div style="margin-top:12px;text-align:right;">
            <span style="color:<?= $txtColor ?>;font-size:14px;font-weight:600;">Pilih Template →</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>