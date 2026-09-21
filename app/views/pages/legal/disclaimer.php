<?php
/* Ported from src/app/legal/disclaimer/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$disclaimer = practice('disclaimer');
$firm = practice('firm');
?>
<section class="hero" style="<?= hero_image_style('/img/band.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Legal</p>
        <h1>Disclaimer</h1>
    </div>
</section>

<section class="section">
    <?php /* prose is nested inside shell, not combined onto it, and prose--wide caps
             it wider than the article-reading --measure - both together keep the
             text starting flush with the hero heading above (not re-centering
             itself narrower) while still giving it more width than a plain
             66ch article column. */ ?>
    <div class="shell">
        <div class="prose prose--wide">
            <p><?= e($disclaimer['intro']) ?></p>

            <ul class="list-check" style="margin:1.2rem 0;">
                <?php foreach ($disclaimer['points'] as $point): ?>
                    <li><?= e($point) ?></li>
                <?php endforeach; ?>
            </ul>

            <p><?= e(str_replace(':firm', $firm, $disclaimer['closing'])) ?></p>

            <h2 style="margin:2rem 0 0.8rem;">No solicitation</h2>
            <p>
                This website is not an advertisement or a solicitation of work. It is an informational
                resource, accessed by the user of their own volition and on their own request, and it
                exists only so that a person who already wishes to know about the practice may do so.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">No lawyer&ndash;client relationship</h2>
            <p>
                Using this website, sending an enquiry through it, uploading a document or requesting
                an appointment does not create a lawyer&ndash;client relationship. Such a relationship arises
                only when the chamber accepts a matter and the engagement is recorded in writing.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Confidentiality of what you send</h2>
            <p>
                Material sent through this website before an engagement exists is not protected as
                privileged communication and may not be treated as confidential in the way an existing
                client&rsquo;s communications would be. Please do not send privileged, time-sensitive or
                sensitive material through this website.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Accuracy</h2>
            <p>
                Content on this website, including the notes published under Insights, is general
                information current at the time of writing. Law and procedure change. Nothing here
                should be acted upon without independent advice on the specific facts.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">External links</h2>
            <p>
                Where this website links to another site, the chamber is not responsible for that
                site&rsquo;s content, accuracy or privacy practices.
            </p>

            <div class="notice" style="margin-top:2rem;">
                This disclaimer is displayed to every visitor on first entry to the site and must be
                accepted before any page can be read. Your acceptance is recorded without storing your
                IP address or any personal data.
            </div>
        </div>
    </div>
</section>
