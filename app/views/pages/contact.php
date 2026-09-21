<?php
/* Ported from src/app/contact/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$contact = practice('contact');
?>
<section class="hero" style="<?= hero_image_style('/img/hero-1.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Contact</p>
        <h1 style="max-width:20ch;">Reaching the chamber</h1>
        <p class="lede" style="margin-top:1rem;">
            The form below reaches the chamber directly and is read the same day. If a matter is
            urgent, say so in the first line and it will be treated accordingly.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell split">
        <div class="stack stack--lg">
            <div>
                <h2 style="margin-bottom:1rem;">Chamber details</h2>
                <dl class="detail-list">
                    <div class="row">
                        <dt>Address</dt>
                        <dd>
                            <?php foreach ($contact['addressLines'] as $index => $line): ?>
                                <?php if ($index > 0): ?><br><?php endif; ?><?= e($line) ?>
                            <?php endforeach; ?>
                        </dd>
                    </div>
                    <?php if (!empty($contact['phone'])): ?>
                        <div class="row">
                            <dt>Telephone</dt>
                            <dd><a href="tel:<?= e($contact['phoneLink']) ?>"><?= e($contact['phone']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <dt>Email</dt>
                        <dd><a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a></dd>
                    </div>
                    <?php foreach ($contact['hours'] as $day => $hours): ?>
                        <div class="row">
                            <dt><?= e($day) ?></dt>
                            <dd><?= e($hours) ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            </div>

            <div class="map-frame">
                <iframe title="Location of the chamber" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://maps.google.com/maps?q=<?= e(rawurlencode($contact['mapQuery'])) ?>&amp;output=embed"></iframe>
            </div>
        </div>

        <div id="contact">
            <h2 style="margin-bottom:1rem;">Send an enquiry</h2>
            <?php partial('partials/forms/enquiry', ['idSuffix' => 'c', 'action' => route('contact')]); ?>
        </div>
    </div>
</section>
