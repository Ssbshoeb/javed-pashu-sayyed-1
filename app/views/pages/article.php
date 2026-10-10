<?php
/* Ported from src/app/insights/[slug]/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$articles = practice('articles');
?>
<section class="hero" style="<?= hero_image_style('/img/hero-3.webp') ?>">
    <div class="shell" style="max-width:900px;">
        <p class="breadcrumb">
            <a href="<?= e(route('home')) ?>">Home</a> &rsaquo;
            <a href="<?= e(route('insights')) ?>">Insights</a> &rsaquo; <?= e($article['category']) ?>
        </p>
        <span class="article-meta" style="margin-bottom:1rem;">
            <span class="tag"><?= e($article['category']) ?></span>
            <span class="ref"><?= e(long_date($article['date'])) ?></span>
            <span class="ref">By <a href="<?= e(route('about')) ?>"><?= e(practice('name')) ?></a></span>
        </span>
        <h1><?= e($article['title']) ?></h1>
        <p class="lede" style="margin-top:1rem;"><?= e($article['excerpt']) ?></p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="prose">
            <?php foreach ($article['body'] as $paragraph): ?>
                <p><?= e($paragraph) ?></p>
            <?php endforeach; ?>

            <div class="notice" style="margin-top:2rem;">
                <strong>This note is general information.</strong> It is not legal advice, it does not
                address any particular set of facts, and no lawyer&ndash;client relationship arises from
                reading it. Independent advice should be taken on any specific matter.
            </div>

            <?php if (!empty($article['area']) && is_array(practice('areas.' . $article['area']))): ?>
                <p style="margin-top:1.4rem;">
                    Related practice area:
                    <a href="<?= e(route('area', $article['area'])) ?>"><?= e(practice('areas.' . $article['area'] . '.title')) ?></a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="section section--sunk">
    <div class="shell">
        <p class="eyebrow">More notes</p>
        <div class="grid">
            <?php foreach (article_slugs() as $other): ?>
                <?php if ($other === $slug) { continue; } ?>
                <?php $item = $articles[$other]; ?>
                <a class="card" href="<?= e(route('article', $other)) ?>">
                    <span class="article-meta">
                        <span class="tag"><?= e($item['category']) ?></span>
                    </span>
                    <h3><?= e($item['title']) ?></h3>
                    <p><?= e($item['excerpt']) ?></p>
                    <span class="card__more">Read the note</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
