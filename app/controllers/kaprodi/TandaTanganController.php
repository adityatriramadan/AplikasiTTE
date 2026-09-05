<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/models/KunciRSAModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';
require_once BASE_PATH . '/app/helpers/CryptoHelper.php';
require_once BASE_PATH . '/app/helpers/PdfHelper.php';
require_once BASE_PATH . '/app/helpers/QrHelper.php';
require_once BASE_PATH . '/app/helpers/NotifikasiHelper.php';
require_once BASE_PATH . '/app/helpers/NomorSuratHelper.php';

class TandaTanganController {

    /**
     * Antrian surat menunggu tanda tangan
     */
    public function antrian(): void {
        Auth::requireRole('kaprodi');
        $suratModel = new SuratModel();
        $notifModel = new NotifikasiModel();

        $data = [
            'title'          => 'Antrian Tanda Tangan — ' . APP_NAME,
            'surat_menunggu' => $suratModel->getSuratMenunggu(),
            'notif_count'    => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'   => Auth::user(),
            'success'        => $_SESSION['success'] ?? null,
            'error'          => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/tanda_tangan/antrian.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Review surat sebelum tanda tangan
     */
    public function review(int $suratId): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $surat      = $suratModel->getById($suratId);
        $notifModel = new NotifikasiModel();

        if (!$surat) {
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }

        $data = [
            'title'       => 'Review Surat — ' . APP_NAME,
            'surat'       => $surat,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/tanda_tangan/review.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Form input PIN sebelum proses tanda tangan
     */
    public function inputPin(int $suratId): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $surat      = $suratModel->getById($suratId);
        $notifModel = new NotifikasiModel();

        if (!$surat || $surat['status'] !== 'menunggu') {
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }

        $data = [
            'title'        => 'Input PIN — ' . APP_NAME,
            'surat'        => $surat,
            'csrf_token'   => Security::generateCsrfToken(),
            'notif_count'  => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
            'error'        => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/tanda_tangan/input_pin.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * PROSES TANDA TANGAN DIGITAL — INTI SISTEM
     */
    public function proses(): void {
        Auth::requireRole('kaprodi');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }

        // 1. Validasi CSRF
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('Token tidak valid.');
        }

        $suratId   = (int)($_POST['surat_id'] ?? 0);
        $pin       = $_POST['pin'] ?? '';
        $kaprodiId = (int)$_SESSION['user_id'];

        if (!$suratId || !$pin) {
            $_SESSION['error'] = 'Data tidak lengkap.';
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }

        try {
            // 2. Ambil data surat
            $suratModel = new SuratModel();
            $surat      = $suratModel->getById($suratId);
            if (!$surat || $surat['status'] !== 'menunggu') {
                throw new Exception('Surat tidak ditemukan atau sudah diproses.');
            }

            // 3. Ambil kunci RSA Kaprodi
            $kunciModel = new KunciRSAModel();
            $kunci      = $kunciModel->getAktifByUserId($kaprodiId);
            if (!$kunci) {
                throw new Exception('Kunci RSA tidak ditemukan. Hubungi Admin untuk generate kunci.');
            }

            // 4. Dekripsi private key dengan PIN
            $encryptedKey = $kunci['private_key_encrypted'];
            $storageKeyFile = STORAGE_KEYS . 'kunci_' . (int)$kunci['id'] . '.enc';
            if (is_file($storageKeyFile)) {
                $storedEncryptedKey = file_get_contents($storageKeyFile);
                if ($storedEncryptedKey !== false && trim($storedEncryptedKey) !== '') {
                    $encryptedKey = trim($storedEncryptedKey);
                }
            }

            $privateKeyPem = CryptoHelper::decryptPrivateKey($encryptedKey, $pin);

            // 5. Generate URL verifikasi & QR Code
            $verifyUrl = VERIFY_URL . $suratId;
            $qrPath    = STORAGE_QR . 'qr_' . $suratId . '.svg';
            QrHelper::generate($verifyUrl, $qrPath);

            // 6. Render PDF final DENGAN QR Code ditempel
            $pdfFinalPath = STORAGE_PDF . 'surat_' . $suratId . '.pdf';
            $pdfHelper   = new PdfHelper();
            $pdfHelper->renderSuratDenganQr($surat, $qrPath, $pdfFinalPath);

            // 7. Hitung hash SHA-256 dari PDF final yang akan diverifikasi publik
            $hashSha256 = CryptoHelper::hashDocument($pdfFinalPath);

            // 8. Buat tanda tangan digital: RSA_Sign(hash, privateKey)
            $signatureBase64 = CryptoHelper::signDocument($hashSha256, $privateKeyPem);

            // 9. Hapus private key dari memori secepatnya
            unset($privateKeyPem);

            // 10. Simpan/update signature + hash ke database
            $tandaTanganModel = new TandaTanganModel();
            $existingTandaTangan = $tandaTanganModel->getBySuratId($suratId);
            $tandaTanganData = [
                'surat_id'              => $suratId,
                'kaprodi_id'            => $kaprodiId,
                'hash_sha256'           => $hashSha256,
                'signature_rsa'         => $signatureBase64,
                'timestamp_tandatangan' => time(),
                'public_key_snapshot'   => $kunci['public_key'],
            ];

            if ($existingTandaTangan) {
                $tandaTanganId = (int)$existingTandaTangan['id'];
                $tandaTanganModel->updateById($tandaTanganId, $tandaTanganData + [
                    'pdf_path'    => 'surat_' . $suratId . '.pdf',
                    'qr_code_url' => $verifyUrl,
                ]);
            } else {
                $tandaTanganId = $tandaTanganModel->simpan($tandaTanganData);
            }

            // 11. Update path PDF & QR di record tanda tangan jika baru dibuat
            if (!$existingTandaTangan) {
                $tandaTanganModel->updatePath($tandaTanganId, [
                    'pdf_path'    => 'surat_' . $suratId . '.pdf',
                    'qr_code_url' => $verifyUrl,
                ]);
            }

            // 12. Update status surat
            $suratModel->updateStatus($suratId, 'ditandatangani');

            // 13. Kirim notifikasi ke Sekretaris
            NotifikasiHelper::kirim(
                (int)$surat['pembuat_id'],
                'Surat "' . $surat['perihal'] . '" telah ditandatangani oleh Kaprodi.',
                BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId
            );

            // 14. Catat log
            $logModel = new LogAktivitasModel();
            $logModel->catat($kaprodiId, 'tanda_tangan', 'Menandatangani surat ID: ' . $suratId . ' — ' . $surat['nomor_surat']);

            $_SESSION['success'] = 'Surat berhasil ditandatangani secara digital!';
            // Prefer query-style redirect when running with EOFFICE_BASE_URL (built-in server testing)
            $baseEnv = getenv('EOFFICE_BASE_URL');
            if ($baseEnv !== false && !empty($baseEnv)) {
                header('Location: ' . $baseEnv . '/?url=kaprodi/sukses/' . $suratId);
            } else {
                header('Location: ' . BASE_URL . '/kaprodi/sukses/' . $suratId);
            }
            exit;

        } catch (Exception $e) {
            error_log('[TandaTangan Error] ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/kaprodi/review/' . $suratId);
            exit;
        }
    }

    /**
     * Halaman konfirmasi sukses tanda tangan
     */
    public function sukses(int $suratId): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $surat      = $suratModel->getById($suratId);
        $ttModel    = new TandaTanganModel();
        $tt         = $ttModel->getBySuratId($suratId);
        $notifModel = new NotifikasiModel();

        if (!$surat || !$tt) {
            header('Location: ' . BASE_URL . '/kaprodi/antrian');
            exit;
        }

        $data = [
            'title'        => 'Tanda Tangan Berhasil — ' . APP_NAME,
            'surat'        => $surat,
            'tanda_tangan' => $tt,
            'notif_count'  => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
            'success'      => $_SESSION['success'] ?? null,
        ];
        unset($_SESSION['success']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/kaprodi/tanda_tangan/sukses.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
