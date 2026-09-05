<?php
$jumlahSurat    = $data['jumlah_surat']    ?? [];
$suratMenunggu  = $data['surat_menunggu']  ?? [];
$tandaBulanIni  = $data['tanda_bulan_ini'] ?? 0;
$hasKunci       = $data['has_kunci']       ?? false;
$notifList      = $data['notif_list']      ?? [];
$notifCount     = $data['notif_count']     ?? 0;

$cntMenunggu    = count($suratMenunggu);
$cntTtd         = $jumlahSurat['ditandatangani'] ?? 0;
$cntDraft       = $jumlahSurat['draft'] ?? 0;
$cntDist        = $jumlahSurat['didistribusikan'] ?? 0;
?>

<!-- Hero Greeting -->
<div style="background:linear-gradient(135deg,#123a63 0%,#1d6fd8 100%);border-radius:20px;padding:28px 32px;margin-bottom:20px;color:#fff;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div style="font-size:13px;opacity:.8;margin-bottom:4px;">Selamat datang kembali 👋</div>
        <h2 style="margin:0 0 6px;font-size:24px;"><?= htmlspecialchars($data['current_user']['nama'] ?? 'Kaprodi', ENT_QUOTES, 'UTF-8') ?></h2>
        <div style="opacity:.85;font-size:14px;">Kepala Program Studi — Teknik Informatika, UNPAM</div>
    </div>
    <div style="text-align:right;">
        <div style="font-size:13px;opacity:.75;margin-bottom:4px;"><?= date('l, d F Y') ?></div>
        <?php if (!$hasKunci): ?>
        <a href="<?= BASE_URL ?>/admin/kunci" style="background:rgba(255,255,255,.2);color:#fff;padding:8px 16px;border-radius:10px;font-size:13px;border:1px solid rgba(255,255,255,.3);display:inline-block;">⚠️ Kunci RSA belum disiapkan</a>
        <?php else: ?>
        <span style="background:rgba(255,255,255,.15);padding:8px 16px;border-radius:10px;font-size:13px;border:1px solid rgba(255,255,255,.25);">🔐 Kunci RSA aktif</span>
        <?php endif; ?>
    </div>
</div>

<!-- Statistik Cards -->
<div class="grid cols-4" style="margin-bottom:20px;">
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#b45309;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Menunggu Tanda Tangan</div>
        <div style="font-size:32px;font-weight:800;color:#b45309;"><?= $cntMenunggu ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">surat antrian</div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#1d6fd8;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Ditandatangani Bulan Ini</div>
        <div style="font-size:32px;font-weight:800;color:#1d6fd8;"><?= $tandaBulanIni ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">dokumen</div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#1f7a4d;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Total Ditandatangani</div>
        <div style="font-size:32px;font-weight:800;color:#1f7a4d;"><?= $cntTtd ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">semua waktu</div>
    </div>
    <div style="background:#fff;border:1px solid var(--line);border-radius:16px;padding:20px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:0;left:0;width:4px;height:100%;background:#6941c6;border-radius:4px 0 0 4px;"></div>
        <div style="color:var(--muted);font-size:13px;margin-bottom:6px;">Sudah Didistribusikan</div>
        <div style="font-size:32px;font-weight:800;color:#6941c6;"><?= $cntDist ?></div>
        <div style="font-size:12px;color:var(--muted);margin-top:4px;">surat keluar</div>
    </div>
</div>

