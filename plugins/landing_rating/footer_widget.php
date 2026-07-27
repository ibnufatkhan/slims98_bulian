<?php
/**
 * Footer widget: Landing Page Rating
 *
 * Variabel opsional:
 * - $landing_rating_embed_mode (bool): hanya output HTML section (tanpa link/script)
 */

defined('INDEX_AUTH') or die('Direct access not allowed!');

require_once __DIR__ . '/helper.php';

try {
    $stats = landing_rating_get_stats();
    $items = landing_rating_get_visible(12);
} catch (Throwable $e) {
    // Tabel belum tersedia (plugin belum diaktifkan / migrasi belum jalan)
    return;
}

$embed = !empty($landing_rating_embed_mode);
$csrf = landing_rating_token();
$submitUrl = SWB . 'index.php?p=landing_rating';
$avg = $stats['average'];
$total = $stats['total'];
$distribution = $stats['distribution'];
$maxDist = max(1, max($distribution));
$assetBase = SWB . 'plugins/landing_rating/assets';

if (!$embed && !defined('LANDING_RATING_CSS_LOADED')) {
    define('LANDING_RATING_CSS_LOADED', true);
    echo '<link rel="stylesheet" href="' . $assetBase . '/landing_rating.css?v=1.0.3">';
}
?>
<section class="lr-section" id="landing-rating" aria-labelledby="lr-heading">
    <div class="lr-container">
        <div class="lr-header">
            <h2 id="lr-heading" class="lr-title"><?= __('Ulasan Pengunjung'); ?></h2>
            <p class="lr-subtitle"><?= __('Bagikan penilaian Anda untuk layanan perpustakaan kami.'); ?></p>
        </div>

        <div class="lr-summary">
            <div class="lr-average">
                <div class="lr-average-label"><?= __('Peringkat Rata-Rata'); ?></div>
                <div id="lr-avg-stars"><?= landing_rating_stars_html($avg); ?></div>
                <div class="lr-average-value">
                    <span class="lr-average-number" id="lr-avg-number"><?= number_format($avg, 1); ?></span>
                    <span class="lr-average-max"><?= __('dari 5 bintang'); ?></span>
                </div>
                <div class="lr-total" id="lr-total-label">
                    <?= sprintf(__('%d ulasan'), $total); ?>
                </div>
            </div>

            <div class="lr-distribution" id="lr-distribution" aria-label="<?= __('Distribusi rating'); ?>">
                <?php for ($star = 5; $star >= 1; $star--):
                    $count = $distribution[$star] ?? 0;
                    $pct = $total > 0 ? round(($count / $maxDist) * 100) : 0;
                ?>
                <div class="lr-dist-row" data-star="<?= $star; ?>">
                    <span class="lr-dist-label"><?= sprintf(__('%d bintang'), $star); ?></span>
                    <div class="lr-dist-bar">
                        <span class="lr-dist-fill" style="width: <?= $pct; ?>%"></span>
                    </div>
                    <span class="lr-dist-count"><?= $count; ?></span>
                </div>
                <?php endfor; ?>
            </div>

            <form class="lr-form" id="lr-form" autocomplete="off">
                <input type="hidden" name="csrf_token" id="lr-csrf" value="<?= landing_rating_e($csrf); ?>">
                <input type="hidden" name="action" value="submit">

                <div class="lr-form-title"><?= __('Tulis Ulasan'); ?></div>

                <label class="lr-label" for="lr-name"><?= __('Nama'); ?></label>
                <input class="lr-input" type="text" id="lr-name" name="visitor_name" maxlength="100" required
                       placeholder="<?= __('Nama Anda'); ?>">

                <label class="lr-label" for="lr-comment"><?= __('Komentar'); ?></label>
                <textarea class="lr-textarea" id="lr-comment" name="comment" maxlength="1000" rows="3" required
                          placeholder="<?= __('Bagikan komentar Anda...'); ?>"></textarea>

                <label class="lr-label"><?= __('Rating'); ?></label>
                <div class="lr-rating-input" id="lr-rating-input" role="radiogroup" aria-label="<?= __('Pilih rating'); ?>">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" id="lr-rate-<?= $i; ?>" value="<?= $i; ?>" <?= $i === 5 ? 'checked' : ''; ?>>
                    <label for="lr-rate-<?= $i; ?>" title="<?= $i; ?> bintang">&#9733;</label>
                    <?php endfor; ?>
                </div>

                <button type="submit" class="lr-submit" id="lr-submit"><?= __('Kirim Ulasan'); ?></button>
                <div class="lr-message" id="lr-message" role="status" aria-live="polite"></div>
            </form>
        </div>

        <div class="lr-list" id="lr-list">
            <?php if (!$items): ?>
                <p class="lr-empty" id="lr-empty"><?= __('Belum ada ulasan. Jadilah yang pertama!'); ?></p>
            <?php else: ?>
                <?php foreach ($items as $item): ?>
                <article class="lr-item">
                    <div class="lr-item-head">
                        <strong class="lr-item-name"><?= landing_rating_e($item['visitor_name']); ?></strong>
                        <?= landing_rating_stars_html((int) $item['rating']); ?>
                    </div>
                    <p class="lr-item-comment"><?= nl2br(landing_rating_e($item['comment'])); ?></p>
                    <time class="lr-item-date" datetime="<?= landing_rating_e($item['created_at']); ?>">
                        <?= landing_rating_e($item['created_at']); ?>
                    </time>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php if (!$embed): ?>
<script>
window.LANDING_RATING = {
    submitUrl: <?= json_encode($submitUrl, JSON_UNESCAPED_SLASHES); ?>,
    labels: {
        reviews: <?= json_encode(__('%d ulasan')); ?>,
        empty: <?= json_encode(__('Belum ada ulasan. Jadilah yang pertama!')); ?>,
        sending: <?= json_encode(__('Mengirim...')); ?>,
        send: <?= json_encode(__('Kirim Ulasan')); ?>,
        error: <?= json_encode(__('Gagal mengirim ulasan. Coba lagi.')); ?>
    }
};
</script>
<script src="<?= $assetBase ?>/landing_rating.js?v=1.0.3"></script>
<?php endif; ?>
