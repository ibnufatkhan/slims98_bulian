<?php
/**
 * Plugin Name: Circulation & Overdue Notification via WhatsApp
 * Plugin URI: https://github.com/hendrowicaksono/Simple-WA-Notif-for-Circulation
 * Description: Notifikasi WhatsApp untuk transaksi sirkulasi dan keterlambatan. Digabung dari Simple-WA-Notif-for-Circulation + fitur overdue BSKDNold. Provider: Fonnte & Whacenter. PHP 8+.
 * Version: 2.0.0
 * Author: SLiMS Community / Hendro Wicaksono / BSKDN
 * Author URI: https://github.com/hendrowicaksono
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

use SLiMS\Plugins;

require_once __DIR__ . '/autoload.php';

$plugin = Plugins::getInstance();

// Menu log di modul Circulation
$plugin->registerMenu('circulation', __('WA Notif Log'), __DIR__ . '/index.php');

// Menu overdue WA di modul Membership
$plugin->registerMenu('membership', __('Overdue WA Notice'), __DIR__ . '/overdue.php');

/**
 * Hook: setelah transaksi sirkulasi sukses (pinjam / kembali / perpanjang).
 */
$plugin->register(Plugins::CIRCULATION_AFTER_SUCCESSFUL_TRANSACTION, function ($data) {
    try {
        $ccnw = require __DIR__ . '/bootstrap.php';
        $service = new \Cncw\Service($ccnw);
        $service->handleCirculationTransaction(is_array($data) ? $data : []);
    } catch (\Throwable $e) {
        error_log('[circ_notif_bywa] circulation hook: ' . $e->getMessage());
    }
});

/**
 * Hook: saat overdue e-mail dikirim, opsional ikut kirim WA
 * (fitur dari BSKDNold sendOverdueNoticeWA).
 */
$plugin->register(Plugins::OVERDUE_NOTICE_INIT, function ($params) {
    try {
        $ccnw = require __DIR__ . '/bootstrap.php';
        if (empty($ccnw['send_on_overdue_email'])) {
            return;
        }

        $member = $params['member'] ?? null;
        if (!is_object($member) || empty($member->member_id)) {
            return;
        }

        $service = new \Cncw\Service($ccnw);
        $service->sendOverdueNotice((string) $member->member_id);
    } catch (\Throwable $e) {
        error_log('[circ_notif_bywa] overdue hook: ' . $e->getMessage());
    }
});
