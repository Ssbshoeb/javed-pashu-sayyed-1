<?php
/* Media & Press: external articles, interviews and coverage. Data: 'media' in config/practice.php. */

defined('APP_RUNNING') or exit('No direct access.');

$sources = [];
$topics = [];
foreach ($items as $m) {
    $sources[(string) ($m["source"] ?? "Media")] ??= (string) $m["url"];
    if (!empty($m['topic'])) {
        $topics[(string) $m['topic']] = true;
    }
}
?>
<section class="hero" style="<?= hero_image_style('/img/hero-2.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Media</p>
        <h1 style="max-width:22ch;">In the <span class="mhead__gold">media</span></h1>
        <p class="lede mhead__lede">
            Coverage and commentary featuring the advocate, as published by news outlets.
        </p>
        <ul class="mhead__stats" aria-label="Coverage summary">
            <li><strong><?= count($items) ?></strong> articles</li>
            <li><strong><?= count($sources) ?></strong> publications</li>
            <li><strong>Sep&ndash;Oct 2026</strong></li>
        </ul>

        <?php if ($sources !== []): ?>
            <div class="mstrip">
                <span class="mstrip__label">Featured in</span>
                <?php foreach ($sources as $name => $srcUrl): ?>
                    <?php
                    $brand = practice('mediaSources.' . $name);
                    $mark = is_array($brand) ? $brand['mark'] : strtoupper(substr($name, 0, 2));
                    $color = is_array($brand) ? $brand['color'] : '#8A6A23';
                    ?>
                    <a class="msrc" href="<?= e($srcUrl) ?>" target="_blank" rel="noopener noreferrer" style="--mc:<?= e($color) ?>">
                        <span class="msrc__mark" aria-hidden="true"><?= e($mark) ?></span><?= e($name) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="shell">
        <?php if ($items === []): ?>
            <p>Media coverage will be listed here shortly.</p>
        <?php else: ?>
            <?php if (count($topics) > 1): ?>
                <div class="mfilter" role="group" aria-label="Filter by topic">
                    <button type="button" data-filter="" aria-pressed="true">All (<?= count($items) ?>)</button>
                    <?php foreach (array_keys($topics) as $t): ?>
                        <button type="button" data-filter="<?= e($t) ?>" aria-pressed="false"><?= e($t) ?></button>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="mlist" id="mgrid">
                <?php foreach ($items as $m): ?>
                    <?php require APP_PATH . '/views/partials/media-card.php'; ?>
                <?php endforeach; ?>
            </div>

            <script>
            (function () {
                var bar = document.querySelector('.mfilter');
                if (!bar) return;
                bar.addEventListener('click', function (ev) {
                    var b = ev.target.closest('button');
                    if (!b) return;
                    var f = b.getAttribute('data-filter');
                    bar.querySelectorAll('button').forEach(function (x) {
                        x.setAttribute('aria-pressed', x === b ? 'true' : 'false');
                    });
                    document.querySelectorAll('#mgrid .mrow').forEach(function (c) {
                        c.hidden = f !== '' && c.getAttribute('data-topic') !== f;
                    });
                });
            })();
            </script>
        <?php endif; ?>
    </div>
</section>
