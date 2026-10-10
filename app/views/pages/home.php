<?php
/*
| Home page, ported from src/app/page.tsx.
|
| Sections 1 to 4 below are built to the design the client approved on
| 18 Aug 2026, replacing the carousel hero, the services strip, the founder
| panel and the photographic area grid that stood here before.
|
| The wording in this block is the client's copy, reproduced as supplied.
| Several phrases in it - "Results", "expert legal services", "best outcomes"
| - are the kind of claim Bar Council of India Rule 36 does not permit, and
| they are flagged where they appear. They were kept on the client's explicit
| instruction. Nothing else on the site uses this register.
|
| $statTiles and $mheroSizes are prepared by the controller.
*/

defined('APP_RUNNING') or exit('No direct access.');

$practiceData = practice();
$contact = $practiceData['contact'];
$areas = $practiceData['areas'];

// The design sets "Adv." in gold against the rest of the name in white.
// Split rather than hard-coded, so a change to the practice name still renders
// correctly instead of silently gilding the wrong words.
$name = $practiceData['name'];
$aboutNamePrefix = str_starts_with($name, 'Adv. ') ? 'Adv.' : null;
$aboutNameRest = $aboutNamePrefix !== null ? substr($name, strlen('Adv. ')) : $name;
?>
<?php /*
    .page-home in site.css is used only as a descendant scope — rules like
    .page-home .section, never .page-home itself — so this wrapper is all
    that is needed to scope the dark treatment to this page. Rendered
    server-side, so there is no flash of the wrong palette on first paint.
*/ ?>
<div class="page-home">

    <?php /* 1. Hero ------------------------------------------------------------- */ ?>
    <section class="mhero">
        <?php /* Background-free cut-out, so the hero's own dark ground shows through -
                 see .mhero__media in site.css. */ ?>
        <div class="mhero__media">
            <picture>
                <source srcset="<?= e(src_set('/img/advocate-cutout-v2.webp')) ?>"
                        sizes="<?= e($mheroSizes) ?>" type="image/webp">
                <img src="/img/advocate-cutout-v2.png"<?= srcset_attr('/img/advocate-cutout-v2.png') ?>
                     sizes="<?= e($mheroSizes) ?>" alt="Adv. Javed Pashu Sayyed, Advocate in Mumbai &amp; Pune" width="833" height="766"
                     fetchpriority="high">
            </picture>
        </div>

        <div class="shell mhero__inner">
            <div class="mhero__copy">
                <p class="mhero__eyebrow">Welcome to the chamber of</p>
                <h1 class="mhero__name">
                    <span>Adv. Javed</span>
                    <span class="mhero__name--gold">Pashu Sayyed</span>
                </h1>
                <?php /* "Results" implies an assured outcome. Flagged, kept as instructed. */ ?>
                <p class="mhero__strap">Advocacy. Strategy. Results.</p>
                <span class="mhero__rule" aria-hidden="true"></span>
                <?php /* "expert" is a superlative Rule 36 does not allow. Flagged, kept. */ ?>
                <p class="mhero__lede">
                    Providing expert legal services with integrity, dedication and a commitment to justice.
                </p>

                <a class="btn btn--ghostgold" href="<?= e(route('areas')) ?>">
                    Explore practice areas <span aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <?php /* 2. Practice area tiles ---------------------------------------------- */ ?>
    <section class="areaband" id="practice-areas">
        <?php /* The pale column watermarks that used to bleed off both edges here were
                 removed on the client's instruction, 29 Aug 2026. */ ?>
        <div class="shell">
            <p class="eyebrow eyebrow--center">Practice Areas</p>
            <h2 class="areaband__head">Comprehensive Legal Solutions</h2>
            <span class="areaband__orn" aria-hidden="true"></span>

            <ul class="tilegrid" data-reveal-group>
                <?php foreach ($practiceData['homeTiles'] as $tile): ?>
                    <li class="tile2">
                        <span class="tile2__ico"><?= icon($tile['icon']) ?></span>
                        <h3><?= e($tile['title']) ?></h3>
                        <p><?= e($tile['tagline']) ?></p>
                        <?php if ($tile['slug'] !== null): ?>
                            <a href="<?= e(route('area', $tile['slug'])) ?>">
                                Read more <span aria-hidden="true">&rarr;</span>
                            </a>
                        <?php else: ?>
                            <?php /* No page: the chamber does not list this area.
                                     See config/practice.php. */ ?>
                            <span class="tile2__nolink">&nbsp;</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <?php /* 3. About band -------------------------------------------------------- */ ?>
    <section class="aboutband" id="about">
        <div class="aboutband__grid">
            <?php /* Phone-only label. It has to sit above the photograph, and the
                     photograph is a sibling of the copy rather than inside it, so this
                     cannot be the eyebrow that lives in .aboutband__copy - that one is
                     hidden on a phone and this one takes over. */ ?>
            <p class="eyebrow aboutband__label">About the Advocate</p>

            <div class="aboutband__portrait">
                <picture>
                    <source srcset="<?= e(src_set('/img/advocate-cutout-v2.webp')) ?>"
                            sizes="<?= e($portraitSizes) ?>" type="image/webp">
                    <img src="/img/advocate-cutout-v2.png"<?= srcset_attr('/img/advocate-cutout-v2.png') ?>
                         sizes="<?= e($portraitSizes) ?>"
                         alt="<?= e($name) ?> speaking at a lectern"
                         loading="lazy" decoding="async" width="833" height="766">
                </picture>
            </div>

            <?php /*
                The ornament, the roles line and the credentials list below are the
                phone layout the client supplied on 29 Aug 2026; they are hidden
                above 620px so the approved desktop band is untouched. The
                credentials repeat the courts strip - the same data the strip below
                this section carries - so that strip is hidden on a phone rather
                than saying it all twice.
            */ ?>
            <div class="aboutband__copy">
                <p class="eyebrow">About</p>
                <h2>
                    <?php if ($aboutNamePrefix !== null): ?>
                        <span class="aboutband__namepre"><?= e($aboutNamePrefix) ?></span>
                    <?php endif; ?>
                    <?= e($aboutNameRest) ?>
                </h2>

                <span class="orn orn--gold aboutband__orn" aria-hidden="true"></span>
                <p class="aboutband__roles">
                    Advocate &nbsp;&middot;&nbsp; Litigator &nbsp;&middot;&nbsp; Legal Advisor
                </p>

                <div class="aboutband__body">
                    <ul class="aboutband__creds">
                        <?php foreach ($practiceData['courtStrip'] as $court): ?>
                            <li>
                                <span class="aboutband__credico"><?= icon($court['icon']) ?></span>
                                <span><?= e($court['name']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="aboutband__text">
                        <p>
                            An Advocate with huge experience of rich and diverse experience in litigation and
                            legal advisory.
                        </p>
                        <?php /* Court names must match the courts list and the bio; this said
                                 "Pune Sessions Court", which appears nowhere in the data. */ ?>
                        <p>
                            Practising in the Supreme Court of India, Bombay High Court, Mumbai Sessions Court,
                            Pune Courts, JMFC Courts and other forums across India.
                        </p>
                        <?php /* "best outcomes for clients" is a promise of result - the clearest
                                 Rule 36 problem on the page. Flagged, kept as instructed. */ ?>
                        <p>
                            Known for strategic thinking, meticulous preparation and an unwavering commitment
                            to achieving the best outcomes for clients.
                        </p>
                    </div>
                </div>

                <a class="btn btn--ghostgold" href="<?= e(route('about')) ?>">
                    Know more about me <span aria-hidden="true">&rarr;</span>
                </a>

                <?php /* Closing device from the design. Decorative only. */ ?>
                <span class="aboutband__seal" aria-hidden="true"><?= icon('scales') ?></span>
            </div>
        </div>
    </section>

    <?php /* 4. Courts strip ------------------------------------------------------ */ ?>
    <section class="courtstrip">
        <ul class="shell courtstrip__inner">
            <?php foreach ($practiceData['courtStrip'] as $court): ?>
                <li>
                    <span class="courtstrip__ico"><?= icon($court['icon']) ?></span>
                    <span>
                        <strong><?= e($court['name']) ?></strong>
                        <span><?= e($court['note']) ?></span>
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php /*
        4b. No Recognition & Engagements band here. It lives on its own page
        (/achievements), where the nav's "Achievements" item points. Repeating it
        on the home page meant a visitor working down this page met the same
        block again before reaching it there.
    */ ?>

    <?php /* 5. Why choose us ---------------------------------------------------- */ ?>
    <section class="section" id="why-us">
        <div class="shell">
            <div data-reveal>
                <p class="eyebrow">Why this chamber</p>
                <h2 class="ruled" style="margin-bottom:1.6rem;">How the work is done here</h2>
            </div>

            <div class="tiles" data-reveal-group>
                <div class="tile">
                    <h3>The papers come first</h3>
                    <p>
                        No view is offered on a matter before the documents have been read. An opinion given
                        over the telephone, without the record, is a guess wearing a suit.
                    </p>
                </div>
                <div class="tile">
                    <h3>Candour about weakness</h3>
                    <p>
                        Where a case has a difficulty &mdash; limitation, an unhelpful document, a gap in evidence
                        &mdash; it is put to the client at the outset.
                    </p>
                </div>
                <div class="tile">
                    <h3>Conducted personally</h3>
                    <p>
                        The advocate you consult is the advocate who appears. Matters are not handed down a
                        chain once the engagement is signed.
                    </p>
                </div>
                <div class="tile">
                    <h3>Confidentiality as a default</h3>
                    <p>
                        Information shared at a first consultation is privileged whether or not the chamber
                        is subsequently instructed.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php /* 6. Statistics band --------------------------------------------------- */ ?>
    <section class="stats parallax">
        <div class="parallax__plate" style="background-image:url('/img/band.webp');" aria-hidden="true"></div>
        <div class="parallax__scrim" aria-hidden="true"></div>

        <div class="shell stats__grid" data-reveal-group>
            <?php foreach ($statTiles as $tile): ?>
                <div>
                    <div class="stat__value" data-count="<?= e((string) $tile['value']) ?>"
                         data-suffix="<?= e($tile['suffix']) ?>">0</div>
                    <div class="stat__label"><?= e($tile['label']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <?php /* 7. Frequently asked questions ---------------------------------------
             Replaced the client-feedback carousel. General, factual answers only:
             no fees, no results, no claim about outcomes (Rule 36). The same list
             is emitted as FAQPage structured data by page_home(). */ ?>
    <section class="section" id="faq-section">
        <div class="shell">
            <div data-reveal>
                <p class="eyebrow">Frequently asked</p>
                <h2 class="ruled" style="margin-bottom:1.6rem;">
                    Questions about engaging the chamber
                </h2>
            </div>

            <div class="faq" data-reveal>
                <?php foreach ($practiceData['homeFaqs'] as $faq): ?>
                    <details>
                        <summary><?= e($faq['q']) ?></summary>
                        <div class="faq__body"><?= e($faq['a']) ?></div>
                    </details>
                <?php endforeach; ?>
            </div>

            <p style="margin-top:0.9rem;font-size:0.84rem;color:var(--muted);">
                General information only, not legal advice. The position in any matter depends on its own facts.
            </p>
        </div>
    </section>

    <?php /* 8. Call band --------------------------------------------------------- */ ?>
    <section class="callband">
        <div class="shell callband__inner">
            <div>
                <h2>Need to speak to the chamber?</h2>
                <p>
                    Monday to Friday <?= e($contact['hours']['Monday to Friday']) ?> &middot;
                    Saturday <?= e($contact['hours']['Saturday']) ?>
                </p>
            </div>
            <div>
                <?php if (!empty($contact['phone'])): ?>
                    <a class="callband__phone" href="tel:<?= e($contact['phoneLink']) ?>"><?= e($contact['phone']) ?></a>
                    <p style="text-align:right;">Telephone for anything urgent</p>
                <?php else: ?>
                    <a class="btn btn--gold" href="<?= e(route('appointment')) ?>">Request an appointment</a>
                    <p style="text-align:right;">Consultations are by appointment</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php /* Featured in the media (hidden until media entries exist) ------------- */ ?>
    <?php $mediaLatest = array_slice(media_items(), 0, 3); ?>
    <?php if ($mediaLatest !== []): ?>
    <section class="section" id="media">
        <div class="shell">
            <div data-reveal>
                <p class="eyebrow">Media</p>
                <h2 class="ruled" style="margin-bottom:1.4rem;">Featured in the media</h2>
            </div>
            <div class="mlist" data-reveal-group>
                <?php foreach ($mediaLatest as $m): ?>
                    <?php require APP_PATH . '/views/partials/media-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <p style="margin-top:1.4rem;"><a class="card__more" href="<?= e(route('media')) ?>">View all media</a></p>
        </div>
    </section>
    <?php endif; ?>

    <?php /* 9. Insights ---------------------------------------------------------- */ ?>
    <section class="section section--sunk" id="insights">
        <div class="shell">
            <div data-reveal>
                <p class="eyebrow">Insights</p>
                <h2 class="ruled" style="margin-bottom:1.4rem;">Notes on procedure and practice</h2>
            </div>

            <div class="grid" data-reveal-group>
                <?php foreach (array_slice(article_slugs(), 0, 3) as $slug): ?>
                    <?php $article = $practiceData['articles'][$slug]; ?>
                    <a class="card" href="<?= e(route('article', $slug)) ?>">
                        <span class="article-meta">
                            <span class="tag"><?= e($article['category']) ?></span>
                        </span>
                        <h3><?= e($article['title']) ?></h3>
                        <p><?= e($article['excerpt']) ?></p>
                        <span class="card__more">Read the note</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* 10. Get in touch ----------------------------------------------------- */ ?>
    <section class="section section--surface" id="contact">
        <div class="shell split">
            <div class="stack stack--lg" data-reveal>
                <div>
                    <p class="eyebrow">Get in touch</p>
                    <h2 class="ruled" style="margin-bottom:0.9rem;">Write to the chamber</h2>
                    <p>
                        Send a brief description of the matter and you will receive a reply at the email
                        address you provide. If something is urgent &mdash; a hearing, an arrest, a deadline &mdash; say
                        so in the first line, and it will be read accordingly.
                    </p>
                </div>

                <dl class="detail-list">
                    <div class="row">
                        <dt>Chamber</dt>
                        <dd>
                            <?php foreach ($contact['addressLines'] as $index => $line): ?>
                                <?php if ($index > 0): ?><br><?php endif; ?><?= e($line) ?>
                            <?php endforeach; ?>
                        </dd>
                    </div>
                    <?php if (!empty($contact['phone'])): ?>
                        <div class="row">
                            <dt>Telephone</dt>
                            <dd><a href="tel:<?= e($contact['phoneLink']) ?>"><?= e($contact['phone']) ?></a></dd>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <dt>Email</dt>
                        <dd><a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a></dd>
                    </div>
                </dl>

                <div class="notice">
                    <strong>Please note.</strong> Contacting the chamber, including through this website,
                    does not by itself create a lawyer&ndash;client relationship. Do not send confidential or
                    privileged material before an engagement is confirmed in writing.
                </div>
            </div>

            <div data-reveal>
                <?php /* Posts to the page it sits on. A failure redirects back here
                         with `#contact`, so the visitor lands on the form and its
                         error box rather than at the top of a long page. */ ?>
                <?php partial('partials/forms/enquiry', ['idSuffix' => 'h', 'action' => route('home')]); ?>
            </div>
        </div>
    </section>
</div>
