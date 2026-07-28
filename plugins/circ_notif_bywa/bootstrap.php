<?php
/**
 * Bootstrap konfigurasi plugin Circulation Notification by WhatsApp
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

use SLiMS\DB;

$configFile = __DIR__ . '/config.php';
$sampleFile = __DIR__ . '/config.sample.php';

$ccnw = is_readable($configFile)
    ? require $configFile
    : (is_readable($sampleFile) ? require $sampleFile : []);

if (!is_array($ccnw)) {
    $ccnw = [];
}

$ccnw = array_merge([
    'library_name' => '',
    'footer_text' => 'Harap simpan resi ini sebagai bukti transaksi.',
    'mode' => 'default',
    'provider' => 'fonnte',
    'token' => '',
    'device_id' => '',
    'send_on_overdue_email' => true,
    'overdue_template' => '',
    'gearman_host' => '127.0.0.1',
    'gearman_port' => '4730',
    'nsq_host' => '127.0.0.1',
    'nsq_port' => '4151',
    'nsq_topic' => 'circulation',
], $ccnw);

// Gunakan koneksi database SLiMS (PDO)
$ccnw['conn'] = DB::getInstance('pdo');

// Fallback nama perpustakaan dari sysconfig
if ($ccnw['library_name'] === '' || $ccnw['library_name'] === 'YOUR_LIBRARY_NAME_HERE') {
    global $sysconf;
    $ccnw['library_name'] = $sysconf['library_name'] ?? 'Perpustakaan';
}

$ccnw['nsq_url'] = sprintf(
    'http://%s:%s/pub?topic=%s',
    $ccnw['nsq_host'],
    $ccnw['nsq_port'],
    $ccnw['nsq_topic']
);

return $ccnw;
