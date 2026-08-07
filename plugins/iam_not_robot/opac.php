<?php
/**
 * AJAX endpoint: solve / refresh I'm Not a Robot challenges
 * URL: index.php?p=iam_not_robot
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

if (!function_exists('iam_not_robot_is_active') || !iam_not_robot_is_active()) {
    echo json_encode(['ok' => false, 'message' => __('Plugin inactive')]);
    exit;
}

$engine = new \IAmNotRobot\ChallengeEngine();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'solve') {
    $id = trim((string) ($_POST['challenge_id'] ?? ''));
    $answer = $_POST['answer'] ?? '';
    echo json_encode($engine->solve($id, $answer), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($action === 'refresh') {
    $old = trim((string) ($_POST['challenge_id'] ?? ''));
    $section = trim((string) ($_POST['section'] ?? 'memberarea'));
    if (!in_array($section, ['librarian', 'memberarea', 'forgot'], true)) {
        $section = 'memberarea';
    }
    $payload = $engine->refresh($old, $section);
    echo json_encode([
        'ok' => true,
        'challenge' => $payload['public'],
        'challenge_id' => $payload['challenge_id'],
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

echo json_encode(['ok' => false, 'message' => __('Unknown action')]);
exit;
