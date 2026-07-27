<?php
/**
 * Public star rating widget for OPAC landing page footer
 *
 * Expected variables: $summary, $reviews, $csrf, $submitUrl
 */
if (!defined('INDEX_AUTH')) {
    die('can not access this file directly');
}

$total = (int) ($summary['total'] ?? 0);
$average = (float) ($summary['average'] ?? 0);
$breakdown = $summary['breakdown'] ?? [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
?>
<section id="star-rating-section" class="sr-section" aria-labelledby="sr-title">
    <div class="sr-container">
        <h2 id="sr-title" class="sr-title">Peringkat Bintang &amp; Ulasan</h2>
        <p class="sr-subtitle">Bagikan penilaian Anda terhadap layanan perpustakaan kami.</p>

        <div class="sr-summary">
            <div class="sr-average">
                <div class="sr-average-label">Peringkat Rata-Rata</div>
                <?php echo star_rating_stars_html($average, 'lg'); ?>
                <div class="sr-average-text">
                    <strong><?php echo number_format($average, 1); ?></strong> dari 5 bintang
                </div>
                <div class="sr-total-text"><?php echo $total; ?> ulasan</div>
            </div>

            <div class="sr-breakdown">
                <?php for ($star = 5; $star >= 1; $star--):
                    $count = (int) ($breakdown[$star] ?? 0);
                    $percent = $total > 0 ? round(($count / $total) * 100) : 0;
                ?>
                <div class="sr-breakdown-row">
                    <span class="sr-breakdown-label"><?php echo $star; ?> bintang</span>
                    <div class="sr-breakdown-bar">
                        <span style="width: <?php echo $percent; ?>%"></span>
                    </div>
                    <span class="sr-breakdown-count"><?php echo $count; ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="sr-form-wrap">
            <h3 class="sr-form-title">Kirim Ulasan</h3>
            <div id="sr-alert" class="sr-alert" role="status" aria-live="polite" hidden></div>
            <form id="star-rating-form" method="post" action="<?php echo htmlspecialchars($submitUrl); ?>" novalidate>
                <?php echo $csrf; ?>
                <div class="sr-form-grid">
                    <div class="sr-field">
                        <label for="sr-name">Nama</label>
                        <input type="text" id="sr-name" name="reviewer_name" maxlength="100" required placeholder="Nama Anda">
                    </div>
                    <div class="sr-field">
                        <label for="sr-rating">Rating</label>
                        <div class="sr-rating-input" id="sr-rating-input">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="sr-rate-btn" data-value="<?php echo $i; ?>" aria-label="<?php echo $i; ?> bintang">
                                <i class="fa fa-star-o"></i>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" name="rating" id="sr-rating" value="" required>
                    </div>
                    <div class="sr-field sr-field-full">
                        <label for="sr-comment">Komentar</label>
                        <textarea id="sr-comment" name="comment" rows="3" maxlength="1000" required placeholder="Tulis komentar Anda..."></textarea>
                    </div>
                </div>
                <button type="submit" class="sr-submit" id="sr-submit">Kirim Ulasan</button>
            </form>
        </div>

        <div class="sr-list-wrap">
            <h3 class="sr-list-title">Daftar Ulasan</h3>
            <?php if (empty($reviews)): ?>
                <p class="sr-empty">Belum ada ulasan. Jadilah yang pertama memberikan penilaian!</p>
            <?php else: ?>
                <ul class="sr-list">
                    <?php foreach ($reviews as $review): ?>
                    <li class="sr-item">
                        <div class="sr-item-head">
                            <strong class="sr-item-name"><?php echo htmlspecialchars($review['reviewer_name']); ?></strong>
                            <?php echo star_rating_stars_html((float) $review['rating'], 'sm'); ?>
                        </div>
                        <p class="sr-item-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></p>
                        <div class="sr-item-meta"><?php echo htmlspecialchars(star_rating_relative_time($review['created_at'])); ?></div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
