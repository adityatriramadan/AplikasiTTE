<?php

require_once dirname(__DIR__) . '/tcpdf/TCPDF-6.7.7/tcpdf_barcodes_2d.php';

if (!defined('QR_ECLEVEL_L')) {
    define('QR_ECLEVEL_L', 'L');
}

if (!defined('QR_ECLEVEL_M')) {
    define('QR_ECLEVEL_M', 'M');
}

if (!defined('QR_ECLEVEL_Q')) {
    define('QR_ECLEVEL_Q', 'Q');
}

if (!defined('QR_ECLEVEL_H')) {
    define('QR_ECLEVEL_H', 'H');
}

class QRcode {

    public static function png(string $text, mixed $outfile = false, $level = QR_ECLEVEL_M, int $size = 3, int $margin = 0): void {
        $type = 'QRCODE,' . strtoupper((string)$level);
        $barcode = new TCPDF2DBarcode($text, $type);
        $pngData = $barcode->getBarcodePngData($size, $size, [0, 0, 0]);

        if ($pngData === false) {
            throw new Exception('GD atau Imagick tidak tersedia untuk generate QR Code.');
        }

        if ($outfile === false || $outfile === null || $outfile === '') {
            header('Content-Type: image/png');
            echo $pngData;
            return;
        }

        $directory = dirname($outfile);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new Exception('Folder output QR tidak dapat dibuat.');
        }

        if (file_put_contents($outfile, $pngData, LOCK_EX) === false) {
            throw new Exception('Gagal menyimpan file QR Code.');
        }
    }
}