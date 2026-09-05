<?php
// Include TCPDF 2D barcode library while suppressing deprecated notices from older TCPDF versions
$oldErr = error_reporting();
error_reporting($oldErr & ~E_DEPRECATED);
require_once BASE_PATH . '/vendor/tcpdf/TCPDF-6.7.7/tcpdf_barcodes_2d.php';
error_reporting($oldErr);

class QrHelper {

    /**
     * Generate QR Code berisi URL verifikasi publik
     * @param string $url URL verifikasi
     * @param string $outputPath Path output file PNG
     */
    public static function generate(string $url, string $outputPath): void {
        // suppress deprecated notices originating from TCPDF internals during barcode generation
        $oldErr = error_reporting();
        error_reporting($oldErr & ~E_DEPRECATED);
        $barcode = new TCPDF2DBarcode($url, 'QRCODE,M');
        $svgData = $barcode->getBarcodeSVGcode(5, 5, 'black');
        error_reporting($oldErr);

        if ($svgData === false || $svgData === '') {
            throw new Exception('Gagal generate QR Code untuk URL: ' . $url);
        }

        $directory = dirname($outputPath);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new Exception('Folder output QR tidak dapat dibuat.');
        }

        if (file_put_contents($outputPath, $svgData, LOCK_EX) === false) {
            throw new Exception('Gagal menyimpan QR Code untuk URL: ' . $url);
        }

        if (!file_exists($outputPath)) {
            throw new Exception('Gagal generate QR Code untuk URL: ' . $url);
        }
    }
}
