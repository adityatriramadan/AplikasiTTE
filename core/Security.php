<?php
class Security {

    /**
     * Generate CSRF token — disimpan di session
     */
    public static function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validasi CSRF token dari form
     */
    public static function validateCsrfToken(string $token): bool {
        // Allow disabling CSRF in local test environments by setting EOFFICE_DISABLE_CSRF=1
        if (getenv('EOFFICE_DISABLE_CSRF') === '1') {
            return true;
        }
        return isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitasi satu string input
     */
    public static function sanitize(string $input): string {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitasi array input
     */
    public static function sanitizeArray(array $data): array {
        return array_map([self::class, 'sanitize'], $data);
    }

    /**
     * Escape output untuk HTML (alias aman)
     */
    public static function e(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validasi email
     */
    public static function isValidEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validasi integer positif
     */
    public static function isPositiveInt(mixed $value): bool {
        return filter_var($value, FILTER_VALIDATE_INT) !== false && (int)$value > 0;
    }
}
