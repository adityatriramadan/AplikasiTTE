<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class DosenDashboardController {

    public function index(): void {
        Auth::requireRole('dosen');

        $userId = (int)$_SESSION['user_id'];
        $suratModel = new SuratModel();
        $notifModel = new NotifikasiModel();
        $dokumen = $suratModel->getForDosen($userId);

        $data = [
            'title' => 'Dashboard Dosen - ' . APP_NAME,
            'dokumen_terbaru' => array_slice($dokumen, 0, 5),
            'total_dokumen' => count($dokumen),
            'notif_count' => $notifModel->countBelumDibaca($userId),
            'current_user' => Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/dosen/dashboard.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}