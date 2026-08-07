<?php
/**
 * Footer — Ibn Bulian D'ACE (Etran)
 */
$library_name = $sysconf['library_name'] ?? 'SLiMS Library';
?>
<footer class="etran-footer" id="footer">
  <div class="etran-footer-grid">
    <div>
      <a class="etran-brand" href="index.php" style="margin-bottom:1rem;">
        <span class="etran-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M4 9h11"/><path d="M12 5l4 4-4 4"/><path d="M20 15H9"/><path d="M12 19l-4-4 4-4"/></svg>
        </span>
        <span><?php echo htmlspecialchars($library_name); ?></span>
      </a>
      <p style="margin:0.75rem 0 0; max-width:28ch; line-height:1.55;">
        <?php echo strip_tags($sysconf['template']['classic_footer_about_us'] ?? 'SLiMS — Senayan Library Management System.'); ?>
      </p>
    </div>
    <div>
      <h4><?php echo __('Contact'); ?></h4>
      <ul>
        <li><a href="mailto:hello@example.com">hello@example.com</a></li>
        <li><a href="<?php echo $sysconf['template']['classic_instagram_link'] ?? '#'; ?>" target="_blank" rel="noopener">Instagram</a></li>
        <li><a href="<?php echo $sysconf['template']['classic_twitter_link'] ?? '#'; ?>" target="_blank" rel="noopener">X</a></li>
        <li><a href="<?php echo $sysconf['template']['classic_youtube_link'] ?? '#'; ?>" target="_blank" rel="noopener">YouTube</a></li>
      </ul>
    </div>
    <div>
      <h4><?php echo __('Legal'); ?></h4>
      <ul>
        <li><a href="index.php?p=visitor"><?php echo __('Visitor'); ?></a></li>
        <li><a href="index.php?p=member"><?php echo __('Member Area'); ?></a></li>
        <li><a href="index.php?p=news"><?php echo __('News'); ?></a></li>
      </ul>
    </div>
  </div>
  <div class="etran-footer-bottom">
    <div>&copy; <?php echo date('Y'); ?> <strong><?php echo htmlspecialchars($library_name); ?></strong> · Ibn Bulian D'ACE</div>
    <div id="visitor-stats-slot"></div>
  </div>
</footer>

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
        $('#show-pchat').click(() => {
            $('.s-chat').hide()
            $('#show-pchat2').show()
        })
        $('#show-pchat2').click(() => {
            $('.s-chat').show(300, () => {
                $('#show-pchat2').hide()
            })
        })
    </script>
<?php endif; ?>
</body>
</html>
