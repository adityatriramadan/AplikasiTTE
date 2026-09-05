<?php
class NomorSuratHelper {

    /**
     * Generate nomor surat otomatis & atomic
     * Format: 012/ST-TI.UNPAM/VI/2026
     */
    public static function generate(string $kodeJenis): string {
        $pdo   = getDB();
        $tahun = (int)date('Y');

        // Atomic increment — mencegah race condition
        $stmt = $pdo->prepare(
            "INSERT INTO nomor_surat_counter (kode_jenis, tahun, counter)
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE counter = counter + 1"
        );
        $stmt->execute([$kodeJenis, $tahun]);

        // Ambil nilai counter yang baru
        $stmt = $pdo->prepare(
            "SELECT counter FROM nomor_surat_counter WHERE kode_jenis = ? AND tahun = ?"
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
