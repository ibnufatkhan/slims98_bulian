<?php
/**
 * Made by lovely form Ibnu Fatkhan ibnufatkhan@gmail.com
 *
 * Plugin Name: Visitor Stats Footer
 * Plugin URI: https://github.com/ibnufatkhan/slims98_bulian
 * Description: Menampilkan penghitung pengunjung web OPAC sepanjang masa di sisi kanan footer.
 * Version: 1.1.0
 * Author: Ibnu Fatkhan
 * Author URI: https://github.com/ibnufatkhan
 */

use SLiMS\Plugins;

require_once __DIR__ . '/helper.php';

$plugin = Plugins::getInstance();

/**
 * Inject widget pengunjung web ke slot kanan footer.
 */
$plugin->register(Plugins::CONTENT_BEFORE_LOAD, function ($opac) {
    $count = visitor_stats_record_and_total();
    $label = __('Pengunjung web');
    $formatted = number_format((int) $count, 0, ',', '.');

    $cssUrl = SWB . 'plugins/visitor_stats/assets/visitor_stats.css?v=1.1.0';
    if (!defined('VISITOR_STATS_CSS_LOADED')) {
        define('VISITOR_STATS_CSS_LOADED', true);
        $opac->js = ($opac->js ?? '') . '<link rel="stylesheet" href="' . $cssUrl . '">';
    }

    $html = '<div id="visitor-stats-footer" class="visitor-stats-footer" title="' . htmlspecialchars($label . ' (sejak awal)', ENT_QUOTES, 'UTF-8') . '">'
        . '<i class="ri-global-line" aria-hidden="true"></i>'
        . '<span class="visitor-stats-label">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>'
        . '<strong class="visitor-stats-count">' . htmlspecialchars($formatted, ENT_QUOTES, 'UTF-8') . '</strong>'
        . '</div>';

    $config = [
        'html' => $html,
        'cssUrl' => $cssUrl,
    ];

    $json = json_encode($config, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    if ($json === false) {
        return;
    }

    $opac->js = ($opac->js ?? '') . <<<HTML
<script>
(function () {
  var cfg = {$json};
  function ensureCss() {
    if (!cfg.cssUrl) return;
    var base = cfg.cssUrl.split('?')[0];
    var links = document.querySelectorAll('link[rel="stylesheet"]');
    for (var i = 0; i < links.length; i++) {
      if ((links[i].getAttribute('href') || '').indexOf(base) !== -1) return;
    }
    var link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = cfg.cssUrl;
    document.head.appendChild(link);
  }
  function boot() {
    ensureCss();
    if (document.getElementById('visitor-stats-footer')) return;

    var wrap = document.createElement('div');
    wrap.innerHTML = cfg.html;
    var widget = wrap.firstElementChild;
    if (!widget) return;

    // Slot kanan footer (template BSKDN)
    var slot = document.getElementById('visitor-stats-slot');
    if (slot) {
      slot.appendChild(widget);
      return;
    }

    // Fallback: taruh di kanan bar bawah footer
    var bar = document.querySelector('footer#footer .footer-bottom-bar, footer#footer .container.d-md-flex, footer .container.d-md-flex');
    var social = document.querySelector('footer#footer .social-links, footer .social-links');
    if (bar) {
      bar.appendChild(widget);
      widget.style.marginLeft = 'auto';
      return;
    }
    if (social && social.parentNode) {
      social.parentNode.appendChild(widget);
      return;
    }
    var footer = document.querySelector('footer#footer, footer');
    if (footer) footer.appendChild(widget);
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
