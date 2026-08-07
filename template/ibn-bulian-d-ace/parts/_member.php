<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 2019-01-30 20:58
 * @File name           : _member.php
 */

?>

<div class="etran-page">
<?php if ($is_login) : ?>
    <div class="etran-page-card member-area">
      <?php echo $main_content; ?>
    </div>
<?php else: ?>
    <div class="etran-page-card page-member-area">
      <section class="mb-4">
        <?php include '_search-form.php'; ?>
      </section>
      <?php echo $main_content; ?>
    </div>
<?php endif; ?>
</div>
