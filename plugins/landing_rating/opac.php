<?php
/**
 * OPAC endpoint for Landing Page Rating
 * URL: index.php?p=landing_rating
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require __DIR__ . '/helper.php';

use SLiMS\DB;
use SLiMS\Json;

// Pastikan tidak ada HTML residual dari OPAC buffer
while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

try {
    if ($action === 'token') {
        exit(Json::stringify([
            'status' => true,
            'token' => landing_rating_token(),
        ])->withHeader());
    }

    if ($action === 'list') {
        exit(Json::stringify([
            'status' => true,
            'token' => landing_rating_token(),
            'stats' => landing_rating_get_stats(),
            'items' => landing_rating_get_visible(30),
        ])->withHeader());
    }

    if ($action === 'submit') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new RuntimeException(__('Metode tidak diizinkan'));
        }

        if (!landing_rating_token_valid($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            exit(Json::stringify([
                'status' => false,
                'code' => 'invalid_token',
                'message' => __('Token keamanan tidak valid. Muat ulang halaman.'),
                'token' => landing_rating_token(),
            ])->withHeader());
        }

        $name = trim((string) utility::filterData('visitor_name', 'post', true, true, true));
        $comment = trim((string) utility::filterData('comment', 'post', true, true, true));
        $rating = (int) utility::filterData('rating', 'post', true, true, true);

        if ($name === '' || mb_strlen($name) < 2) {
            throw new RuntimeException(__('Nama minimal 2 karakter.'));
        }
        if (mb_strlen($name) > 100) {
            throw new RuntimeException(__('Nama maksimal 100 karakter.'));
        }
        if ($comment === '' || mb_strlen($comment) < 3) {
            throw new RuntimeException(__('Komentar minimal 3 karakter.'));
        }
        if (mb_strlen($comment) > 1000) {
            throw new RuntimeException(__('Komentar maksimal 1000 karakter.'));
        }
        if ($rating < 1 || $rating > 5) {
            throw new RuntimeException(__('Rating harus antara 1 sampai 5.'));
        }

        // Basic flood control: max 3 submissions / IP / hour
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $flood = DB::getInstance()->prepare(
            'SELECT COUNT(*) FROM landing_rating
             WHERE ip_address = :ip AND created_at >= (NOW() - INTERVAL 1 HOUR)'
        );
        $flood->execute(['ip' => $ip]);
        if ((int) $flood->fetchColumn() >= 3) {
            throw new RuntimeException(__('Anda sudah mengirim terlalu banyak ulasan. Coba lagi nanti.'));
        }

        $now = date('Y-m-d H:i:s');
        $stmt = DB::getInstance()->prepare(
            'INSERT INTO landing_rating
                (visitor_name, comment, rating, is_hidden, ip_address, created_at, updated_at)
             VALUES
                (:visitor_name, :comment, :rating, 0, :ip_address, :created_at, :updated_at)'
        );
        $stmt->execute([
            'visitor_name' => $name,
            'comment' => $comment,
            'rating' => $rating,
            'ip_address' => $ip,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        exit(Json::stringify([
            'status' => true,
            'message' => __('Terima kasih! Ulasan Anda berhasil dikirim.'),
            'token' => landing_rating_token(),
            'stats' => landing_rating_get_stats(),
            'items' => landing_rating_get_visible(30),
        ])->withHeader());
    }

    throw new RuntimeException(__('Aksi tidak dikenal'));
} catch (Throwable $e) {
    http_response_code(400);
    exit(Json::stringify([
        'status' => false,
        'message' => $e->getMessage(),
        'token' => landing_rating_token(),
    ])->withHeader());
}
