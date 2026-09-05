<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';
require_once BASE_PATH . '/app/helpers/NotifikasiHelper.php';

class TolakSuratController {

    public function proses(): void {
        Auth::requireRole('kaprodi');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('Token tidak valid.');
        }

        $suratId     = (int)($_POST['surat_id'] ?? 0);
        // Support kedua nama field: 'alasan_tolak' (dari controller lama) dan 'alasan' (dari view)
        $alasanTolak = Security::sanitize($_POST['alasan_tolak'] ?? $_POST['alasan'] ?? '');

        if (!$suratId || !$alasanTolak) {
            $_SESSION['error'] = 'Alasan penolakan wajib diisi.';
            header('Location: ' . BASE_URL . '/kaprodi/review/' . $suratId);
            exit;
        }

        $suratModel = new SuratModel();
        $surat      = $suratModel->getById($suratId);

        if (!$surat) {
            $_SESSION['error'] = 'Surat tidak ditemukan.';
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }

        $suratModel->tolak($suratId, $alasanTolak);

        NotifikasiHelper::kirim(
            (int)$surat['pembuat_id'],
            'Surat "' . $surat['perihal'] . '" ditolak. Alasan: ' . $alasanTolak,
            BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId
        );

        $logModel = new LogAktivitasModel();
        $logModel->catat(
            (int)$_SESSION['user_id'],
            'tolak_surat',
            'Menolak surat ID: ' . $suratId . ' — ' . $alasanTolak
        );

        $_SESSION['success'] = 'Surat berhasil ditolak.';
        header('Location: ' . BASE_URL . '/kaprodi/antrian');
        exit;
    }
}
