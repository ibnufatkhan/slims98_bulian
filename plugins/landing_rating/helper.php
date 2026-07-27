<?php
/**
 * Shared helpers for Landing Page Rating plugin
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

use SLiMS\DB;

/**
 * Fetch visible ratings for OPAC display.
 *
 * @param int $limit
 * @return array
 */
function landing_rating_get_visible(int $limit = 20): array
{
    $stmt = DB::getInstance()->prepare(
        'SELECT id, visitor_name, comment, rating, created_at
         FROM landing_rating
         WHERE is_hidden = 0
         ORDER BY created_at DESC
         LIMIT :limit'
    );
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
}

/**
 * Aggregate stats for visible ratings.
 *
 * @return array{total:int,average:float,distribution:array}
 */
function landing_rating_get_stats(): array
{
    $db = DB::getInstance();
    $summary = $db->query(
        'SELECT COUNT(*) AS total, AVG(rating) AS average
         FROM landing_rating
         WHERE is_hidden = 0'
    )->fetch(PDO::FETCH_ASSOC);

    $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    $rows = $db->query(
        'SELECT rating, COUNT(*) AS total
         FROM landing_rating
         WHERE is_hidden = 0
         GROUP BY rating'
    )->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
        $star = (int) $row['rating'];
        if (isset($distribution[$star])) {
            $distribution[$star] = (int) $row['total'];
        }
    }

    return [
        'total' => (int) ($summary['total'] ?? 0),
        'average' => round((float) ($summary['average'] ?? 0), 1),
        'distribution' => $distribution,
    ];
}

/**
 * Render star icons HTML.
 *
 * @param float|int $rating
 * @return string
 */
function landing_rating_stars_html($rating): string
{
    $rating = (float) $rating;
    $html = '<span class="lr-stars" aria-label="' . htmlspecialchars(number_format($rating, 1)) . ' / 5">';
    for ($i = 1; $i <= 5; $i++) {
        if ($rating >= $i) {
            $html .= '<span class="lr-star lr-star-full">&#9733;</span>';
        } elseif ($rating >= ($i - 0.5)) {
            $html .= '<span class="lr-star lr-star-half">&#9733;</span>';
        } else {
            $html .= '<span class="lr-star lr-star-empty">&#9733;</span>';
        }
    }
    $html .= '</span>';

    return $html;
}

/**
 * Escape text for HTML output.
 *
 * @param string|null $value
 * @return string
 */
function landing_rating_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
