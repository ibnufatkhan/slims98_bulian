<?php
/**
 * @Created by          : Waris Agung Widodo (ido.alit@gmail.com)
 * @Date                : 2019-01-29 10:43
 * @File name           : _other.php
 */

?>

<div class="etran-page result-search">
  <div class="etran-page-card">
    <section class="mb-4">
      <?php include '_search-form.php'; ?>
    </section>
    <section>
      <?php
      if ($_GET['p'] !== 'show_detail') {
        echo '<h2 class="mb-4">' . $page_title . '</h2><hr>';
        if ($_GET['p'] === 'librarian') {
          echo '<div class="flex flex-row flex-wrap">' . $main_content . '</div>';
        } else {
          echo $main_content;
        }
      } else {
        echo $main_content;
      }
      ?>
    </section>
  </div>
</div>