<div class="grid cols-2" style="margin-bottom:20px;">
    <!-- Antrian Tanda Tangan -->
    <div class="card">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 style="margin:0;">⏳ Antrian Tanda Tangan</h3>
            <a href="<?= BASE_URL ?>/kaprodi/antrian" class="btn secondary" style="font-size:13px;padding:7px 14px;">Lihat Semua</a>
        </div>
        <?php if (empty($suratMenunggu)): ?>
        <div style="text-align:center;padding:30px;color:var(--muted);">
            <div style="font-size:40px;margin-bottom:8px;">✅</div>
            <p style="margin:0;font-size:14px;">Semua surat sudah diproses.<br>Tidak ada antrian saat ini.</p>
        </div>
        <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:10px;">
            <?php foreach (array_slice($suratMenunggu, 0, 4) as $s): ?>
            <div style="border:1px solid #ffe4b5;background:#fffbf0;border-radius:12px;padding:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;">
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:600;font-size:14px;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= htmlspecialchars($s['perihal'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px;"><?= htmlspecialchars($s['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($s['nama_pembuat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <a href="<?= BASE_URL ?>/kaprodi/review/<?= (int)$s['id'] ?>" class="btn" style="font-size:12px;padding:7px 12px;white-space:nowrap;background:#b45309;">📋 Review</a>
            </div>
            <?php endforeach; ?>
            <?php if (count($suratMenunggu) > 4): ?>
            <a href="<?= BASE_URL ?>/kaprodi/antrian" style="text-align:center;color:var(--brand-2);font-size:13px;padding:10px;">+<?= count($suratMenunggu) - 4 ?> surat lainnya →</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Notifikasi & Aksi Cepat -->
    <div style="display:flex;flex-direction:column;gap:16px;">
        <!-- Aksi Cepat -->
        <div class="card">
            <h3 style="margin:0 0 14px;">🚀 Aksi Cepat</h3>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <a href="<?= BASE_URL ?>/kaprodi/antrian" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:12px;color:var(--text);background:#f9fafb;text-decoration:none;">
                    <span style="font-size:22px;">📋</span>
                    <div>
                        <div style="font-weight:600;font-size:14px;">Antrian Tanda Tangan</div>
                        <div style="font-size:12px;color:var(--muted);"><?= $cntMenunggu ?> surat menunggu</div>
                    </div>
                    <span style="margin-left:auto;color:var(--muted);">→</span>
                </a>
                <a href="<?= BASE_URL ?>/kaprodi/riwayat" style="display:flex;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:12px;color:var(--text);background:#f9fafb;text-decoration:none;">
                    <span style="font-size:22px;">📜</span>
                    <div>
                        <div style="font-weight:600;font-size:14px;">Riwayat Tanda Tangan</div>
                        <div style="font-size:12px;color:var(--muted);">Lihat semua surat yang pernah ditandatangani</div>
                    </div>
                    <span style="margin-left:auto;color:var(--muted);">→</span>
                </a>
            </div>
        </div>

        <!-- Notifikasi Terbaru -->
        <div class="card">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                <h3 style="margin:0;">🔔 Notifikasi</h3>
                <?php if ($notifCount > 0): ?>
                <span style="background:#fef3f2;color:#b42318;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;"><?= $notifCount ?> baru</span>
                <?php endif; ?>
            </div>
            <?php if (empty($notifList)): ?>
            <p style="color:var(--muted);font-size:14px;margin:0;text-align:center;padding:16px 0;">Tidak ada notifikasi.</p>
            <?php else: ?>
            <div style="display:flex;flex-direction:column;gap:8px;">
                <?php foreach ($notifList as $n): ?>
                <div style="padding:10px 12px;border-radius:10px;background:<?= $n['is_dibaca'] ? '#f9fafb' : '#eff6ff' ?>;border:1px solid <?= $n['is_dibaca'] ? 'var(--line)' : '#bfdbfe' ?>;">
                    <div style="font-size:13px;color:var(--text);"><?= htmlspecialchars($n['pesan'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:11px;color:var(--muted);margin-top:4px;"><?= htmlspecialchars(substr($n['created_at'] ?? '', 0, 16), ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Status Kunci RSA -->
<?php if (!$hasKunci): ?>
<div class="card" style="border-left:4px solid #b42318;background:#fef3f2;">
    <div style="display:flex;align-items:center;gap:14px;">
        <span style="font-size:32px;">🔑</span>
        <div>
            <div style="font-weight:700;color:#b42318;font-size:15px;">Kunci RSA Belum Disiapkan</div>
            <div style="color:var(--muted);font-size:14px;margin-top:4px;">Anda belum memiliki kunci RSA aktif. Hubungi Admin untuk melakukan generate kunci RSA agar bisa menandatangani surat secara digital.</div>
        </div>
    </div>
</div>
<?php endif; ?>