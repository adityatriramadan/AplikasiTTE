<?php
class NotifikasiHelper {

    /**
     * Kirim notifikasi ke user
     */
    public static function kirim(int $userId, string $pesan, string $url = ''): void {
        $pdo  = getDB();
        $stmt = $pdo->prepare("INSERT INTO notifikasi (user_id, pesan, url) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $pesan, $url]);
    }

    /**
     * Hitung notifikasi belum dibaca
     */
    public static function countBelumDibaca(int $userId): int {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_dibaca = 0");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
