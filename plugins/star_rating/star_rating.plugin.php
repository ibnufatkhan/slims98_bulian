<?php
/**
 * Plugin Name: Star Rating
 * Plugin URI: https://github.com/slims/slims9_bulian
 * Description: Fitur rating & ulasan pada footer landing page OPAC. Pengunjung dapat mengirim nama, komentar, dan rating; admin dapat menyembunyikan atau menghapus ulasan.
 * Version: 1.0.0
 * Author: SLiMS Community
 * Author URI: https://slims.web.id
 */

require_once __DIR__ . '/helper.php';

$plugin = \SLiMS\Plugins::getInstance();

// Admin menu under System
$plugin->registerMenu('system', __('Rating & Ulasan'), __DIR__ . '/index.php');

// Public AJAX submit endpoint
$plugin->registerMenu('opac', 'star_rating_submit', __DIR__ . '/pages/submit.php');

// Render widget in OPAC footer (landing page only)
$plugin->register('opac_footer', function () {
    star_rating_render_footer();
});
