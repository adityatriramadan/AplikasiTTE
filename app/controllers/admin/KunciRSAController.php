<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/KunciRSAModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';
require_once BASE_PATH . '/app/helpers/CryptoHelper.php';

class KunciRSAController {

    public function index(): void {
        Auth::requireRole('admin');

        $kunciModel = new KunciRSAModel();
        $userModel  = new UserModel();
        $notifModel = new NotifikasiModel();

        $data = [
            'title'        => 'Manajemen Kunci RSA — ' . APP_NAME,
            'kunci_list'   => $kunciModel->getAllWithUser(),
            'kaprodi_list' => $userModel->getByRole('kaprodi'),
            'notif_count'  => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
            'success'      => $_SESSION['success'] ?? null,
            'error'        => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/kunci/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function generate(): void {
        Auth::requireRole('admin');
        $userModel  = new UserModel();
        $notifModel = new NotifikasiModel();

        $data = [
            'title'        => 'Generate Kunci RSA — ' . APP_NAME,
            'kaprodi_list' => $userModel->getByRole('kaprodi'),
            'csrf_token'   => Security::generateCsrfToken(),
            'notif_count'  => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/kunci/generate.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function proses(): void {
        Auth::requireRole('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/admin/kunci/generate');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $kaprodiId = (int)($_POST['kaprodi_id'] ?? 0);
        $pin       = $_POST['pin'] ?? '';
        $pinKonfirm = $_POST['pin_konfirmasi'] ?? '';

        if (!$kaprodiId || strlen($pin) < 6) {
            $_SESSION['error'] = 'Pilih Kaprodi dan masukkan PIN minimal 6 karakter.';
            header('Location: ' . BASE_URL . '/admin/kunci/generate');
            exit;
        }

        if ($pin !== $pinKonfirm) {
            $_SESSION['error'] = 'PIN dan konfirmasi PIN tidak cocok.';
            header('Location: ' . BASE_URL . '/admin/kunci/generate');
            exit;
        }

        try {
            // Generate pasangan kunci RSA 2048-bit
            $keyPair = CryptoHelper::generateKeyPair();

            // Enkripsi private key dengan PIN
            $encryptedPrivateKey = CryptoHelper::encryptPrivateKey($keyPair['private_key'], $pin);

            // Simpan ke database
            $kunciModel = new KunciRSAModel();
            $kunciId = $kunciModel->simpan([
                'user_id'               => $kaprodiId,
                'public_key'            => $keyPair['public_key'],
                'private_key_encrypted' => $encryptedPrivateKey,
            ]);

            if (!is_dir(STORAGE_KEYS) && !mkdir(STORAGE_KEYS, 0775, true) && !is_dir(STORAGE_KEYS)) {
                throw new Exception('Folder storage kunci tidak dapat dibuat.');
            }

            $keyFilePath = STORAGE_KEYS . 'kunci_' . $kunciId . '.enc';
            if (file_put_contents($keyFilePath, $encryptedPrivateKey, LOCK_EX) === false) {
                throw new Exception('Gagal menyimpan kunci terenkripsi ke storage.');
            }

            // Clear private key dari memori
            unset($keyPair);

            $logModel = new LogAktivitasModel();
            $logModel->catat(
                (int)$_SESSION['user_id'],
                'generate_kunci_rsa',
                'Generate kunci RSA untuk Kaprodi ID: ' . $kaprodiId
            );

            $_SESSION['success'] = 'Kunci RSA berhasil digenerate dan disimpan di storage kunci. Informasikan PIN ke Kaprodi secara aman.';
            header('Location: ' . BASE_URL . '/admin/kunci');
            exit;

        } catch (Exception $e) {
            error_log('[KunciRSA Error] ' . $e->getMessage());
            $_SESSION['error'] = 'Gagal generate kunci: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/admin/kunci/generate');
            exit;
        }
    }

    public function nonaktifkan(int $id): void {
        Auth::requireRole('admin');
        $kunciModel = new KunciRSAModel();
        $kunciModel->nonaktifkan($id);

        $logModel = new LogAktivitasModel();
        $logModel->catat((int)$_SESSION['user_id'], 'nonaktifkan_kunci', 'Menonaktifkan kunci RSA ID: ' . $id);

        $_SESSION['success'] = 'Kunci RSA berhasil dinonaktifkan.';
        header('Location: ' . BASE_URL . '/admin/kunci');
        exit;
    }
}
