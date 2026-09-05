# CLAUDE.md
## Panduan Pengembangan Sistem E-Office Tanda Tangan Digital
### Prodi Teknik Informatika — Universitas Pamulang
### Stack: PHP Native + MySQL + OpenSSL

---

## KONTEKS PROYEK

Sistem e-office berbasis web untuk Prodi Teknik Informatika Universitas Pamulang yang mengimplementasikan tanda tangan digital menggunakan RSA 2048-bit dan SHA-256. Sistem ini dibangun menggunakan **PHP Native murni tanpa framework**, dengan MySQL sebagai database, dan OpenSSL sebagai library kriptografi.

---

## STRUKTUR FOLDER WAJIB

> Setiap role memiliki folder controller dan views **tersendiri dan terpisah**.
> Jika ingin mengedit fitur salah satu role, cukup masuk ke folder role tersebut — tidak akan mengganggu role lain.

```
eoffice/
├── index.php                        # Entry point & router utama
├── .htaccess                        # URL rewriting Apache
│
├── config/
│   ├── database.php                 # Koneksi PDO MySQL
│   ├── session.php                  # Konfigurasi session & auto-expire
│   └── app.php                      # Konstanta aplikasi (BASE_URL, STORAGE_*, dll)
│
├── core/                            # Komponen inti — JANGAN DIUBAH sembarangan
│   ├── Router.php                   # Routing sederhana
│   ├── Controller.php               # Base controller (parent semua controller)
│   ├── Model.php                    # Base model dengan PDO
│   ├── Auth.php                     # Autentikasi & cek role
│   └── Security.php                 # CSRF token, sanitasi, validasi input
│
├── app/
│   │
│   ├── controllers/
│   │   ├── AuthController.php       # Login & logout (semua role)
│   │   │
│   │   ├── admin/                   # ← SEMUA CONTROLLER ROLE ADMIN
│   │   │   ├── AdminDashboardController.php   # Dashboard & statistik admin
│   │   │   ├── UserController.php             # CRUD user (tambah, edit, nonaktifkan)
│   │   │   ├── KunciRSAController.php         # Generate & kelola kunci RSA Kaprodi
│   │   │   ├── TemplateSuratController.php    # CRUD template surat
│   │   │   └── LogAktivitasController.php     # Lihat log aktivitas sistem
│   │   │
│   │   ├── kaprodi/                 # ← SEMUA CONTROLLER ROLE KAPRODI
│   │   │   ├── KaprodiDashboardController.php # Dashboard & statistik kaprodi
│   │   │   ├── TandaTanganController.php      # Antrian, review, proses tanda tangan
│   │   │   ├── TolakSuratController.php       # Tolak surat dengan alasan
│   │   │   └── RiwayatController.php          # Riwayat surat yang ditandatangani
│   │   │
│   │   ├── sekretaris/              # ← SEMUA CONTROLLER ROLE SEKRETARIS/TU
│   │   │   ├── SekretarisDashboardController.php  # Dashboard & statistik sekretaris
│   │   │   ├── BuatSuratController.php            # Buat surat dari template
│   │   │   ├── SuratKeluarController.php          # Kelola & distribusi surat keluar
│   │   │   ├── SuratMasukController.php           # Input & disposisi surat masuk
│   │   │   └── ArsipController.php                # Arsip & pencarian surat
│   │   │
│   │   ├── dosen/                   # ← SEMUA CONTROLLER ROLE DOSEN
│   │   │   ├── DosenDashboardController.php   # Dashboard & notifikasi dosen
│   │   │   ├── DokumenController.php          # Lihat & unduh dokumen yang diterima
│   │   │   └── VerifikasiDosenController.php  # Verifikasi dokumen dari dalam sistem
│   │   │
│   │   └── publik/                  # ← CONTROLLER TANPA LOGIN (akses publik)
│   │       └── VerifikasiPublikController.php # Verifikasi via QR Code tanpa akun
│   │
│   ├── models/                      # Model — dipakai bersama semua role
│   │   ├── UserModel.php            # CRUD tabel users
│   │   ├── SuratModel.php           # CRUD tabel surat
│   │   ├── TemplateSuratModel.php   # CRUD tabel template_surat
│   │   ├── TandaTanganModel.php     # CRUD tabel tanda_tangan
│   │   ├── DistribusiModel.php      # CRUD tabel distribusi
│   │   ├── KunciRSAModel.php        # CRUD tabel kunci_rsa
│   │   ├── NotifikasiModel.php      # CRUD tabel notifikasi
│   │   ├── NomorSuratModel.php      # CRUD tabel nomor_surat_counter
│   │   └── LogAktivitasModel.php    # CRUD tabel log_aktivitas
│   │
│   └── helpers/                     # Helper — utility dipakai semua role
│       ├── CryptoHelper.php         # RSA 2048-bit + SHA-256 (tanda tangan & verifikasi)
│       ├── PdfHelper.php            # Generate PDF surat (TCPDF)
│       ├── QrHelper.php             # Generate QR Code (phpqrcode)
│       ├── NomorSuratHelper.php     # Generate nomor surat otomatis & atomic
│       └── NotifikasiHelper.php     # Kirim notifikasi antar user dalam sistem
│
├── views/
│   ├── layouts/                     # Layout template — dipakai semua role
│   │   ├── header.php               # HTML head + navbar atas
│   │   ├── sidebar.php              # Sidebar navigasi (dinamis per role)
│   │   ├── footer.php               # HTML footer
│   │   └── public.php               # Layout khusus halaman publik (tanpa sidebar)
│   │
│   ├── auth/                        # Halaman login/logout — semua role
│   │   ├── login.php
│   │   └── error_403.php            # Halaman akses ditolak
│   │
│   ├── admin/                       # ← SEMUA VIEW ROLE ADMIN
│   │   ├── dashboard.php            # Dashboard admin
│   │   ├── user/
│   │   │   ├── index.php            # Daftar semua user
│   │   │   ├── tambah.php           # Form tambah user baru
│   │   │   └── edit.php             # Form edit user
│   │   ├── kunci/
│   │   │   ├── index.php            # Status kunci RSA Kaprodi
│   │   │   └── generate.php         # Form generate kunci RSA baru
│   │   ├── template/
│   │   │   ├── index.php            # Daftar template surat
│   │   │   ├── tambah.php           # Form tambah template
│   │   │   └── edit.php             # Form edit template
│   │   └── log/
│   │       └── index.php            # Daftar log aktivitas sistem
│   │
│   ├── kaprodi/                     # ← SEMUA VIEW ROLE KAPRODI
│   │   ├── dashboard.php            # Dashboard kaprodi (statistik surat)
│   │   ├── tanda_tangan/
│   │   │   ├── antrian.php          # Daftar surat menunggu tanda tangan
│   │   │   ├── review.php           # Detail surat sebelum tanda tangan
│   │   │   ├── input_pin.php        # Form input PIN untuk tanda tangan
│   │   │   └── sukses.php           # Konfirmasi tanda tangan berhasil
│   │   └── riwayat/
│   │       ├── index.php            # Riwayat semua surat yang ditandatangani
│   │       └── detail.php           # Detail surat yang sudah ditandatangani
│   │
│   ├── sekretaris/                  # ← SEMUA VIEW ROLE SEKRETARIS/TU
│   │   ├── dashboard.php            # Dashboard sekretaris
│   │   ├── surat_keluar/
│   │   │   ├── buat.php             # Form buat surat baru (pilih template)
│   │   │   ├── isi_form.php         # Form dinamis isi data surat sesuai template
│   │   │   ├── preview.php          # Preview surat sebelum submit ke Kaprodi
│   │   │   ├── daftar.php           # Daftar semua surat keluar
│   │   │   └── detail.php           # Detail surat keluar + status
│   │   ├── surat_masuk/
│   │   │   ├── index.php            # Daftar surat masuk
│   │   │   ├── tambah.php           # Input surat masuk baru
│   │   │   └── disposisi.php        # Form disposisi surat masuk
│   │   └── arsip/
│   │       ├── keluar.php           # Arsip surat keluar + pencarian
│   │       └── masuk.php            # Arsip surat masuk + pencarian
│   │
│   ├── dosen/                       # ← SEMUA VIEW ROLE DOSEN
│   │   ├── dashboard.php            # Dashboard dosen (notifikasi & dokumen baru)
│   │   ├── dokumen/
│   │   │   ├── index.php            # Daftar dokumen yang diterima
│   │   │   └── detail.php           # Detail dokumen + tombol unduh PDF
│   │   └── verifikasi/
│   │       └── hasil.php            # Hasil verifikasi dokumen dari dalam sistem
│   │
│   └── publik/                      # ← VIEW TANPA LOGIN (akses publik)
│       └── verifikasi/
│           ├── index.php            # Halaman verifikasi via QR Code
│           ├── valid.php            # Tampilan hasil: DOKUMEN VALID
│           └── tidak_valid.php      # Tampilan hasil: DOKUMEN TIDAK VALID
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css
│   │   ├── js/
│   │   │   └── app.js
│   │   └── img/
│   │       └── logo_unpam.png
│   ├── pdf/                         # PDF final yang sudah ditandatangani
│   └── qr/                          # File QR Code PNG yang digenerate
│
├── storage/
│   └── keys/                        # Private key terenkripsi — JANGAN expose ke publik
│
├── vendor/
│   ├── tcpdf/                       # Library generate PDF
│   └── phpqrcode/                   # Library generate QR Code
│
└── database/
    └── eoffice.sql                  # Schema database lengkap + data awal
```

