<?php
/**
 * Endpoint AJAX kirim overdue WA (kompatibel pola BSKDNold overdue_wa.php)
 *
 * POST memberID=<id>
 */

define('INDEX_AUTH', '1');

require '../../../sysconfig.inc.php';
require LIB . 'ip_based_access.inc.php';
do_checkIP('smc');
do_checkIP('smc-membership');
require SB . 'admin/default/session.inc.php';
require SB . 'admin/default/session_check.inc.php';

$can_read = utility::havePrivilege('membership', 'r');
if (!$can_read) {
    die('<div class="alert alert-danger">' . __('You are not authorized to view this section') . '</div>');
}

require_once __DIR__ . '/autoload.php';
$ccnw = require __DIR__ . '/bootstrap.php';
$service = new \Cncw\Service($ccnw);

$memberId = trim((string) ($_POST['memberID'] ?? ''));
if ($memberId === '') {
    echo '<div class="alert alert-danger">Member ID kosong.</div>';
    exit;
}

$status = $service->sendOverdueNotice($memberId);
$alertType = $status['status'] === 'SENT' ? 'alert-success' : 'alert-danger';
echo '<div class="alert ' . $alertType . '">' . htmlspecialchars($status['message'], ENT_QUOTES, 'UTF-8') . '</div>';
