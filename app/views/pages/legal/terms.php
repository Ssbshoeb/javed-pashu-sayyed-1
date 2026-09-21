<?php
/* Ported from src/app/legal/terms/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');
?>
<section class="hero" style="<?= hero_image_style('/img/band.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Legal</p>
        <h1>Terms of Use</h1>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="prose prose--wide">
            <h2 style="margin-bottom:0.8rem;">Acceptance</h2>
            <p>
                By entering this website you accept the disclaimer shown on entry and these terms. If
                you do not accept them, please leave the site.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Purpose of the site</h2>
            <p>
                This website exists to provide information about the practice to a person who has
                sought that information. It is not an advertisement, a solicitation, or an offer to act
                in any matter.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">No advice</h2>
            <p>
                Nothing on this website is legal advice or a legal opinion. No responsibility is
                accepted for any action taken or not taken on the basis of anything published here.
                Independent advice must be obtained on any specific matter.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Use of the forms</h2>
            <ul class="list-check">
                <li>Submit accurate information, and only information you are entitled to share.</li>
                <li>
                    Do not upload material subject to a sealing order or belonging to a matter in which
                    another advocate is on record, without disclosing that fact.
                </li>
                <li>Do not use the forms to send unlawful, abusive or automated content.</li>
                <li>
                    Submissions are rate-limited and abuse of the forms may result in access being
                    blocked.
                </li>
            </ul>

            <h2 style="margin:2rem 0 0.8rem;">Intellectual property</h2>
            <p>
                The text, design and structure of this website belong to <?= e(practice('firm')) ?>. You may read
                and print pages for your own use. Republication, systematic copying or commercial use
                is not permitted without written consent.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Availability</h2>
            <p>
                The site is provided as it stands. No warranty is given that it will be available
                without interruption or free of error, and it may be changed or withdrawn at any time.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Governing law</h2>
            <p>
                These terms are governed by the laws of India, and the courts at Mumbai have exclusive
                jurisdiction over any dispute arising out of the use of this website.
            </p>
        </div>
    </div>
</section>