---

### Panduan Edit Per Role

> Gunakan tabel ini setiap kali ingin mengubah fitur salah satu role.
> Cukup masuk ke folder role yang dituju — tidak ada file yang dipakai bersama antar role di level views & controllers.

| Role | Controller ada di | Views ada di | Route prefix |
|---|---|---|---|
| **Admin** | `app/controllers/admin/` | `views/admin/` | `/admin/...` |
| **Kaprodi** | `app/controllers/kaprodi/` | `views/kaprodi/` | `/kaprodi/...` |
| **Sekretaris** | `app/controllers/sekretaris/` | `views/sekretaris/` | `/sekretaris/...` |
| **Dosen** | `app/controllers/dosen/` | `views/dosen/` | `/dosen/...` |
| **Publik** | `app/controllers/publik/` | `views/publik/` | `/verifikasi` |
| **Shared** | `app/models/` + `app/helpers/` | `views/layouts/` | — |

---

## ATURAN WAJIB PENGEMBANGAN

### 1. KEAMANAN DATABASE — TIDAK BOLEH DILANGGAR

**SELALU gunakan Prepared Statement PDO. TIDAK PERNAH string concatenation.**

```php
// ✅ BENAR — SELALU seperti ini
$stmt = $pdo->prepare("SELECT * FROM surat WHERE id = ? AND status = ?");
$stmt->execute([$id, $status]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// ❌ SALAH — DILARANG KERAS
$query = "SELECT * FROM surat WHERE id = $id";
$result = mysqli_query($conn, $query);
```

