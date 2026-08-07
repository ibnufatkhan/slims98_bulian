<?php
/**
 * SLiMS Captcha provider — I'm Not a Robot
 */

namespace IAmNotRobot;

use SLiMS\Captcha\Factory;
use SLiMS\Captcha\Providers\Contract;

class CaptchaProvider extends Contract
{
    protected $factory;
    protected $error;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function validate()
    {
        if (!function_exists('iam_not_robot_validate')) {
            require_once dirname(__DIR__) . '/helper.php';
        }
        $ok = iam_not_robot_validate();
        if (!$ok) {
            $this->error = iam_not_robot_error();
        }
        return $ok;
    }

    public function generateCaptcha()
    {
        if (!function_exists('iam_not_robot_render')) {
            require_once dirname(__DIR__) . '/helper.php';
        }
        $section = $this->factory->getCaptchaSection() ?: 'memberarea';
        return iam_not_robot_render($section);
    }
}
