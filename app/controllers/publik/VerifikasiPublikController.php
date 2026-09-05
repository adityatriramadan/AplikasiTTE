<?php
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/helpers/CryptoHelper.php';

class VerifikasiPublikController {

    public function index(): void {
        $suratId = (int)($_GET['id'] ?? 0);
        $surat = null;
        $tandaTangan = null;
        $hasil = null;
        $error = null;

        if ($suratId > 0) {
            try {
                $suratModel = new SuratModel();
                $ttModel = new TandaTanganModel();

                $surat = $suratModel->getById($suratId);
                if (!$surat) {
                    throw new Exception('Dokumen tidak ditemukan.');
                }

                $tandaTangan = $ttModel->getBySuratId($suratId);
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
        }

        $data = [
            'title' => 'Verifikasi Dokumen - ' . APP_NAME,
            'surat' => $surat,
            'tanda_tangan' => $tandaTangan,
            'hasil' => $hasil,
            'error' => $error,
        ];

        include BASE_PATH . '/views/layouts/public.php';
        include BASE_PATH . '/views/publik/verifikasi/index.php';
    }
}