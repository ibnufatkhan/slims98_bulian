<?php
/**
 * Admin: manage star ratings (hide / delete)
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-system');

require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';
require SIMBIO . 'simbio_GUI/table/simbio_table.inc.php';
require SIMBIO . 'simbio_GUI/form_maker/simbio_form_table_AJAX.inc.php';
require SIMBIO . 'simbio_GUI/paging/simbio_paging.inc.php';
require SIMBIO . 'simbio_DB/datagrid/simbio_dbgrid.inc.php';

require_once __DIR__ . '/helper.php';

use SLiMS\DB;

$can_read = utility::havePrivilege('system', 'r');
$can_write = utility::havePrivilege('system', 'w');

if (!$can_read) {
    die('<div class="errorBox">' . __('You are not authorized to view this section') . '</div>');
}

// Toggle hide / show
if ($can_write && isset($_GET['hide']) && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $hide = (int) $_GET['hide'] === 1 ? 1 : 0;
    DB::getInstance()->prepare('UPDATE plugin_star_rating SET is_hidden = ? WHERE id = ?')->execute([$hide, $id]);
    utility::jsToastr(__('Success'), $hide ? __('Ulasan disembunyikan') : __('Ulasan ditampilkan kembali'), 'success');
    $reloadQuery = $_GET;
    unset($reloadQuery['hide'], $reloadQuery['id']);
    $reloadUrl = $_SERVER['PHP_SELF'] . (count($reloadQuery) ? '?' . http_build_query($reloadQuery) : '');
    echo '<script>parent.$(\'#mainContent\').simbioAJAX(\'' . $reloadUrl . '\')</script>';
    exit;
}

// Bulk delete
if ($can_write && isset($_POST['itemID']) && !empty($_POST['itemID']) && isset($_POST['itemAction'])) {
    $stmt = DB::getInstance()->prepare('DELETE FROM plugin_star_rating WHERE id = ?');
    foreach ($_POST['itemID'] as $id) {
        $stmt->execute([(int) $id]);
    }
    utility::jsToastr(__('Success'), __('Data berhasil dihapus'), 'success');
    echo '<script>parent.$(\'#mainContent\').simbioAJAX(\'' . $_SERVER['PHP_SELF'] . '\')</script>';
    exit;
}

$summary = star_rating_get_summary();
?>
<div class="menuBox">
    <div class="menuBoxInner masterFileIcon">
        <div class="per_title">
            <h2><?php echo __('Manajemen Rating & Ulasan'); ?></h2>
        </div>
        <div class="infoBox">
            <?php echo __('Kelola ulasan pengunjung pada footer landing page OPAC. Sembunyikan atau hapus ulasan jika diperlukan.'); ?>
            <div class="mt-2">
                <strong><?php echo number_format($summary['average'], 1); ?></strong> / 5
                &middot; <?php echo (int) $summary['total']; ?> <?php echo __('ulasan tampil'); ?>
            </div>
        </div>
        <div class="sub_section">
            <form name="search" action="<?php echo $_SERVER['PHP_SELF']; ?>" id="search" method="get" class="form-inline">
                <?php echo __('Search'); ?>
                <input type="text" name="keywords" class="form-control col-md-3" value="<?php echo isset($_GET['keywords']) ? htmlspecialchars($_GET['keywords']) : ''; ?>" />
                <input type="submit" id="doSearch" value="<?php echo __('Search'); ?>" class="s-btn btn btn-default" />
            </form>
        </div>
    </div>
</div>
<?php

function star_rating_admin_rating_col($dbs, $data)
{
    $rating = (int) $data[3];
    return star_rating_stars_html((float) $rating, 'sm') . ' (' . $rating . ')';
}

function star_rating_admin_status_col($dbs, $data)
{
    $hidden = (int) $data[4];
    if ($hidden) {
        return '<span class="badge badge-secondary">' . __('Disembunyikan') . '</span>';
    }
    return '<span class="badge badge-success">' . __('Tampil') . '</span>';
}

function star_rating_admin_action_col($dbs, $data)
{
    global $can_write;
    if (!$can_write) {
        return '-';
    }

    $id = (int) $data[0];
    $hidden = (int) $data[4];
    $next = $hidden ? 0 : 1;
    $label = $hidden ? __('Tampilkan') : __('Sembunyikan');
    $btn = $hidden ? 'btn-info' : 'btn-warning';
    $url = $_SERVER['PHP_SELF'] . '?' . http_build_query(array_merge($_GET, ['id' => $id, 'hide' => $next]));

    return '<a href="' . htmlspecialchars($url) . '" class="btn btn-sm ' . $btn . '">' . $label . '</a>';
}

$datagrid = new simbio_datagrid();
$datagrid->setSQLColumn(
    'id',
    'reviewer_name AS `' . __('Nama') . '`',
    'comment AS `' . __('Komentar') . '`',
    'rating AS `' . __('Rating') . '`',
    'is_hidden AS `' . __('Status') . '`',
    'created_at AS `' . __('Tanggal') . '`',
    'id AS `' . __('Aksi') . '`'
);
$datagrid->setSQLorder('created_at DESC');
$datagrid->invisible_fields = [0];
$datagrid->modifyColumnContent(3, 'callback{star_rating_admin_rating_col}');
$datagrid->modifyColumnContent(4, 'callback{star_rating_admin_status_col}');
$datagrid->modifyColumnContent(6, 'callback{star_rating_admin_action_col}');

if (isset($_GET['keywords']) && $_GET['keywords']) {
    $keywords = utility::filterData('keywords', 'get', true, true, true);
    $datagrid->setSQLCriteria("reviewer_name LIKE '%{$keywords}%' OR comment LIKE '%{$keywords}%'");
}

$datagrid->table_attr = 'id="dataList" class="s-table table"';
$datagrid->table_header_attr = 'class="dataListHeader" style="font-weight: bold;"';
$datagrid->edit_property = false;
$datagrid->chbox_property = $can_write ? ['itemID', __('Delete')] : false;
$datagrid->chbox_form_URL = $_SERVER['PHP_SELF'];

echo $datagrid->createDataGrid($dbs, 'plugin_star_rating', 20, $can_read);
