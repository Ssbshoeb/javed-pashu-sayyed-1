<?php
/*
|--------------------------------------------------------------------------
| Validation
|--------------------------------------------------------------------------
| Ported one rule at a time from SubmissionController, through the zod schemas
| in lib/validation.ts.
|
| The message wording is Laravel's, so the error box on the form reads exactly
| as it did before — including the two overrides the controller carried for the
| consent checkbox and the honeypot.
|
| A tiny validator rather than a library: the eight rules this site needs are
| `required`, `max`, `email`, `in`, `accepted`, `date`, `after` and `file`, and
| a dependency for that would be a dependency to install on the host.
*/

defined('APP_RUNNING') or exit('No direct access.');

const CONSENT_MESSAGE = 'Please confirm you have read the notice before submitting.';
const HONEYPOT_MESSAGE = 'This submission could not be accepted.';

/** `The :label field is required.` */
function msg_required(string $label): string
{
    return 'The ' . $label . ' field is required.';
}

/** `The :label field must not be greater than :n characters.` */
function msg_too_long(string $label, int $max): string
{
    return 'The ' . $label . ' field must not be greater than ' . $max . ' characters.';
}

/** `The selected :label is invalid.` */
function msg_invalid_choice(string $label): string
{
    return 'The selected ' . $label . ' is invalid.';
}

/**
 * A validator instance is a plain array: the values it has accepted, the flat
 * error list and the per-field errors.
 */
function validator_new(): array
{
    return ['data' => [], 'errors' => [], 'fieldErrors' => []];
}

/**
 * Records a failure. Only the FIRST error per field goes into `fieldErrors`,
 * matching Laravel's behaviour and the React port's, but every message goes
 * into the flat list the error box prints.
 */
function validator_fail(array &$v, string $field, string $message): void
{
    $v['errors'][] = $message;

    if (!isset($v['fieldErrors'][$field])) {
        $v['fieldErrors'][$field] = $message;
    }
}

function validator_failed(array $v): bool
{
    return $v['errors'] !== [];
}

/** required|string|max:N */
function v_text(array &$v, string $field, string $label, int $max): void
{
    $value = post($field, '');

    if ($value === null || $value === '') {
        validator_fail($v, $field, msg_required($label));
        return;
    }

    // mb_strlen, not strlen: `max:150` in Laravel counted characters, and an
    // email or a message in Devanagari or with an em dash would otherwise be
    // rejected for a length the visitor cannot see.
    if (mb_strlen($value, 'UTF-8') > $max) {
        validator_fail($v, $field, msg_too_long($label, $max));
        return;
    }

    $v['data'][$field] = $value;
}

/** nullable|string|max:N — an empty field arrives as '' and becomes null. */
function v_optional_text(array &$v, string $field, string $label, int $max): void
{
    $value = post($field, '');

    if ($value === null || $value === '') {
        $v['data'][$field] = null;
        return;
    }

    if (mb_strlen($value, 'UTF-8') > $max) {
        validator_fail($v, $field, msg_too_long($label, $max));
        return;
    }

    $v['data'][$field] = $value;
}

/** required|email|max:150 */
function v_email(array &$v, string $field = 'email'): void
{
    $value = post($field, '');

    if ($value === null || $value === '') {
        validator_fail($v, $field, msg_required('email'));
        return;
    }

    if (mb_strlen($value, 'UTF-8') > 150) {
        validator_fail($v, $field, msg_too_long('email', 150));
        return;
    }

    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        validator_fail($v, $field, 'The email field must be a valid email address.');
        return;
    }

    $v['data'][$field] = $value;
}

/** required|in:<list> */
function v_in(array &$v, string $field, string $label, array $allowed): void
{
    $value = post($field, '');

    if ($value === null || $value === '') {
        validator_fail($v, $field, msg_required($label));
        return;
    }

    if (!in_array($value, $allowed, true)) {
        validator_fail($v, $field, msg_invalid_choice($label));
        return;
    }

    $v['data'][$field] = $value;
}

/** nullable|in:<list> */
function v_optional_in(array &$v, string $field, string $label, array $allowed): void
{
    $value = post($field, '');

    if ($value === null || $value === '') {
        $v['data'][$field] = null;
        return;
    }

    if (!in_array($value, $allowed, true)) {
        validator_fail($v, $field, msg_invalid_choice($label));
        return;
    }

    $v['data'][$field] = $value;
}

/**
 * `accepted` in Laravel: the box must be ticked.
 *
 * An unticked checkbox is simply absent from the form data, so a missing value
 * has to fail too — which is why this cannot be written as an `in:` rule.
 */
