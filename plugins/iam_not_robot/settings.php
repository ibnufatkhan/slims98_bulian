<?php
/**
 * Admin settings — I'm Not a Robot
 */

use SLiMS\Captcha\Factory;

define('INDEX_AUTH', '1');

require '../../../sysconfig.inc.php';
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';

$can_read = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');
if (!($can_read && $can_write)) {
    die('<div class="errorBox">' . __("You don't have enough privileges to view this section") . '</div>');
}

if (isset($_POST['saveData'])) {
    $configPath = SB . 'config/captcha.php';
    $current = is_readable($configPath) ? include $configPath : [];
    if (!is_array($current)) {
        $current = [];
    }

    $enableLibrarian = isset($_POST['enable_librarian']);
    $enableMember = isset($_POST['enable_memberarea']);
    $useAsDefault = isset($_POST['use_as_default']);

    $current['sections'] = $current['sections'] ?? [];
    $current['sections']['librarian'] = ['active' => $enableLibrarian];
    $current['sections']['memberarea'] = ['active' => $enableMember];
    if (!isset($current['sections']['forgot'])) {
        $current['sections']['forgot'] = ['active' => false];
    }

    $current['providers'] = $current['providers'] ?? [];
    $current['providers']['IAmNotRobot'] = [
        'class' => \IAmNotRobot\CaptchaProvider::class,
    ];

    if ($useAsDefault) {
        $current['default'] = 'IAmNotRobot';
    }

    // Keep ReCaptcha keys if present in sample merge
    if (!isset($current['providers']['ReCaptcha'])) {
        $sample = include SB . 'config/captcha.sample.php';
        if (is_array($sample) && isset($sample['providers']['ReCaptcha'])) {
            $current['providers']['ReCaptcha'] = $sample['providers']['ReCaptcha'];
        }
    }

    $export = "<?php\nreturn " . var_export($current, true) . ";\n";
    // Fix class constants in var_export (exports as \ClassName::class string sometimes wrong)
    $export = str_replace(
        "'class' => 'IAmNotRobot\\\\CaptchaProvider'",
        "'class' => \\IAmNotRobot\\CaptchaProvider::class",
        $export
    );
    // var_export of ::class already gives full class name string — Factory accepts string class name
    file_put_contents($configPath, $export);

    // Force provider name for current process
    try {
        Factory::getInstance()->setProvider('IAmNotRobot');
        Factory::getInstance()->registerProvider('IAmNotRobot', \IAmNotRobot\CaptchaProvider::class);
    } catch (\Throwable $e) {
    }

    toastr(__('Settings saved. I Am Not Robot is ready for login forms.'))->success();
    exit;
}

$sections = config('captcha.sections') ?? [];
$isDefault = (config('captcha.default') === 'IAmNotRobot');
?>
<div class="menuBox">
  <div class="menuBoxInner systemIcon">
    <div class="per_title">
      <h2><?= __("I'm Not a Robot") ?></h2>
    </div>
    <div class="infoBox">
      <?= __('Inspired by') ?> <a href="https://neal.fun/not-a-robot/" target="_blank" rel="noopener">neal.fun/not-a-robot</a>.
      <?= __('Interactive challenges protect librarian and member login.') ?>
    </div>
  </div>
</div>
<?php
$form = new simbio_form_table_AJAX('mainForm', $_SERVER['PHP_SELF'], 'post');
$form->table_attr = 'id="dataList" class="s-table table"';
$form->table_header_attr = 'class="alterCell font-weight-bold"';
$form->table_content_attr = 'class="alterCell2"';
$form->submit_button_attr = 'name="saveData" value="' . __('Save Settings') . '" class="btn btn-default"';

$form->addCheckBox(
    'use_as_default',
    __('Use as default captcha provider'),
    [['1', __('Yes — set IAmNotRobot as default')]],
    $isDefault ? ['1'] : [],
    ' class="form-control"'
);

$form->addCheckBox(
    'enable_librarian',
    __('Enable on admin / librarian login'),
    [['1', __('Active')]],
    !empty($sections['librarian']['active']) ? ['1'] : [],
    ' class="form-control"'
);

$form->addCheckBox(
    'enable_memberarea',
    __('Enable on member login'),
    [['1', __('Active')]],
    !empty($sections['memberarea']['active']) ? ['1'] : [],
    ' class="form-control"'
);

echo $form->printOut();
?>
<div class="alert alert-info mt-3">
  <strong><?= __('Challenges included') ?>:</strong>
  <?= __('Stop signs, vegetables, wiggling text, affirmations, tic-tac-toe, whack-a-mole, number order, reverse traffic light.') ?>
</div>
