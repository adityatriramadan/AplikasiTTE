<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/LogAktivitasModel.php';
require_once BASE_PATH . '/app/models/NotifikasiModel.php';

class SuratMasukController {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = getDB();
    }

    public function index(): void {
        Auth::requireRole('sekretaris');
        $notifModel = new NotifikasiModel();

        $stmt = $this->pdo->prepare(
            "SELECT sm.*, u.nama AS nama_input FROM surat_masuk sm JOIN users u ON sm.input_oleh = u.id ORDER BY sm.tanggal_terima DESC"
        );
        $stmt->execute();

        $data = [
            'title'        => 'Surat Masuk — ' . APP_NAME,
            'surat_masuk'  => $stmt->fetchAll(),
            'csrf_token'   => Security::generateCsrfToken(),
            'notif_count'  => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user' => Auth::user(),
            'success'      => $_SESSION['success'] ?? null,
            'error'        => $_SESSION['error'] ?? null,
        ];
        unset($_SESSION['success'], $_SESSION['error']);

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_masuk/index.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function tambah(): void {
        Auth::requireRole('sekretaris');
        $notifModel = new NotifikasiModel();
        $data = [
            'title'       => 'Input Surat Masuk — ' . APP_NAME,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_masuk/tambah.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    public function simpan(): void {
        Auth::requireRole('sekretaris');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/sekretaris/surat-masuk');
            exit;
        }
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $nomorSurat   = Security::sanitize($_POST['nomor_surat'] ?? '');
        $pengirim     = Security::sanitize($_POST['pengirim'] ?? '');
        $perihal      = Security::sanitize($_POST['perihal'] ?? '');
        $tanggalSurat = Security::sanitize($_POST['tanggal_surat'] ?? '');
        $tanggalTerima= Security::sanitize($_POST['tanggal_terima'] ?? date('Y-m-d'));
        $catatan      = Security::sanitize($_POST['catatan'] ?? '');

        if (!$nomorSurat || !$pengirim || !$perihal || !$tanggalSurat) {
            $_SESSION['error'] = 'Field wajib belum diisi.';
            header('Location: ' . BASE_URL . '/sekretaris/surat-masuk/tambah');
            exit;
        }

        $stmt = $this->pdo->prepare(
            "INSERT INTO surat_masuk (nomor_surat, pengirim, perihal, tanggal_surat, tanggal_terima, catatan, input_oleh) VALUES (?,?,?,?,?,?,?)"
        );
        $stmt->execute([$nomorSurat, $pengirim, $perihal, $tanggalSurat, $tanggalTerima, $catatan, (int)$_SESSION['user_id']]);

        $logModel = new LogAktivitasModel();
        $logModel->catat((int)$_SESSION['user_id'], 'input_surat_masuk', 'Input surat masuk: ' . $nomorSurat);

        $_SESSION['success'] = 'Surat masuk berhasil diinput.';
        header('Location: ' . BASE_URL . '/sekretaris/surat-masuk');
        exit;
    }

    public function disposisi(int $id): void {
        Auth::requireRole('sekretaris');
        $notifModel = new NotifikasiModel();

        $stmt = $this->pdo->prepare("SELECT * FROM surat_masuk WHERE id = ?");
        $stmt->execute([$id]);
        $suratMasuk = $stmt->fetch();

        if (!$suratMasuk) {
            header('Location: ' . BASE_URL . '/sekretaris/surat-masuk');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
                die('CSRF token tidak valid.');
            }
            $penerima = Security::sanitize($_POST['penerima_disposisi'] ?? '');
            $catatan  = Security::sanitize($_POST['catatan_disposisi'] ?? '');

            $stmt = $this->pdo->prepare(
                "UPDATE surat_masuk SET status_disposisi='sudah', penerima_disposisi=?, catatan_disposisi=? WHERE id=?"
            );
            $stmt->execute([$penerima, $catatan, $id]);

            $logModel = new LogAktivitasModel();
            $logModel->catat((int)$_SESSION['user_id'], 'disposisi_surat_masuk', 'Disposisi surat masuk ID: ' . $id . ' ke: ' . $penerima);

            $_SESSION['success'] = 'Disposisi berhasil disimpan.';
            header('Location: ' . BASE_URL . '/sekretaris/surat-masuk');
            exit;
        }

        $data = [
            'title'       => 'Disposisi Surat Masuk — ' . APP_NAME,
            'surat_masuk' => $suratMasuk,
            'csrf_token'  => Security::generateCsrfToken(),
            'notif_count' => $notifModel->countBelumDibaca((int)$_SESSION['user_id']),
            'current_user'=> Auth::user(),
        ];

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/layouts/sidebar.php';
        include BASE_PATH . '/views/sekretaris/surat_masuk/disposisi.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }
}
