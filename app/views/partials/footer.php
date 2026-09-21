<?php
/* Ported from src/components/Footer.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$contact = practice('contact');
$areas = practice('areas');
?>
<footer class="footer">
    <div class="shell">
        <div class="footer__grid">
            <div>
                <div class="footer__brand"><?= e(practice('firm')) ?></div>
                <p><?= e(practice('shortIntro')) ?></p>
            </div>

            <div>
                <h4>Practice Areas</h4>
                <ul>
                    <?php foreach (area_slugs() as $slug): ?>
                        <li><a href="<?= e(route('area', $slug)) ?>"><?= e($areas[$slug]['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h4>Navigate</h4>
                <ul>
                    <li><a href="<?= e(route('about')) ?>">About</a></li>
                    <li><a href="<?= e(route('whyUs')) ?>">Approach</a></li>
                    <li><a href="<?= e(route('insights')) ?>">Insights</a></li>
                    <li><a href="<?= e(route('caseEvaluation')) ?>">Case Evaluation</a></li>
                    <li><a href="<?= e(route('upload')) ?>">Upload a Document</a></li>
                    <li><a href="<?= e(route('appointment')) ?>">Request an Appointment</a></li>
                </ul>
            </div>

            <div>
                <h4>Chamber</h4>
                <ul>
                    <?php foreach ($contact['addressLines'] as $line): ?>
                        <li><?= e($line) ?></li>
                    <?php endforeach; ?>
                    <?php if (!empty($contact['phone'])): ?>
                        <li><a href="tel:<?= e($contact['phoneLink']) ?>"><?= e($contact['phone']) ?></a></li>
                    <?php endif; ?>
                    <li><a href="mailto:<?= e($contact['email']) ?>"><?= e($contact['email']) ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer__legal-note">
            The rules of the Bar Council of India prohibit advocates from soliciting work or
            advertising in any manner. This website is intended purely as an informational resource,
            made available at the user&rsquo;s specific request. Nothing on it constitutes legal advice,
            and no lawyer&ndash;client relationship is created by using it or by contacting the chamber
            through it. Read the full <a href="<?= e(route('legal', 'disclaimer')) ?>">disclaimer</a>.
        </div>

        <?php /*
            Attribution for the Creative Commons photographs used on the site.
            Required by the CC BY-SA licence. Remove this line only if every
            such image has been replaced with the client's own photography.
        */ ?>
        <p style="margin-top:16px;font-size:0.76rem;color:#7E8C9E;max-width:none;">
            Photographs of the Bombay High Court, the Supreme Court of India and Nariman Point via
            Wikimedia Commons, used under CC BY-SA. Some practice-area images licensed from Unsplash.
            Portraits of the advocate supplied by the chamber.
        </p>

        <div class="footer__bar">
            <span>&copy; <?= e(date('Y')) ?> <?= e(practice('firm')) ?>. All rights reserved.</span>
            <span>
                <?php
                $legal = [
                    'disclaimer' => 'Disclaimer',
                    'privacy' => 'Privacy Policy',
                    'terms' => 'Terms of Use',
                    'cookies' => 'Cookie Policy',
                ];
                $first = true;
                foreach ($legal as $slug => $label):
                    ?>
                    <?php if (!$first): ?>&nbsp;&middot;&nbsp;<?php endif; ?>
                    <a href="<?= e(route('legal', $slug)) ?>"><?= e($label) ?></a>
                    <?php
                    $first = false;
                endforeach;
                ?>
            </span>
        </div>
    </div>
</footer>