function v_consent(array &$v): void
{
    $value = post('consent', '');

    if (!in_array($value, ['1', 'on', 'true'], true)) {
        validator_fail($v, 'consent', CONSENT_MESSAGE);
        return;
    }

    $v['data']['consent'] = true;
}

/**
 * Honeypot — `max:0` in Laravel, i.e. it must arrive empty. A bot that fills
 * every input it finds trips this and is told nothing useful.
 */
function v_honeypot(array &$v): void
{
    $value = post('website', '');

    if ($value !== null && $value !== '') {
        validator_fail($v, 'website', HONEYPOT_MESSAGE);
    }
}

/** required|date|after:today, on the appointment form's preferred date. */
function v_appointment_date(array &$v): void
{
    $field = 'preferred_date';
    $value = post($field, '');

    if ($value === null || $value === '') {
        validator_fail($v, $field, msg_required('preferred date'));
        return;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        validator_fail($v, $field, 'The preferred date field must be a valid date.');
        return;
    }

    // A well-formed but impossible date ("2026-02-31") passes the pattern and
    // has to be caught by asking the date library to round-trip it.
    [$year, $month, $day] = array_map('intval', explode('-', $value));
    if (!checkdate($month, $day, $year)) {
        validator_fail($v, $field, 'The preferred date field must be a valid date.');
        return;
    }

    // String comparison is safe and exact for YYYY-MM-DD, and avoids any
    // timezone question about what "tomorrow" means.
    if ($value < earliest_appointment_date()) {
        validator_fail($v, $field, 'Please choose a date from tomorrow onwards.');
        return;
    }

    $v['data'][$field] = $value;
}

// ---------------------------------------------------------------------------
// The four form schemas
// ---------------------------------------------------------------------------

/** The general enquiry form, on the home page and on /contact. */
function validate_enquiry(): array
{
    $v = validator_new();

    v_text($v, 'name', 'name', 120);
    v_email($v);
    v_text($v, 'phone', 'phone', 20);
    v_optional_in($v, 'area', 'area', area_slugs());
    v_text($v, 'subject', 'subject', 150);
    v_text($v, 'message', 'message', 3000);
    v_consent($v);
    v_honeypot($v);

    return $v;
}

function validate_case_evaluation(): array
{
    $v = validator_new();

    v_text($v, 'name', 'name', 120);
    v_email($v);
    v_text($v, 'phone', 'phone', 20);
    v_in($v, 'area', 'area', area_slugs());
    v_text($v, 'jurisdiction', 'jurisdiction', 120);
    v_in($v, 'urgency', 'urgency', ['immediate', 'this_week', 'this_month', 'no_deadline']);
    v_text($v, 'message', 'message', 4000);
    v_consent($v);
    v_honeypot($v);

    return $v;
}

function validate_upload(): array
{
    $v = validator_new();

    v_text($v, 'name', 'name', 120);
    v_email($v);
    v_text($v, 'phone', 'phone', 20);
    v_in($v, 'document_type', 'document type', ['affidavit', 'agreement', 'notice', 'court_order', 'other']);
    v_optional_text($v, 'note', 'note', 2000);
    v_consent($v);
    v_honeypot($v);

    return $v;
}

function validate_appointment(): array
{
    $v = validator_new();

    v_text($v, 'name', 'name', 120);
    v_email($v);
    v_text($v, 'phone', 'phone', 20);
    v_appointment_date($v);
    // The same list the form offers — see config/practice.php. Keeping one list
    // read by both is what stopped the labels and the rule drifting apart.
    v_in($v, 'preferred_slot', 'preferred slot', practice('appointmentSlots'));
    v_in($v, 'mode', 'mode', ['in_person', 'telephonic', 'video']);
    v_optional_in($v, 'area', 'area', area_slugs());
    v_optional_text($v, 'note', 'note', 2000);
    v_consent($v);
    v_honeypot($v);

    return $v;
}

// ---------------------------------------------------------------------------
// Uploads
// ---------------------------------------------------------------------------

/** `max:10240` in Laravel is kilobytes. */
const MAX_UPLOAD_BYTES = 10 * 1024 * 1024;

const ALLOWED_UPLOAD_EXTENSIONS = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

/**
 * The types an attachment may be sent as. This is the list that reaches the
 * chamber's mailbox, so it is deliberately narrow.
 */
const ALLOWED_UPLOAD_MIMES = [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png',
];

/**
 * `required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png`.
 *
 * PHP can do here what the Node port could not: `finfo` sniffs the file's own
 * bytes rather than trusting the Content-Type the browser attached, which is
 * what Laravel's `mimes` rule did. The extension must agree with the sniffed
 * type, so a `.php` renamed to `.pdf` fails and a real PDF sent with a wrong
 * header still passes.
 *
 * Delivery is by email attachment; nothing is ever written to a public path or
 * executed, and the temporary file is discarded with the request.
 *
 * @return array{ok: true, file: array}|array{ok: false, message: string}
 */
