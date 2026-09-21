<?php
/*
| The general enquiry form, used on the home page and on /contact.
| Ported from src/components/forms/EnquiryForm.tsx.
|
| $idSuffix keeps the two instances' input ids distinct — the home page and the
| contact page each render one, and duplicate ids would break every `<label for>`
| on whichever came second. It also names the form in the flash bag, so a
| failure on one page never repopulates the other.
|
| $action is the path the form POSTs to: the page it sits on. A failed submit
| redirects back there with the errors, so the visitor never sees a bare POST
| response and a refresh never re-sends the message.
*/

defined('APP_RUNNING') or exit('No direct access.');

require_once __DIR__ . '/parts.php';

$form = 'enquiry-' . $idSuffix;
$id = static fn (string $field): string => $field . '-' . $idSuffix;
?>
<?= form_error_box($form) ?>

<form action="<?= e($action) ?>" method="post" class="form">
    <?= csrf_field() ?>
    <?= honeypot($id('website')) ?>

    <div class="form__grid">
        <div class="field">
            <label for="<?= e($id('name')) ?>">Full name <?= required_mark() ?></label>
            <input type="text" id="<?= e($id('name')) ?>" name="name"
                   value="<?= e(old('name', $form)) ?>" required maxlength="120"
                   <?= field_invalid_attrs('name', $form) ?>>
        </div>

        <div class="field">
            <label for="<?= e($id('email')) ?>">Email <?= required_mark() ?></label>
            <input type="email" id="<?= e($id('email')) ?>" name="email"
                   value="<?= e(old('email', $form)) ?>" required maxlength="150"
                   <?= field_invalid_attrs('email', $form) ?>>
        </div>

        <div class="field">
            <label for="<?= e($id('phone')) ?>">Telephone <?= required_mark() ?></label>
            <input type="tel" id="<?= e($id('phone')) ?>" name="phone"
                   value="<?= e(old('phone', $form)) ?>" required maxlength="20"
                   <?= field_invalid_attrs('phone', $form) ?>>
        </div>

        <div class="field">
            <label for="<?= e($id('area')) ?>">Area of law (optional)</label>
            <?= area_select($id('area'), $form) ?>
        </div>

        <div class="field field--wide">
            <label for="<?= e($id('subject')) ?>">Subject <?= required_mark() ?></label>
            <input type="text" id="<?= e($id('subject')) ?>" name="subject"
                   value="<?= e(old('subject', $form)) ?>" required maxlength="150"
                   <?= field_invalid_attrs('subject', $form) ?>>
        </div>

        <div class="field field--wide">
            <label for="<?= e($id('message')) ?>">Message <?= required_mark() ?></label>
            <textarea id="<?= e($id('message')) ?>" name="message" required maxlength="3000"
                      style="min-height:130px;"
                      <?= field_invalid_attrs('message', $form) ?>><?= e(old('message', $form)) ?></textarea>
            <span class="hint">Please do not include confidential or privileged material.</span>
        </div>

        <?= consent_field(', and I understand that sending this message does not create a lawyer&ndash;client relationship.', $form) ?>

        <div class="field field--wide">
            <button type="submit" class="btn btn--primary" data-pending="Sending&hellip;">Send enquiry</button>
        </div>
    </div>
</form>
