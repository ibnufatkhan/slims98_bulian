<?php
/**
 * Post enable/disable actions for I Am Not Robot plugin
 */

class Post
{
    public function enable()
    {
        $configPath = SB . 'config/captcha.php';
        $current = [];

        if (is_readable($configPath)) {
            $loaded = include $configPath;
            if (is_array($loaded)) {
                $current = $loaded;
            }
        } elseif (is_readable(SB . 'config/captcha.sample.php')) {
            // Build from sample with placeholders replaced conservatively
            $current = [
                'default' => 'IAmNotRobot',
                'sections' => [
                    'librarian' => ['active' => true],
                    'memberarea' => ['active' => true],
                    'forgot' => ['active' => false],
                ],
                'providers' => [
                    'ReCaptcha' => [
                        'varify_url' => 'https://www.google.com/recaptcha/api/siteverify',
                        'publickey' => \SLiMS\Captcha\Providers\ReCaptcha::PUBKEY,
                        'privatekey' => \SLiMS\Captcha\Providers\ReCaptcha::PRIVKEY,
                        'class' => \SLiMS\Captcha\Providers\ReCaptcha::class,
                    ],
                ],
            ];
        }

        $current['default'] = 'IAmNotRobot';
        $current['sections'] = $current['sections'] ?? [];
        $current['sections']['librarian'] = ['active' => true];
        $current['sections']['memberarea'] = ['active' => true];
        if (!isset($current['sections']['forgot'])) {
            $current['sections']['forgot'] = ['active' => false];
        }

        $current['providers'] = $current['providers'] ?? [];
        $current['providers']['IAmNotRobot'] = [
            'class' => \IAmNotRobot\CaptchaProvider::class,
        ];

        $export = "<?php\nreturn " . var_export($current, true) . ";\n";
        file_put_contents($configPath, $export);

        // Deploy provider shim into core Captcha Providers for settings dropdown discovery
        $shim = SB . 'lib/Captcha/Providers/IAmNotRobot.php';
        if (!is_file($shim)) {
            $code = <<<'PHP'
<?php
/**
 * Shim so System → Captcha Setting lists IAmNotRobot.
 * Real implementation lives in plugins/iam_not_robot.
 */
namespace SLiMS\Captcha\Providers;

use SLiMS\Captcha\Factory;

if (!class_exists('IAmNotRobot\\CaptchaProvider', false)) {
    $helper = dirname(__DIR__, 3) . '/plugins/iam_not_robot/helper.php';
    if (is_readable($helper)) {
        require_once $helper;
    }
}

class IAmNotRobot extends Contract
{
    private $inner;

    public function __construct(Factory $factory)
    {
        if (!class_exists('IAmNotRobot\\CaptchaProvider')) {
            throw new \Exception('I Am Not Robot plugin is not available');
        }
        $this->inner = new \IAmNotRobot\CaptchaProvider($factory);
    }

    public function validate()
    {
        return $this->inner->validate();
    }

    public function generateCaptcha()
    {
        return $this->inner->generateCaptcha();
    }

    public function getError()
    {
        return $this->inner->getError();
    }
}
PHP;
            @file_put_contents($shim, $code);
        }
    }

    public function disable()
    {
        $configPath = SB . 'config/captcha.php';
        if (!is_readable($configPath)) {
            return;
        }
        $current = include $configPath;
        if (!is_array($current)) {
            return;
        }

        if (($current['default'] ?? '') === 'IAmNotRobot') {
            $current['default'] = 'ReCaptcha';
        }
        if (isset($current['sections']['librarian'])) {
            $current['sections']['librarian']['active'] = false;
        }
        if (isset($current['sections']['memberarea'])) {
            $current['sections']['memberarea']['active'] = false;
        }

        $export = "<?php\nreturn " . var_export($current, true) . ";\n";
        file_put_contents($configPath, $export);
    }
}