### 2. CSRF PROTECTION — WAJIB DI SEMUA FORM

```php
// Di Security.php — generate token
public static function generateCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Di Security.php — validasi token
public static function validateCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) 
        && hash_equals($_SESSION['csrf_token'], $token);
}

// Di setiap view form — WAJIB
<input type="hidden" name="csrf_token" 
       value="<?= Security::generateCsrfToken() ?>">

// Di setiap controller POST — WAJIB validasi dulu
if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('CSRF token tidak valid.');
}
```

### 3. SANITASI INPUT — WAJIB SEBELUM DIPROSES

```php
// Di Security.php
public static function sanitize(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

public static function sanitizeArray(array $data): array {
    return array_map([self::class, 'sanitize'], $data);
}

// Penggunaan di controller
$nama = Security::sanitize($_POST['nama'] ?? '');
$perihal = Security::sanitize($_POST['perihal'] ?? '');
```

### 4. CEK ROLE — WAJIB DI SETIAP CONTROLLER

```php
// Di Auth.php
public static function requireRole(string|array $role): void {
    self::requireLogin();
    $roles = is_array($role) ? $role : [$role];
    if (!in_array($_SESSION['user_role'], $roles)) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/error/403');
        exit;
    }
}

public static function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

// Penggunaan di setiap controller yang butuh role
Auth::requireRole('admin');           // Hanya admin
Auth::requireRole('kaprodi');         // Hanya kaprodi
Auth::requireRole(['admin', 'kaprodi']); // Admin atau kaprodi
```

### 5. OUTPUT ESCAPING — WAJIB DI SEMUA VIEW

```php
// Di views — SELALU escape output
<?= htmlspecialchars($data['nama'], ENT_QUOTES, 'UTF-8') ?>

// DILARANG output langsung tanpa escape
<?= $data['nama'] ?>  // ❌ BERBAHAYA — XSS
```

---

## KONFIGURASI WAJIB

### config/database.php
```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eoffice_unpam');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . 
                   ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log('DB Error: ' . $e->getMessage());
            die('Koneksi database gagal.');
        }
    }
    return $pdo;
}
```

### config/app.php
```php
<?php
define('BASE_URL', 'http://localhost/eoffice');
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_KEYS', BASE_PATH . '/storage/keys/');
define('STORAGE_PDF', BASE_PATH . '/public/pdf/');
define('STORAGE_QR', BASE_PATH . '/public/qr/');
define('SESSION_LIFETIME', 3600); // 1 jam dalam detik
define('RSA_BITS', 2048);
define('VERIFY_URL', BASE_URL . '/verifikasi?id=');
```

### config/session.php
```php
<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);   // Set 1 jika pakai HTTPS
ini_set('session.use_strict_mode', 1);
ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
session_start();

// Cek session expired
if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/login?expired=1');
        exit;
    }
}
$_SESSION['last_activity'] = time();

// Regenerate session ID setiap 30 menit (cegah session fixation)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
```

### .htaccess
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [QSA,L]

# Blokir akses ke folder sensitif
<FilesMatch "\.(sql|log|md|env)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Blokir akses langsung ke storage/keys
<Directory "storage/keys">
    Order Allow,Deny
    Deny from all
</Directory>
```

---

## IMPLEMENTASI KRIPTOGRAFI

### app/helpers/CryptoHelper.php

```php
<?php
class CryptoHelper {

