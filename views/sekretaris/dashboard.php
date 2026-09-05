<?php
$countStatus   = $data['count_status']  ?? [];
$suratTerbaru  = $data['surat_terbaru'] ?? [];
$notifList     = $data['notif_list']    ?? [];
$notifCount    = $data['notif_count']   ?? 0;

$cntDraft      = $countStatus['draft']           ?? 0;
$cntMenunggu   = $countStatus['menunggu']        ?? 0;
$cntTtd        = $countStatus['ditandatangani']  ?? 0;
$cntTolak      = $countStatus['ditolak']         ?? 0;
$cntDist       = $countStatus['didistribusikan'] ?? 0;
$cntTotal      = array_sum($countStatus);
?>

<!-- Hero -->
<div style="background:linear-gradient(135deg,#1d6fd8 0%,#0f4c81 100%);border-radius:20px;padding:28px 32px;margin-bottom:20px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div style="font-size:13px;opacity:.8;margin-bottom:4px;">Selamat datang kembali 👋</div>
        <h2 style="margin:0 0 6px;font-size:24px;"><?= htmlspecialchars($data['current_user']['nama'] ?? 'Sekretaris', ENT_QUOTES, 'UTF-8') ?></h2>
        <div style="opacity:.85;font-size:14px;">Sekretaris — Prodi Teknik Informatika, UNPAM</div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:13px;opacity:.75;margin-bottom:6px;"><?= date('l, d F Y') ?></div>
        <a href="<?= BASE_URL ?>/sekretaris/buat-surat" style="background:rgba(255,255,255,.2);color:#fff;padding:10px 20px;border-radius:12px;font-size:14px;font-weight:600;border:1px solid rgba(255,255,255,.3);display:inline-block;text-decoration:none;">✏️ Buat Surat Baru</a>
    </div>
</div>

<!-- Stats Row -->
<div class="grid cols-4" style="margin-bottom:20px;">
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#667085;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Draft</div>
        <div style="font-size:32px;font-weight:800;color:#667085;"><?= $cntDraft ?></div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#b45309;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Menunggu Tanda Tangan</div>
        <div style="font-size:32px;font-weight:800;color:#b45309;"><?= $cntMenunggu ?></div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#1f7a4d;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Ditandatangani</div>
        <div style="font-size:32px;font-weight:800;color:#1f7a4d;"><?= $cntTtd ?></div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#b42318;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Ditolak</div>
        <div style="font-size:32px;font-weight:800;color:#b42318;"><?= $cntTolak ?></div>
    </div>
</div>

<div class="grid cols-2" style="margin-bottom:20px;">
    <!-- Surat Terbaru -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 style="margin:0;">📄 Surat Terbaru Saya</h3>
            <a href="<?= BASE_URL ?>/sekretaris/surat-keluar" class="btn secondary" style="font-size:13px;padding:7px 14px;">Lihat Semua</a>
        </div>
        <?php if (empty($suratTerbaru)): ?>
        <div style="text-align:center;padding:30px;color:var(--muted);">
            <div style="font-size:40px;margin-bottom:8px;">📂</div>
            <p style="margin:0;font-size:14px;">Belum ada surat yang dibuat.</p>
            <a href="<?= BASE_URL ?>/sekretaris/buat-surat" class="btn" style="margin-top:14px;display:inline-block;">Buat Surat Pertama</a>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <?php
            $statusColors = [
                'draft'           => ['bg'=>'#f9fafb','border'=>'#d0d5dd','text'=>'#667085','label'=>'Draft'],
                'menunggu'        => ['bg'=>'#fffbf0','border'=>'#fcd34d','text'=>'#b45309','label'=>'Menunggu'],
                'ditandatangani'  => ['bg'=>'#ecfdf3','border'=>'#abefc6','text'=>'#1f7a4d','label'=>'✓ Ditandatangani'],
                'ditolak'         => ['bg'=>'#fef3f2','border'=>'#fecdca','text'=>'#b42318','label'=>'✗ Ditolak'],
                'didistribusikan' => ['bg'=>'#eff6ff','border'=>'#bfdbfe','text'=>'#1d4ed8','label'=>'📨 Didistribusikan'],
            ];
            foreach ($suratTerbaru as $s):
                $sc = $statusColors[$s['status']] ?? $statusColors['draft'];
            ?>
            <div style="border:1px solid <?= $sc['border'] ?>;background:<?= $sc['bg'] ?>;border-radius:12px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($s['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($s['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    <span style="color:<?= $sc['text'] ?>;font-size:12px;font-weight:600;"><?= $sc['label'] ?></span>
                    <a href="<?= BASE_URL ?>/sekretaris/surat-keluar/detail/<?= (int)$s['id'] ?>" style="color:var(--brand-2);font-size:12px;">→</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Aksi Cepat + Notifikasi -->
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <h3 style="margin:0 0 14px;">🚀 Aksi Cepat</h3>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="<?= BASE_URL ?>/sekretaris/buat-surat" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #bfdbfe;border-radius:12px;color:var(--text);background:#eff6ff;text-decoration:none;">
                    <span style="font-size:22px;">✏️</span>
                    <div><div style="font-weight:600;font-size:14px;">Buat Surat Baru</div><div style="font-size:12px;color:var(--muted);">Pilih template dan isi data surat</div></div>
                    <span style="margin-left:auto;color:var(--muted);">→</span>
                </a>
                <a href="<?= BASE_URL ?>/sekretaris/surat-masuk" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:12px;color:var(--text);background:#f9fafb;text-decoration:none;">
                    <span style="font-size:22px;">📥</span>
                    <div><div style="font-weight:600;font-size:14px;">Surat Masuk</div><div style="font-size:12px;color:var(--muted);">Input dan disposisi surat masuk</div></div>
                    <span style="margin-left:auto;color:var(--muted);">→</span>
                </a>
                <a href="<?= BASE_URL ?>/sekretaris/arsip" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:12px;color:var(--text);background:#f9fafb;text-decoration:none;">
                    <span style="font-size:22px;">🗄️</span>
                    <div><div style="font-weight:600;font-size:14px;">Arsip Surat</div><div style="font-size:12px;color:var(--muted);">Cari surat di arsip</div></div>
                    <span style="margin-left:auto;color:var(--muted);">→</span>
                </a>
            </div>
        </div>
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <h3 style="margin:0;">🔔 Notifikasi</h3>
                <?php if ($notifCount > 0): ?><span style="background:#fef3f2;color:#b42318;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;"><?= $notifCount ?> baru</span><?php endif; ?>
            </div>
            <?php if (empty($notifList)): ?>
            <p style="color:var(--muted);font-size:14px;margin:0;text-align:center;padding:12px 0;">Tidak ada notifikasi.</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach ($notifList as $n): ?>
                <div style="padding:10px 12px;border-radius:10px;background:<?= $n['is_dibaca'] ? '#f9fafb' : '#eff6ff' ?>;border:1px solid <?= $n['is_dibaca'] ? 'var(--line)' : '#bfdbfe' ?>;">
                    <div style="font-size:13px;"><?= htmlspecialchars($n['pesan'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;"><?= htmlspecialchars(substr($n['created_at'] ?? '', 0, 16), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>