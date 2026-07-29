<?php
/**
 * Made by lovely form Ibnu Fatkhan ibnufatkhan@gmail.com
 *
 * Helper untuk Visitor Stats Footer plugin.
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

use SLiMS\DB;

/**
 * Hitung jumlah pengunjung dari tabel visitor_count
 * untuk N bulan terakhir (default 12).
 */
function visitor_stats_count_last_months(int $months = 12): int
{
    static $cache = [];
    $months = max(1, min(120, $months));
    if (isset($cache[$months])) {
        return $cache[$months];
    }

    try {
        $db = DB::getInstance();
        $months = (int) $months;
        $stmt = $db->query(
            "SELECT COUNT(*) AS total
             FROM visitor_count
             WHERE checkin_date >= DATE_SUB(NOW(), INTERVAL {$months} MONTH)"
        );
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $cache[$months] = (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        // Tabel belum ada / DB error — tampilkan 0 agar footer tetap aman
        $cache[$months] = 0;
    }

    return $cache[$months];
}
