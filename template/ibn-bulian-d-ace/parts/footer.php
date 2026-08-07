<?php
/**
 * Footer — uiux Figma prototype (light #EDEDED)
 */
$library_name = $sysconf['library_name'] ?? 'SLiMS Library';
$img = CURRENT_TEMPLATE_DIR . 'assets/images/etran/';
?>
<footer class="etran-footer" id="footer">
  <div class="etran-footer-left">
    <img class="logo-mark" src="<?php echo $img; ?>footer-logo.svg" alt="">
    <img class="wordmark" src="<?php echo $img; ?>footer-wordmark.svg" alt="<?php echo htmlspecialchars($library_name); ?>">
  </div>
  <div class="etran-footer-right">
    <div>
      <h4><?php echo __('Contact'); ?></h4>
      <p>hello@example.com</p>
      <a href="<?php echo $sysconf['template']['classic_instagram_link'] ?? '#'; ?>" target="_blank" rel="noopener">Instagram</a>
      <a href="<?php echo $sysconf['template']['classic_twitter_link'] ?? '#'; ?>" target="_blank" rel="noopener">X</a>
      <a href="<?php echo $sysconf['template']['classic_youtube_link'] ?? '#'; ?>" target="_blank" rel="noopener">LinkedIn</a>
    </div>
    <div class="etran-footer-legal">
      <a href="index.php?p=visitor"><?php echo __('Terms & Conditions'); ?></a>
      <a href="index.php?p=member"><?php echo __('Privacy'); ?></a>
    </div>
  </div>
</footer>
<div class="etran-footer-bottom">
  <div>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($library_name); ?> · Ibn Bulian D'ACE</div>
  <div id="visitor-stats-slot"></div>
</div>

<?php if ($sysconf['chat_system']['enabled'] && $sysconf['chat_system']['opac']) : ?>
    <div id="show-pchat2" style="position: fixed; bottom: 16px; right: 16px" class="shadow rounded">
        <button title="Chat" class="btn btn-primary"><i class="fas fa-comments mr-2"></i><?= __('Chat'); ?></button>
    </div>
<?php endif; ?>

<?php include LIB . "contents/chat.php"; ?>
<?php include "_modal_topic.php"; ?>
<?php include "_modal_advanced.php"; ?>
<?php include "_modal_social_media.php"; ?>

<script src="<?= JWB; ?>highlight.js"></script>
<?php if (isset($engine) && $searchableInJsArray = $this->generateKeywords($engine->searchable_fields)) : ?>
<script>
  $('.card-body > *').highlight(<?= $searchableInJsArray ?>);
</script>
<?php endif; ?>

<script src="<?php echo assets('one/vendor/aos/aos.js'); ?>"></script>
<script src="<?php echo assets(v('js/app.js')); ?>"></script>
<script src="<?php echo assets(v('js/app_jquery.js')); ?>"></script>
<?php include __DIR__ . "/../assets/js/vegas.js.php"; ?>
<script>
  if (window.AOS) { AOS.init({ once: true, duration: 700 }); }
</script>
<?php if ($sysconf['chat_system']['enabled'] && $sysconf['chat_system']['opac']) : ?>
    <script>
        $('#show-pchat').click(() => { $('.s-chat').hide(); $('#show-pchat2').show(); })
        $('#show-pchat2').click(() => { $('.s-chat').show(300, () => { $('#show-pchat2').hide(); }); })
    </script>
<?php endif; ?>
</body>
</html>
