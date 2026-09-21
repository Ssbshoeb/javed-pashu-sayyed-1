<?php
/* Ported from src/components/forms/AppointmentForm.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

require_once __DIR__ . '/parts.php';

$form = 'appointment';
$hours = practice('contact.hours');

const MODE_OPTIONS = [
    ['in_person', 'At the chamber'],
    ['telephonic', 'By telephone'],
    ['video', 'Over video'],
];
?>
<?= form_error_box($form) ?>

<form action="<?= e(route('appointment')) ?>" method="post" class="form">
    <?= csrf_field() ?>
    <?= honeypot('website-ap') ?>

    <div class="form__grid">
        <div class="field">
            <label for="name">Full name <?= required_mark() ?></label>
            <input type="text" id="name" name="name" value="<?= e(old('name', $form)) ?>"
                   required maxlength="120" <?= field_invalid_attrs('name', $form) ?>>
        </div>

        <div class="field">
            <label for="email">Email <?= required_mark() ?></label>
            <input type="email" id="email" name="email" value="<?= e(old('email', $form)) ?>"
                   required maxlength="150" <?= field_invalid_attrs('email', $form) ?>>
        </div>

        <div class="field">
            <label for="phone">Telephone <?= required_mark() ?></label>
            <input type="tel" id="phone" name="phone" value="<?= e(old('phone', $form)) ?>"
                   required maxlength="20" <?= field_invalid_attrs('phone', $form) ?>>
        </div>

        <div class="field">
            <label for="area">Area of law (optional)</label>
            <?= area_select('area', $form) ?>
        </div>

        <div class="field">
            <label for="preferred_date">Preferred date <?= required_mark() ?></label>
            <?php /* `min` is the same boundary the server enforces — see
                     earliest_appointment_date(). Computed per request, so the page
                     cannot go stale overnight the way a cached one would. */ ?>
            <input type="date" id="preferred_date" name="preferred_date"
                   value="<?= e(old('preferred_date', $form)) ?>"
                   min="<?= e(earliest_appointment_date()) ?>" required
                   <?= field_invalid_attrs('preferred_date', $form) ?>>
            <span class="hint">
                Chamber hours: Monday to Friday <?= e($hours['Monday to Friday']) ?>,
                Saturday <?= e($hours['Saturday']) ?>. Closed on Sunday.
            </span>
        </div>

        <div class="field">
            <label for="preferred_slot">Preferred hour <?= required_mark() ?></label>
            <?= slot_select('preferred_slot', 'preferred_slot', practice('appointmentSlots'), $form, 'Select a slot') ?>
        </div>

        <div class="field field--wide">
            <label for="mode">Mode of consultation <?= required_mark() ?></label>
            <?= select_field('mode', 'mode', MODE_OPTIONS, $form) ?>
        </div>

        <div class="field field--wide">
            <label for="note">What the consultation concerns (optional)</label>
            <textarea id="note" name="note" maxlength="2000" style="min-height:110px;"
                      <?= field_invalid_attrs('note', $form) ?>><?= e(old('note', $form)) ?></textarea>
            <span class="hint">
                A sentence or two is enough &mdash; the details are discussed at the consultation itself.
            </span>
        </div>

        <?= consent_field(
            ', and I understand that this request does not confirm an appointment or create a '
            . 'lawyer&ndash;client relationship.',
            $form
        ) ?>

        <div class="field field--wide">
            <button type="submit" class="btn btn--primary" data-pending="Sending&hellip;">Send request</button>
        </div>
    </div>
</form>