    /**
     * Generate pasangan kunci RSA 2048-bit
     * Dipanggil oleh Admin saat setup awal
     * @return array ['public_key' => string, 'private_key' => string]
     */
    public static function generateKeyPair(): array {
        $config = [
            'digest_alg'       => 'sha256',
            'private_key_bits' => RSA_BITS,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $resource = openssl_pkey_new($config);
        if (!$resource) {
            throw new Exception('Gagal generate kunci RSA: ' . openssl_error_string());
        }

        // Ambil private key dalam format PEM
        openssl_pkey_export($resource, $privateKeyPem);

        // Ambil public key dalam format PEM
        $keyDetails = openssl_pkey_get_details($resource);
        $publicKeyPem = $keyDetails['key'];

        openssl_free_key($resource);

        return [
            'public_key'  => $publicKeyPem,
            'private_key' => $privateKeyPem,
        ];
    }

    /**
     * Enkripsi private key dengan AES-256-CBC berbasis PIN
     * Private key yang terenkripsi inilah yang disimpan di server
     * PIN TIDAK PERNAH disimpan ke database
     * @param string $privateKeyPem Private key PEM plaintext
     * @param string $pin PIN dari Kaprodi
     * @return string Private key terenkripsi dalam format base64
     */
    public static function encryptPrivateKey(string $privateKeyPem, string $pin): string {
        $salt   = random_bytes(16);
        $key    = hash_pbkdf2('sha256', $pin, $salt, 100000, 32, true);
        $iv     = random_bytes(16);
        $encrypted = openssl_encrypt(
            $privateKeyPem, 
            'AES-256-CBC', 
            $key, 
            OPENSSL_RAW_DATA, 
            $iv
        );

        if ($encrypted === false) {
            throw new Exception('Gagal enkripsi private key.');
        }

        // Format: base64(salt + iv + ciphertext)
        return base64_encode($salt . $iv . $encrypted);
    }

    /**
     * Dekripsi private key menggunakan PIN
     * Dipanggil saat Kaprodi input PIN untuk tanda tangan
     * @param string $encryptedKey Private key terenkripsi (dari database)
     * @param string $pin PIN dari Kaprodi
     * @return string Private key PEM plaintext
     * @throws Exception Jika PIN salah atau dekripsi gagal
     */
    public static function decryptPrivateKey(string $encryptedKey, string $pin): string {
        $decoded = base64_decode($encryptedKey);
        if ($decoded === false || strlen($decoded) < 33) {
            throw new Exception('Format kunci tidak valid.');
        }

        $salt      = substr($decoded, 0, 16);
        $iv        = substr($decoded, 16, 16);
        $ciphertext = substr($decoded, 32);

        $key = hash_pbkdf2('sha256', $pin, $salt, 100000, 32, true);
        $decrypted = openssl_decrypt(
            $ciphertext, 
            'AES-256-CBC', 
            $key, 
            OPENSSL_RAW_DATA, 
            $iv
        );

        if ($decrypted === false || strpos($decrypted, '-----BEGIN') === false) {
            throw new Exception('PIN salah atau kunci rusak.');
        }

        return $decrypted;
    }

    /**
     * Hash dokumen menggunakan SHA-256
     * Input: path ke file PDF yang sudah final
     * @param string $pdfPath Path ke file PDF
     * @return string Hash SHA-256 dalam format hexadecimal (64 karakter)
     */
    public static function hashDocument(string $pdfPath): string {
        if (!file_exists($pdfPath)) {
            throw new Exception('File dokumen tidak ditemukan: ' . $pdfPath);
        }
        return hash_file('sha256', $pdfPath);
    }

    /**
     * Buat tanda tangan digital
     * Proses: SHA-256(PDF) → RSA_Sign(hash, privateKey) → base64(signature)
     * @param string $hash Hash SHA-256 dari dokumen (64 char hex)
     * @param string $privateKeyPem Private key PEM plaintext (sudah didekripsi)
     * @return string Digital signature dalam format base64
     */
    public static function signDocument(string $hash, string $privateKeyPem): string {
        $privateKey = openssl_pkey_get_private($privateKeyPem);
        if (!$privateKey) {
            throw new Exception('Private key tidak valid: ' . openssl_error_string());
        }

        $signature = '';
        $result = openssl_sign($hash, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);

        if (!$result) {
            throw new Exception('Gagal membuat tanda tangan digital.');
        }

        return base64_encode($signature);
    }

    /**
     * Verifikasi tanda tangan digital
     * Proses: SHA-256(PDF) → RSA_Verify(hash, signature, publicKey)
     * @param string $hash Hash SHA-256 dari dokumen yang diterima
     * @param string $signatureBase64 Digital signature dalam base64 (dari database)
     * @param string $publicKeyPem Public key PEM (dari database)
     * @return bool true jika valid, false jika tidak valid / dipalsukan
     */
    public static function verifyDocument(
        string $hash, 
        string $signatureBase64, 
        string $publicKeyPem
    ): bool {
        $publicKey = openssl_pkey_get_public($publicKeyPem);
        if (!$publicKey) {
            return false;
        }

        $signature = base64_decode($signatureBase64);
        $result = openssl_verify($hash, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKey);

        return $result === 1;
    }
}
```

---

## ALUR TANDA TANGAN DIGITAL — IMPLEMENTASI LENGKAP

### app/controllers/TandaTanganController.php

```php
<?php
require_once BASE_PATH . '/core/Auth.php';
require_once BASE_PATH . '/core/Security.php';
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/models/KunciRSAModel.php';
require_once BASE_PATH . '/app/helpers/CryptoHelper.php';
require_once BASE_PATH . '/app/helpers/PdfHelper.php';
require_once BASE_PATH . '/app/helpers/QrHelper.php';
require_once BASE_PATH . '/app/helpers/NotifikasiHelper.php';
require_once BASE_PATH . '/app/helpers/NomorSuratHelper.php';

class TandaTanganController {

    /**
     * Halaman antrian surat menunggu tanda tangan
     * Hanya bisa diakses Kaprodi
     */
    public function antrian(): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $data['surat_menunggu'] = $suratModel->getSuratMenunggu();
        $data['title'] = 'Antrian Tanda Tangan';

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/tanda_tangan/antrian.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Review surat sebelum tanda tangan
     */
    public function review(int $suratId): void {
        Auth::requireRole('kaprodi');

        $suratModel = new SuratModel();
        $data['surat'] = $suratModel->getById($suratId);

        if (!$data['surat']) {
            header('Location: ' . BASE_URL . '/tanda-tangan/antrian');
            exit;
        }

        $data['csrf_token'] = Security::generateCsrfToken();
        $data['title'] = 'Review Surat';

        include BASE_PATH . '/views/layouts/header.php';
        include BASE_PATH . '/views/tanda_tangan/review.php';
        include BASE_PATH . '/views/layouts/footer.php';
    }

    /**
     * Proses tanda tangan digital
     * Inilah inti dari seluruh sistem — dipanggil saat Kaprodi input PIN & klik tanda tangan
     */
    public function proses(): void {
        Auth::requireRole('kaprodi');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/tanda-tangan/antrian');
            exit;
        }

        // 1. Validasi CSRF
        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('Token tidak valid.');
        }

        $suratId = (int)($_POST['surat_id'] ?? 0);
        $pin     = $_POST['pin'] ?? '';
        $kaprodiId = (int)$_SESSION['user_id'];

        if (!$suratId || !$pin) {
            $_SESSION['error'] = 'Data tidak lengkap.';
            header('Location: ' . BASE_URL . '/tanda-tangan/antrian');
            exit;
        }

