<?php
/**
 * Admin: manage Landing Page Ratings (hide / delete)
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

// IP based access limitation
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');

// start the session
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';
require SIMBIO . 'simbio_DB/simbio_dbop.inc.php';

use SLiMS\DB;

// privileges checking
$can_read = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

function landing_rating_admin_url(array $overrides = []): string
{
    $query = $_GET;
    foreach ($overrides as $key => $value) {
        if ($value === null) {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }
    }
    $built = http_build_query($query);
    return $_SERVER['PHP_SELF'] . ($built !== '' ? '?' . $built : '');
}

$db = DB::getInstance();

/* TOGGLE HIDE / SHOW */
if (isset($_GET['hide']) && $can_write) {
    $id = (int) $_GET['hide'];
    $hidden = isset($_GET['value']) ? ((int) $_GET['value'] ? 1 : 0) : 1;
    $stmt = $db->prepare('UPDATE landing_rating SET is_hidden = :hidden, updated_at = :updated_at WHERE id = :id');
    $ok = $stmt->execute([
        'hidden' => $hidden,
        'updated_at' => date('Y-m-d H:i:s'),
        'id' => $id,
    ]);

    if ($ok) {
        writeLog(
            'staff',
            $_SESSION['uid'],
            'system',
            $_SESSION['realname'] . ($hidden ? ' HIDE' : ' SHOW') . ' landing rating #' . $id,
            'LandingRating',
            $hidden ? 'Hide' : 'Show'
        );
        utility::jsToastr(__('Landing Page Rating'), $hidden ? __('Ulasan disembunyikan') : __('Ulasan ditampilkan kembali'), 'success');
    } else {
        utility::jsToastr(__('Landing Page Rating'), __('Gagal mengubah status ulasan'), 'error');
    }

    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . landing_rating_admin_url(['hide' => null, 'value' => null]) . '\');</script>';
    exit;
}

/* DELETE */
if (isset($_POST['itemID']) && !empty($_POST['itemID']) && isset($_POST['itemAction']) && $can_write) {
    if (!is_array($_POST['itemID'])) {
        $_POST['itemID'] = [(int) $_POST['itemID']];
    }

    $error_num = 0;
    $stmt = $db->prepare('DELETE FROM landing_rating WHERE id = :id');
    foreach ($_POST['itemID'] as $itemID) {
        $itemID = (int) $itemID;
        if (!$stmt->execute(['id' => $itemID])) {
            $error_num++;
        } else {
            writeLog('staff', $_SESSION['uid'], 'system', $_SESSION['realname'] . ' DELETE landing rating #' . $itemID, 'LandingRating', 'Delete');
        }
    }

    if ($error_num === 0) {
        utility::jsToastr(__('Landing Page Rating'), __('Semua data berhasil dihapus'), 'success');
    } else {
        utility::jsToastr(__('Landing Page Rating'), __('Sebagian data gagal dihapus'), 'error');
    }
    echo '<script type="text/javascript">parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '?' . ($_POST['lastQueryStr'] ?? '') . '\');</script>';
    exit;
}

/* SEARCH / FILTER */
$criteria = '1';
if (isset($_GET['keywords']) && $_GET['keywords'] !== '') {
    $keywords = $dbs->escape_string(trim($_GET['keywords']));
    $criteria = "(visitor_name LIKE '%{$keywords}%' OR comment LIKE '%{$keywords}%')";
}

if (isset($_GET['filter'])) {
    if ($_GET['filter'] === 'hidden') {
        $criteria .= ' AND is_hidden = 1';
    } elseif ($_GET['filter'] === 'visible') {
        $criteria .= ' AND is_hidden = 0';
    }
}

?>
<div class="menuBox">
    <div class="menuBoxInner systemIcon">
        <div class="per_title">
            <h2><?= __('Landing Page Rating'); ?></h2>
        </div>
        <div class="infoBox">
            <?= __('Kelola ulasan pengunjung pada footer landing page. Anda dapat menyembunyikan atau menghapus ulasan.'); ?>
        </div>
        <div class="sub_section">
            <div class="btn-group">
                <a href="<?= landing_rating_admin_url(['filter' => null, 'keywords' => $_GET['keywords'] ?? null]); ?>" class="btn btn-default"><?= __('Semua'); ?></a>
                <a href="<?= landing_rating_admin_url(['filter' => 'visible']); ?>" class="btn btn-default"><?= __('Tampil'); ?></a>
                <a href="<?= landing_rating_admin_url(['filter' => 'hidden']); ?>" class="btn btn-default"><?= __('Tersembunyi'); ?></a>
            </div>
            <form name="search" action="<?= $_SERVER['PHP_SELF']; ?>" id="search" method="get" class="form-inline">
                <input type="text" name="keywords" class="form-control col-md-3"
                       value="<?= htmlspecialchars($_GET['keywords'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="<?= __('Cari nama / komentar'); ?>">
                <?php if (!empty($_GET['filter'])): ?>
                    <input type="hidden" name="filter" value="<?= htmlspecialchars($_GET['filter'], ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>
                <input type="submit" class="s-btn btn btn-default" value="<?= __('Search'); ?>">
            </form>
        </div>
    </div>
</div>
<?php

/**
 * Action buttons callback for datagrid
 *
 * @param mysqli $db_obj
 * @param array $data
 * @return string
 */
function landing_rating_action_links($db_obj, $data)
{
    global $can_write;
    if (!$can_write) {
        return '-';
    }

    // data[0] = id, data[6] = "id|is_hidden"
    $meta = explode('|', (string) ($data[6] ?? ''));
    $id = (int) ($meta[0] ?? $data[0] ?? 0);
    $is_hidden = (int) ($meta[1] ?? 0);
    $next = $is_hidden ? 0 : 1;
    $label = $is_hidden ? __('Tampilkan') : __('Sembunyikan');
    $btnClass = $is_hidden ? 'btn-success' : 'btn-warning';

    $hideUrl = landing_rating_admin_url([
        'hide' => $id,
        'value' => $next,
    ]);

    return '<a class="btn btn-sm ' . $btnClass . '" href="#" '
        . 'onclick="parent.$(\'#mainContent\').simbioAJAX(\'' . $hideUrl . '\'); return false;">'
        . $label . '</a>';
}

/* DATAGRID */
$datagrid = new simbio_datagrid();
$datagrid->table_attr = 'id="dataList" class="s-table table"';
$datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
$datagrid->setSQLColumn(
    'id',
    'visitor_name AS \'' . __('Nama') . '\'',
    'comment AS \'' . __('Komentar') . '\'',
    'rating AS \'' . __('Rating') . '\'',
    'IF(is_hidden=1, \'' . __('Tersembunyi') . '\', \'' . __('Tampil') . '\') AS \'' . __('Status') . '\'',
    'created_at AS \'' . __('Tanggal') . '\'',
    'CONCAT(id, \'|\', is_hidden) AS \'' . __('Aksi') . '\''
);
$datagrid->setSQLorder('created_at DESC');
$datagrid->setSQLCriteria($criteria);
$datagrid->modifyColumnContent(6, 'callback{landing_rating_action_links}');

if ($can_write) {
    $datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];
}

echo $datagrid->createDataGrid($dbs, 'landing_rating', 20, ($can_read AND $can_write));
