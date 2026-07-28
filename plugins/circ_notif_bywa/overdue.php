<?php
/**
 * Halaman kirim notifikasi overdue via WhatsApp (modul Membership)
 * Digabung dari fitur BSKDNold: overdue_wa.php + sendOverdueNoticeWA()
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-membership');

require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('membership', 'r');
if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

require_once __DIR__ . '/autoload.php';
$ccnw = require __DIR__ . '/bootstrap.php';
$service = new \Cncw\Service($ccnw);

$flash = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['memberID'])) {
    $memberId = trim((string) $_POST['memberID']);
    if ($memberId !== '') {
        $flash = $service->sendOverdueNotice($memberId);
    }
}

$keyword = trim((string) ($_GET['keywords'] ?? ''));
$page = isset($_GET['page']) && ctype_digit((string) $_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;
$total = $service->countOverdueMembers($keyword);
$totalPages = (int) max(1, (int) ceil($total / $perPage));
$members = $service->listOverdueMembers($keyword, $perPage, $offset);

$self = htmlspecialchars($_SERVER['PHP_SELF'] . '?mod=' . urlencode((string) ($_GET['mod'] ?? '')) . '&id=' . urlencode((string) ($_GET['id'] ?? '')), ENT_QUOTES, 'UTF-8');
?>
<div class="menuBox">
    <div class="menuBoxInner memberIcon">
        <div class="per_title">
            <h2><?php echo __('Overdue WA Notice'); ?></h2>
        </div>
        <div class="infoBox">
            <?= __('Kirim notifikasi keterlambatan pinjaman melalui WhatsApp (Fonnte / Whacenter).') ?>
            <br>
            Pastikan kolom <strong>Phone Number</strong> pada data anggota terisi nomor WhatsApp.
        </div>
        <?php if (is_array($flash)) : ?>
            <div class="alert <?= $flash['status'] === 'SENT' ? 'alert-success' : 'alert-danger' ?>">
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
        <div class="sub_section">
            <form method="get" action="<?= $self ?>" class="form-inline">
                <input type="hidden" name="mod" value="<?= htmlspecialchars((string) ($_GET['mod'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars((string) ($_GET['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                <?php echo __('Member ID') . ' / ' . __('Member Name'); ?>:&nbsp;
                <input type="text" name="keywords" class="form-control col-md-3" value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" autocomplete="off"/>
                <input type="submit" value="<?php echo __('Search'); ?>" class="s-btn btn btn-success"/>
            </form>
        </div>
    </div>
</div>

<?php if ($total > 0) : ?>
<div class="alert alert-info">Total anggota overdue (dengan nomor WA): <?= (int) $total ?></div>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?php echo __('Member ID'); ?></th>
            <th><?php echo __('Member Name'); ?></th>
            <th><?php echo __('Membership Type'); ?></th>
            <th><?php echo __('Phone Number'); ?></th>
            <th>Overdue items</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($members as $m) : ?>
        <tr>
            <td><?= htmlspecialchars((string) $m['member_id'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $m['member_name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) ($m['member_type_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars((string) $m['member_phone'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= (int) $m['overdue_count'] ?></td>
            <td>
                <form method="post" action="<?= $self ?>&amp;keywords=<?= urlencode($keyword) ?>&amp;page=<?= (int) $page ?>" style="display:inline" onsubmit="return confirm('Kirim notifikasi overdue WA ke anggota ini?');">
                    <input type="hidden" name="memberID" value="<?= htmlspecialchars((string) $m['member_id'], ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-primary">Kirim WA</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php if ($totalPages > 1) : ?>
<nav>
  <ul class="pagination">
    <?php for ($p = 1; $p <= $totalPages; $p++) : ?>
      <li class="page-item <?= $p === $page ? 'active' : '' ?>">
        <a class="page-link" href="<?= $self ?>&amp;keywords=<?= urlencode($keyword) ?>&amp;page=<?= $p ?>"><?= $p ?></a>
      </li>
    <?php endfor; ?>
  </ul>
</nav>
<?php endif; ?>
<?php else : ?>
<div class="alert alert-warning">Tidak ada anggota dengan pinjaman overdue / nomor WhatsApp.</div>
<?php endif; ?>
