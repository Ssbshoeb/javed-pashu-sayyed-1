<?php
/* Ported from src/app/legal/cookies/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$validityDays = (int) practice('disclaimer.validityDays');
?>
<section class="hero" style="<?= hero_image_style('/img/band.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Legal</p>
        <h1>Cookie Policy</h1>
        <p class="lede" style="margin-top:1rem;">
            This website stores very little in your browser, and nothing optional is stored until you
            choose to allow it.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="prose prose--wide">
            <h2 style="margin-bottom:0.8rem;">Essential storage</h2>
            <ul class="list-check">
                <li>
                    <strong>Session cookie</strong> &mdash; set once, when you accept the entry disclaimer, so
                    that the acceptance can be recorded without keeping your IP address. It holds a
                    random value that identifies nothing about you, and it expires when you close the
                    browser.
                </li>
                <?php if ($validityDays === 0): ?>
                    <li>
                        <strong>Disclaimer acceptance</strong> &mdash; stored in your browser&rsquo;s session
                        storage so the entry disclaimer is not repeated as you move between pages during a
                        visit. It holds only a timestamp, and it is discarded when you close the browser,
                        so the disclaimer appears again on your next visit.
                    </li>
                <?php else: ?>
                    <li>
                        <strong>Disclaimer acceptance</strong> &mdash; stored in your browser&rsquo;s local
                        storage so the entry disclaimer is not shown again for <?= e((string) $validityDays) ?> days. It
                        holds only an expiry timestamp.
                    </li>
                <?php endif; ?>
                <li>
                    <strong>Cookie choice</strong> &mdash; stored in local storage so the banner is not shown
                    again after you have answered it.
                </li>
            </ul>
            <p>
                The last two items are browser storage rather than cookies: they stay on your device
                and are never sent to the server.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Optional analytics</h2>
            <p>
                If you select &quot;Accept analytics&quot;, the site loads Google Analytics, which sets
                its own cookies and reports aggregate figures such as pages visited and time spent.
                Nothing analytics-related is loaded before you accept, and selecting &quot;Essential
                only&quot; leaves it entirely absent.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Changing your mind</h2>
            <p>
                Clearing your browser&rsquo;s site data for this website removes every item listed above,
                including your cookie choice and your disclaimer acceptance. You will be asked for both
                again on your next visit.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Third parties</h2>
            <p>
                The contact page embeds a Google Maps frame to show the chamber&rsquo;s location, and web
                fonts are loaded from Google Fonts. Those services receive your request in order to
                serve the content. No advertising or tracking network is used on this website.
            </p>
        </div>
    </div>
</section>
