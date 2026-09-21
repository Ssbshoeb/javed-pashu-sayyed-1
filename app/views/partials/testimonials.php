<?php
/*
| Testimonial carousel, ported from src/components/Testimonials.tsx (which was
| itself ported from the `tcar` block in the original public/js/site.js).
|
| The markup is complete and readable with JavaScript off — every quote is in
| the track, the controls simply do nothing. js/site.js takes over the paging,
| recomputing the page count on resize and clamping the current page, because
| the number of quotes visible depends on the viewport: a visitor who paged to
| the end on a phone and then rotated the device otherwise landed on empty space.
*/

defined('APP_RUNNING') or exit('No direct access.');
?>
<div class="tcar" id="testimonials" data-reveal>
    <div class="tcar__viewport">
        <div class="tcar__track">
            <?php foreach ($items as $item): ?>
                <div class="tcar__item">
                    <figure class="quote">
                        <span class="quote__mark" aria-hidden="true">&ldquo;</span>
                        <blockquote><?= e($item['quote']) ?></blockquote>
                        <figcaption>
                            <?= e($item['attribution'] ?? 'Name withheld') ?> &mdash; <?= e($item['matter']) ?>
                        </figcaption>
                    </figure>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="tcar__controls">
        <button type="button" aria-label="Previous testimonials" data-tcar="prev" disabled>&larr;</button>
        <button type="button" aria-label="Next testimonials" data-tcar="next">&rarr;</button>
    </div>
</div>
