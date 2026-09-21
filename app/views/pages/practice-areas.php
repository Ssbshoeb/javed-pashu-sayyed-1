<?php
/* Ported from src/app/practice-areas/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$areas = practice('areas');
$slugs = area_slugs();
$count = count($slugs);

// Counted from the same list the grid below iterates. It was written out as a
// word and went stale - it said "Six" while eight cards rendered underneath it.
$words = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten'];
$countWord = $words[$count] ?? (string) $count;
?>
<section class="hero" style="<?= hero_image_style('/img/hero-1.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Practice Areas</p>
        <h1 style="max-width:22ch;">Areas in which the chamber accepts instructions</h1>
        <p class="lede" style="margin-top:1rem;">
            <?= e($countWord) ?> areas, kept deliberately limited. Each page below describes
            the matters ordinarily handled, the usual course of proceedings, and the questions
            clients ask most often.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="grid">
            <?php foreach ($slugs as $slug): ?>
                <?php $area = $areas[$slug]; ?>
                <a class="card" href="<?= e(route('area', $slug)) ?>">
                    <h2><?= e($area['title']) ?></h2>
                    <p><?= e($area['summary']) ?></p>
                    <span class="card__more">Read more</span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="notice" style="margin-top:2rem;max-width:72ch;">
            <strong>Not sure which area your matter falls under?</strong> That is common, and several
            matters fall under more than one. Describe the issue in the
            <a href="<?= e(route('caseEvaluation')) ?>">case evaluation form</a> and it will be read as
            written.
        </div>
    </div>
</section>
