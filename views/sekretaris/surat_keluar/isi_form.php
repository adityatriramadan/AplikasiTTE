<?php
$template     = $data['template']      ?? [];
$fieldDinamis = $data['field_dinamis'] ?? [];
$csrfToken    = $data['csrf_token']    ?? '';
$oldInput     = $_SESSION['old_input'] ?? [];
$errorMessage = $_SESSION['error'] ?? null;
// clear flash
unset($_SESSION['old_input'], $_SESSION['error']);
$namaJenis    = htmlspecialchars($template['nama_jenis'] ?? 'Surat', ENT_QUOTES, 'UTF-8');
$kodeJenis    = htmlspecialchars($template['kode_jenis'] ?? '-', ENT_QUOTES, 'UTF-8');
?>

<div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
    <div>
        <div style="font-size:13px;color:var(--muted);margin-bottom:4px;">
            <a href="<?= BASE_URL ?>/sekretaris/buat-surat" style="color:var(--muted);">Pilih Template</a> → Isi Data
        </div>
        <h2 style="margin:0 0 4px;">📝 Isi Form — <?= $namaJenis ?></h2>
        <span class="muted">Isi semua data yang diperlukan untuk surat jenis <strong><?= $kodeJenis ?></strong></span>
    </div>
    <a href="<?= BASE_URL ?>/sekretaris/buat-surat" class="btn secondary">← Ganti Template</a>
</div>

<form method="post" action="<?= BASE_URL ?>/sekretaris/preview-surat" id="form-surat">
    <input type="hidden" name="csrf_token"   value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <input type="hidden" name="template_id"  value="<?= (int)($template['id'] ?? 0) ?>">

    <div class="grid cols-2">
        <!-- KOLOM KIRI: Data Pokok Surat -->
        <div>
            <div class="card">
                <?php if (!empty($errorMessage)): ?>
                <div style="background:#fff1f0;border:1px solid #fecaca;padding:12px;border-radius:8px;margin-bottom:12px;color:#991b1b;">
                    <strong>Terjadi kesalahan:</strong>
                    <div style="margin-top:6px;"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
                </div>
                <?php endif; ?>
                <h3 style="margin:0 0 16px;color:var(--brand);">📋 Data Pokok Surat</h3>

                <div class="field">
                    <label for="perihal">Perihal Surat <span style="color:#b42318;">*</span></label>
                    <input type="text" id="perihal" name="perihal" required
                           placeholder="Contoh: Permohonan Surat Keterangan Aktif Kuliah"
                           maxlength="255"
                           value="<?= htmlspecialchars($oldInput['perihal'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="field">
                    <label for="tanggal_surat">Tanggal Surat <span style="color:#b42318;">*</span></label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" required
                           value="<?= htmlspecialchars($oldInput['tanggal_surat'] ?? ($data['tanggal_hari_ini'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>

            <?php if (!empty($fieldDinamis)): ?>
            <div class="card">
                <h3 style="margin:0 0 16px;color:var(--brand);">📌 Data Khusus <?= $namaJenis ?></h3>
                  <?php $fieldIndex = 0; foreach ($fieldDinamis as $field):
                    $fieldName  = trim((string)($field['name'] ?? $field['key'] ?? ''));
                    $fnameValue = $fieldName !== '' ? $fieldName : 'field_' . (++$fieldIndex);
                    $flabelValue = trim((string)($field['label'] ?? $field['title'] ?? ''));
                    $flabelValue = $flabelValue !== '' ? $flabelValue : ucwords(str_replace('_', ' ', $fnameValue));
                    $fname  = htmlspecialchars($fnameValue, ENT_QUOTES, 'UTF-8');
                    $flabel = htmlspecialchars($flabelValue, ENT_QUOTES, 'UTF-8');
                    $ftype  = $field['type'] ?? 'text';
                    $freq   = !empty($field['required']);
                      $existing = $oldInput[$fnameValue] ?? '';
                ?>
                <div class="field">
                    <label for="f_<?= $fname ?>"><?= $flabel ?><?= $freq ? ' <span style="color:#b42318;">*</span>' : '' ?></label>
                    <?php if ($ftype === 'textarea'): ?>
                      <textarea id="f_<?= $fname ?>" name="<?= $fname ?>" rows="4"
                          <?= $freq ? 'required' : '' ?>
                          placeholder="<?= $flabel ?>..."><?= htmlspecialchars($existing, ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php elseif ($ftype === 'date'): ?>
                      <input type="date" id="f_<?= $fname ?>" name="<?= $fname ?>" <?= $freq ? 'required' : '' ?> value="<?= htmlspecialchars($existing, ENT_QUOTES, 'UTF-8') ?>">
                    <?php elseif ($ftype === 'number'): ?>
                      <input type="number" id="f_<?= $fname ?>" name="<?= $fname ?>" <?= $freq ? 'required' : '' ?>
                          placeholder="<?= $flabel ?>..." value="<?= htmlspecialchars($existing, ENT_QUOTES, 'UTF-8') ?>">
                    <?php else: ?>
                      <input type="text" id="f_<?= $fname ?>" name="<?= $fname ?>" <?= $freq ? 'required' : '' ?>
                          placeholder="<?= $flabel ?>..." value="<?= htmlspecialchars($existing, ENT_QUOTES, 'UTF-8') ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- KOLOM KANAN: Penerima + Preview Template -->
        <div>
            <div class="card">
                <h3 style="margin:0 0 16px;color:var(--brand);">📮 Data Penerima</h3>
                <p style="font-size:13px;color:var(--muted);margin:0 0 14px;">Opsional — isi jika surat ditujukan kepada seseorang secara spesifik.</p>
                <div class="field">
                    <label for="penerima_nama">Nama Penerima</label>
                    <input type="text" id="penerima_nama" name="penerima_nama" placeholder="Nama lengkap penerima">
                </div>
                <div class="field">
                    <label for="penerima_jabatan">Jabatan Penerima</label>
                    <input type="text" id="penerima_jabatan" name="penerima_jabatan" placeholder="Jabatan penerima">
                </div>
                <div class="field">
                    <label for="penerima_instansi">Instansi Penerima</label>
                    <input type="text" id="penerima_instansi" name="penerima_instansi" placeholder="Nama instansi/lembaga">
                </div>
            </div>

            <!-- Preview Template -->
            <?php if (!empty($template['konten_template'])): ?>
            <div class="card">
                <h3 style="margin:0 0 12px;color:var(--brand);">👁️ Preview Template</h3>
                <p style="font-size:12px;color:var(--muted);margin:0 0 12px;">Ini adalah struktur template surat. Data yang Anda isi akan menggantikan placeholder <code style="background:#f3f4f6;padding:2px 6px;border-radius:4px;">&#123;&#123;variabel&#125;&#125;</code>.</p>
                <div style="border:1px solid var(--line);border-radius:10px;padding:16px;background:#fafbfd;max-height:280px;overflow-y:auto;font-size:12px;color:var(--text);">
                    <?= $template['konten_template'] ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tombol Aksi -->
    <div class="card" style="display:flex;gap:12px;align-items:center;justify-content:flex-end;">
        <a href="<?= BASE_URL ?>/sekretaris/buat-surat" class="btn secondary">Batal</a>
        <button type="submit" class="btn ok" style="padding:12px 28px;font-size:15px;font-weight:600;">
            👁️ Preview Surat
        </button>
    </div>
</form>