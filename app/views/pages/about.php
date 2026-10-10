<?php
/*
| Built to the About page PDF the client supplied on 19 Aug 2026, which
| supersedes the earlier About mock-up. Ported from src/app/about/page.tsx.
|
| The copy is theirs, reproduced as supplied on their explicit instruction.
| Phrases Bar Council of India Rule 36 does not permit are flagged inline.
|
| Three blocks on this page are assertions of FACT, not marketing, and they
| matter more than any wording question: the ten courts, the eight
| memberships, and the MNC / international / NGO client claim. Each needs the
| advocate's written confirmation before this page is published. See the notes
| in config/practice.php.
*/

defined('APP_RUNNING') or exit('No direct access.');

$about = practice('aboutPage');
?>
<?php /* 1. Hero --------------------------------------------------------------- */ ?>
<?php /*
    advocate-about-hero.jpg (supplied 28 Aug 2026): the same photo on every
    breakpoint, on the client's explicit instruction that the mobile view
    should match desktop rather than swap to the cutout treatment a version of
    this hero used earlier. It's a top-anchored `cover` crop of the raw
    photograph, pre-cropped to the band's own 1.383:1 ASPECT CONTRACT (see the
    CSS) - so on the desktop band, `cover` has nothing to crop.

    On the mobile band the box is sized by `aspect-ratio: 1.383` (see the 860px
    rule in the CSS) rather than a fixed height, for the same reason: matching
    the box ratio to the photo's own ratio means `cover` still has nothing to
    crop there either, at any phone width. That does make the mobile band
    taller than the old fixed-height version - that's the deliberate trade for
    showing the same, uncropped image everywhere.
*/ ?>
<section class="ahero ahero--photo">
    <div class="ahero__photo">
        <picture>
            <source srcset="<?= e(src_set('/img/advocate-about-hero.webp')) ?>"
                    sizes="<?= e($aheroSizes) ?>" type="image/webp">
            <img src="/img/advocate-about-hero.jpg"<?= srcset_attr('/img/advocate-about-hero.jpg') ?>
                 sizes="<?= e($aheroSizes) ?>" alt="Adv. Javed Pashu Sayyed, Advocate in Mumbai" width="1086" height="786" fetchpriority="high">
        </picture>
    </div>

    <div class="shell ahero__inner">
        <h1 class="ahero__title">
            <span><?= e($about['heading1']) ?></span>
            <span class="ahero__title--gold"><?= e($about['heading2']) ?></span>
        </h1>
        <span class="orn" aria-hidden="true"></span>
        <?php /* "Excellence" is a superlative. Flagged, kept as instructed. */ ?>
        <p class="ahero__strap"><?= e($about['strap']) ?></p>

        <div class="ahero__copy">
            <?php /* "result-oriented" and "highest standards" both overstate. Flagged. */ ?>
            <?php foreach ($about['intro'] as $line): ?>
                <p><?= e($line) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php /* 2. About the advocate, and the areas card ----------------------------- */ ?>
<section class="section asplit">
    <div class="shell asplit__grid">
        <div>
            <h2 class="asplit__head">
                <span class="asplit__ico"><?= icon('building') ?></span>
                <?= e($about['advocateHeading']) ?>
            </h2>

            <?php /* "distinguished experience" and "result-oriented" are flagged; the
                     MNC / international / NGO sentence is a claim to verify. */ ?>
            <?php foreach ($about['advocateCopy'] as $paragraph): ?>
                <p><?= e($paragraph) ?></p>
            <?php endforeach; ?>
        </div>

        <aside class="expertise">
            <?php /* The design's own heading. Rule 36 permits stating areas of
                     PRACTICE; "expertise" is a claim of specialisation and is the
                     riskiest label on the site. Kept as instructed. */ ?>
            <h2 class="expertise__head">
                <span class="expertise__hico"><?= icon('scales') ?></span>
                <?= e($about['expertiseHeading']) ?>
            </h2>

            <ul>
                <?php foreach ($about['expertise'] as $item): ?>
                    <li>
                        <span class="expertise__ico">
                            <?php if (!empty($item['img'])): ?>
                                <img src="/img/<?= e($item['img']) ?>" alt="" loading="lazy" decoding="async">
                            <?php else: ?>
                                <?= icon($item['icon']) ?>
                            <?php endif; ?>
                        </span>
                        <?php if ($item['slug'] !== null): ?>
                            <a href="<?= e(route('area', $item['slug'])) ?>"><?= e($item['title']) ?></a>
                        <?php else: ?>
                            <?php /* No page behind this one: it is not among the eight
                                     practice areas the site actually carries. */ ?>
                            <span><?= e($item['title']) ?></span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</section>

<?php /* 3. Courts of practice. Carries the #courts anchor the header links to. -- */ ?>
<section class="courts" id="courts">
    <div class="shell">
        <h2 class="courts__head">
            <span><?= icon('building') ?></span>
            <?= e($about['courtsHeading']) ?>
        </h2>

        <?php /* Every court here is a factual claim about where he appears. */ ?>
        <ul class="courts__grid">
            <?php foreach ($about['courts'] as $court): ?>
                <li>
                    <span class="courts__ico"><?= icon('building') ?></span>
                    <span><?= e($court) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<?php /* 4. Memberships -------------------------------------------------------- */ ?>
<section class="section members">
    <div class="shell">
        <h2 class="members__head"><?= e($about['membershipsHeading']) ?></h2>

        <?php /* Each membership must be real and current, and each body must exist.
                 This is the list a hostile reader checks first. */ ?>
        <ul class="members__grid">
            <?php foreach ($about['memberships'] as $membership): ?>
                <li>
                    <?php if (!empty($membership['img'])): ?>
                        <?php /* The artwork carries its own disc, so it replaces the styled
                                 circle rather than sitting inside one. */ ?>
                        <img class="members__ico members__ico--img" src="/img/<?= e($membership['img']) ?>"
                             alt="<?= e($membership['name']) ?>" loading="lazy" decoding="async">
                    <?php else: ?>
                        <span class="members__ico"><?= icon($membership['icon']) ?></span>
                    <?php endif; ?>
                    <span><?= e($membership['name']) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <p class="members__note"><?= e($about['membershipsNote']) ?></p>
    </div>
</section>

<?php /*
    5. No Recognition/Achievements band here - the client's About page PDF
    (Adv_Javed_Pashu_Sayyed_About_Page.pdf) goes straight from Human Rights
    memberships to Core Values, and Achievements has its own page
    (/achievements) since 20 Aug 2026. Adding it back here would both depart
    from the approved design and duplicate that page.
*/ ?>

<?php /* 6. Core values and the closing line ----------------------------------- */ ?>
<section class="corevals">
    <div class="shell">
        <h2 class="corevals__head"><?= e($about['valuesHeading']) ?></h2>

        <ul class="corevals__grid">
            <?php /* "Excellence" and "Result Oriented" are both flagged above. */ ?>
            <?php foreach ($about['values'] as $value): ?>
                <li>
                    <span class="corevals__ico"><?= icon($value['icon']) ?></span>
                    <strong><?= e($value['title']) ?></strong>
                    <p><?= e($value['body']) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php /* Closing slogan from the design. Pure advertising in Rule 36 terms and
                 the most quotable line on the page. Kept as instructed. */ ?>
        <blockquote class="arearow__quote">
            <span aria-hidden="true">&ldquo;</span><?= e($about['quote']) ?><span aria-hidden="true">&rdquo;</span>
        </blockquote>
    </div>
</section>
