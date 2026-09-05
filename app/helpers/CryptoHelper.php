<?php
class CryptoHelper {

    private static function getOpenSSLConfigPath(): ?string {
        $candidates = [
            'C:\\xampp\\php\\extras\\openssl\\openssl.cnf',
            'C:\\xampp\\php\\extras\\ssl\\openssl.cnf',
            'C:\\xampp\\apache\\conf\\openssl.cnf',
            'C:\\xampp\\php84\\extras\\ssl\\openssl.cnf',
            'C:\\xampp\\php\\windowsXamppPhp\\extras\\ssl\\openssl.cnf',
        ];

        foreach ($candidates as $candidate) {
            if (is_file($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Generate pasangan kunci RSA 2048-bit
     * @return array ['public_key' => string, 'private_key' => string]
     */
    public static function generateKeyPair(): array {
        $config = [
            'digest_alg'       => 'sha256',
            'private_key_bits' => RSA_BITS,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];

        $opensslConfig = self::getOpenSSLConfigPath();
        if ($opensslConfig !== null) {
            $config['config'] = $opensslConfig;
        }

        $resource = openssl_pkey_new($config);
        if (!$resource) {
            $errors = [];
            while ($error = openssl_error_string()) {
                $errors[] = $error;
            }
            throw new Exception('Gagal generate kunci RSA: ' . implode(' | ', $errors));
        }

        if (!openssl_pkey_export($resource, $privateKeyPem, null, $config)) {
            $errors = [];
            while ($error = openssl_error_string()) {
                $errors[] = $error;
            }
            throw new Exception('Gagal mengekspor private key: ' . implode(' | ', $errors));
        }

        $keyDetails = openssl_pkey_get_details($resource);
        if ($keyDetails === false || empty($keyDetails['key'])) {
            throw new Exception('Gagal membaca public key hasil generate.');
        }

        $publicKeyPem = $keyDetails['key'];

        return [
            'public_key'  => $publicKeyPem,
            'private_key' => $privateKeyPem,
        ];
    }

    /**
     * Enkripsi private key dengan AES-256-CBC berbasis PIN
     * Format output: base64(salt[16] + iv[16] + ciphertext)
     */
    public static function encryptPrivateKey(string $privateKeyPem, string $pin): string {
        $salt      = random_bytes(16);
        $key       = hash_pbkdf2('sha256', $pin, $salt, 100000, 32, true);
        $iv        = random_bytes(16);
        $encrypted = openssl_encrypt($privateKeyPem, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($encrypted === false) {
            throw new Exception('Gagal enkripsi private key.');
        }

        return base64_encode($salt . $iv . $encrypted);
    }

    /**
     * Dekripsi private key menggunakan PIN
     * @throws Exception Jika PIN salah atau format tidak valid
     */
    public static function decryptPrivateKey(string $encryptedKey, string $pin): string {
        $decoded = base64_decode($encryptedKey);
        if ($decoded === false || strlen($decoded) < 33) {
            throw new Exception('Format kunci tidak valid.');
        }

        $salt       = substr($decoded, 0, 16);
        $iv         = substr($decoded, 16, 16);
        $ciphertext = substr($decoded, 32);

        $key       = hash_pbkdf2('sha256', $pin, $salt, 100000, 32, true);
        $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false || strpos($decrypted, '-----BEGIN') === false) {
            throw new Exception('PIN salah atau kunci rusak. Silakan periksa PIN Anda.');
        }

        return $decrypted;
    }

    /**
     * Hash dokumen menggunakan SHA-256
     * @return string Hash hex 64 karakter
     */
    public static function hashDocument(string $pdfPath): string {
        if (!file_exists($pdfPath)) {
            throw new Exception('File dokumen tidak ditemukan: ' . $pdfPath);
        }
        return hash_file('sha256', $pdfPath);
    }

    /**
     * Buat tanda tangan digital: SHA-256(hash) → RSA_Sign → base64
     */
    public static function signDocument(string $hash, string $privateKeyPem): string {
        $privateKey = openssl_pkey_get_private($privateKeyPem);
        if (!$privateKey) {
            throw new Exception('Private key tidak valid: ' . openssl_error_string());
        }

        $signature = '';
        $result    = openssl_sign($hash, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        if (!$result) {
            throw new Exception('Gagal membuat tanda tangan digital.');
        }

        return base64_encode($signature);
    }

    /**
     * Verifikasi tanda tangan digital
     * @return bool true jika valid
     */
    public static function verifyDocument(string $hash, string $signatureBase64, string $publicKeyPem): bool {
        $publicKey = openssl_pkey_get_public($publicKeyPem);
        if (!$publicKey) {
            return false;
        }

        $signature = base64_decode($signatureBase64);
        $result    = openssl_verify($hash, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        return $result === 1;
    }
}
