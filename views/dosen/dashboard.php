<?php
$dokumenTerbaru = $data['dokumen_terbaru'] ?? [];
$totalDokumen   = $data['total_dokumen']   ?? 0;
$notifCount     = $data['notif_count']     ?? 0;
?>

<!-- Hero Dosen -->
<div style="background:linear-gradient(135deg,#1f7a4d 0%,#166534 100%);border-radius:20px;padding:28px 32px;margin-bottom:20px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div style="font-size:13px;opacity:.8;margin-bottom:4px;">Selamat datang 👋</div>
        <h2 style="margin:0 0 6px;font-size:24px;"><?= htmlspecialchars($data['current_user']['nama'] ?? 'Dosen', ENT_QUOTES, 'UTF-8') ?></h2>
        <div style="opacity:.85;font-size:14px;"><?= htmlspecialchars($data['current_user']['jabatan'] ?? 'Dosen', ENT_QUOTES, 'UTF-8') ?> — Prodi Teknik Informatika, UNPAM</div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:13px;opacity:.75;margin-bottom:6px;"><?= date('l, d F Y') ?></div>
        <a href="<?= BASE_URL ?>/dosen/dokumen" style="background:rgba(255,255,255,.2);color:#fff;padding:10px 20px;border-radius:12px;font-size:14px;font-weight:600;border:1px solid rgba(255,255,255,.3);display:inline-block;text-decoration:none;">📂 Lihat Dokumen</a>
    </div>
</div>

<!-- Stats -->
<div class="grid cols-3" style="margin-bottom:20px;">
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#1f7a4d;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Total Dokumen Diterima</div>
        <div style="font-size:36px;font-weight:800;color:#1f7a4d;"><?= $totalDokumen ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">dokumen yang didistribusikan</div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#b42318;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Notifikasi Belum Dibaca</div>
        <div style="font-size:36px;font-weight:800;color:#b42318;"><?= $notifCount ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">perlu diperhatikan</div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#1d6fd8;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Dokumen Terbaru</div>
        <div style="font-size:36px;font-weight:800;color:#1d6fd8;"><?= count($dokumenTerbaru) ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">ditampilkan di sini</div>
    </div>
</div>

<div class="grid cols-2">
    <!-- Dokumen Terbaru -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 style="margin:0;">📄 Dokumen Terbaru</h3>
            <a href="<?= BASE_URL ?>/dosen/dokumen" class="btn secondary" style="font-size:13px;padding:7px 14px;">Lihat Semua</a>
        </div>
        <?php if (empty($dokumenTerbaru)): ?>
        <div style="text-align:center;padding:30px;color:var(--muted);">
            <div style="font-size:40px;margin-bottom:8px;">📭</div>
            <p style="margin:0;font-size:14px;">Belum ada dokumen yang didistribusikan untuk Anda.</p>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach ($dokumenTerbaru as $dok): ?>
            <div style="border:1px solid #d1fae5;background:#ecfdf3;border-radius:12px;padding:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:14px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($dok['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($dok['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <div style="display:flex;gap:8px;align-items:center;flex-shrink:0;">
                    <?php if (($dok['status_baca'] ?? '') !== 'dibaca'): ?>
                    <span style="background:#fee2e2;color:#b42318;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:600;">Baru</span>
                    <?php endif; ?>
                    <a href="<?= BASE_URL ?>/dosen/dokumen/detail/<?= (int)$dok['id'] ?>" class="btn secondary" style="font-size:12px;padding:6px 12px;">Lihat →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Aksi & Info -->
    <div style="display:flex;flex-direction:column;gap:16px;">
        <div class="card">
            <h3 style="margin:0 0 14px;">🚀 Aksi Cepat</h3>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="<?= BASE_URL ?>/dosen/dokumen" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid #d1fae5;border-radius:12px;color:var(--text);background:#ecfdf3;text-decoration:none;">
                    <span style="font-size:22px;">📂</span>
                    <div><div style="font-weight:600;font-size:14px;">Semua Dokumen Saya</div><div style="font-size:12px;color:var(--muted);">Lihat dan unduh dokumen resmi</div></div>
                    <span style="margin-left:auto;color:var(--muted);">→</span>
                </a>
            </div>
        </div>

        <!-- Info Verifikasi -->
        <div class="card" style="border-left:4px solid #1d6fd8;">
            <h3 style="margin:0 0 10px;color:#1d6fd8;">🔍 Verifikasi Dokumen</h3>
            <p style="color:var(--muted);font-size:14px;margin:0 0 14px;">Scan QR Code pada dokumen resmi untuk memverifikasi keasliannya, atau masukkan ID dokumen di bawah ini.</p>
            <form method="get" action="<?= BASE_URL ?>/verifikasi" style="display:flex;gap:8px;">
                <input type="number" name="id" placeholder="ID Dokumen..." style="flex:1;padding:10px 12px;border:1px solid var(--line);border-radius:10px;font:inherit;">
                <button class="btn" type="submit" style="white-space:nowrap;">Verifikasi</button>
            </form>
        </div>
    </div>
</div>