        try {
            // 2. Ambil data surat
            $suratModel = new SuratModel();
            $surat = $suratModel->getById($suratId);
            if (!$surat || $surat['status'] !== 'menunggu') {
                throw new Exception('Surat tidak ditemukan atau sudah diproses.');
            }

            // 3. Ambil kunci RSA Kaprodi dari database
            $kunciModel = new KunciRSAModel();
            $kunci = $kunciModel->getAktifByUserId($kaprodiId);
            if (!$kunci) {
                throw new Exception('Kunci RSA tidak ditemukan. Hubungi Admin.');
            }

            // 4. Dekripsi private key menggunakan PIN Kaprodi
            // Jika PIN salah, CryptoHelper::decryptPrivateKey() akan throw Exception
            $privateKeyPem = CryptoHelper::decryptPrivateKey(
                $kunci['private_key_encrypted'], 
                $pin
            );

            // 5. Render dokumen PDF final dari template + data surat
            // PDF final inilah yang akan di-hash — SEBELUM QR Code ditempel
            $pdfHelper = new PdfHelper();
            $pdfTempPath = STORAGE_PDF . 'temp_' . $suratId . '_' . time() . '.pdf';
            $pdfHelper->renderSurat($surat, $pdfTempPath);

            // 6. Hitung hash SHA-256 dari PDF final
            $hashSha256 = CryptoHelper::hashDocument($pdfTempPath);

            // 7. Buat tanda tangan digital: RSA_Sign(hash, privateKey)
            $signatureBase64 = CryptoHelper::signDocument($hashSha256, $privateKeyPem);

            // 8. Hapus private key dari memori secepatnya
            unset($privateKeyPem);

            // 9. Simpan signature + hash ke database
            $tandaTanganModel = new TandaTanganModel();
            $tandaTanganId = $tandaTanganModel->simpan([
                'surat_id'             => $suratId,
                'kaprodi_id'           => $kaprodiId,
                'hash_sha256'          => $hashSha256,
                'signature_rsa'        => $signatureBase64,
                'timestamp_tandatangan'=> time(),
                'public_key_snapshot'  => $kunci['public_key'],
            ]);

            // 10. Generate URL verifikasi & QR Code
            $verifyUrl = VERIFY_URL . $suratId;
            $qrPath    = STORAGE_QR . 'qr_' . $suratId . '.png';
            QrHelper::generate($verifyUrl, $qrPath);

            // 11. Render PDF final dengan QR Code ditempel
            $pdfFinalPath = STORAGE_PDF . 'surat_' . $suratId . '.pdf';
            $pdfHelper->renderSuratDenganQr($surat, $qrPath, $pdfFinalPath);

            // 12. Hapus PDF temp (yang tanpa QR Code)
            if (file_exists($pdfTempPath)) {
                unlink($pdfTempPath);
            }

            // 13. Update path PDF & QR di record tanda tangan
            $tandaTanganModel->updatePath($tandaTanganId, [
                'pdf_path'    => 'surat_' . $suratId . '.pdf',
                'qr_code_url' => $verifyUrl,
            ]);

            // 14. Update status surat menjadi 'ditandatangani'
            $suratModel->updateStatus($suratId, 'ditandatangani');

            // 15. Kirim notifikasi ke Sekretaris pembuat surat
            NotifikasiHelper::kirim(
                $surat['pembuat_id'],
                'Surat "' . $surat['perihal'] . '" telah ditandatangani oleh Kaprodi.',
                BASE_URL . '/surat/detail/' . $suratId
            );

            // 16. Catat log aktivitas
            self::catatLog($kaprodiId, 'tanda_tangan', 'Menandatangani surat ID: ' . $suratId);

            $_SESSION['success'] = 'Surat berhasil ditandatangani secara digital.';
            header('Location: ' . BASE_URL . '/tanda-tangan/antrian');
            exit;

        } catch (Exception $e) {
            error_log('[TandaTangan Error] ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            header('Location: ' . BASE_URL . '/tanda-tangan/review/' . $suratId);
            exit;
        }
    }

    /**
     * Tolak surat dengan alasan
     */
    public function tolak(): void {
        Auth::requireRole('kaprodi');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/tanda-tangan/antrian');
            exit;
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            die('Token tidak valid.');
        }

        $suratId      = (int)($_POST['surat_id'] ?? 0);
        $alasanTolak  = Security::sanitize($_POST['alasan_tolak'] ?? '');

        if (!$suratId || !$alasanTolak) {
            $_SESSION['error'] = 'Alasan penolakan wajib diisi.';
            header('Location: ' . BASE_URL . '/tanda-tangan/review/' . $suratId);
            exit;
        }

        $suratModel = new SuratModel();
        $surat = $suratModel->getById($suratId);

        $suratModel->tolak($suratId, $alasanTolak);

        NotifikasiHelper::kirim(
            $surat['pembuat_id'],
            'Surat "' . $surat['perihal'] . '" ditolak. Alasan: ' . $alasanTolak,
            BASE_URL . '/surat/detail/' . $suratId
        );

        self::catatLog(
            (int)$_SESSION['user_id'], 
            'tolak_surat', 
            'Menolak surat ID: ' . $suratId . ' — ' . $alasanTolak
        );

        $_SESSION['success'] = 'Surat berhasil ditolak.';
        header('Location: ' . BASE_URL . '/tanda-tangan/antrian');
        exit;
    }

    private static function catatLog(int $userId, string $aksi, string $keterangan): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO log_aktivitas (user_id, aksi, keterangan, ip_address, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$userId, $aksi, $keterangan, $_SERVER['REMOTE_ADDR'] ?? '']);
    }
}
```

---

## ALUR VERIFIKASI PUBLIK — IMPLEMENTASI LENGKAP

### app/controllers/VerifikasiController.php

```php
<?php
require_once BASE_PATH . '/app/models/SuratModel.php';
require_once BASE_PATH . '/app/models/TandaTanganModel.php';
require_once BASE_PATH . '/app/helpers/CryptoHelper.php';

