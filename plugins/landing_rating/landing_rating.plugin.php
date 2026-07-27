<?php
/**
 * Plugin Name: Landing Page Rating
 * Plugin URI: https://github.com/slims/slims9_bulian
 * Description: Fitur rating di footer landing page. Pengunjung dapat mengirim nama, komentar, dan bintang. Admin dapat menyembunyikan atau menghapus rating.
 * Version: 1.0.1
 * Author: SLiMS Community
 * Author URI: https://slims.web.id
 */

use SLiMS\Plugins;

$plugin = Plugins::getInstance();

// Admin: kelola rating (hide / hapus)
$plugin->registerMenu('system', __('Landing Page Rating'), __DIR__ . '/admin.php');

// OPAC endpoint: submit rating via AJAX (index.php?p=landing_rating)
$plugin->registerMenu('opac', 'Landing Rating', __DIR__ . '/opac.php');

/**
 * Apakah halaman saat ini adalah landing page OPAC?
 */
function landing_rating_is_home(): bool
{
    return !(
        isset($_GET['p'])
        || isset($_GET['search'])
        || isset($_GET['keywords'])
        || isset($_GET['title'])
        || isset($_GET['author'])
        || isset($_GET['subject'])
    );
}

/**
 * Render HTML widget (section saja).
 */
function landing_rating_capture_html(): string
{
    $landing_rating_embed_mode = true;
    ob_start();
    include __DIR__ . '/footer_widget.php';
    return trim((string) ob_get_clean());
}

/**
 * Render penuh (untuk hook template opac_footer).
 */
function landing_rating_render_once(): void
{
    static $rendered = false;
    if ($rendered || !landing_rating_is_home()) {
        return;
    }
    $rendered = true;
    $landing_rating_embed_mode = false;
    include __DIR__ . '/footer_widget.php';
}

/**
 * Inject mandiri ke OPAC tanpa mengubah template.
 * Homepage template default tidak menampilkan $main_content,
 * jadi kita sisipkan lewat $opac->js sebelum <footer>.
 */
$plugin->register(Plugins::CONTENT_BEFORE_LOAD, function ($opac) {
    if (!landing_rating_is_home()) {
        return;
    }

    $html = landing_rating_capture_html();
    if ($html === '') {
        // biasanya tabel belum ada / migrasi gagal
        return;
    }

    if (!defined('LANDING_RATING_CSS_LOADED')) {
        define('LANDING_RATING_CSS_LOADED', true);
        $css = SWB . 'plugins/landing_rating/assets/landing_rating.css?v=1.0.1';
        $opac->metadata = ($opac->metadata ?? '') . '<link rel="stylesheet" href="' . $css . '">';
    }

    $config = [
        'submitUrl' => SWB . 'index.php?p=landing_rating',
        'scriptUrl' => SWB . 'plugins/landing_rating/assets/landing_rating.js?v=1.0.1',
        'html' => $html,
        'labels' => [
            'reviews' => __('%d ulasan'),
            'empty' => __('Belum ada ulasan. Jadilah yang pertama!'),
            'sending' => __('Mengirim...'),
            'send' => __('Kirim Ulasan'),
            'error' => __('Gagal mengirim ulasan. Coba lagi.'),
        ],
    ];

    $json = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json === false) {
        return;
    }

    $opac->js = ($opac->js ?? '') . <<<HTML
<script>
(function () {
  var cfg = {$json};
  function syncCsrf(root) {
    var pageToken = document.querySelector('input[name="csrf_token"]');
    var formToken = root.querySelector('#lr-csrf, input[name="csrf_token"]');
    if (pageToken && formToken && pageToken.value) formToken.value = pageToken.value;
  }
  function boot() {
    if (document.getElementById('landing-rating')) {
      syncCsrf(document.getElementById('landing-rating'));
      return;
    }
    var wrap = document.createElement('div');
    wrap.innerHTML = cfg.html;
    var footer = document.querySelector('footer.s-footer, footer.py-4, footer');
    var nodes = Array.prototype.slice.call(wrap.childNodes);
    nodes.forEach(function (node) {
      if (footer && footer.parentNode) footer.parentNode.insertBefore(node, footer);
      else document.body.appendChild(node);
    });
    var widget = document.getElementById('landing-rating');
    if (widget) syncCsrf(widget);
    window.LANDING_RATING = {
      submitUrl: cfg.submitUrl,
      labels: cfg.labels
    };
    var s = document.createElement('script');
    s.src = cfg.scriptUrl;
    document.body.appendChild(s);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
</script>
HTML;
});

// Opsional: jika template memanggil Plugins::run('opac_footer')
$plugin->register('opac_footer', function () {
    // Hindari dobel jika sudah di-inject via JS
    echo '<script>window.__LR_TEMPLATE_HOOK=1;</script>';
    landing_rating_render_once();
});
