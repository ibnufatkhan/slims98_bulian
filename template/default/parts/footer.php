<?php
# @Author: Waris Agung Widodo <user>
# @Date:   2018-01-23T11:26:05+07:00
# @Email:  ido.alit@gmail.com
# @Filename: footer.php
# Customized for Perpustakaan Soepardjo Roestam (BSKDN)
?>

<footer id="footer">

    <div class="footer-top">
      <div class="container">
        <div class="row">
		  <div class="col-lg-3 col-md-6 footer-contact">
            <img src="template/default/assets/one/img/logo.png" alt="" class="img-fluid right" width="70%">
            
          </div>
          <div class="col-lg-3 col-md-6 footer-contact">
            
            <p>
              Jl. Kramat Raya No.132, RW.9, Kenari, Kec. Senen, Kota Jakarta Pusat, Daerah Khusus Ibukota Jakarta 10430<br>
              Indonesia <br><br>
              <strong>Phone:</strong> (021) 3101953, 3101955, 3901071, 3901072<br>
              <strong>Email:</strong> badanlitbang@kemendagri.go.id<br>
            </p>
          </div>

          <div class="col-lg-2 col-md-6 footer-links">
            <h4>Related Link</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="http://perpustakaan.kemendagri.go.id">Perpustakaan Kemendagri</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="http://www.pnri.go.id">Perpustakaan Nasional</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="http://perpustakaan.dpr.go.id">Perpustakaan DPR</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="http://lib.unj.ac.id">Perpustakaan UNJ</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="http://lipi.go.id">LIPI</a></li>
            </ul>
          </div>
		<!--
          <div class="col-lg-3 col-md-6 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Design</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Web Development</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Product Management</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Marketing</a></li>
              <li><i class="bx bx-chevron-right"></i> <a href="#">Graphic Design</a></li>
            </ul>
          </div>

          <div class="col-lg-4 col-md-6 footer-newsletter">
            <h4>Join Our Newsletter</h4>
            <p>Tamen quem nulla quae legam multos aute sint culpa legam noster magna</p>
            <form action="" method="post">
              <input type="email" name="email"><input type="submit" value="Subscribe">
            </form>
          </div>
		-->
        </div>
      </div>
    </div>

    <div class="container d-md-flex py-4">

      <div class="me-md-auto text-center text-md-start">
        <div class="copyright">
          &copy; Template by <strong><span>SLiMS Community 2026</span></strong>
        </div>
        <div class="credits">
          <!-- All the links in the footer should remain intact. -->
          <!-- You can delete the links only if you purchased the pro version. -->
          <!-- Licensing information: https://bootstrapmade.com/license/ -->
          <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/onepage-multipurpose-bootstrap-template/ -->
          
        </div>
      </div>
      <div class="social-links text-center text-md-right pt-3 pt-md-0">
        <a href="https://www.youtube.com/channel/UCnPNf4BtDe90oblU6CKyf7w" class="youtube"><i class="bx bxl-youtube"></i></a>
        <a href="https://twitter.com/litbangKDN" class="twitter"><i class="bx bxl-twitter"></i></a>
        <a href="https://www.facebook.com/badanlitbangkemendagri" class="facebook"><i class="bx bxl-facebook"></i></a>
        <a href="https://www.instagram.com/soepardjoroestam.lib" target="_blank" rel="noopener" class="instagram"><i class="bx bxl-instagram"></i></a>
        
      </div>
    </div>
  </footer>

<!-- End Footer -->

  <div id="preloader"></div>
  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="template/default/assets/one/vendor/purecounter/purecounter.js"></script>
  <script src="template/default/assets/one/vendor/aos/aos.js"></script>
  <script src="template/default/assets/one/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="template/default/assets/one/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="template/default/assets/one/vendor/isotope-layout/isotope.pkgd.min.js"></script>
  <script src="template/default/assets/one/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="template/default/assets/one/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="template/default/assets/one/js/main.js"></script>









<?php if ($sysconf['chat_system']['enabled'] && $sysconf['chat_system']['opac']) : ?>
    <div id="show-pchat2" style="position: fixed; bottom: 16px; right: 16px" class="shadow rounded">
        <button title="Chat" class="btn btn-primary"><i class="fas fa-comments mr-2"></i><?= __('Chat'); ?></button>
    </div>
<?php endif; ?>

<?php
// Chat Engine
include LIB . "contents/chat.php"; ?>

<!-- // Load modal -->
<?php include "_modal_topic.php"; ?>
<?php include "_modal_advanced.php"; ?>
<?php include "_modal_social_media.php"; ?>

<!-- // Load highlight -->
<script src="<?= JWB; ?>highlight.js"></script>
<?php if(isset($engine) && $searchableInJsArray = $this->generateKeywords($engine->searchable_fields)) : ?>
<script>
  $('.card-body > *').highlight(<?= $searchableInJsArray ?>);
</script>
<?php endif; ?>

<!-- // load our vue app.js -->
<script src="<?php echo assets(v('js/app.js')); ?>"></script>
<script src="<?php echo assets(v('js/app_jquery.js')); ?>"></script>
<?php include __DIR__ . "/../assets/js/vegas.js.php"; ?>
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