class VerifikasiController {

    /**
     * Halaman verifikasi publik — TANPA LOGIN
     * Diakses via URL dari QR Code: /verifikasi?id=[ID_DOKUMEN]
     */
    public function index(): void {
        // Halaman ini TIDAK memerlukan login sama sekali
        $suratId = (int)($_GET['id'] ?? 0);

        $data['title']   = 'Verifikasi Dokumen — E-Office Prodi TI UNPAM';
        $data['hasil']   = null;
        $data['surat']   = null;
        $data['error']   = null;

        if ($suratId > 0) {
            try {
                // 1. Ambil data surat dari database
                $suratModel = new SuratModel();
                $surat = $suratModel->getById($suratId);

                if (!$surat) {
                    throw new Exception('Dokumen dengan ID tersebut tidak ditemukan di sistem.');
                }

                // 2. Ambil data tanda tangan dari database
                $ttModel   = new TandaTanganModel();
                $tandaTangan = $ttModel->getBySuratId($suratId);

                if (!$tandaTangan) {
                    throw new Exception('Dokumen ini belum memiliki tanda tangan digital.');
                }

                // 3. Ambil PDF dari server & hitung ulang hash SHA-256
                $pdfPath = STORAGE_PDF . $tandaTangan['pdf_path'];
                if (!file_exists($pdfPath)) {
                    throw new Exception('File dokumen tidak ditemukan di server.');
                }

                // Hash SHA-256 dari PDF yang ada di server (dokumen asli)
                $hashDariFile = CryptoHelper::hashDocument($pdfPath);

                // 4. Verifikasi: bandingkan hash dari file vs signature di database
                // RSA_Decrypt(signature, publicKey) → hash tersimpan
                // Bandingkan dengan hash yang baru dihitung dari file
                $isValid = CryptoHelper::verifyDocument(
                    $hashDariFile,
                    $tandaTangan['signature_rsa'],
                    $tandaTangan['public_key_snapshot']
                );

                $data['hasil']       = $isValid;
                $data['surat']       = $surat;
                $data['tanda_tangan'] = $tandaTangan;
                $data['hash_file']   = $hashDariFile;

            } catch (Exception $e) {
                $data['error'] = $e->getMessage();
            }
        }

        // Render halaman verifikasi publik (layout tanpa sidebar/login)
        include BASE_PATH . '/views/layouts/public.php';
        // public.php sudah include views/verifikasi/index.php
    }
}
```

---

## SKEMA DATABASE LENGKAP

### database/eoffice.sql

```sql
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Tabel users
-- ----------------------------
CREATE TABLE `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama`       VARCHAR(150) NOT NULL,
  `nip`        VARCHAR(30)  NOT NULL UNIQUE,
  `jabatan`    VARCHAR(100) NOT NULL,
  `role`       ENUM('admin','kaprodi','sekretaris','dosen') NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
  `status`     ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel kunci_rsa
-- ----------------------------
CREATE TABLE `kunci_rsa` (
  `id`                    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`               INT UNSIGNED NOT NULL COMMENT 'FK ke users (Kaprodi)',
  `public_key`            TEXT NOT NULL COMMENT 'Public key PEM',
  `private_key_encrypted` TEXT NOT NULL COMMENT 'Private key terenkripsi AES-256 + PIN',
  `is_aktif`              TINYINT(1) NOT NULL DEFAULT 1,
  `created_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel template_surat
-- ----------------------------
CREATE TABLE `template_surat` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jenis`       VARCHAR(10)  NOT NULL UNIQUE COMMENT 'ST, SK, SU, ND, BA, SP, DS',
  `nama_jenis`       VARCHAR(100) NOT NULL,
  `konten_template`  TEXT NOT NULL COMMENT 'Template HTML dengan placeholder {{variable}}',
  `format_nomor`     VARCHAR(100) NOT NULL COMMENT 'Contoh: {urut}/ST-TI.UNPAM/{bulan_romawi}/{tahun}',
  `field_dinamis`    JSON NOT NULL COMMENT 'Daftar field yang harus diisi user',
  `status`           ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel surat
-- ----------------------------
CREATE TABLE `surat` (
  `id`                  INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_surat`         VARCHAR(100) NOT NULL UNIQUE,
  `template_id`         INT UNSIGNED NOT NULL COMMENT 'FK ke template_surat',
  `perihal`             VARCHAR(255) NOT NULL,
  `isi_data`            JSON NOT NULL COMMENT 'Data dinamis sesuai template',
  `pembuat_id`          INT UNSIGNED NOT NULL COMMENT 'FK ke users (Sekretaris)',
  `penerima_nama`       VARCHAR(150) DEFAULT NULL,
  `penerima_jabatan`    VARCHAR(100) DEFAULT NULL,
  `penerima_instansi`   VARCHAR(200) DEFAULT NULL,
  `status`              ENUM('draft','menunggu','ditandatangani','ditolak','didistribusikan') 
                        NOT NULL DEFAULT 'draft',
  `alasan_tolak`        TEXT DEFAULT NULL,
  `tanggal_surat`       DATE NOT NULL,
  `created_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`          TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`template_id`) REFERENCES `template_surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`pembuat_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel tanda_tangan
