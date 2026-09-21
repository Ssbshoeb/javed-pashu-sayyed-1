<?php
/* Ported from src/app/case-evaluation/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');
?>
<section class="hero" style="<?= hero_image_style('/img/hero-3.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Case Evaluation</p>
        <h1 style="max-width:22ch;">Send the outline of your matter for an initial reading</h1>
        <p class="lede" style="margin-top:1rem;">
            The form below asks for the details needed to tell whether the chamber can assist, and
            what the next step would be. It is not a request for advice and no advice is given in
            reply.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell split">
        <div>
            <?php partial('partials/forms/case-evaluation'); ?>
        </div>

        <aside class="stack">
            <div class="notice">
                <strong>What happens next.</strong> The submission is read at the chamber and you
                receive a reply indicating whether a consultation is appropriate. A reference number is
                shown on the confirmation screen and repeated in your acknowledgement email.
            </div>

            <div class="notice">
                <strong>Before you write.</strong> Do not include anything you would not want read by
                someone other than the intended recipient. Email is not a secure channel, and no
                engagement exists yet.
            </div>

            <div class="notice">
                <?php /* This notice sits in the aside beside the form, not above it, so
                         "the description below" pointed the reader the wrong way. */ ?>
                <strong>Urgent matters.</strong> If a hearing, arrest or deadline is imminent, say so in
                the first line of the brief facts and mark the urgency accordingly &mdash; it is read
                the same day.
            </div>

            <div class="tile">
                <h3>Have documents to send?</h3>
                <p style="margin-bottom:12px;">
                    Papers can be uploaded separately once you have submitted this form.
                </p>
                <a class="textlink" href="<?= e(route('upload')) ?>">Upload a document</a>
            </div>
        </aside>
    </div>
</section>
