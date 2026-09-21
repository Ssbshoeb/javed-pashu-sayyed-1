<?php
/*
| Standalone Achievements page, added at the client's request (20 Aug 2026) so
| "Achievements" in the nav leads to its own page rather than an anchor on
| About. Hero uses the same photo treatment as the About page hero, per the
| client's instruction that the portrait should be "placed the same way".
| Ported from src/app/achievements/page.tsx.
|
| The artwork is the composite, not the About page's own hero crop. Both are
| 1.383:1 so either satisfies the aspect contract in site.css, but they do not
| frame alike: the composite is the tighter crop, which seats the advocate
| large with his head just under the masthead, and that is the framing the
| client's reference design shows. The About crop is a wider view of the same
| room, so in this band it renders him smaller and lower, with the ceiling
| taking the top third. Swapped to the About crop on 29 Aug 2026 and put back
| the same day against the client's reference - check the framing, not just
| the ratio, before swapping again.
|
| Content is unchanged from the Recognition & Engagements block in
| config/practice.php - see the RULE 36 note there before adding anything.
| This page must stay limited to speaking/teaching engagements; no case
| results, no awards, no comparisons.
*/

defined('APP_RUNNING') or exit('No direct access.');

$about = practice('aboutPage');
?>
<?php /* Hero, same treatment as the About page: portrait bled in from the right,
         faded into the dark ground on the left so the heading stays readable.
         id kept from the band this replaced, in case anything off-site deep-links it. */ ?>
<section class="ahero ahero--photo" id="achievements">
    <div class="ahero__photo" aria-hidden="true">
        <picture>
            <source srcset="<?= e(src_set('/img/advocate-achievements-hero.webp')) ?>"
                    sizes="<?= e($aheroSizes) ?>" type="image/webp">
            <img src="/img/advocate-achievements-hero.jpg"<?= srcset_attr('/img/advocate-achievements-hero.jpg') ?>
                 sizes="<?= e($aheroSizes) ?>" alt="" width="1030" height="745" fetchpriority="high">
        </picture>
    </div>

    <div class="shell ahero__inner">
        <?php /* Sentence case, and the strap no longer ends on the heading's own word.
                 The CSS uppercases these anyway; typing them in caps meant the text a
                 screen reader announced was shouted and the About hero, which uses the
                 identical treatment, did not match. */ ?>
        <h1 class="ahero__title">
            <span>Recognition &amp;</span>
            <span class="ahero__title--gold">Engagements</span>
        </h1>
        <span class="orn" aria-hidden="true"></span>
        <p class="ahero__strap">Speaking. Teaching. Lecturing.</p>

        <div class="ahero__copy">
            <p>Where the advocate has spoken and taught, and on what.</p>
        </div>

        <?php /*
            The engagements themselves, in the hero rather than in a band of
            their own (29 Aug 2026). They used to sit below it, in a shared
            recognition partial, and that band opened with its own
            "Recognition & Engagements" heading - the page's own h1 repeated a
            few hundred pixels lower, over a different background, while the
            hero's left column sat empty from the strap down. The two read as
            two unrelated pages stacked rather than one.

            achievementsHeading is deliberately not printed here: the h1 above
            already says it. The list is the same content data the band used, so
            nothing in config/practice.php had to move - including the RULE 36
            limit noted there, which still governs what may be listed.
        */ ?>
        <ul class="ahero__wins">
            <?php foreach ($about['achievements'] as $item): ?>
                <li>
                    <span class="ahero__wico"><?= icon($item['icon']) ?></span>
                    <span>
                        <strong><?= e($item['title']) ?></strong>
                        <span><?= e($item['body']) ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="ahero__note"><?= e($about['achievementsNote']) ?></p>
    </div>
</section>
