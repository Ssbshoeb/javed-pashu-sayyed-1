<?php
/*
| Header built to the design the client approved on 18 Aug 2026: near-black
| bar, gold monogram over a stacked name, and that design's nav order.
|
| One nav item in the design still has no page of its own:
|   COURTS -> the courts list on the About page for now.
|
| ACHIEVEMENTS has its own page (client request, 20 Aug 2026), built to the
| same hero treatment as About. Its content is still the same limited
| Recognition & Engagements block - see the RULE 36 note in
| config/practice.php before adding anything to it.
|
| The nav toggle, the masthead-height measurement and the same-page scroll
| links all live in js/site.js. `aria-current` is decided here on the server,
| from the request path, rather than in the browser as the React port did it —
| which means a crawler and a screen reader both see it in the first response.
*/

defined('APP_RUNNING') or exit('No direct access.');

$path = request_path();

/**
 * `page` when the request is on one of the given paths or below it.
 * Printed as an attribute, so it returns the whole attribute or nothing.
 */
$current = static function (string ...$paths) use ($path): string {
    foreach ($paths as $candidate) {
        if ($path === $candidate || str_starts_with($path, $candidate . '/')) {
            return ' aria-current="page"';
        }
    }

    return '';
};
?>
<header class="masthead masthead--dark">
    <div class="shell masthead__bar">
        <a class="brand brand--logo" href="<?= e(route('home')) ?>">
            <?php partial('partials/logo'); ?>
            <?php /*
                Set to match the wordmark in the client's own logo artwork
                (Adv_Javed_Sayyed_Logo_High_Resolution.pdf): a ruled ADVOCATE
                over the name in gold serif caps, over COUNSEL / ADVOCATE.

                Live text rather than the logo's own lockup image: at this bar's
                height the artwork's wording would render about four pixels tall,
                and the practice name is the one string on the page that has to
                stay selectable, translatable and readable to a crawler.

                NOTE the artwork says "JAVED SAYYED" while the rest of the site
                says "Adv. Javed Pashu Sayyed" (practice name, and every page
                title through it). Reproduced as drawn, on instruction.
            */ ?>
            <span class="brand__stack">
                <span class="brand__rolebar brand__rolebar--top">Advocate</span>
                <span class="brand__line2">Javed Sayyed</span>
                <span class="brand__line3">Counsel <i aria-hidden="true">&bull;</i> Advocate</span>
            </span>
        </a>

        <?php /* The bars are decorative; the label carries the accessible name and is
                 what the script rewrites when the panel opens. */ ?>
        <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="primary-nav">
            <span class="nav-toggle__bars" aria-hidden="true"></span>
            <span class="nav-toggle__label">Menu</span>
        </button>

        <?php /*
            Menu links carry both a real page URL (for search engines and for
            direct visits) and, through `data-scroll`, the id of the matching
            section on the home page. When that section is on the page already,
            js/site.js scrolls to it instead of navigating.
        */ ?>
        <nav class="nav" id="primary-nav" aria-label="Primary">
            <a href="<?= e(route('home')) ?>"<?= $current(route('home')) ?>>Home</a>
            <a href="<?= e(route('about')) ?>" data-scroll="#about"<?= $current(route('about')) ?>>About</a>
            <a href="<?= e(route('areas')) ?>" data-scroll="#practice-areas"<?= $current(route('areas')) ?>>
                Practice Areas <span class="nav__caret" aria-hidden="true"></span>
            </a>
            <a href="<?= e(route('about')) ?>#courts">Courts</a>
            <a href="<?= e(route('achievements')) ?>"<?= $current(route('achievements')) ?>>Achievements</a>
            <a href="<?= e(route('insights')) ?>" data-scroll="#insights"<?= $current(route('insights')) ?>>Insights</a>
            <a href="<?= e(route('media')) ?>"<?= $current(route('media')) ?>>Media</a>
            <a href="<?= e(route('contact')) ?>" data-scroll="#contact"<?= $current(route('contact')) ?>>Contact</a>
            <a class="nav__cta" href="<?= e(route('appointment')) ?>"<?= $current(route('appointment')) ?>>
                <?= icon('scales') ?>
                Consult Now
            </a>
        </nav>
    </div>
</header>
