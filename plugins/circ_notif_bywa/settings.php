<?php
/**
 * Konfigurasi WhatsApp Notification — submenu System
 * Menggantikan pengeditan manual config.php
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');

require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';

require_once __DIR__ . '/autoload.php';

use Cncw\Notification;
use Cncw\Settings;
use SLiMS\DB;

$can_read = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

$pdo = DB::getInstance('pdo');

/* ========== SAVE SETTINGS ========== */
if (isset($_POST['saveWaSettings']) && $can_write) {
    $ok = Settings::save($_POST, $pdo);
    if ($ok) {
        writeLog('staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' update WA Notif settings', 'circ_notif_bywa', 'Update');
        utility::jsToastr(__('WA Notif Settings'), __('Pengaturan berhasil disimpan.'), 'success');
    } else {
        utility::jsToastr(__('WA Notif Settings'), __('Gagal menyimpan pengaturan.'), 'error');
    }
    echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '?' . http_build_query([
        'mod' => $_GET['mod'] ?? '',
        'id' => $_GET['id'] ?? '',
    ]) . '\');</script>';
    exit;
}

/* ========== TEST SEND ========== */
if (isset($_POST['sendTestWa']) && $can_write) {
    $ccnw = Settings::runtime($pdo);
    $phone = Settings::normalizePhoneInput((string) ($_POST['test_phone'] ?? $ccnw['test_phone'] ?? ''));
    if ($phone === '') {
        utility::jsToastr(__('WA Notif Settings'), __('Nomor handphone uji masih kosong.'), 'error');
    } else {
        try {
            $message = '*' . strtoupper((string) $ccnw['library_name']) . "*\n"
                . "Ini pesan uji notifikasi WhatsApp dari SLiMS.\n"
                . 'Waktu: ' . date('Y-m-d H:i:s') . "\n"
                . 'Provider: ' . ($ccnw['provider'] ?? '-') . "\n"
                . ($ccnw['footer_text'] ?? '');
            $sender = new Notification($ccnw);
            $sender->send([
                'number' => $phone,
                'message' => $message,
            ]);
            // Simpan nomor uji jika diisi dari form tes
            if (!empty($_POST['test_phone'])) {
                $current = Settings::load($pdo);
                $current['test_phone'] = $phone;
                Settings::save($current, $pdo);
            }
            writeLog('staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' send WA test to ' . $phone, 'circ_notif_bywa', 'Test');
            utility::jsToastr(__('WA Notif Settings'), __('Pesan uji terkirim ke ') . $phone, 'success');
        } catch (Throwable $e) {
            utility::jsToastr(__('WA Notif Settings'), __('Gagal kirim uji: ') . $e->getMessage(), 'error');
        }
    }
    echo '<script type="text/javascript">parent.jQuery(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '?' . http_build_query([
        'mod' => $_GET['mod'] ?? '',
        'id' => $_GET['id'] ?? '',
    ]) . '\');</script>';
    exit;
}

$cfg = Settings::load($pdo);
?>
<div class="menuBox">
    <div class="menuBoxInner systemIcon">
        <div class="per_title">
            <h2><?php echo __('WA Notif Settings'); ?></h2>
        </div>
        <div class="infoBox">
            <?= __('Atur provider Fonnte/Whacenter, token, device ID, dan nomor handphone tanpa mengubah file config.php.') ?>
        </div>
    </div>
</div>

<?php if (!$can_write) : ?>
<div class="alert alert-warning"><?= __('Anda hanya memiliki hak baca pada modul System.') ?></div>
<?php endif; ?>

<?php
$form = new simbio_form_table_AJAX('waNotifSettingsForm', $_SERVER['PHP_SELF'] . '?' . http_build_query([
    'mod' => $_GET['mod'] ?? '',
    'id' => $_GET['id'] ?? '',
]), 'post');
$form->submit_button_attr = 'name="saveWaSettings" value="' . __('Save Settings') . '" class="btn btn-default"' . ($can_write ? '' : ' disabled');
$form->table_attr = 'id="dataList" class="s-table table"';
$form->table_header_attr = 'class="alterCell font-weight-bold"';
$form->table_content_attr = 'class="alterCell2"';

$form->addAnything(__('Provider API'), '');
$providerOptions = [
    ['fonnte', 'Fonnte'],
    ['whacenter', 'Whacenter'],
];
$form->addSelectList('provider', __('Provider'), $providerOptions, $cfg['provider'] ?? 'fonnte', 'class="form-control col-3"');

$form->addTextField(
    'text',
    'token',
    __('Fonnte Token'),
    (string) ($cfg['token'] ?? ''),
    'class="form-control" style="width:70%" placeholder="Token dari dashboard Fonnte"'
);
$form->addTextField(
    'text',
    'device_id',
    __('Whacenter Device ID'),
    (string) ($cfg['device_id'] ?? ''),
    'class="form-control" style="width:70%" placeholder="Device ID dari dashboard Whacenter"'
);

