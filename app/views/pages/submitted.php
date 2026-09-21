<?php
/*
| Confirmation screen, ported from src/app/submitted/[type]/page.tsx.
|
| The reference arrives in the query string. Laravel flashed it through the
| session; this port could do the same, but a query parameter is kept because
| it means a visitor can bookmark or forward the screen and still see their
| number — and a refresh does not lose it. The page is noindex, and the
| reference identifies nothing on its own: there is no database to look it up in.
|
| $copy and $reference are prepared by the controller.
*/

defined('APP_RUNNING') or exit('No direct access.');
?>
<section class="section">
    <div class="shell center-narrow" style="padding-block:clamp(30px,6vw,60px);">
        <p class="eyebrow">Received</p>
        <h1 style="margin-bottom:1rem;"><?= e($copy['heading']) ?></h1>
        <p class="lede"><?= e($copy['body']) ?></p>

        <?php if ($reference !== null): ?>
            <div class="notice" style="margin:1.8rem auto 0;text-align:left;max-width:44ch;">
                <strong>Your reference:</strong> <span class="ref"><?= e($reference) ?></span>
                <br>
                Quote this number in any correspondence about this submission. It also appears in your
                acknowledgement email.
            </div>
        <?php endif; ?>

        <div class="btn-row" style="margin-top:2rem;">
            <a class="btn btn--ghost" href="<?= e(route('home')) ?>">Back to home</a>
            <a class="btn btn--ghost" href="<?= e(route('insights')) ?>">Read the notes</a>
        </div>

        <div class="notice" style="margin:2rem auto 0;text-align:left;">
            A reply from the chamber, and this acknowledgement, do not by themselves create a
            lawyer&ndash;client relationship. An engagement begins only when it is recorded in writing.
        </div>
    </div>
</section>
