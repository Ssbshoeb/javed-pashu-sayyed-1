<?php
/* Ported from src/app/insights/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$articles = practice('articles');
?>
<section class="hero" style="<?= hero_image_style('/img/hero-2.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Insights</p>
        <h1 style="max-width:22ch;">Notes on procedure and practice</h1>
        <p class="lede" style="margin-top:1rem;">
            General explanations of processes the chamber is asked about often. These notes describe
            how things ordinarily work. They are not legal advice and cannot be relied on as such.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="grid">
            <?php foreach (article_slugs() as $slug): ?>
                <?php $article = $articles[$slug]; ?>
                <a class="card" href="<?= e(route('article', $slug)) ?>">
                    <span class="article-meta">
                        <span class="tag"><?= e($article['category']) ?></span>
                        <span class="ref"><?= e(month_year($article['date'])) ?></span>
                    </span>
                    <h2><?= e($article['title']) ?></h2>
                    <p><?= e($article['excerpt']) ?></p>
                    <span class="card__more">Read the note</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
