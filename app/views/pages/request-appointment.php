<?php
/* Ported from src/app/request-appointment/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$contact = practice('contact');
?>
<section class="hero" style="<?= hero_image_style('/img/hero-2.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Consultations</p>
        <h1 style="max-width:22ch;">Request a consultation</h1>
        <p class="lede" style="margin-top:1rem;">
            Choose a date and a preferred hour below. This is a request, not a confirmed booking &mdash;
            the chamber will confirm or propose an alternative by email or telephone.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell split">
        <div>
            <?php partial('partials/forms/appointment'); ?>
        </div>

        <aside class="stack">
            <div class="tile">
                <h3>Chamber hours</h3>
                <dl class="detail-list" style="margin-top:10px;">
                    <?php foreach ($contact['hours'] as $day => $hours): ?>
                        <div class="row">
                            <dt><?= e($day) ?></dt>
                            <dd><?= e($hours) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>

            <div class="notice">
                <strong>This is a request.</strong> The slot is held only once the chamber has
                confirmed it. Please do not travel to the chamber before receiving that confirmation.
            </div>

            <div class="notice">
                <strong>Bring the papers.</strong> Whatever the matter, bring every document connected
                with it to the consultation, including the ones that appear unhelpful.
            </div>
        </aside>
    </div>
</section>
