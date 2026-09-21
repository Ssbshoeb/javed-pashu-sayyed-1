<?php
/* Ported from src/app/upload-document/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');
?>
<section class="hero" style="<?= hero_image_style('/img/hero-1.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Documents</p>
        <h1 style="max-width:22ch;">Send a document to the chamber</h1>
        <p class="lede" style="margin-top:1rem;">
            Affidavits, agreements, notices and court orders can be sent through this page. The file
            is delivered to the chamber by email and is never written to the server, published, or
            made accessible through a public link.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell split">
        <div>
            <?php partial('partials/forms/upload'); ?>
        </div>

        <aside class="stack">
            <div class="notice">
                <strong>How the file is handled.</strong> The document is attached to a single
                notification email to the chamber and is not stored on this website at all &mdash; there is
                no upload directory to reach and no address to guess. What happens to it after that is
                governed by the <a href="<?= e(route('legal', 'privacy')) ?>">privacy policy</a>.
            </div>

            <div class="notice">
                <strong>Please do not upload.</strong> Original documents of which you hold no copy;
                material belonging to a matter in which another advocate is on record, without
                disclosing that fact; anything subject to a court&rsquo;s sealing order.
            </div>

            <div class="notice">
                <strong>Reference number.</strong> A reference is shown on screen once the upload
                completes and is repeated in your acknowledgement email. Quote it in any correspondence
                about the document.
            </div>
        </aside>
    </div>
</section>