-- ----------------------------
CREATE TABLE `tanda_tangan` (
  `id`                     INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `surat_id`               INT UNSIGNED NOT NULL UNIQUE COMMENT 'FK ke surat',
  `kaprodi_id`             INT UNSIGNED NOT NULL COMMENT 'FK ke users (Kaprodi)',
  `hash_sha256`            CHAR(64) NOT NULL COMMENT 'SHA-256 hash PDF final (hex)',
  `signature_rsa`          TEXT NOT NULL COMMENT 'Digital signature base64',
  `public_key_snapshot`    TEXT NOT NULL COMMENT 'Snapshot public key saat penandatanganan',
  `timestamp_tandatangan`  INT UNSIGNED NOT NULL COMMENT 'Unix timestamp UTC',
  `pdf_path`               VARCHAR(255) DEFAULT NULL COMMENT 'Nama file PDF di STORAGE_PDF',
  `qr_code_url`            VARCHAR(500) DEFAULT NULL COMMENT 'URL verifikasi publik',
  `created_at`             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`surat_id`)   REFERENCES `surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`kaprodi_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel distribusi
-- ----------------------------
CREATE TABLE `distribusi` (
  `id`                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `surat_id`             INT UNSIGNED NOT NULL COMMENT 'FK ke surat',
  `penerima_id`          INT UNSIGNED DEFAULT NULL COMMENT 'FK ke users jika internal',
  `penerima_eksternal`   VARCHAR(200) DEFAULT NULL COMMENT 'Nama penerima jika eksternal',
  `tanggal_kirim`        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_baca`          ENUM('belum','dibaca') NOT NULL DEFAULT 'belum',
  `dibaca_pada`          TIMESTAMP DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`surat_id`)    REFERENCES `surat`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`penerima_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel notifikasi
-- ----------------------------
CREATE TABLE `notifikasi` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL COMMENT 'FK ke users penerima notifikasi',
  `pesan`      VARCHAR(500) NOT NULL,
  `url`        VARCHAR(500) DEFAULT NULL,
  `is_dibaca`  TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel log_aktivitas
-- ----------------------------
CREATE TABLE `log_aktivitas` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL COMMENT 'FK ke users',
  `aksi`       VARCHAR(100) NOT NULL,
  `keterangan` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Tabel nomor_surat_counter
-- (untuk generate nomor surat otomatis per jenis per tahun)
-- ----------------------------
CREATE TABLE `nomor_surat_counter` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_jenis`  VARCHAR(10)  NOT NULL,
  `tahun`       YEAR NOT NULL,
  `counter`     INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_jenis_tahun` (`kode_jenis`, `tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
```

---

## GENERATE NOMOR SURAT OTOMATIS

### app/helpers/NomorSuratHelper.php

```php
<?php
class NomorSuratHelper {

    /**
     * Generate nomor surat otomatis & atomic (tidak boleh duplikat)
     * Format: 012/ST-TI.UNPAM/VI/2026
     * @param string $kodeJenis Kode jenis surat (ST, SK, SU, dll)
     * @return string Nomor surat lengkap
     */
    public static function generate(string $kodeJenis): string {
        $pdo   = getDB();
        $tahun = (int)date('Y');

        // Atomic increment menggunakan INSERT ... ON DUPLICATE KEY UPDATE
        // Mencegah race condition jika ada 2 request bersamaan
        $stmt = $pdo->prepare(
            "INSERT INTO nomor_surat_counter (kode_jenis, tahun, counter) 
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE counter = counter + 1"
        );
        $stmt->execute([$kodeJenis, $tahun]);

        // Ambil nilai counter yang baru
        $stmt = $pdo->prepare(
            "SELECT counter FROM nomor_surat_counter 
             WHERE kode_jenis = ? AND tahun = ?"
        );
        $stmt->execute([$kodeJenis, $tahun]);
        $counter = (int)$stmt->fetchColumn();

        $nomorUrut   = str_pad($counter, 3, '0', STR_PAD_LEFT);
        $bulanRomawi = self::toBulanRomawi((int)date('n'));

        return "{$nomorUrut}/{$kodeJenis}-TI.UNPAM/{$bulanRomawi}/{$tahun}";
    }

    private static function toBulanRomawi(int $bulan): string {
        $romawi = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
        return $romawi[$bulan - 1] ?? 'I';
    }
}
```

---

## GENERATE PDF & QR CODE

### app/helpers/PdfHelper.php

```php
<?php
require_once BASE_PATH . '/vendor/tcpdf/tcpdf.php';

class PdfHelper {

    /**
     * Render PDF surat tanpa QR Code
     * Digunakan untuk menghitung hash SHA-256 (sebelum QR Code ditempel)
     */
    public function renderSurat(array $surat, string $outputPath): void {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('E-Office TI UNPAM');
        $pdf->SetAuthor('Sistem E-Office Prodi TI UNPAM');
        $pdf->SetTitle($surat['perihal']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 11);

        // Render konten surat dari template
        $konten = $this->renderTemplate($surat);
        $pdf->writeHTML($konten, true, false, true, false, '');

        $pdf->Output($outputPath, 'F');
    }

    /**
     * Render PDF surat final dengan QR Code ditempel di pojok kanan bawah
     * QR Code ditempel SETELAH hash dihitung — tidak mempengaruhi hash
     */
    public function renderSuratDenganQr(array $surat, string $qrPath, string $outputPath): void {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator('E-Office TI UNPAM');
        $pdf->SetAuthor('Sistem E-Office Prodi TI UNPAM');
        $pdf->SetTitle($surat['perihal']);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 11);

        $konten = $this->renderTemplate($surat);
        $pdf->writeHTML($konten, true, false, true, false, '');

        // Tempel QR Code pojok kanan bawah (x=170, y=265, lebar=30, tinggi=30 mm)
        if (file_exists($qrPath)) {
            $pdf->Image($qrPath, 170, 265, 30, 30, 'PNG');
            $pdf->SetXY(155, 258);
            $pdf->SetFont('dejavusans', '', 6);
            $pdf->Cell(55, 5, 'Scan untuk verifikasi keaslian dokumen', 0, 0, 'C');
        }

        $pdf->Output($outputPath, 'F');
    }

    /**
     * Render konten HTML surat dari template + data dinamis
     */
    private function renderTemplate(array $surat): string {
        $isiData  = json_decode($surat['isi_data'], true) ?? [];
        $template = $surat['konten_template'];

        // Ganti placeholder {{variable}} dengan data aktual
        foreach ($isiData as $key => $value) {
            $template = str_replace('{{' . $key . '}}', 
                htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), 
                $template
            );
        }

        // Ganti placeholder sistem
        $template = str_replace('{{nomor_surat}}', $surat['nomor_surat'], $template);
        $template = str_replace('{{tanggal_surat}}', 
            date('d F Y', strtotime($surat['tanggal_surat'])), $template);
        $template = str_replace('{{perihal}}', $surat['perihal'], $template);

        return $template;
    }
}
```

### app/helpers/QrHelper.php

```php
<?php
require_once BASE_PATH . '/vendor/phpqrcode/qrlib.php';

class QrHelper {

    /**
     * Generate QR Code berisi URL verifikasi publik
     * @param string $url URL verifikasi (VERIFY_URL + suratId)
     * @param string $outputPath Path output file PNG
     */
    public static function generate(string $url, string $outputPath): void {
        // QR_ECLEVEL_M = Medium error correction (15% recovery)
        // Ukuran pixel per module = 5
        // Margin = 2
        QRcode::png($url, $outputPath, QR_ECLEVEL_M, 5, 2);

        if (!file_exists($outputPath)) {
            throw new Exception('Gagal generate QR Code.');
        }
    }
}
```

---

## ROUTER SEDERHANA

### index.php

```php
<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/core/Security.php';
require_once __DIR__ . '/core/Auth.php';

// Ambil URL dari request
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$segments = explode('/', $url);

$controller = $segments[0] ?? 'dashboard';
$method     = $segments[1] ?? 'index';
$param      = isset($segments[2]) ? (int)$segments[2] : null;

// Routing
$routes = [
    ''              => ['AuthController',        'index'],
    'login'         => ['AuthController',        'index'],
    'logout'        => ['AuthController',        'logout'],
    'dashboard'     => ['DashboardController',   'index'],
    'surat'         => ['SuratController',       $method],
    'tanda-tangan'  => ['TandaTanganController', $method],
    'verifikasi'    => ['VerifikasiController',  'index'],
    'arsip'         => ['ArsipController',       $method],
    'admin'         => ['AdminController',       $method],
];

if (!array_key_exists($controller, $routes)) {
    http_response_code(404);
    include BASE_PATH . '/views/404.php';
    exit;
}

[$controllerClass, $controllerMethod] = $routes[$controller];
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerClass . '.php';

if (!file_exists($controllerFile)) {
    http_response_code(404);
    include BASE_PATH . '/views/404.php';
    exit;
}

require_once $controllerFile;
$obj = new $controllerClass();

if ($param !== null) {
    $obj->$controllerMethod($param);
} else {
    $obj->$controllerMethod();
}
```

---

## CHECKLIST KEAMANAN SEBELUM DEPLOY

```
[ ] Semua query database menggunakan PDO prepared statement
[ ] CSRF token terpasang & divalidasi di semua form POST
[ ] Semua output di views menggunakan htmlspecialchars()
[ ] Semua input di-sanitasi sebelum diproses
[ ] Cek role terpasang di setiap controller
[ ] Session expire aktif (SESSION_LIFETIME)
[ ] Folder storage/keys tidak bisa diakses dari web (.htaccess)
[ ] Error PHP tidak ditampilkan ke user (display_errors = Off di production)
[ ] Error dicatat ke log file (error_log aktif)
[ ] Private key tidak pernah dikirim ke frontend / JavaScript
[ ] PIN tidak tersimpan di database manapun
[ ] HTTPS aktif di server production
[ ] File PDF di folder public/pdf tidak bisa di-listing (index.php kosong)
[ ] Semua library (TCPDF, phpqrcode) sudah terinstall dan berjalan
[ ] OpenSSL extension aktif di PHP (phpinfo() untuk cek)
```

---

## LIBRARY YANG DIBUTUHKAN

```bash
# Cek versi PHP (minimal 7.4, rekomendasi 8.x)
php -v

# Cek OpenSSL aktif
php -r "echo extension_loaded('openssl') ? 'OpenSSL OK' : 'OpenSSL TIDAK AKTIF';"

# Download TCPDF (generate PDF)
# https://github.com/tecnickcom/TCPDF
# Ekstrak ke vendor/tcpdf/

# Download PHP QR Code (generate QR Code)
# https://phpqrcode.sourceforge.net/
# Ekstrak ke vendor/phpqrcode/
```

---

*CLAUDE.md ini adalah panduan teknis lengkap untuk pengembangan sistem e-office PHP Native.*
*Setiap developer yang mengerjakan sistem ini WAJIB membaca dokumen ini terlebih dahulu.*
*Versi: 1.0 — Juni 2026*
