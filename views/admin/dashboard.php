<?php
$jumlahUser   = $data['jumlah_user']  ?? [];
$jumlahSurat  = $data['jumlah_surat'] ?? [];
$logTerbaru   = $data['log_terbaru']  ?? [];
$logHariIni   = $data['log_hari_ini'] ?? 0;
$kunciList    = $data['kunci_list']   ?? [];

$totalUser = array_sum($jumlahUser);
$totalSurat = array_sum($jumlahSurat);
?>

<!-- Hero Admin -->
<div style="background:linear-gradient(135deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);border-radius:20px;padding:28px 32px;margin-bottom:20px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div style="font-size:13px;opacity:.7;margin-bottom:4px;">Panel Administrator 🛡️</div>
        <h2 style="margin:0 0 6px;font-size:24px;"><?= htmlspecialchars($data['current_user']['nama'] ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></h2>
        <div style="opacity:.75;font-size:14px;">E-Office Tanda Tangan Digital — Prodi Teknik Informatika UNPAM</div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:13px;opacity:.7;margin-bottom:6px;"><?= date('l, d F Y') ?></div>
        <div style="display:flex;gap:8px;justify-content:flex-end;flex-wrap:wrap;">
            <a href="<?= BASE_URL ?>/admin/users" style="background:rgba(255,255,255,.15);color:#fff;padding:8px 14px;border-radius:10px;font-size:13px;border:1px solid rgba(255,255,255,.2);text-decoration:none;">👥 Kelola User</a>
            <a href="<?= BASE_URL ?>/admin/kunci" style="background:rgba(255,255,255,.15);color:#fff;padding:8px 14px;border-radius:10px;font-size:13px;border:1px solid rgba(255,255,255,.2);text-decoration:none;">🔑 Kunci RSA</a>
        </div>
    </div>
</div>

<?php if (!extension_loaded('gd')): ?>
<div style="margin-bottom:18px;">
    <div style="background:#fff7ed;border:1px solid #fed7aa;padding:12px;border-radius:8px;color:#92400e;">
        <strong>Perhatian:</strong> Ekstensi PHP <em>GD</em> tidak aktif. Beberapa fitur (mis. konversi/penyimpanan QR sebagai PNG) mungkin tidak tersedia. Aktifkan ekstensi GD di php.ini lalu restart Apache.
    </div>
</div>
<?php endif; ?>

<!-- Stats User -->
<div style="margin-bottom:12px;"><span style="font-size:13px;font-weight:600;color:var(--muted);">📊 STATISTIK USER</span></div>
<div class="grid cols-4" style="margin-bottom:20px;">
    <?php
    $userRoles = [
        'admin'      => ['label'=>'Admin',      'color'=>'#1a1a2e','icon'=>'🛡️'],
        'kaprodi'    => ['label'=>'Kaprodi',    'color'=>'#0f3460','icon'=>'👨‍💼'],
        'sekretaris' => ['label'=>'Sekretaris', 'color'=>'#1d6fd8','icon'=>'👩‍💻'],
        'dosen'      => ['label'=>'Dosen',      'color'=>'#1f7a4d','icon'=>'👨‍🏫'],
    ];
    foreach ($userRoles as $role => $info):
    ?>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:<?= $info['color'] ?>;border-radius:4px 0 0 4px;"></div>
        <div style="font-size:24px;margin-bottom:8px;"><?= $info['icon'] ?></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:4px;"><?= $info['label'] ?></div>
        <div style="font-size:32px;font-weight:800;color:<?= $info['color'] ?>;"><?= (int)($jumlahUser[$role] ?? 0) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Stats Surat -->
<div style="margin-bottom:12px;"><span style="font-size:13px;font-weight:600;color:var(--muted);">📄 STATISTIK SURAT</span></div>
<div class="grid cols-4" style="margin-bottom:24px;">
    <?php
    $suratStats = [
        'draft'           => ['label'=>'Draft',           'color'=>'#667085'],
        'menunggu'        => ['label'=>'Menunggu TTD',    'color'=>'#b45309'],
        'ditandatangani'  => ['label'=>'Ditandatangani',  'color'=>'#1f7a4d'],
        'didistribusikan' => ['label'=>'Didistribusikan', 'color'=>'#1d6fd8'],
    ];
    foreach ($suratStats as $status => $info):
    ?>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:<?= $info['color'] ?>;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:4px;"><?= $info['label'] ?></div>
        <div style="font-size:32px;font-weight:800;color:<?= $info['color'] ?>;"><?= (int)($jumlahSurat[$status] ?? 0) ?></div>
    </div>
    <?php endforeach; ?>
</div>

<div class="grid cols-2" style="margin-bottom:20px;">
    <!-- Kunci RSA -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 style="margin:0;">🔐 Status Kunci RSA</h3>
            <a href="<?= BASE_URL ?>/admin/kunci" class="btn secondary" style="font-size:13px;padding:7px 14px;">Kelola Kunci</a>
        </div>
        <?php if (empty($kunciList)): ?>
        <div style="text-align:center;padding:24px;color:var(--muted);">
            <div style="font-size:36px;margin-bottom:8px;">🔑</div>
            <p style="margin:0;">Belum ada kunci RSA yang di-generate.</p>
            <a href="<?= BASE_URL ?>/admin/kunci/generate" class="btn" style="margin-top:12px;display:inline-block;">Generate Kunci RSA</a>
        </div>
        <?php else: ?>
        <table class="table">
            <thead><tr><th>Kaprodi</th><th>Status</th><th>Dibuat</th></tr></thead>
            <tbody>
                <?php foreach ($kunciList as $k): ?>
                <tr>
                    <td><?= htmlspecialchars($k['nama'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php if ($k['is_aktif']): ?>
                        <span style="color:#1f7a4d;font-weight:600;font-size:13px;">✓ Aktif</span>
                        <?php else: ?>
                        <span style="color:var(--muted);font-size:13px;">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:13px;color:var(--muted);"><?= htmlspecialchars(substr($k['created_at'] ?? '', 0, 10), ENT_QUOTES, 'UTF-8') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Log Terbaru -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 style="margin:0;">📋 Log Aktivitas Terbaru</h3>
            <a href="<?= BASE_URL ?>/admin/log" class="btn secondary" style="font-size:13px;padding:7px 14px;">Lihat Semua</a>
        </div>
        <?php if (empty($logTerbaru)): ?>
        <p class="muted" style="text-align:center;padding:20px 0;">Belum ada log aktivitas.</p>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;max-height:300px;overflow-y:auto;">
            <?php foreach (array_slice($logTerbaru, 0, 7) as $log): ?>
            <div style="padding:10px 12px;border-radius:10px;background:#f9fafb;border:1px solid var(--line);">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:8px;">
                    <div>
                        <span style="font-weight:600;font-size:13px;"><?= htmlspecialchars($log['nama'] ?? 'System', ENT_QUOTES, 'UTF-8') ?></span>
                        <span style="font-size:12px;color:var(--muted);margin-left:6px;"><?= htmlspecialchars($log['aksi'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <span style="font-size:11px;color:var(--muted);white-space:nowrap;"><?= htmlspecialchars(substr($log['created_at'] ?? '', 0, 16), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <?php if (!empty($log['keterangan'])): ?>
                <div style="font-size:12px;color:var(--muted);margin-top:3px;"><?= htmlspecialchars($log['keterangan'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <h3 style="margin:0 0 16px;">⚡ Menu Admin</h3>
    <div class="grid cols-4">
        <?php
        $menus = [
            ['url'=>'/admin/users',      'icon'=>'👥', 'label'=>'Kelola User',       'desc'=>'Tambah, edit, nonaktifkan user'],
            ['url'=>'/admin/kunci',      'icon'=>'🔑', 'label'=>'Kunci RSA',         'desc'=>'Generate & kelola kunci kaprodi'],
            ['url'=>'/admin/template',   'icon'=>'📝', 'label'=>'Template Surat',    'desc'=>'CRUD template surat'],
            ['url'=>'/admin/log',        'icon'=>'📋', 'label'=>'Log Aktivitas',     'desc'=>'Pantau semua aktivitas sistem'],
        ];
        foreach ($menus as $m):
        ?>
        <a href="<?= BASE_URL ?><?= $m['url'] ?>" style="display:flex;flex-direction:column;align-items:center;padding:20px 16px;border:1px solid var(--line);border-radius:16px;color:var(--text);background:#f9fafb;text-decoration:none;gap:8px;transition:all .15s;">
            <span style="font-size:32px;"><?= $m['icon'] ?></span>
            <div style="font-weight:700;font-size:14px;"><?= $m['label'] ?></div>
            <div style="font-size:12px;color:var(--muted);text-align:center;"><?= $m['desc'] ?></div>
        </a>
        <?php endforeach; ?>
    </div>
</div>