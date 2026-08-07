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