function validate_upload_file(): array
{
    $file = $_FILES['document'] ?? null;

    if (!is_array($file) || !isset($file['error'])) {
        return ['ok' => false, 'message' => msg_required('document')];
    }

    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            return ['ok' => false, 'message' => msg_required('document')];
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            // The server refused it before this code ran. Say the size, not
            // "upload failed" — the visitor can act on the first and not
            // the second.
            return ['ok' => false, 'message' => 'The file must be 10 MB or smaller.'];
        default:
            log_error('[upload] transfer failed', ['code' => $file['error']]);
            return ['ok' => false, 'message' => 'The file could not be uploaded. Please try again.'];
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        // Only ever reachable if something other than a real multipart upload
        // set $_FILES. Refuse rather than read whatever path was named.
        return ['ok' => false, 'message' => msg_required('document')];
    }

    $size = (int) $file['size'];

    if ($size === 0) {
        return ['ok' => false, 'message' => msg_required('document')];
    }

    if ($size > MAX_UPLOAD_BYTES) {
        return ['ok' => false, 'message' => 'The file must be 10 MB or smaller.'];
    }

    $originalName = (string) $file['name'];
    $extension = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($extension, ALLOWED_UPLOAD_EXTENSIONS, true)) {
        return ['ok' => false, 'message' => 'Please upload a PDF, Word document or image.'];
    }

    $sniffed = upload_sniff_mime($file['tmp_name']);

    if (!mime_matches_extension($sniffed, $extension)) {
        return ['ok' => false, 'message' => 'Please upload a PDF, Word document or image.'];
    }

    return [
        'ok' => true,
        'file' => [
            'tmp_name' => $file['tmp_name'],
            // The visitor's filename is used as the attachment name and printed
            // in the notification email. It is sanitised here, at the boundary,
            // rather than trusted anywhere downstream.
            'name' => safe_filename($originalName, $extension),
            'size' => $size,
            // The canonical type for the extension, not whatever finfo happened
            // to call the container. This is what goes on the MIME part, and a
            // .docx labelled application/zip opens in the wrong program.
            'mime' => canonical_upload_mime($extension),
        ],
    ];
}

/** The file's own type, from its bytes. Falls back to the reported one. */
function upload_sniff_mime(string $path): string
{
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $type = finfo_file($finfo, $path);
            finfo_close($finfo);
            if (is_string($type) && $type !== '') {
                return $type;
            }
        }
    }

    // fileinfo is compiled in by default and present on every mainstream host,
    // but if it is genuinely absent the extension check above still stands and
    // this returns something the allow-list can reject rather than passing
    // everything through.
    return 'application/octet-stream';
}

/**
 * The sniffed type has to agree with the extension.
 *
 * Each extension carries more than one accepted type because finfo does not
 * name these formats consistently across builds:
 *
 *   - .docx is a ZIP container, and finfo reports it as application/zip unless
 *     the build's magic file knows the OOXML signature;
 *   - .doc is an OLE2 compound file, reported as application/msword,
 *     application/vnd.ms-office or application/CDFV2 depending on the build.
 *
 * Each alternative is accepted for ONE extension only, so a plain .zip renamed
 * to .docx is still refused: the pairing is what is checked, never the type on
 * its own.
 */
function mime_matches_extension(string $mime, string $extension): bool
{
    $map = [
        'pdf' => ['application/pdf'],
        'doc' => [
            'application/msword',
            'application/vnd.ms-office',
            'application/CDFV2',
            'application/CDFV2-corrupt',
        ],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
        ],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
    ];

    return in_array($mime, $map[$extension] ?? [], true);
}

/** The type an accepted upload is actually attached as. */
function canonical_upload_mime(string $extension): string
{
    $map = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
    ];

    return $map[$extension] ?? 'application/octet-stream';
}

/**
 * A filename safe to put in a Content-Disposition header and in an HTML email.
 *
 * Strips every directory separator and control character; keeps letters,
 * digits, spaces and the few punctuation marks a document name normally has.
 */
function safe_filename(string $name, string $extension): string
{
    $base = (string) pathinfo($name, PATHINFO_FILENAME);
    $base = preg_replace('/[^\p{L}\p{N} ._\-()]+/u', '', $base) ?? '';
    $base = trim(preg_replace('/\s+/', ' ', $base) ?? '');
    $base = mb_substr($base, 0, 100, 'UTF-8');

    if ($base === '') {
        $base = 'document';
    }

    return $base . '.' . $extension;
}
