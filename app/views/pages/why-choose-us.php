<?php
/* Ported from src/app/why-choose-us/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$principles = [
    [
        'The papers come first',
        'No view is offered on a matter before the documents have been read. An opinion given over the telephone, without the record, is a guess wearing a suit.',
    ],
    [
        'Candour about weakness',
        'Where a case has a difficulty — limitation, an unhelpful document, a gap in evidence — it is put to the client at the outset, not discovered in cross-examination.',
    ],
    [
        'Settlement considered honestly',
        'Where a dispute can be closed by notice, negotiation or mediation, that is said plainly, even though litigation would be the longer engagement.',
    ],
    [
        'Conducted personally',
        'The advocate you consult is the advocate who appears. Matters are not handed down a chain once the engagement is signed.',
    ],
    [
        'Informed before every date',
        'Clients are told what is listed, what is expected to happen, and what is required from them, before each hearing rather than after it.',
    ],
    [
        'Written engagement',
        'The scope of work and the basis of fees are recorded in writing before the matter is accepted, so that neither side is relying on memory.',
    ],
    [
        'Confidentiality as a default',
        'Information shared at a first consultation is privileged whether or not the chamber is subsequently instructed.',
    ],
    [
        'Selective intake',
        'Matters are declined where the chamber cannot give them proper attention, or where a conflict exists. A prompt refusal is more useful than a slow one.',
    ],
];
?>
<section class="hero" style="<?= hero_image_style('/img/hero-3.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Approach</p>
        <h1 style="max-width:20ch;">How this chamber conducts a matter</h1>
        <p class="lede" style="margin-top:1rem;">
            Not a claim to be better than anyone else &mdash; the rules of the Bar Council do not permit
            that, and it would tell you nothing useful. What follows is simply how the work is done
            here, so you can judge whether it suits the matter you have.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="tiles">
            <?php foreach ($principles as [$title, $body]): ?>
                <div class="tile">
                    <h3><?= e($title) ?></h3>
                    <p><?= e($body) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section--sunk">
    <div class="shell split">
        <div>
            <h2 style="margin-bottom:0.9rem;">What is expected from a client</h2>
            <ul class="list-check">
                <li>A complete set of documents, including the ones that are unhelpful.</li>
                <li>An accurate account of the facts, including anything already said on record.</li>
                <li>Prompt instructions when a date or a deadline requires a decision.</li>
                <li>Realistic expectations about time &mdash; court schedules are not within anyone&rsquo;s control.</li>
            </ul>
        </div>
        <div>
            <h2 style="margin-bottom:0.9rem;">What the chamber will not do</h2>
            <ul class="list-check">
                <li>Guarantee an outcome, or quantify a chance of success as a percentage.</li>
                <li>Advise a course that is improper, however convenient it may appear.</li>
                <li>Accept a matter where there is a conflict of interest.</li>
                <li>Discuss one client&rsquo;s matter with another, at any time.</li>
            </ul>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="shell center-narrow">
        <h2 style="margin-bottom:0.8rem;">If that approach suits your matter</h2>
        <p style="margin-bottom:1.6rem;">
            Send a short description of the issue, or arrange a consultation.
        </p>
        <div class="btn-row">
            <a class="btn btn--primary" href="<?= e(route('caseEvaluation')) ?>">Request a case evaluation</a>
            <a class="btn btn--ghost" href="<?= e(route('appointment')) ?>">Request an appointment</a>
        </div>
    </div>
</section>
