<?php
/**
 * Challenge engine inspired by neal.fun/not-a-robot
 * Lightweight login-friendly challenges with server-side validation.
 */

namespace IAmNotRobot;

class ChallengeEngine
{
    public const SESSION_KEY = 'iam_not_robot_challenges';
    public const TOKEN_FIELD = 'iamnr_token';
    public const TTL = 300;

    private static string $lastError = '';

    public static function lastError(): string
    {
        return self::$lastError;
    }

    /**
     * Create a random challenge and store expected answer in session.
     */
    public function createChallenge(string $section): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $type = $this->pickType();
        $built = $this->build($type);
        $challengeId = bin2hex(random_bytes(16));

        $_SESSION[self::SESSION_KEY][$challengeId] = [
            'section' => $section,
            'type' => $type,
            'answer' => $built['answer'],
            'token' => hash_hmac('sha256', $challengeId . '|' . $section, $this->secret()),
            'expires' => time() + self::TTL,
            'solved' => false,
        ];

        return [
            'challenge_id' => $challengeId,
            'token_field' => self::TOKEN_FIELD,
            'public' => [
                'id' => $challengeId,
                'type' => $type,
                'title' => $built['title'],
                'hint' => $built['hint'],
                'data' => $built['public'],
            ],
        ];
    }

    /**
     * Validate POST payload.
     */
    public function validateFromRequest(): bool
    {
        self::$lastError = '';

        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $challengeId = trim((string) ($_POST['iamnr_challenge_id'] ?? ''));
        $token = trim((string) ($_POST[self::TOKEN_FIELD] ?? ''));
        $answerRaw = $_POST['iamnr_answer'] ?? null;

        if ($challengeId === '' || $token === '') {
            self::$lastError = __("Please complete the I'm not a robot challenge!");
            return false;
        }

        $store = $_SESSION[self::SESSION_KEY][$challengeId] ?? null;
        if (!is_array($store)) {
            self::$lastError = __('Challenge expired. Please reload and try again.');
            return false;
        }

        if (($store['expires'] ?? 0) < time()) {
            unset($_SESSION[self::SESSION_KEY][$challengeId]);
            self::$lastError = __('Challenge expired. Please reload and try again.');
            return false;
        }

        $expectedToken = (string) ($store['token'] ?? '');
        if (!hash_equals($expectedToken, $token) || empty($store['solved'])) {
            // Allow solving in the same request via answer payload (no JS token race).
            if (!$this->checkAnswer($store, $answerRaw)) {
                self::$lastError = __("I'm not a robot verification failed. Please try again.");
                return false;
            }
            $_SESSION[self::SESSION_KEY][$challengeId]['solved'] = true;
        }

        // one-time use
        unset($_SESSION[self::SESSION_KEY][$challengeId]);
        return true;
    }

    /**
     * AJAX endpoint helper: mark challenge solved after answer check.
     */
    public function solve(string $challengeId, $answerRaw): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }

        $store = $_SESSION[self::SESSION_KEY][$challengeId] ?? null;
        if (!is_array($store)) {
            return ['ok' => false, 'message' => __('Challenge expired. Please reload and try again.')];
        }
        if (($store['expires'] ?? 0) < time()) {
            unset($_SESSION[self::SESSION_KEY][$challengeId]);
            return ['ok' => false, 'message' => __('Challenge expired. Please reload and try again.')];
        }
        if (!$this->checkAnswer($store, $answerRaw)) {
            return ['ok' => false, 'message' => __('Not quite — try again')];
        }

        $_SESSION[self::SESSION_KEY][$challengeId]['solved'] = true;
        return [
            'ok' => true,
            'token' => $store['token'],
            'message' => __('Verified'),
        ];
    }

    /**
     * Issue a fresh challenge (for retry) keeping same session map.
     */
    public function refresh(string $oldId, string $section): array
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            @session_start();
        }
        if ($oldId !== '' && isset($_SESSION[self::SESSION_KEY][$oldId])) {
            unset($_SESSION[self::SESSION_KEY][$oldId]);
        }
        return $this->createChallenge($section);
    }

    private function pickType(): string
    {
        $types = [
            'stopsign',
            'vegetables',
            'wiggle',
            'affirmation',
            'tictactoe',
            'whack',
            'math_order',
            'traffic_reverse',
        ];
        return $types[array_rand($types)];
    }

    private function build(string $type): array
    {
        switch ($type) {
            case 'stopsign':
                return $this->buildGridSelect(
                    __("Select all squares with a STOP sign"),
                    __('Tap every tile that shows a stop sign, then Verify.'),
                    ['🛑', '🚗', '🚲', '🌳', '🚦', '🏢', '🐕', '☁️'],
                    ['🛑'],
                    9
                );
            case 'vegetables':
                return $this->buildGridSelect(
                    __('Select all vegetables'),
                    __('Ignore fruits. Choose vegetables only.'),
                    ['🥕', '🧅', '🌽', '🍆', '🍎', '🍌', '🍇', '🍓', '🥦', '🥔'],
                    ['🥕', '🧅', '🌽', '🍆', '🥦', '🥔'],
                    9
                );
            case 'traffic_reverse':
                return $this->buildGridSelect(
                    __('Select all squares WITHOUT a traffic light'),
                    __('This is a reverse challenge — avoid the traffic lights.'),
                    ['🚦', '🛑', '🚗', '🚙', '🚕', '🌳', '🏢', '☁️'],
                    ['🛑', '🚗', '🚙', '🚕', '🌳', '🏢', '☁️'],
                    9,
                    true
                );
            case 'wiggle':
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $code = '';
                for ($i = 0; $i < 5; $i++) {
                    $code .= $chars[random_int(0, strlen($chars) - 1)];
                }
                return [
                    'title' => __('Type the wiggling characters'),
                    'hint' => __('Enter the capital letters/numbers exactly as shown.'),
                    'answer' => strtoupper($code),
                    'public' => ['code' => $code],
                ];
            case 'affirmation':
                $options = [
                    __("I'm a toaster"),
                    __("Beep boop"),
                    __("I'm Not a Robot"),
                    __("01001000"),
                    __('Definitely silicon'),
                ];
                shuffle($options);
                return [
                    'title' => __('Pick the correct affirmation'),
                    'hint' => __('Select only: I\'m Not a Robot'),
                    'answer' => __("I'm Not a Robot"),
                    'public' => ['options' => array_values($options)],
                ];
            case 'tictactoe':
                // Pre-filled board where human (X) can win in one move.
                // Board indexes 0-8. O occupies some, X occupies some, one winning cell.
                $winLines = [
                    [0, 1, 2], [3, 4, 5], [6, 7, 8],
                    [0, 3, 6], [1, 4, 7], [2, 5, 8],
                    [0, 4, 8], [2, 4, 6],
                ];
                $line = $winLines[array_rand($winLines)];
                $board = array_fill(0, 9, '');
                $board[$line[0]] = 'X';
                $board[$line[1]] = 'X';
                $winning = $line[2];
                // place O elsewhere
                $empties = array_values(array_filter(range(0, 8), fn($i) => $board[$i] === '' && $i !== $winning));
                shuffle($empties);
                if (isset($empties[0])) {
                    $board[$empties[0]] = 'O';
                }
                if (isset($empties[1])) {
                    $board[$empties[1]] = 'O';
                }
                return [
                    'title' => __('Win at tic-tac-toe'),
                    'hint' => __('You are X. Take the winning move.'),
                    'answer' => (string) $winning,
                    'public' => ['board' => $board, 'mark' => 'X'],
                ];
            case 'whack':
                $need = 5;
                return [
                    'title' => __('Whack-a-mole'),
                    'hint' => sprintf(__('Hit the mole %d times.'), $need),
                    'answer' => (string) $need,
                    'public' => ['need' => $need],
                ];
            case 'math_order':
                $nums = [];
                while (count($nums) < 4) {
                    $n = random_int(1, 20);
                    if (!in_array($n, $nums, true)) {
                        $nums[] = $n;
                    }
                }
                $sorted = $nums;
                sort($sorted);
                return [
                    'title' => __('Click numbers from least to greatest'),
                    'hint' => __('Tap each value in ascending order.'),
                    'answer' => implode(',', $sorted),
                    'public' => ['numbers' => $nums],
                ];
            default:
                return $this->build('stopsign');
        }
    }

    private function buildGridSelect(
        string $title,
        string $hint,
        array $pool,
        array $targets,
        int $size = 9,
        bool $reverseMode = false
    ): array {
        $tiles = [];
        $answer = [];
        for ($i = 0; $i < $size; $i++) {
            // ensure at least 3 targets when possible
            if ($i < 3 && !$reverseMode) {
                $item = $targets[array_rand($targets)];
            } elseif ($reverseMode && $i < 3) {
                // force some traffic lights for reverse
                $item = '🚦';
            } else {
                $item = $pool[array_rand($pool)];
            }
            $tiles[] = $item;
            $isTarget = in_array($item, $targets, true);
            if ($reverseMode) {
                // reverseMode targets already exclude traffic light
                if ($isTarget) {
                    $answer[] = $i;
                }
            } else {
                if ($isTarget) {
                    $answer[] = $i;
                }
            }
        }

        // Guarantee non-empty answer
        if ($answer === []) {
            $tiles[0] = $targets[0];
            $answer[] = 0;
        }

        sort($answer);
        return [
            'title' => $title,
            'hint' => $hint,
            'answer' => implode(',', $answer),
            'public' => ['tiles' => $tiles],
        ];
    }

    private function checkAnswer(array $store, $answerRaw): bool
    {
        $expected = (string) ($store['answer'] ?? '');
        $type = (string) ($store['type'] ?? '');

        if (is_array($answerRaw)) {
            $answerRaw = implode(',', $answerRaw);
        }
        $given = trim((string) $answerRaw);

        if ($type === 'wiggle' || $type === 'affirmation') {
            return hash_equals(mb_strtoupper($expected), mb_strtoupper($given));
        }

        if (in_array($type, ['math_order', 'stopsign', 'vegetables', 'traffic_reverse'], true)) {
            $normalize = static function (string $csv): array {
                $parts = array_filter(explode(',', $csv), static fn($v) => $v !== '');
                return array_map(static fn($v) => (string) (int) $v, array_values($parts));
            };
            $expParts = $normalize($expected);
            $gotParts = $normalize($given);
            if ($type === 'math_order') {
                return $expParts === $gotParts;
            }
            sort($expParts);
            sort($gotParts);
            return $expParts === $gotParts;
        }

        return hash_equals($expected, $given);
    }

    private function secret(): string
    {
        $seed = defined('SENAYAN_VERSION') ? SENAYAN_VERSION : 'slims';
        if (defined('DB_NAME')) {
            $seed .= DB_NAME;
        }
        return hash('sha256', $seed . '|iam-not-robot');
    }
}
