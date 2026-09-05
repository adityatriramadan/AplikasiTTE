<?php
require_once BASE_PATH . '/vendor/tcpdf/tcpdf.php';

class PdfHelper {

    /**
     * Render PDF surat TANPA QR Code
     * Digunakan untuk menghitung hash SHA-256 (sebelum QR Code ditempel)
     */
    public function renderSurat(array $surat, string $outputPath): void {
        $pdf = $this->createPdf($surat['perihal']);
        $konten = $this->renderTemplate($surat);
        $pdf->writeHTML($konten, true, false, true, false, '');
        $pdf->Output($outputPath, 'F');
    }

    /**
     * Render PDF surat final DENGAN QR Code di pojok kanan bawah
     * QR ditempel SETELAH hash dihitung — tidak mempengaruhi hash
     */
    public function renderSuratDenganQr(array $surat, string $qrPath, string $outputPath): void {
        $pdf = $this->createPdf($surat['perihal']);
        $konten = $this->renderTemplate($surat);
        $pdf->writeHTML($konten, true, false, true, false, '');

        // Tempel QR Code pojok kanan bawah
        if (file_exists($qrPath)) {
            $pdf->Image($qrPath, 170, 265, 30, 30, 'PNG');
            $pdf->SetXY(155, 258);
            $pdf->SetFont('dejavusans', '', 6);
            $pdf->Cell(55, 5, 'Scan untuk verifikasi keaslian dokumen', 0, 0, 'C');
        }

        $pdf->Output($outputPath, 'F');
    }

    private function createPdf(string $title): TCPDF {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
        $pdf->SetCreator(APP_NAME);
        $pdf->SetAuthor('Sistem E-Office Prodi TI UNPAM');
        $pdf->SetTitle($title);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();
        $pdf->SetFont('dejavusans', '', 11);
        $pdf->SetMargins(20, 15, 20);
        return $pdf;
    }

    /**
     * Render konten HTML dari template + data dinamis surat
     */
    public function renderTemplate(array $surat): string {
        $isiData  = is_string($surat['isi_data']) ? (json_decode($surat['isi_data'], true) ?? []) : $surat['isi_data'];
        $template = $surat['konten_template'];

        // Ganti placeholder {{variable}} dengan data aktual
        foreach ($isiData as $key => $value) {
            $template = str_replace(
                '{{' . $key . '}}',
                htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'),
                $template
            );
        }

        // Ganti placeholder sistem
        $template = str_replace('{{nomor_surat}}', htmlspecialchars($surat['nomor_surat'], ENT_QUOTES, 'UTF-8'), $template);
        $template = str_replace('{{tanggal_surat}}', date('d F Y', strtotime($surat['tanggal_surat'])), $template);
        $template = str_replace('{{perihal}}', htmlspecialchars($surat['perihal'], ENT_QUOTES, 'UTF-8'), $template);

        return $template;
    }
}
