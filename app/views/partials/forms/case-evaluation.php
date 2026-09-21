<?php
/* Ported from src/components/forms/CaseEvaluationForm.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

require_once __DIR__ . '/parts.php';

$form = 'case-evaluation';

const URGENCY_OPTIONS = [
    ['immediate', 'Immediately — a date or deadline is near'],
    ['this_week', 'Within this week'],
    ['this_month', 'Within this month'],
    ['no_deadline', 'No fixed deadline'],
];
?>
<?= form_error_box($form) ?>

<form action="<?= e(route('caseEvaluation')) ?>" method="post" class="form">
    <?= csrf_field() ?>
    <?= honeypot('website-ce') ?>

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
            <label for="area">Area of law <?= required_mark() ?></label>
            <?= area_select('area', $form, true) ?>
        </div>

        <div class="field">
            <label for="jurisdiction">Court or city where the matter lies <?= required_mark() ?></label>
            <input type="text" id="jurisdiction" name="jurisdiction"
                   value="<?= e(old('jurisdiction', $form)) ?>" required maxlength="120"
                   placeholder="e.g. City Civil Court, Mumbai"
                   <?= field_invalid_attrs('jurisdiction', $form) ?>>
        </div>

        <div class="field">
            <label for="urgency">How soon is a response needed? <?= required_mark() ?></label>
            <?= select_field('urgency', 'urgency', URGENCY_OPTIONS, $form) ?>
        </div>

        <div class="field field--wide">
            <label for="message">Brief facts of the matter <?= required_mark() ?></label>
            <textarea id="message" name="message" required maxlength="4000"
                      <?= field_invalid_attrs('message', $form) ?>><?= e(old('message', $form)) ?></textarea>
            <span class="hint">
                What happened, when, and what has been done so far. Please keep it factual and do not
                attach or paste privileged material.
            </span>
        </div>

        <?= consent_field(
            '. I understand that submitting this form does not create a lawyer&ndash;client relationship, '
            . 'that no legal advice is given in reply, and I consent to the details above being stored '
            . 'for the purpose of responding to me.',
            $form
        ) ?>

        <div class="field field--wide">
            <button type="submit" class="btn btn--primary" data-pending="Submitting&hellip;">Submit for evaluation</button>
        </div>
    </div>
</form>
