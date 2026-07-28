<?php
/**
 * Bootstrap konfigurasi plugin Circulation Notification by WhatsApp
 * Preferensi utama: pengaturan dari backend (tabel setting).
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require_once __DIR__ . '/autoload.php';

return \Cncw\Settings::runtime();
