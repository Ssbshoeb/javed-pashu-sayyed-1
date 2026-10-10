<?php
/* Ported from src/app/practice-areas/[slug]/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$areas = practice('areas');
?>
<section class="hero" style="<?= hero_image_style('/img/area-' . $area['image'] . '.webp') ?>">
    <div class="shell hero__grid">
        <div>
            <p class="breadcrumb">
                <a href="<?= e(route('home')) ?>">Home</a> &rsaquo;
                <a href="<?= e(route('areas')) ?>">Practice Areas</a> &rsaquo; <?= e($area['title']) ?>
            </p>
            <h1><?= e($area['title']) ?></h1>
            <p class="lede" style="margin-top:1rem;"><?= e($area['summary']) ?></p>
        </div>

        <div class="hero__rail">
            <p class="eyebrow">Other areas</p>
            <ul class="list-check">
                <?php foreach (area_slugs() as $other): ?>
                    <?php if ($other === $slug) { continue; } ?>
                    <li><a href="<?= e(route('area', $other)) ?>"><?= e($areas[$other]['title']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<section class="section">
    <div class="shell split">
        <div class="stack stack--lg">
            <div>
                <h2 style="margin-bottom:0.9rem;">Overview</h2>
                <p><?= e($area['intro']) ?></p>
            </div>

            <div>
                <h2 style="margin-bottom:0.9rem;">Matters ordinarily handled</h2>
                <ul class="list-check">
                    <?php foreach ($area['matters'] as $matter): ?>
                        <li><?= e($matter) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h2 style="margin-bottom:0.9rem;">How a matter usually proceeds</h2>
                <ol class="list-steps">
                    <?php foreach ($area['process'] as $step): ?>
                        <li><?= e($step) ?></li>
                    <?php endforeach; ?>
                </ol>
                <p style="font-size:0.86rem;color:var(--muted);margin-top:0.6rem;">
                    This is the ordinary course. Any particular matter may take a different one.
                </p>
            </div>

            <div>
                <h2 style="margin-bottom:0.9rem;">Questions clients ask</h2>
                <div class="faq">
                    <?php foreach ($area['faqs'] as $faq): ?>
                        <details>
                            <summary><?= e($faq['q']) ?></summary>
                            <div class="faq__body"><?= e($faq['a']) ?></div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <?php /*
            No enquiry form here by design. This is an informational page; the
            intake forms live at /contact, /case-evaluation and /request-appointment
            so the site reads as a reference rather than a lead-capture funnel.
        */ ?>
        <aside class="stack">
            <div class="tile">
                <h3 style="margin-bottom:0.5rem;">Governing law</h3>
                <ul class="list-check" style="margin-top:10px;margin-bottom:0;">
                    <?php foreach ($area['statutes'] as $statute): ?>
                        <li><?= e($statute) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tile">
                <h3 style="margin-bottom:0.5rem;">Forums</h3>
                <ul class="list-check" style="margin-top:10px;margin-bottom:0;">
                    <?php foreach ($area['forums'] as $forum): ?>
                        <li><?= e($forum) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="tile">
                <h3 style="margin-bottom:0.5rem;">Bring to a consultation</h3>
                <ul class="list-check" style="margin-top:10px;margin-bottom:0;">
                    <?php foreach ($area['bring'] as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="notice">
                <strong>To discuss a matter in this area.</strong> Use the
                <a href="<?= e(route('caseEvaluation')) ?>">case evaluation form</a> to send the outline in
                writing, or <a href="<?= e(route('appointment')) ?>">request an appointment</a> at the
                chamber.
            </div>

            <?php
            $related = array_filter(practice('articles'), static fn (array $a): bool => ($a['area'] ?? null) === $slug);
            ?>
            <?php if ($related): ?>
                <div class="tile">
                    <h3 style="margin-bottom:0.5rem;">Related notes</h3>
                    <ul class="list-check" style="margin-top:10px;margin-bottom:0;">
                        <?php foreach ($related as $aSlug => $a): ?>
                            <li><a href="<?= e(route('article', $aSlug)) ?>"><?= e($a['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="btn-row">
                <a class="btn btn--primary" href="<?= e(route('appointment')) ?>">Request an appointment</a>
                <a class="btn btn--ghost" href="<?= e(route('contact')) ?>">Contact details</a>
            </div>
        </aside>
    </div>
</section>
