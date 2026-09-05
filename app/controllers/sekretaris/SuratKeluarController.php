<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/models/DistribusiModel.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';
require_once BASE_PATH . '/app/helpers/NotifikasiHelper.php';

class SuratKeluarController {

    public function daftar(): void {
        Auth::requireRole('sekretaris');
        $suratModel = new SuratModel();
        $notifModel = new NotifikasiModel();
        $userId     = (int)$_SESSION['user_id'];

        $filter = [
            'pembuat_id' => $userId,
            'search'     => Security::sanitize($_GET['search'] ?? ''),
        ];
        if (!empty($_GET['status'])) {
            $filter['status'] = Security::sanitize($_GET['status']);
        }

        $data = [
            'title'       => 'Daftar Surat Keluar — ' . APP_NAME,
            'surat_list'  => $suratModel->getAll($filter),
            'search'      => $filter['search'],
            'filter_status'=> $_GET['status'] ?? '',
            'notif_count' => $notifModel->countBelumDibaca($userId),
            'current_user'=> Auth::user(),
            'success'     => $_SESSION['success'] ?? null,
            'error'       => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_keluar/daftar.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function detail(int $suratId): void {
        Auth::requireRole('sekretaris');
        $suratModel = new SuratModel();
        $ttModel    = new TandaTanganModel();
        $distModel  = new DistribusiModel();
        $notifModel = new NotifikasiModel();
        $userId     = (int)$_SESSION['user_id'];

        $surat = $suratModel->getById($suratId);
        if (!$surat) {
            header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/daftar');
            exit;
        }

        $data = [
            'title'        => 'Detail Surat — ' . APP_NAME,
            'surat'        => $surat,
            'tanda_tangan' => $ttModel->getBySuratId($suratId),
            'distribusi'   => $distModel->getBySuratId($suratId),
            'csrf_token'   => Security::generateCsrfToken(),
            'notif_count'  => $notifModel->countBelumDibaca($userId),
            'current_user' => Auth::user(),
            'success'      => $_SESSION['success'] ?? null,
            'error'        => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_keluar/detail.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function distribusi(): void {
        Auth::requireRole('sekretaris');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/daftar');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $suratId = (int)($_POST['surat_id'] ?? 0);
        $suratModel = new SuratModel();
        $surat = $suratModel->getById($suratId);

        if (!$surat || $surat['status'] !== 'ditandatangani') {
            $_SESSION['error'] = 'Surat belum ditandatangani atau tidak ditemukan.';
            header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId);
            exit;
        }

        $distModel = new DistribusiModel();
        // Distribusi internal ke dosen
        $penerimaIds = $_POST['penerima_internal'] ?? [];
        foreach ($penerimaIds as $pid) {
            $pid = (int)$pid;
            if ($pid > 0) {
                $distModel->kirim($suratId, $pid);
                NotifikasiHelper::kirim(
                    $pid,
                    'Anda menerima surat: "' . $surat['perihal'] . '"',
                    BASE_URL . '/dosen/dokumen/detail/' . $suratId
                );
            }
        }

        // Distribusi eksternal
        $eksternalNama = Security::sanitize($_POST['penerima_eksternal'] ?? '');
        if ($eksternalNama) {
            $distModel->kirim($suratId, null, $eksternalNama);
        }

        $suratModel->updateStatus($suratId, 'didistribusikan');

        $logModel = new LogAktivitasModel();
        $logModel->catat((int)$_SESSION['user_id'], 'distribusi_surat', 'Distribusi surat ID: ' . $suratId);

        $_SESSION['success'] = 'Surat berhasil didistribusikan.';
        header('Location: ' . BASE_URL . '/sekretaris/surat-keluar/detail/' . $suratId);
        exit;
    }
}
