<?php
/**
 * Star Rating plugin helpers
 */

use SLiMS\DB;
use SLiMS\Json;

if (!defined('INDEX_AUTH')) {
    die('can not access this file directly');
}

if (!function_exists('star_rating_asset')) {
    /**
     * Build public URL for plugin assets.
     */
    function star_rating_asset(string $file): string
    {
        return SWB . 'plugins/star_rating/assets/' . ltrim($file, '/');
    }
}

if (!function_exists('star_rating_is_home')) {
    /**
     * Detect OPAC landing page.
     */
    function star_rating_is_home(): bool
    {
        return !(isset($_GET['search']) || isset($_GET['title']) || isset($_GET['keywords']) || isset($_GET['p']));
    }
}

if (!function_exists('star_rating_stars_html')) {
    /**
     * Render filled/empty star icons for a rating value.
     */
    function star_rating_stars_html(float $rating, string $size = 'md'): string
    {
        $full = (int) floor($rating);
        $half = ($rating - $full) >= 0.25 && ($rating - $full) < 0.75;
        if (($rating - $full) >= 0.75) {
            $full++;
            $half = false;
        }

        $html = '<span class="sr-stars sr-stars-' . htmlspecialchars($size) . '" aria-label="' . htmlspecialchars(number_format($rating, 1)) . ' dari 5 bintang">';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $full) {
                $html .= '<i class="fa fa-star"></i>';
            } elseif ($half && $i === $full + 1) {
                $html .= '<i class="fa fa-star-half-o"></i>';
            } else {
                $html .= '<i class="fa fa-star-o"></i>';
            }
        }
        $html .= '</span>';

        return $html;
    }
}

if (!function_exists('star_rating_get_summary')) {
    /**
     * Get rating summary statistics (visible reviews only).
     */
    function star_rating_get_summary(): array
    {
        $db = DB::getInstance();
        $summary = [
            'total' => 0,
            'average' => 0.0,
            'breakdown' => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
        ];

        try {
            $stmt = $db->query('SELECT rating, COUNT(*) AS total FROM plugin_star_rating WHERE is_hidden = 0 GROUP BY rating');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rating = (int) $row['rating'];
                $total = (int) $row['total'];
                if (isset($summary['breakdown'][$rating])) {
                    $summary['breakdown'][$rating] = $total;
                    $summary['total'] += $total;
                }
            }

            if ($summary['total'] > 0) {
                $weighted = 0;
                foreach ($summary['breakdown'] as $star => $count) {
                    $weighted += $star * $count;
                }
                $summary['average'] = round($weighted / $summary['total'], 1);
            }
        } catch (Throwable $e) {
            // Table may not exist yet if plugin was not migrated.
        }

        return $summary;
    }
}

if (!function_exists('star_rating_get_reviews')) {
    /**
     * Get visible reviews for public display.
     */
    function star_rating_get_reviews(int $limit = 20): array
    {
        $db = DB::getInstance();
        $reviews = [];

        try {
            $stmt = $db->prepare('SELECT id, reviewer_name, comment, rating, created_at FROM plugin_star_rating WHERE is_hidden = 0 ORDER BY created_at DESC LIMIT :limit');
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {
            // ignore when table is missing
        }

        return $reviews;
    }
}

if (!function_exists('star_rating_relative_time')) {
    /**
     * Human-readable relative time in Indonesian.
     */
    function star_rating_relative_time(string $datetime): string
    {
        $timestamp = strtotime($datetime);
        if (!$timestamp) {
            return $datetime;
        }

        $diff = time() - $timestamp;
        if ($diff < 60) {
            return 'baru saja';
        }
        if ($diff < 3600) {
            $m = (int) floor($diff / 60);
            return $m . ' menit yang lalu';
        }
        if ($diff < 86400) {
            $h = (int) floor($diff / 3600);
            return $h . ' jam yang lalu';
        }
        if ($diff < 2592000) {
            $d = (int) floor($diff / 86400);
            return $d . ' hari yang lalu';
        }
        if ($diff < 31536000) {
            $mo = (int) floor($diff / 2592000);
            return $mo . ' bulan yang lalu';
        }
        $y = (int) floor($diff / 31536000);
        return $y . ' tahun yang lalu';
    }
}

if (!function_exists('star_rating_render_footer')) {
    /**
     * Render rating widget for OPAC footer / landing page.
     */
    function star_rating_render_footer(): void
    {
        if (!star_rating_is_home()) {
            return;
        }

        $summary = star_rating_get_summary();
        $reviews = star_rating_get_reviews(15);
        $csrf = class_exists('\\Volnix\\CSRF\\CSRF') ? \Volnix\CSRF\CSRF::getHiddenInputString() : '';
        $submitUrl = SWB . 'index.php?p=star_rating_submit';

        echo '<link rel="stylesheet" href="' . htmlspecialchars(star_rating_asset('star_rating.css')) . '">';
        include __DIR__ . '/views/footer_widget.php';
        echo '<script src="' . htmlspecialchars(star_rating_asset('star_rating.js')) . '"></script>';
    }
}

if (!function_exists('star_rating_json_response')) {
    /**
     * Send JSON response and exit.
     */
    function star_rating_json_response(array $payload, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo Json::stringify($payload);
        exit;
    }
}
