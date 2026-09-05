<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/app/models/UserModel.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/KunciRSAModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class AdminDashboardController {

    public function index(): void {
        Auth::requireRole('admin');

        $userModel  = new UserModel();
        $suratModel = new SuratModel();
        $logModel   = new LogAktivitasModel();
        $kunciModel = new KunciRSAModel();
        $notifModel = new NotifikasiModel();

        $data = [
            'title'          => 'Dashboard Admin — ' . APP_NAME,
            'jumlah_user'    => $userModel->countByRole(),
            'jumlah_surat'   => $suratModel->countByStatus(),
            'log_terbaru'    => $logModel->getAll(['limit' => 10]),
            'log_hari_ini'   => $logModel->countToday(),
            'kunci_list'     => $kunciModel->getAllWithUser(),
            'notif_count'    => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'   => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/admin/dashboard.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
