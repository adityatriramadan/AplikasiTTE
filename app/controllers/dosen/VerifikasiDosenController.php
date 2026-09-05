<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/helpers/CryptoHelper.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class VerifikasiDosenController {

    public function hasil(int $suratId): void {
        Auth::requireRole('dosen');

        $suratModel = new SuratModel();
        $ttModel = new TandaTanganModel();
        $notifModel = new NotifikasiModel();

        $surat = $suratModel->getById($suratId);
        $tandaTangan = $ttModel->getBySuratId($suratId);
        $hasil = null;
        $error = null;

        try {
            if (!$surat) {
                throw new Exception('Dokumen tidak ditemukan.');
            }
            if (!$tandaTangan || empty($tandaTangan['pdf_path'])) {
                throw new Exception('Dokumen ini belum memiliki tanda tangan digital.');
            }

            $pdfPath = STORAGE_PDF . $tandaTangan['pdf_path'];
            if (!file_exists($pdfPath)) {
                throw new Exception('File PDF tidak ditemukan di server.');
            }

            $hash = CryptoHelper::hashDocument($pdfPath);
            $hasil = CryptoHelper::verifyDocument($hash, $tandaTangan['signature_rsa'], $tandaTangan['public_key_snapshot']);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        $data = [
            'title' => 'Verifikasi Dosen - ' . APP_NAME,
            'surat' => $surat,
            'tanda_tangan' => $tandaTangan,
            'hasil' => $hasil,
            'error' => $error,
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/dosen/verifikasi/hasil.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}