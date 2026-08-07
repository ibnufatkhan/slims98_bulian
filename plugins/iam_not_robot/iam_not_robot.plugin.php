<?php
/**
 * Made by lovelygirl Ibnu Fatkhan ibnufatkhan@gmail.com
 *
 * Plugin Name: I Am Not Robot
 * Plugin URI: https://github.com/ibnufatkhan/iam-not-robot-slims
 * Description: Verifikasi "I'm not a robot" bergaya neal.fun untuk login admin (librarian) dan login member. Tantangan interaktif dengan validasi server-side.
 * Version: 1.0.0
 * Author: Ibnu Fatkhan
 * Author URI: https://github.com/ibnufatkhan
 */

use SLiMS\Plugins;
use SLiMS\Captcha\Factory;

if (!defined('IAM_NOT_ROBOT_ACTIVE')) {
    define('IAM_NOT_ROBOT_ACTIVE', true);
}

require_once __DIR__ . '/helper.php';

$plugin = Plugins::getInstance();

// Admin settings page
$plugin->registerMenu('system', __("I'm Not a Robot"), __DIR__ . '/settings.php');

// OPAC AJAX endpoint for solve / refresh
// → index.php?p=iam_not_robot
$plugin->registerMenu('opac', 'iam not robot', __DIR__ . '/opac.php');

// Register captcha provider so System → Captcha Setting can use it
try {
    Factory::getInstance()->registerProvider('IAmNotRobot', \IAmNotRobot\CaptchaProvider::class);
} catch (\Throwable $e) {
    // Factory may not be ready in some CLI contexts
}

/**
 * Ensure captcha config knows about our provider while plugin is active.
 * Does not overwrite an existing non-empty captcha.php unless provider missing.
 */
$plugin->register(Plugins::SYSCONFIG_ALL_INIT, function () {
    iam_not_robot_ensure_provider_config();
});

function iam_not_robot_ensure_provider_config(): void
{
    $configPath = SB . 'config/captcha.php';
    $current = is_readable($configPath) ? include $configPath : null;
    if (!is_array($current)) {
        return;
    }

    $providers = $current['providers'] ?? [];
    if (isset($providers['IAmNotRobot']['class'])) {
        return;
    }

    $providers['IAmNotRobot'] = [
        'class' => \IAmNotRobot\CaptchaProvider::class,
    ];
    $current['providers'] = $providers;

    $export = "<?php\nreturn " . var_export($current, true) . ";\n";
    @file_put_contents($configPath, $export);
}
