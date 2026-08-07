<?php
/**
 * Ibn Bulian D'ACE — Etran-inspired home for SLiMS OPAC
 * Visual reference: Figma Sites "Ibn-bulian-d-ace-template"
 */
$library_name = $sysconf['library_name'] ?? 'SLiMS Library';
$library_sub = $sysconf['library_subname'] ?? 'Senayan Library Management System';
$theme_base = CURRENT_TEMPLATE_DIR;
$img = $theme_base . 'assets/images/etran/';
?>

<section class="etran-hero" id="hero">
  <div class="etran-hero-left">
    <div class="etran-hero-left-top etran-reveal">
      <a class="etran-brand" href="index.php" aria-label="<?php echo htmlspecialchars($library_name); ?>">
        <span class="etran-mark" aria-hidden="true">
          <svg viewBox="0 0 24 24"><path d="M4 9h11"/><path d="M12 5l4 4-4 4"/><path d="M20 15H9"/><path d="M12 19l-4-4 4-4"/></svg>
        </span>
        <span><?php echo htmlspecialchars($library_name); ?></span>
      </a>
      <a class="etran-btn" href="#cari"><?php echo __('Get started'); ?></a>
    </div>

    <div class="etran-hero-copy etran-reveal etran-reveal-delay-1">
      <h1><?php echo __('Knowledge Access Made Simple'); ?></h1>
      <p><?php echo htmlspecialchars($library_sub); ?>. <?php echo __('Search the catalog, borrow collections, and explore digital resources without friction.'); ?></p>
    </div>

    <div class="etran-offerings etran-reveal etran-reveal-delay-2">
      <span class="etran-offerings-label"><?php echo __('Our offerings'); ?></span>
      <div class="etran-offerings-grid">
        <a class="etran-offer-card" href="#cari">
          <div class="etran-offer-icon"><i class="bi bi-lightning-charge-fill"></i></div>
          <strong><?php echo __('Instant Search'); ?></strong>
        </a>
        <a class="etran-offer-card" href="index.php?p=member">
          <div class="etran-offer-icon"><i class="bi bi-shield-check"></i></div>
          <strong><?php echo __('Member Access'); ?></strong>
        </a>
        <a class="etran-offer-card" href="index.php?p=news">
          <div class="etran-offer-icon"><i class="bi bi-globe2"></i></div>
          <strong><?php echo __('Library News'); ?></strong>
        </a>
      </div>
    </div>

    <nav class="etran-hero-left-links etran-reveal etran-reveal-delay-3" aria-label="Footer quick links">
      <a href="#contact"><?php echo __('Contact'); ?></a>
      <a href="<?php echo $sysconf['template']['classic_instagram_link'] ?? '#'; ?>" target="_blank" rel="noopener"><?php echo __('Social'); ?></a>
      <a href="#contact"><?php echo __('Address'); ?></a>
      <a href="index.php?p=visitor"><?php echo __('Visitor'); ?></a>
    </nav>
  </div>

  <div class="etran-hero-right">
    <div class="etran-hero-media etran-reveal">
      <img src="<?php echo $img; ?>hero-person.jpg" alt="<?php echo htmlspecialchars($library_name); ?>">
      <span class="etran-toast etran-toast-1"><i class="bi bi-check-circle-fill"></i> <?php echo __('Book reserved!'); ?></span>
      <span class="etran-toast etran-toast-2"><i class="bi bi-journal-check"></i> <?php echo __('Loan renewed!'); ?></span>
      <span class="etran-toast etran-toast-3"><i class="bi bi-bookmark-check-fill"></i> <?php echo __('New arrival!'); ?></span>
    </div>
    <p class="etran-hero-tagline etran-reveal etran-reveal-delay-1">
      <?php echo __('We escalate discovery efficiency and reading productivity.'); ?>
    </p>
    <div class="etran-partners etran-reveal etran-reveal-delay-2" aria-label="Partners">
      <div class="etran-partner"><i class="bi bi-flower1"></i> Blooming</div>
      <div class="etran-partner"><i class="bi bi-check2-circle"></i> BuildRight</div>
      <div class="etran-partner"><i class="bi bi-robot"></i> Flowbot</div>
      <div class="etran-partner"><i class="bi bi-box"></i> EXPOR</div>
      <div class="etran-partner"><i class="bi bi-arrow-repeat"></i> Redo</div>
    </div>
  </div>
</section>

<section class="etran-search" id="cari">
  <div class="etran-search-inner etran-reveal">
    <h2><?php echo __('Search the collection'); ?></h2>
    <?php include __DIR__ . '/_search-form.php'; ?>
  </div>
</section>

<section class="etran-section etran-stats" id="stats">
  <div class="etran-section-inner">
    <div class="etran-kicker"><?php echo __('Productivity'); ?></div>
    <h2><?php echo __('Get More Done In A Week'); ?></h2>
    <p class="etran-lead"><?php echo __('Maximize research productivity with smarter catalog tools designed to streamline discovery, stay organized, and automate routine library tasks.'); ?></p>
    <div class="etran-stats-grid">
      <article class="etran-stat-card etran-reveal">
        <div class="etran-stat-visual"><i class="bi bi-graph-up-arrow"></i></div>
        <div class="num">2x</div>
        <span><?php echo __('Double Your Discovery'); ?></span>
      </article>
      <article class="etran-stat-card etran-reveal etran-reveal-delay-1">
        <div class="etran-stat-visual"><i class="bi bi-bar-chart-fill"></i></div>
        <div class="num">130%</div>
        <span><?php echo __('More Activity'); ?></span>
      </article>
      <article class="etran-stat-card etran-reveal etran-reveal-delay-2">
        <div class="etran-stat-visual"><i class="bi bi-arrow-left-right"></i></div>
        <div class="num">∞</div>
        <span><?php echo __('Centralize Your Library'); ?></span>
      </article>
    </div>
  </div>
