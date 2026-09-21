<?php
/*
| Ported from src/components/forms/UploadForm.tsx.
|
| enctype="multipart/form-data" is what carries the file. Without it the fields
| arrive and $_FILES is empty, which is the single easiest thing to get wrong on
| this page.
*/

defined('APP_RUNNING') or exit('No direct access.');

require_once __DIR__ . '/parts.php';

$form = 'upload';

const DOCUMENT_TYPE_OPTIONS = [
    ['affidavit', 'Affidavit'],
    ['agreement', 'Agreement or deed'],
    ['notice', 'Notice received or issued'],
    ['court_order', 'Court order or pleading'],
    ['other', 'Other'],
];
?>
<?= form_error_box($form) ?>

<form action="<?= e(route('upload')) ?>" method="post" class="form" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <?php /* Read by PHP before the upload is processed; a file larger than this
             is refused by the server rather than transferred in full. */ ?>
    <input type="hidden" name="MAX_FILE_SIZE" value="<?= e((string) MAX_UPLOAD_BYTES) ?>">
    <?= honeypot('website-up') ?>

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
            <label for="document_type">Type of document <?= required_mark() ?></label>
            <?= select_field('document_type', 'document_type', DOCUMENT_TYPE_OPTIONS, $form) ?>
        </div>

        <div class="field field--wide">
            <label for="document">Document <?= required_mark() ?></label>
            <?php /* data-max-mb is read by js/site.js, which rewrites the hint below
                     with the chosen file's size — so an oversized file is caught at
                     the file picker rather than after a long POST. */ ?>
            <input type="file" id="document" name="document" required
                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                   data-max-mb="10" data-note="document-note"
                   <?= field_invalid_attrs('document', $form) ?>>
            <span class="hint" id="document-note">PDF, Word or image. Maximum 10 MB.</span>
        </div>

        <div class="field field--wide">
            <label for="note">Note (optional)</label>
            <textarea id="note" name="note" maxlength="2000" style="min-height:110px;"
                      <?= field_invalid_attrs('note', $form) ?>><?= e(old('note', $form)) ?></textarea>
            <span class="hint">What the document relates to, and what you would like looked at.</span>
        </div>

        <?= consent_field(
            '. I confirm I am entitled to share this document, I understand that uploading it does not '
            . 'create a lawyer&ndash;client relationship, and I consent to it being stored for the purpose '
            . 'of responding to me.',
            $form
        ) ?>

        <div class="field field--wide">
            <button type="submit" class="btn btn--primary" data-pending="Uploading&hellip;">Upload document</button>
        </div>
    </div>
</form>
