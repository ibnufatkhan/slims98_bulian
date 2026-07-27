<?php
/**
 * OPAC endpoint: submit star rating (AJAX JSON)
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require_once __DIR__ . '/../helper.php';

use SLiMS\DB;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    star_rating_json_response(['success' => false, 'message' => 'Metode tidak diizinkan.'], 405);
}

if (class_exists('\\Volnix\\CSRF\\CSRF') && !\Volnix\CSRF\CSRF::validate($_POST)) {
    star_rating_json_response(['success' => false, 'message' => 'Token keamanan tidak valid. Muat ulang halaman.'], 403);
}

$name = trim((string) (utility::filterData('reviewer_name', 'post', false, true, true) ?? ''));
$comment = trim((string) (utility::filterData('comment', 'post', false, true, true) ?? ''));
$rating = (int) ($_POST['rating'] ?? 0);

if ($name === '' || mb_strlen($name) > 100) {
    star_rating_json_response(['success' => false, 'message' => 'Nama wajib diisi (maks. 100 karakter).'], 422);
}

if ($comment === '' || mb_strlen($comment) > 1000) {
    star_rating_json_response(['success' => false, 'message' => 'Komentar wajib diisi (maks. 1000 karakter).'], 422);
}

if ($rating < 1 || $rating > 5) {
    star_rating_json_response(['success' => false, 'message' => 'Rating harus antara 1 sampai 5.'], 422);
}

$ip = $_SERVER['REMOTE_ADDR'] ?? null;

try {
    $stmt = DB::getInstance()->prepare(
        'INSERT INTO plugin_star_rating (reviewer_name, comment, rating, is_hidden, ip_address, created_at)
         VALUES (:name, :comment, :rating, 0, :ip, :created_at)'
    );
    $stmt->execute([
        'name' => $name,
        'comment' => $comment,
        'rating' => $rating,
        'ip' => $ip,
        'created_at' => date('Y-m-d H:i:s'),
    ]);
} catch (Throwable $e) {
    star_rating_json_response([
        'success' => false,
        'message' => 'Gagal menyimpan ulasan. Pastikan plugin sudah diaktifkan.',
    ], 500);
}

star_rating_json_response([
    'success' => true,
    'message' => 'Ulasan berhasil dikirim. Terima kasih!',
]);
