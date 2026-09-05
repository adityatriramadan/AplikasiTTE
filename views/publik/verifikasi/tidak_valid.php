<div class="alert error" style="margin-top:16px;padding:20px;">
    <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:36px;">⚠️</span>
        <div>
            <strong style="font-size:16px;display:block;">DOKUMEN TIDAK VALID</strong>
            <span>Hash dokumen tidak cocok dengan tanda tangan digital. Dokumen mungkin telah dimodifikasi atau rusak.</span>
        </div>
    </div>
</div>

<?php if (!empty($surat)): ?>
<div class="card" style="margin-top:16px;">
    <h3 style="margin-top:0;color:var(--danger);">⚠️ Peringatan Keamanan</h3>
    <p style="margin:0 0 14px;color:var(--muted);font-size:14px;">
        Dokumen dengan nomor <strong><?= htmlspecialchars($surat['nomor_surat'] ?? '-', ENT_QUOTES, 'UTF-8') ?></strong>
        ditemukan di sistem, namun verifikasi tanda tangan digital GAGAL.
        Ini berarti file dokumen telah diubah setelah ditandatangani.
    </p>
    <div style="background:#fef3f2;border:1px solid #fecdca;border-radius:10px;padding:14px;font-size:14px;color:var(--danger);">
        ⚠️ JANGAN menerima dokumen ini sebagai sah. Hubungi instansi penerbit untuk klarifikasi.
    </div>
</div>
<?php endif; ?>