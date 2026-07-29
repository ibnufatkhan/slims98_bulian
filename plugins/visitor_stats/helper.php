<?php
/**
 * Made by lovely form Ibnu Fatkhan ibnufatkhan@gmail.com
 *
 * Helper untuk Visitor Stats Footer plugin (pengunjung web OPAC).
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

use SLiMS\DB;

/**
 * Pastikan tabel counter web ada (aman jika migrasi belum jalan).
 */
function visitor_stats_ensure_table(): void
{
    static $ready = false;
    if ($ready) {
        return;
    }

    try {
        $db = DB::getInstance();
        $db->exec(
            "CREATE TABLE IF NOT EXISTS plugin_web_visitor_stats (
                id INT NOT NULL DEFAULT 1,
                total BIGINT NOT NULL DEFAULT 0,
                updated_at DATETIME NULL,
                PRIMARY KEY (id)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $exists = $db->query('SELECT id FROM plugin_web_visitor_stats WHERE id = 1 LIMIT 1');
        if ($exists && !$exists->fetch()) {
            $db->exec("INSERT INTO plugin_web_visitor_stats (id, total, updated_at) VALUES (1, 0, NOW())");
        }
        $ready = true;
    } catch (Throwable $e) {
        // biarkan caller menangani fallback 0
    }
}

/**
 * Catat 1 kunjungan web per sesi browser, lalu kembalikan total sepanjang masa.
 * Bukan pengunjung fisik (visitor_count).
 */
function visitor_stats_record_and_total(): int
{
    static $total = null;
    if ($total !== null) {
        return $total;
    }

    try {
        visitor_stats_ensure_table();
        $db = DB::getInstance();

        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        // Satu sesi = satu pengunjung (hindari naik tiap refresh halaman)
        if (empty($_SESSION['visitor_stats_web_hit'])) {
            $db->exec('UPDATE plugin_web_visitor_stats SET total = total + 1, updated_at = NOW() WHERE id = 1');
            $_SESSION['visitor_stats_web_hit'] = 1;
        }

        $stmt = $db->query('SELECT total FROM plugin_web_visitor_stats WHERE id = 1 LIMIT 1');
        $row = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;
        $total = (int) ($row['total'] ?? 0);
    } catch (Throwable $e) {
        $total = 0;
    }

    return $total;
}
