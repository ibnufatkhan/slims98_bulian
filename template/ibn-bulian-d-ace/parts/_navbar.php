<?php
$library_name = $sysconf['library_name'] ?? 'SLiMS';
$img = CURRENT_TEMPLATE_DIR . 'assets/images/etran/';
$is_home = !isset($_GET['p']) && !isset($_GET['search']);
?>
<?php if (!$is_home): ?>
<header class="etran-topbar">
  <a class="etran-brand" href="index.php">
    <img src="<?php echo $img; ?>logo-symbol.svg" alt="">
    <span><?php echo htmlspecialchars($library_name); ?></span>
  </a>
  <nav class="etran-nav etran-nav-desktop" aria-label="Primary">
    <a href="index.php"><?php echo __('Home'); ?></a>
    <a href="index.php#cari"><?php echo __('Search'); ?></a>
    <a href="index.php?p=news"><?php echo __('News'); ?></a>
    <a href="index.php?p=member"><?php echo __('Member'); ?></a>
  </nav>
  <a class="etran-btn" href="index.php#cari"><?php echo __('Get started'); ?></a>
</header>
<?php endif; ?>
