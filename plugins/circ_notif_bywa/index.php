<?php
/**
 * Halaman log notifikasi WhatsApp (modul Circulation)
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-circulation');

require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('circulation', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

require_once __DIR__ . '/autoload.php';
$ccnw = require __DIR__ . '/bootstrap.php';
$service = new \Cncw\Service($ccnw);

$flash = null;
if (isset($_GET['kirim_id']) && ctype_digit((string) $_GET['kirim_id'])) {
    $flash = $service->resendLog((int) $_GET['kirim_id']);
}

$filters = [
    'member_id' => trim((string) ($_GET['member_id'] ?? '')),
    'member_name' => trim((string) ($_GET['member_name'] ?? '')),
    'member_phone' => trim((string) ($_GET['member_phone'] ?? '')),
    'transaction_date' => trim((string) ($_GET['transaction_date'] ?? '')),
    'orderBy' => (string) ($_GET['orderBy'] ?? 'id'),
    'sort' => (string) ($_GET['sort'] ?? 'DESC'),
];
$page = isset($_GET['page']) && ctype_digit((string) $_GET['page']) ? (int) $_GET['page'] : 1;
$log = new \Cncw\Log($ccnw['conn'], $filters, $page, 10);
$rows = $log->getData();
?>
<div class="menuBox">
    <div class="menuBoxInner printIcon">
        <div class="per_title">
            <h2><?php echo __('Circulation Notification Log'); ?></h2>
        </div>
        <div class="infoBox">
            <?= __('Filter log notifikasi WhatsApp sirkulasi & overdue.') ?>
            <br>
            Provider: <strong><?= htmlspecialchars((string) $ccnw['provider'], ENT_QUOTES, 'UTF-8') ?></strong>
            | Mode: <strong><?= htmlspecialchars((string) $ccnw['mode'], ENT_QUOTES, 'UTF-8') ?></strong>
            <br>
            <?= __('Ubah token / device ID / nomor HP di') ?> <em>System &rarr; WA Notif Settings</em>
        </div>
        <?php if (is_array($flash)) : ?>
            <div class="alert <?= $flash['status'] === 'SENT' ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <div class="sub_section">
            <form name="wa_log_filter" action="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery(), ENT_QUOTES, 'UTF-8') ?>" method="get" class="form-inline">
                <input type="hidden" name="mod" value="<?= htmlspecialchars((string) ($_GET['mod'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php echo __('Member ID'); ?>:&nbsp;
                <input type="text" name="member_id" class="form-control col-md-1" value="<?= htmlspecialchars($filters['member_id'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="off"/>
                &nbsp;<?php echo __('Member Name'); ?>:&nbsp;
                <input type="text" name="member_name" class="form-control col-md-1" value="<?= htmlspecialchars($filters['member_name'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="off"/>
                &nbsp;<?php echo __('Member Phone'); ?>:&nbsp;
                <input type="text" name="member_phone" class="form-control col-md-1" value="<?= htmlspecialchars($filters['member_phone'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="off"/>
                &nbsp;<?php echo __('Transaction Date'); ?>:&nbsp;
                <input type="text" name="transaction_date" class="form-control col-md-1" value="<?= htmlspecialchars($filters['transaction_date'], ENT_QUOTES, 'UTF-8') ?>" autocomplete="off"/>
                <input type="submit" value="<?php echo __('Search'); ?>" class="s-btn btn btn-success"/>
            </form>
        </div>
    </div>
</div>

<?php if (count($rows) > 0) : ?>
<div class="alert alert-success" role="alert">
  Found log data: <?= (int) $log->getTotal() ?>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>id</th>
            <th><a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery('member_id'), ENT_QUOTES, 'UTF-8') ?>">member_id</a></th>
            <th><a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery('member_name'), ENT_QUOTES, 'UTF-8') ?>">member_name</a></th>
            <th>member_type</th>
            <th><a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery('member_phone'), ENT_QUOTES, 'UTF-8') ?>">member_phone</a></th>
            <th>type</th>
            <th><a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery('transaction_date'), ENT_QUOTES, 'UTF-8') ?>">transaction_date</a></th>
            <th>transaction_id</th>
            <th><a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery('created_at'), ENT_QUOTES, 'UTF-8') ?>">created_at</a></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rows as $vr) : ?>
        <tr class="alterCell2">
            <td valign="top"><?= (int) $vr['id'] ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['member_id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['member_name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['member_type'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['member_phone'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) ($vr['notif_type'] ?? 'circulation'), ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['transaction_date'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['transaction_id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top"><?= htmlspecialchars((string) $vr['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
            <td valign="top">
                <a href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery(\Cncw\Uri::sendLink()) . '&page=' . ($log->getPage()) . '&kirim_id=' . (int) $vr['id'], ENT_QUOTES, 'UTF-8') ?>"
                   title="<?= htmlspecialchars((string) $vr['message'], ENT_QUOTES, 'UTF-8') ?>">
                    <span>Kirim ulang</span>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if ($log->getTotalPages() > 1) : ?>
<nav>
  <ul class="pagination">
    <?php for ($p = 1; $p <= $log->getTotalPages(); $p++) : ?>
      <li class="page-item <?= $p === $log->getPage() ? 'active' : '' ?>">
        <a class="page-link" href="<?= htmlspecialchars($_SERVER['PHP_SELF'] . '?' . \Cncw\Uri::httpQuery($filters['orderBy'], $filters['sort']) . '&page=' . $p, ENT_QUOTES, 'UTF-8') ?>"><?= $p ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>
<?php else : ?>
<div class="alert alert-warning" role="alert">
    Log data not found!
</div>
<?php endif; ?>
