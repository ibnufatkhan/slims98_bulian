<?php
/**
 * Navbar — Ibn Bulian D'ACE (Etran)
 * Home uses the split-hero brand bar; this sticky bar appears on inner pages.
 */
$library_name = $sysconf['library_name'] ?? 'SLiMS';
$is_home = !isset($_GET['p']) && !isset($_GET['search']);
?>
<?php if (!$is_home): ?>
<header class="etran-topbar">
  <a class="etran-brand" href="index.php">
    <span class="etran-mark" aria-hidden="true">
      <svg viewBox="0 0 24 24"><path d="M4 9h11"/><path d="M12 5l4 4-4 4"/><path d="M20 15H9"/><path d="M12 19l-4-4 4-4"/></svg>
    </span>
    <span><?php echo htmlspecialchars($library_name); ?></span>
  </a>
  <nav class="etran-nav etran-nav-desktop" aria-label="Primary">
    <a href="index.php"><?php echo __('Home'); ?></a>
    <a href="index.php#cari"><?php echo __('Search'); ?></a>
    <a href="index.php?p=news"><?php echo __('News'); ?></a>
    <a href="index.php?p=member"><?php echo __('Member'); ?></a>
    <a href="index.php#contact"><?php echo __('Contact'); ?></a>
  </nav>
  <a class="etran-btn" href="index.php#cari"><?php echo __('Get started'); ?></a>
</header>
<?php endif; ?>
