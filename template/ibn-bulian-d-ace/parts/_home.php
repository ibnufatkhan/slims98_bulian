<?php
/**
 * Ibn Bulian D'ACE — uiux Figma prototype (Desktop frame 2:27)
 */
$library_name = $sysconf['library_name'] ?? 'SLiMS Library';
$library_sub = $sysconf['library_subname'] ?? 'No personal credit checks or founder guarantee.';
$img = CURRENT_TEMPLATE_DIR . 'assets/images/etran/';
?>

<div class="etran-shell" id="hero">
  <aside class="etran-sidebar">
    <div class="etran-nav-row">
      <a class="etran-brand" href="index.php">
        <img src="<?php echo $img; ?>logo-symbol.svg" alt="">
        <span><?php echo htmlspecialchars($library_name); ?></span>
      </a>
      <a class="etran-btn" href="#cari"><?php echo __('Get started'); ?></a>
    </div>

    <div class="etran-hero-copy">
      <h1 class="etran-hero-title">
        <?php echo __('Knowledge access made'); ?> <span class="accent"><?php echo __('simple'); ?></span>
      </h1>
      <p class="etran-hero-sub"><?php echo htmlspecialchars($library_sub); ?></p>
    </div>

    <div class="etran-offerings">
      <span class="etran-offerings-label"><?php echo __('Our offerings'); ?></span>
      <div class="etran-offerings-grid">
        <a class="etran-offer-card" href="#stats">
          <img src="<?php echo $img; ?>icons/productivity.svg" alt="" width="30" height="30">
          <?php echo __('Instant'); ?><br><?php echo __('Productivity'); ?>
        </a>
        <a class="etran-offer-card" href="#cari">
          <img src="<?php echo $img; ?>icons/expense.svg" alt="" width="30" height="30">
          <?php echo __('Collection'); ?><br><?php echo __('Management'); ?>
        </a>
        <a class="etran-offer-card" href="#software">
          <img src="<?php echo $img; ?>icons/technology.svg" alt="" width="30" height="30">
          <?php echo __('Advanced'); ?><br><?php echo __('Technology'); ?>
        </a>
      </div>
    </div>

    <nav class="etran-sidebar-links" aria-label="Quick links">
      <a href="#contact"><?php echo __('Contact'); ?></a>
      <a href="<?php echo $sysconf['template']['classic_instagram_link'] ?? '#contact'; ?>"><?php echo __('Social'); ?></a>
      <a href="#contact"><?php echo __('Address'); ?></a>
      <a href="index.php?p=visitor"><?php echo __('Legal Terms'); ?></a>
    </nav>
  </aside>

  <div class="etran-main">
    <section class="etran-header-block">
      <div class="etran-header-image">
        <img src="<?php echo $img; ?>hero-header.jpg" alt="<?php echo htmlspecialchars($library_name); ?>">
      </div>
      <h2 class="etran-header-tagline">
        <?php echo __('We escalate discovery efficiency'); ?><br><?php echo __('and reading productivity.'); ?>
      </h2>
      <div class="etran-partners" aria-label="Partners">
        <div class="etran-partner"><img src="<?php echo $img; ?>partners/blooming.svg" alt=""> Blooming</div>
        <div class="etran-partner"><img src="<?php echo $img; ?>partners/buildright.svg" alt=""> BuildRight</div>
        <div class="etran-partner"><img src="<?php echo $img; ?>partners/flowbot.svg" alt=""> Flowbot</div>
        <div class="etran-partner"><img src="<?php echo $img; ?>partners/expor.svg" alt=""> Expor</div>
        <div class="etran-partner"><img src="<?php echo $img; ?>partners/redo.svg" alt=""> Redo</div>
      </div>
    </section>

    <section class="etran-search" id="cari">
      <h2><?php echo __('Search the collection'); ?></h2>
      <?php include __DIR__ . '/_search-form.php'; ?>
    </section>

    <section class="etran-section" id="stats">
      <h2 class="etran-section-title"><?php echo __('Get more done in a week'); ?></h2>
      <p class="etran-section-lead"><?php echo __('Maximize your productivity with smarter tools designed to streamline your workflow to automate tasks, stay organized'); ?></p>
      <div class="etran-bento-wrap">
        <div class="etran-bento-row">
          <article class="etran-bento-card">
            <p class="etran-bento-num">2x</p>
            <span><?php echo __('Double Your Productivity'); ?></span>
          </article>
          <article class="etran-bento-card">
            <img class="chart" src="<?php echo $img; ?>bento/chart.svg" alt="" width="142" height="98">
            <span><?php echo __('Efficiency Increase Per Transfer'); ?></span>
          </article>
        </div>
        <div class="etran-bento-row">
          <article class="etran-bento-card tall-center">
            <img class="finance" src="<?php echo $img; ?>bento/finance.svg" alt="" width="124" height="112">
            <span><?php echo __('Centralize Your Finances'); ?></span>
          </article>
          <article class="etran-bento-card">
            <img class="percent" src="<?php echo $img; ?>bento/130.svg" alt="130%" width="199" height="68">
            <span><?php echo __('More Activity'); ?></span>
          </article>
        </div>
      </div>
    </section>

    <section class="etran-section" id="reliable">
      <h2 class="etran-section-title"><?php echo __('The Most Reliable App'); ?></h2>
      <div class="etran-features">
        <article class="etran-feature">
          <img src="<?php echo $img; ?>benefit-card.jpg" alt="">
          <h3><?php echo __('Scale Your Team, Not Your Card Expenses'); ?></h3>
          <p><?php echo __('Issue virtual and physical cards at no additional cost to support teams of any size.'); ?></p>
        </article>
        <article class="etran-feature">
          <img src="<?php echo $img; ?>benefit-form.jpg" alt="">
          <h3><?php echo __('Effortless Paper Tracking, Mobile Convenience'); ?></h3>
          <p><?php echo __('Get precise control—at scale—with the ability to lock any card and restrict any type of spend.'); ?></p>
        </article>
      </div>
    </section>

    <section class="etran-image-breaker">
      <img src="<?php echo $img; ?>image-breaker.jpg" alt="">
    </section>

    <section class="etran-section" id="software">
      <h2 class="etran-section-title"><?php echo __('First class software'); ?></h2>
      <p class="etran-section-lead"><?php echo __('Get real-time insights, seamless transactions, and advanced tools to manage your wealth effortlessly.'); ?></p>
      <div class="etran-soft-row">
        <div class="etran-soft-item">
          <img src="<?php echo $img; ?>icons/safe-storage.png" alt="">
          <p><?php echo __('Safe Storage'); ?></p>
        </div>
        <div class="etran-soft-item">
          <img src="<?php echo $img; ?>icons/secure.png" alt="">
          <p><?php echo __('Secure'); ?></p>
        </div>
        <div class="etran-soft-item">
          <img src="<?php echo $img; ?>icons/earn-interest.png" alt="">
          <p><?php echo __('Earn Interest'); ?></p>
        </div>
        <div class="etran-soft-item">
          <img src="<?php echo $img; ?>icons/family-plans.png" alt="">
          <p><?php echo __('Family Plans'); ?></p>
        </div>
      </div>
    </section>

    <div id="slims-home" class="etran-section etran-collections">
      <?php if ($sysconf['template']['classic_popular_collection'] ?? 1): ?>
        <section class="mb-5">
          <h2 class="etran-section-title" style="font-size:32px;"><?php echo __('Popular collections'); ?></h2>
          <slims-group-subject url="index.php?p=api/subject/popular"></slims-group-subject>
          <slims-collection url="index.php?p=api/biblio/popular"></slims-collection>
        </section>
      <?php endif; ?>
      <?php if ($sysconf['template']['classic_new_collection'] ?? 1): ?>
        <section>
          <h2 class="etran-section-title" style="font-size:32px;"><?php echo __('New and updated'); ?></h2>
          <slims-group-subject url="index.php?p=api/subject/latest"></slims-group-subject>
          <slims-collection url="index.php?p=api/biblio/latest"></slims-collection>
        </section>
      <?php endif; ?>
    </div>

    <section class="etran-cta-block" id="download">
      <div class="etran-cta-card">
        <img src="<?php echo $img; ?>cta-bg.jpg" alt="">
        <div class="etran-cta-copy">
          <h2><?php echo __('Open the catalog and manage everything from your phone.'); ?></h2>
          <a class="etran-btn" href="#cari"><?php echo __('Get started'); ?></a>
        </div>
      </div>
    </section>

    <section class="etran-section" id="contact">
      <h2 class="etran-section-title"><?php echo __('Contact'); ?></h2>
      <p class="etran-section-lead" style="max-width:520px;">
        <?php echo strip_tags($sysconf['template']['classic_map_desc'] ?? $library_sub); ?>
      </p>
      <?php if (!empty($sysconf['template']['classic_map_link'])): ?>
        <div class="mt-4" style="border-radius:10px; overflow:hidden; height:320px;">
          <iframe src="<?php echo $sysconf['template']['classic_map_link']; ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
      <?php endif; ?>
    </section>
  </div>
</div>
