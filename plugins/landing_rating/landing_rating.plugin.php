<?php
/**
 * Plugin Name: Landing Page Rating
 * Plugin URI: https://github.com/slims/slims9_bulian
 * Description: Fitur rating di footer landing page. Pengunjung dapat mengirim nama, komentar, dan bintang. Admin dapat menyembunyikan atau menghapus rating.
 * Version: 1.0.0
 * Author: SLiMS Community
 * Author URI: https://slims.web.id
 */

use SLiMS\Plugins;

$plugin = Plugins::getInstance();

// Admin: kelola rating (hide / hapus)
$plugin->registerMenu('system', __('Landing Page Rating'), __DIR__ . '/admin.php');

// OPAC endpoint: submit rating via AJAX (index.php?p=landing_rating)
$plugin->registerMenu('opac', 'Landing Rating', __DIR__ . '/opac.php');

// Hook footer landing page
$plugin->register('opac_footer', function () {
    // Hanya tampil di landing page (tanpa query pencarian / halaman konten)
    if (isset($_GET['p']) || isset($_GET['search']) || isset($_GET['keywords']) || isset($_GET['title'])) {
        return;
    }

    include __DIR__ . '/footer_widget.php';
});
