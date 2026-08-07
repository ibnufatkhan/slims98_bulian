<?php
/**
 * I Am Not Robot — helpers
 */

if (!defined('INDEX_AUTH')) {
    die('can not access this file directly');
}

require_once __DIR__ . '/src/ChallengeEngine.php';
require_once __DIR__ . '/src/CaptchaProvider.php';

/**
 * Plugin file is only loaded when active.
 */
function iam_not_robot_is_active(): bool
{
    return defined('IAM_NOT_ROBOT_ACTIVE') && IAM_NOT_ROBOT_ACTIVE === true;
}

/**
 * Render widget HTML (+ CSS/JS) for a login section.
 *
 * @param string $section librarian|memberarea
 */
function iam_not_robot_render(string $section = 'memberarea'): string
{
    if (!iam_not_robot_is_active()) {
        return '';
    }

    $engine = new \IAmNotRobot\ChallengeEngine();
    $payload = $engine->createChallenge($section);
    $version = '1.0.0';
    $css = SWB . 'plugins/iam_not_robot/assets/iam_not_robot.css?v=' . $version;
    $js = SWB . 'plugins/iam_not_robot/assets/iam_not_robot.js?v=' . $version;
    $config = [
        'section' => $section,
        'solveUrl' => SWB . 'index.php?p=iam_not_robot',
        'challenge' => $payload['public'],
        'labels' => [
            'title' => __("I'm not a robot"),
            'verify' => __('Verify'),
            'retry' => __('Try again'),
            'success' => __('Verified'),
            'fail' => __('Not quite — try again'),
            'hint' => __('Complete the challenge to continue'),
            'close' => __('Close'),
        ],
    ];
    $json = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json === false) {
        return '';
    }

    $tokenName = htmlspecialchars($payload['token_field'], ENT_QUOTES, 'UTF-8');
    $challengeId = htmlspecialchars($payload['challenge_id'], ENT_QUOTES, 'UTF-8');

    return <<<HTML
<link rel="stylesheet" href="{$css}">
<div class="iamnr-root" data-iamnr-section="{$section}" data-iamnr-id="{$challengeId}">
  <div class="iamnr-box" role="group" aria-label="{$config['labels']['title']}">
    <label class="iamnr-checkrow">
      <input type="checkbox" class="iamnr-checkbox" autocomplete="off">
      <span class="iamnr-spinner" aria-hidden="true"></span>
      <span class="iamnr-checkicon" aria-hidden="true"></span>
      <span class="iamnr-label">{$config['labels']['title']}</span>
    </label>
    <div class="iamnr-brand" aria-hidden="true">
      <span class="iamnr-brand-mark"></span>
      <span class="iamnr-brand-text">SLiMS<br>Humanity</span>
    </div>
  </div>
  <input type="hidden" name="{$tokenName}" class="iamnr-token" value="">
  <input type="hidden" name="iamnr_challenge_id" class="iamnr-challenge-id" value="{$challengeId}">
  <div class="iamnr-modal" hidden>
    <div class="iamnr-modal-backdrop" data-iamnr-close></div>
    <div class="iamnr-modal-panel" role="dialog" aria-modal="true">
      <div class="iamnr-modal-head">
        <strong class="iamnr-modal-title"></strong>
        <button type="button" class="iamnr-modal-close" data-iamnr-close aria-label="{$config['labels']['close']}">×</button>
      </div>
      <p class="iamnr-modal-hint"></p>
      <div class="iamnr-modal-body"></div>
      <div class="iamnr-modal-foot">
        <button type="button" class="iamnr-btn iamnr-btn-secondary" data-iamnr-retry>{$config['labels']['retry']}</button>
        <button type="button" class="iamnr-btn iamnr-btn-primary" data-iamnr-verify>{$config['labels']['verify']}</button>
      </div>
    </div>
  </div>
</div>
<script>window.IAM_NOT_ROBOT = {$json};</script>
<script src="{$js}"></script>
HTML;
}

/**
 * Validate posted challenge answer / token.
 */
function iam_not_robot_validate(): bool
{
    if (!iam_not_robot_is_active()) {
        return true;
    }
    $engine = new \IAmNotRobot\ChallengeEngine();
    return $engine->validateFromRequest();
}

/**
 * Error message from last validation.
 */
function iam_not_robot_error(): string
{
    return \IAmNotRobot\ChallengeEngine::lastError() ?: __("Please complete the I'm not a robot challenge!");
}