</section>

<section class="etran-section" id="reliable">
  <div class="etran-section-inner">
    <div class="etran-kicker"><?php echo __('Platform'); ?></div>
    <h2><?php echo __('The Most Reliable Catalog'); ?></h2>
    <p class="etran-lead"><?php echo __('Effortless collection tracking and mobile convenience — get precise control at scale for loans, membership, and digital resources.'); ?></p>
    <div class="etran-features-grid">
      <article class="etran-feature-card etran-reveal">
        <img src="<?php echo $img; ?>hero-person.jpg" alt="">
        <div class="body">
          <h3><?php echo __('Effortless Paper Tracking'); ?></h3>
          <p><?php echo __('Keep bibliographic records, loans, and member history organized in one place.'); ?></p>
        </div>
      </article>
      <article class="etran-feature-card etran-reveal etran-reveal-delay-1">
        <img src="<?php echo $img; ?>productivity.jpg" alt="">
        <div class="body">
          <h3><?php echo __('Mobile Convenience'); ?></h3>
          <p><?php echo __('Browse, search, and manage memberships from any device with a responsive OPAC.'); ?></p>
        </div>
      </article>
      <article class="etran-feature-card etran-reveal etran-reveal-delay-2">
        <img src="<?php echo $img; ?>cta-phone.jpg" alt="">
        <div class="body">
          <h3><?php echo __('Secure Access'); ?></h3>
          <p><?php echo __('Member authentication and privilege controls protect personal and library data.'); ?></p>
        </div>
      </article>
    </div>
  </div>
</section>

<section class="etran-section etran-software" id="software">
  <div class="etran-section-inner">
    <h2 class="etran-serif"><?php echo __('First Class Software'); ?></h2>
    <p class="etran-lead"><?php echo __('Get real-time insights, seamless transactions, and advanced tools to manage your library effortlessly.'); ?></p>
    <div class="etran-soft-grid">
      <article class="etran-soft-card">
        <div class="etran-soft-icon"><i class="bi bi-wallet2"></i></div>
        <strong><?php echo __('Digital Archives'); ?></strong>
      </article>
      <article class="etran-soft-card">
        <div class="etran-soft-icon"><i class="bi bi-fingerprint"></i></div>
        <strong><?php echo __('Secure'); ?></strong>
      </article>
      <article class="etran-soft-card">
        <div class="etran-soft-icon"><i class="bi bi-graph-up"></i></div>
        <strong><?php echo __('Circulation Insights'); ?></strong>
      </article>
      <article class="etran-soft-card">
        <div class="etran-soft-icon"><i class="bi bi-people"></i></div>
        <strong><?php echo __('Family / Group Plans'); ?></strong>
      </article>
    </div>
  </div>
</section>

<div id="slims-home" class="etran-section etran-collections">
  <div class="etran-section-inner">
    <?php if ($sysconf['template']['classic_popular_collection'] ?? 1): ?>
      <section class="mb-5">
        <h2 style="font-size:1.75rem;"><?php echo __('Popular collections'); ?></h2>
        <p class="etran-lead mb-4"><?php echo __('Our library\'s line of collection that have been favoured by our users.'); ?></p>
        <slims-group-subject url="index.php?p=api/subject/popular"></slims-group-subject>
        <slims-collection url="index.php?p=api/biblio/popular"></slims-collection>
      </section>
    <?php endif; ?>

    <?php if ($sysconf['template']['classic_new_collection'] ?? 1): ?>
      <section>
        <h2 style="font-size:1.75rem;"><?php echo __('New and updated'); ?></h2>
        <p class="etran-lead mb-4"><?php echo __('These are new collections list fresh from our processing oven.'); ?></p>
        <slims-group-subject url="index.php?p=api/subject/latest"></slims-group-subject>
        <slims-collection url="index.php?p=api/biblio/latest"></slims-collection>
      </section>
    <?php endif; ?>
  </div>
</div>

<section class="etran-cta" id="download">
  <div class="etran-cta-inner etran-reveal">
    <h2><?php echo __('Open the catalog and manage everything from anywhere'); ?></h2>
    <a class="etran-btn" href="#cari"><?php echo __('Get started'); ?></a>
  </div>
</section>

<section class="etran-section" id="contact" style="background:#fff;">
  <div class="etran-section-inner">
    <h2><?php echo __('Contact'); ?></h2>
    <p class="etran-lead">
      <?php
      echo $sysconf['template']['classic_map_desc']
        ?? 'Library address and contact details can be configured from System → Theme → Customize.';
      ?>
    </p>
    <?php if (!empty($sysconf['template']['classic_map_link'])): ?>
      <div class="mt-4 rounded overflow-hidden" style="border-radius:18px; height:320px;">
        <iframe src="<?php echo $sysconf['template']['classic_map_link']; ?>" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
      </div>
    <?php endif; ?>
  </div>
</section>
