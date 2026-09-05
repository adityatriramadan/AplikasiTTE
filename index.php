<?php
// ============================================================
// Entry Point & Router Utama
// E-Office Tanda Tangan Digital — Prodi TI UNPAM
// ============================================================

// Load konfigurasi
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';

// Load core
require_once __DIR__ . '/core/Security.php';
require_once __DIR__ . '/core/Auth.php';

// Ambil URL dari request
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$segments = explode('/', $url);

$seg0 = $segments[0] ?? '';
$seg1 = $segments[1] ?? 'index';
$seg2 = $segments[2] ?? null;

// ============================================================
// ROUTING TABLE
// ============================================================
$route = null;

// --- Halaman awal & auth ---
if ($seg0 === '' || $seg0 === 'login') {
    $route = ['file' => __DIR__ . '/app/controllers/AuthController.php', 'class' => 'AuthController', 'method' => 'login', 'param' => null];
} elseif ($seg0 === 'logout') {
    $route = ['file' => __DIR__ . '/app/controllers/AuthController.php', 'class' => 'AuthController', 'method' => 'logout', 'param' => null];
} elseif ($seg0 === 'auth' && $seg1 === 'proses') {
    $route = ['file' => __DIR__ . '/app/controllers/AuthController.php', 'class' => 'AuthController', 'method' => 'proses', 'param' => null];

// --- ADMIN routes ---
} elseif ($seg0 === 'admin') {
    $method = $seg1 ?: 'dashboard';
    $param  = $seg2 ? (int)$seg2 : null;
    switch ($method) {
        case 'dashboard':
            $route = ['file' => __DIR__ . '/app/controllers/admin/AdminDashboardController.php', 'class' => 'AdminDashboardController', 'method' => 'index', 'param' => null];
            break;
        case 'users':
            $act = $segments[2] ?? 'index';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            $route = ['file' => __DIR__ . '/app/controllers/admin/UserController.php', 'class' => 'UserController', 'method' => $act, 'param' => $id];
            break;
        case 'kunci':
            $act = $segments[2] ?? 'index';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            // Map 'proses' langsung, 'nonaktifkan' dengan ID
            if ($act === 'nonaktifkan' && $id !== null) {
                $route = ['file' => __DIR__ . '/app/controllers/admin/KunciRSAController.php', 'class' => 'KunciRSAController', 'method' => 'nonaktifkan', 'param' => $id];
            } else {
                $route = ['file' => __DIR__ . '/app/controllers/admin/KunciRSAController.php', 'class' => 'KunciRSAController', 'method' => $act, 'param' => null];
            }
            break;
        case 'template':
            $act = $segments[2] ?? 'index';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            $route = ['file' => __DIR__ . '/app/controllers/admin/TemplateSuratController.php', 'class' => 'TemplateSuratController', 'method' => $act, 'param' => $id];
            break;
        case 'log':
            $route = ['file' => __DIR__ . '/app/controllers/admin/LogAktivitasController.php', 'class' => 'LogAktivitasController', 'method' => 'index', 'param' => null];
            break;
        default:
            $route = ['file' => __DIR__ . '/app/controllers/admin/AdminDashboardController.php', 'class' => 'AdminDashboardController', 'method' => 'index', 'param' => null];
    }

// --- KAPRODI routes ---
} elseif ($seg0 === 'kaprodi') {
    $method = $seg1 ?: 'dashboard';
    switch ($method) {
        case 'dashboard':
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/KaprodiDashboardController.php', 'class' => 'KaprodiDashboardController', 'method' => 'index', 'param' => null];
            break;
        case 'antrian':
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/TandaTanganController.php', 'class' => 'TandaTanganController', 'method' => 'antrian', 'param' => null];
            break;
        case 'review':
            $id = isset($segments[2]) ? (int)$segments[2] : 0;
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/TandaTanganController.php', 'class' => 'TandaTanganController', 'method' => 'review', 'param' => $id];
            break;
        case 'input-pin':
            $id = isset($segments[2]) ? (int)$segments[2] : 0;
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/TandaTanganController.php', 'class' => 'TandaTanganController', 'method' => 'inputPin', 'param' => $id];
            break;
        case 'tanda-tangan':
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/TandaTanganController.php', 'class' => 'TandaTanganController', 'method' => 'proses', 'param' => null];
            break;
        case 'sukses':
            $id = isset($segments[2]) ? (int)$segments[2] : 0;
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/TandaTanganController.php', 'class' => 'TandaTanganController', 'method' => 'sukses', 'param' => $id];
            break;
        case 'tolak':
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/TolakSuratController.php', 'class' => 'TolakSuratController', 'method' => 'proses', 'param' => null];
            break;
        case 'riwayat':
            $act = $segments[2] ?? 'index';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/RiwayatController.php', 'class' => 'RiwayatController', 'method' => $act, 'param' => $id];
            break;
        default:
            $route = ['file' => __DIR__ . '/app/controllers/kaprodi/KaprodiDashboardController.php', 'class' => 'KaprodiDashboardController', 'method' => 'index', 'param' => null];
    }

// --- SEKRETARIS routes ---
} elseif ($seg0 === 'sekretaris') {
    $method = $seg1 ?: 'dashboard';
    switch ($method) {
        case 'dashboard':
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/SekretarisDashboardController.php', 'class' => 'SekretarisDashboardController', 'method' => 'index', 'param' => null];
            break;
        case 'buat-surat':
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/BuatSuratController.php', 'class' => 'BuatSuratController', 'method' => 'index', 'param' => null];
            break;
        case 'isi-form':
            $id = isset($segments[2]) ? (int)$segments[2] : 0;
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/BuatSuratController.php', 'class' => 'BuatSuratController', 'method' => 'isiForm', 'param' => $id];
            break;
        case 'preview-surat':
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/BuatSuratController.php', 'class' => 'BuatSuratController', 'method' => 'preview', 'param' => null];
            break;
        case 'simpan-surat':
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/BuatSuratController.php', 'class' => 'BuatSuratController', 'method' => 'simpan', 'param' => null];
            break;
        case 'ajukan-surat':
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/BuatSuratController.php', 'class' => 'BuatSuratController', 'method' => 'ajukan', 'param' => null];
            break;
        case 'surat-keluar':
            $act = $segments[2] ?? 'daftar';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/SuratKeluarController.php', 'class' => 'SuratKeluarController', 'method' => $act, 'param' => $id];
            break;
        case 'surat-masuk':
            $act = $segments[2] ?? 'index';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/SuratMasukController.php', 'class' => 'SuratMasukController', 'method' => $act, 'param' => $id];
            break;
        case 'arsip':
            $act = $segments[2] ?? 'keluar';
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/ArsipController.php', 'class' => 'ArsipController', 'method' => $act, 'param' => null];
            break;
        case 'distribusi':
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/SuratKeluarController.php', 'class' => 'SuratKeluarController', 'method' => 'distribusi', 'param' => null];
            break;
        default:
            $route = ['file' => __DIR__ . '/app/controllers/sekretaris/SekretarisDashboardController.php', 'class' => 'SekretarisDashboardController', 'method' => 'index', 'param' => null];
    }

// --- DOSEN routes ---
} elseif ($seg0 === 'dosen') {
    $method = $seg1 ?: 'dashboard';
    switch ($method) {
        case 'dashboard':
            $route = ['file' => __DIR__ . '/app/controllers/dosen/DosenDashboardController.php', 'class' => 'DosenDashboardController', 'method' => 'index', 'param' => null];
            break;
        case 'dokumen':
            $act = $segments[2] ?? 'index';
            $id  = isset($segments[3]) ? (int)$segments[3] : null;
            $route = ['file' => __DIR__ . '/app/controllers/dosen/DokumenController.php', 'class' => 'DokumenController', 'method' => $act, 'param' => $id];
            break;
        case 'verifikasi':
            $id = isset($segments[2]) ? (int)$segments[2] : 0;
            $route = ['file' => __DIR__ . '/app/controllers/dosen/VerifikasiDosenController.php', 'class' => 'VerifikasiDosenController', 'method' => 'hasil', 'param' => $id];
            break;
        default:
            $route = ['file' => __DIR__ . '/app/controllers/dosen/DosenDashboardController.php', 'class' => 'DosenDashboardController', 'method' => 'index', 'param' => null];
    }

// --- PUBLIK routes (tanpa login) ---
} elseif ($seg0 === 'verifikasi') {
    $route = ['file' => __DIR__ . '/app/controllers/publik/VerifikasiPublikController.php', 'class' => 'VerifikasiPublikController', 'method' => 'index', 'param' => null];
}

// ============================================================
// DISPATCH
// ============================================================
if ($route === null) {
    http_response_code(404);
    include BASE_PATH . '/views/auth/error_403.php';
    exit;
}

if (!file_exists($route['file'])) {
    http_response_code(500);
    die('Controller tidak ditemukan: ' . htmlspecialchars($route['file']));
}

require_once $route['file'];

if (!class_exists($route['class'])) {
    die('Class tidak ditemukan: ' . htmlspecialchars($route['class']));
}

$obj    = new $route['class']();
$method = $route['method'];

if (!method_exists($obj, $method)) {
    http_response_code(404);
    include BASE_PATH . '/views/auth/error_403.php';
    exit;
}

if ($route['param'] !== null) {
    $obj->$method($route['param']);
} else {
    $obj->$method();
}