$form->addAnything(__('Identitas & Kontak'), '');
$form->addTextField(
    'text',
    'library_name',
    __('Library Name'),
    (string) ($cfg['library_name'] ?? ''),
    'class="form-control" style="width:70%" placeholder="' . htmlspecialchars(__('Kosongkan = pakai nama dari System'), ENT_QUOTES, 'UTF-8') . '"'
);
$form->addTextField(
    'text',
    'library_phone',
    __('Library Phone / WA'),
    (string) ($cfg['library_phone'] ?? ''),
    'class="form-control" style="width:40%" placeholder="08xxxxxxxxxx / 62xxxxxxxxxx"'
);
$form->addTextField(
    'text',
    'test_phone',
    __('Test Phone Number'),
    (string) ($cfg['test_phone'] ?? ''),
    'class="form-control" style="width:40%" placeholder="Nomor untuk uji kirim pesan"'
);
$form->addTextField(
    'textarea',
    'footer_text',
    __('Footer Text'),
    (string) ($cfg['footer_text'] ?? ''),
    'class="form-control" style="width:80%" rows="2"'
);

$form->addAnything(__('Overdue'), '');
$yesNo = [
    ['1', __('Enable')],
    ['0', __('Disable')],
];
$form->addSelectList(
    'send_on_overdue_email',
    __('Send WA when overdue e-mail clicked'),
    $yesNo,
    !empty($cfg['send_on_overdue_email']) ? '1' : '0',
    'class="form-control col-3"'
);
$form->addTextField(
    'textarea',
    'overdue_template',
    __('Overdue Message Template'),
    (string) ($cfg['overdue_template'] ?? ''),
    'class="form-control" style="width:90%" rows="8"'
);

$form->addAnything(__('Advanced'), '');
$modeOptions = [
    ['default', 'default'],
    ['gearman', 'gearman'],
    ['nsq', 'nsq'],
];
$form->addSelectList('mode', __('Send Mode'), $modeOptions, $cfg['mode'] ?? 'default', 'class="form-control col-3"');
$form->addTextField('text', 'gearman_host', __('Gearman Host'), (string) ($cfg['gearman_host'] ?? '127.0.0.1'), 'class="form-control col-3"');
$form->addTextField('text', 'gearman_port', __('Gearman Port'), (string) ($cfg['gearman_port'] ?? '4730'), 'class="form-control col-2"');
$form->addTextField('text', 'nsq_host', __('NSQ Host'), (string) ($cfg['nsq_host'] ?? '127.0.0.1'), 'class="form-control col-3"');
$form->addTextField('text', 'nsq_port', __('NSQ Port'), (string) ($cfg['nsq_port'] ?? '4151'), 'class="form-control col-2"');
$form->addTextField('text', 'nsq_topic', __('NSQ Topic'), (string) ($cfg['nsq_topic'] ?? 'circulation'), 'class="form-control col-3"');

echo $form->printOut();
?>

<div class="menuBox" style="margin-top:1.5rem">
    <div class="menuBoxInner systemIcon">
        <div class="per_title">
            <h2><?php echo __('Kirim Pesan Uji'); ?></h2>
        </div>
        <div class="infoBox">
            <?= __('Gunakan nomor handphone uji untuk memastikan token/device ID sudah benar.') ?>
        </div>
        <div class="sub_section">
            <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . http_build_query(['mod' => $_GET['mod'] ?? '', 'id' => $_GET['id'] ?? '']), ENT_QUOTES, 'UTF-8') ?>" class="form-inline">
                <label class="mr-2"><?= __('Test Phone Number'); ?>:</label>
                <input type="text" name="test_phone" class="form-control col-md-3" value="<?= htmlspecialchars((string) ($cfg['test_phone'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="08xxxxxxxxxx" <?= $can_write ? '' : 'disabled' ?> />
                <button type="submit" name="sendTestWa" value="1" class="s-btn btn btn-primary ml-2" <?= $can_write ? '' : 'disabled' ?>>
                    <?= __('Kirim Uji WA') ?>
                </button>
            </form>
        </div>
    </div>
</div>

<div class="alert alert-info mt-3">
    <strong>Fonnte:</strong> isi <em>Provider = Fonnte</em> dan <em>Fonnte Token</em>.<br>
    <strong>Whacenter:</strong> isi <em>Provider = Whacenter</em> dan <em>Whacenter Device ID</em>.<br>
    Nomor anggota tetap diambil dari field <em>Phone Number</em> pada data keanggotaan.
</div>
