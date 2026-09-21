<?php
/* Ported from src/app/not-found.tsx. */

defined('APP_RUNNING') or exit('No direct access.');
?>
<section class="section">
    <div class="shell center-narrow" style="padding-block:clamp(40px,8vw,80px);">
        <p class="eyebrow">404</p>
        <h1 style="margin-bottom:1rem;">This page could not be found</h1>
        <p class="lede">
            The address may have changed, or the page may never have existed. The practice areas and
            contact details are still where they were.
        </p>
        <div class="btn-row" style="margin-top:2rem;">
            <a class="btn btn--primary" href="<?= e(route('home')) ?>">Back to home</a>
            <a class="btn btn--ghost" href="<?= e(route('areas')) ?>">Practice areas</a>
            <a class="btn btn--ghost" href="<?= e(route('contact')) ?>">Contact</a>
        </div>
    </div>
</section>
