<?php
/*
|--------------------------------------------------------------------------
| Submissions
|--------------------------------------------------------------------------
| The four write paths, ported from SubmissionController through lib/actions.ts.
|
| Nothing here is persisted. As in the Laravel app and the Next.js port, a
| submission exists only for as long as it takes to email it to the chamber —
| the notification and the visitor's acknowledgement are the only record, and
| the privacy notice says so.
|
| Every path follows the same shape:
|
|   CSRF -> throttle -> validate -> notify the chamber -> acknowledge the
|   visitor -> redirect to the confirmation screen with the reference
|
| A failure at any step before delivery redirects back to the form with the
| errors and the old input flashed, which is what post/redirect/get is for: the
| visitor never sees a bare POST response, and a refresh never re-sends.
|
| Rate limiting mirrors the `throttle:8,1` middleware the routes carried.
*/

defined('APP_RUNNING') or exit('No direct access.');

const RATE_LIMIT = 8;

/** The message shown when the throttle refuses a submission. */
function too_many_requests_error(): array
{
    return ['Too many submissions from this connection. Please try again in a minute.'];
}

/** The message shown when SMTP would not take the message. */
function delivery_failed_error(): array
{
    return [
        'The submission could not be delivered to the chamber. Please try again, or write to '
            . practice('contact.email') . ' directly.',
    ];
}

/**
 * Flashes a failure and sends the visitor back to the form.
 *
 * @param string[]              $errors
 * @param array<string, string> $fieldErrors
 */
function submission_failed(string $form, string $back, array $errors, array $fieldErrors = [], string $fragment = ''): void
{
    flash_errors($errors, $fieldErrors, collect_values());
    flash_form($form);

    redirect($back . $fragment);
}

/**
 * The guard every write path opens with: a valid CSRF token and room in the
 * throttle bucket.
 *
 * Returns null when the request may proceed, or the error list when it may not.
 */
function submission_guard(string $bucket): ?array
{
    if (!csrf_valid()) {
        // Either a cross-site POST, or a session that expired while the form
        // sat open in a tab. The second is far more likely and is what the
        // wording addresses.
        return ['This form expired before it was submitted. Please check the details below and send it again.'];
    }

    if (throttled($bucket, RATE_LIMIT)) {
        return too_many_requests_error();
    }

    return null;
}

/**
 * Notification to the chamber. Reply-To is the visitor, so the advocate can
 * answer straight from the inbox.
 */
function notify_chamber(string $subject, string $html, array $replyTo, array $attachments = []): bool
{
    return mail_send([
        // NOTIFY_EMAIL lets a staging deploy divert submissions away from the chamber.
        'to' => (string) env('NOTIFY_EMAIL', practice('contact.notifyEmail')),
        'subject' => $subject,
        'html' => $html,
        'replyTo' => $replyTo,
        'attachments' => $attachments,
    ]);
}

/**
 * Acknowledgement to the visitor. Best-effort: if the chamber's copy went out
 * and this one did not, the submission is not lost, so a failure here is logged
 * (inside mail_send) and not shown to the visitor.
 */
function acknowledge_visitor(string $email, string $name, string $subject, string $kind, string $reference): void
{
    mail_send([
        'to' => $email,
        'toName' => $name,
        'subject' => $subject,
        'html' => acknowledgement_email($name, $reference, $kind),
    ]);
}

// ---------------------------------------------------------------------------

/**
 * The general enquiry, from the home page and from /contact.
 *
 * $back is the page the form sits on, so a failure returns the visitor to the
 * one they were actually on.
 */
function submit_enquiry(string $form, string $back, string $fragment = ''): void
{
    $blocked = submission_guard('enquiry');
    if ($blocked !== null) {
        submission_failed($form, $back, $blocked, [], $fragment);
        return;
    }

    $v = validate_enquiry();

    if (validator_failed($v)) {
        submission_failed($form, $back, $v['errors'], $v['fieldErrors'], $fragment);
        return;
    }

    $data = $v['data'];
    $record = [
        'reference' => reference('ENQ'),
        'kind' => 'enquiry',
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'area' => $data['area'],
        'subject' => $data['subject'],
        'jurisdiction' => null,
        'urgency' => null,
        'message' => $data['message'],
        'createdAt' => new DateTimeImmutable(),
    ];

    $delivered = notify_chamber(
        'Website enquiry — ' . $record['reference'],
        enquiry_email($record),
        ['address' => $record['email'], 'name' => $record['name']]
    );

    if (!$delivered) {
        submission_failed($form, $back, delivery_failed_error(), [], $fragment);
        return;
    }

    acknowledge_visitor(
        $record['email'],
        $record['name'],
        'We have received your enquiry',
        'enquiry',
        $record['reference']
    );

    redirect(route('submitted', 'enquiry') . '?ref=' . rawurlencode($record['reference']));
}

function submit_case_evaluation(): void
{
    $form = 'case-evaluation';
    $back = route('caseEvaluation');

    $blocked = submission_guard('case-evaluation');
    if ($blocked !== null) {
        submission_failed($form, $back, $blocked);
        return;
    }

    $v = validate_case_evaluation();

    if (validator_failed($v)) {
        submission_failed($form, $back, $v['errors'], $v['fieldErrors']);
        return;
    }

    $data = $v['data'];
    $record = [
        'reference' => reference('CE'),
        'kind' => 'case_evaluation',
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'area' => $data['area'],
        'subject' => 'Case evaluation request',
        'jurisdiction' => $data['jurisdiction'],
        'urgency' => $data['urgency'],
        'message' => $data['message'],
        'createdAt' => new DateTimeImmutable(),
    ];

    $delivered = notify_chamber(
        'Case evaluation request — ' . $record['reference'],
        enquiry_email($record),
        ['address' => $record['email'], 'name' => $record['name']]
    );

    if (!$delivered) {
        submission_failed($form, $back, delivery_failed_error());
        return;
    }

    acknowledge_visitor(
        $record['email'],
        $record['name'],
        'We have received your request',
        'case evaluation request',
        $record['reference']
    );

    redirect(route('submitted', 'case-evaluation') . '?ref=' . rawurlencode($record['reference']));
}

function submit_upload(): void
{
    $form = 'upload';
    $back = route('upload');

    $blocked = submission_guard('upload');
    if ($blocked !== null) {
        submission_failed($form, $back, $blocked);
        return;
    }

    $v = validate_upload();
    $fileResult = validate_upload_file();

    if (validator_failed($v) || !$fileResult['ok']) {
        $errors = $v['errors'];
        $fieldErrors = $v['fieldErrors'];

        if (!$fileResult['ok']) {
            $errors[] = $fileResult['message'];
            $fieldErrors['document'] = $fileResult['message'];
        }

        submission_failed($form, $back, $errors, $fieldErrors);
        return;
    }

    $file = $fileResult['file'];

    // Never written to a public path: the bytes go straight from PHP's own
    // temporary upload file into the notification email, and the temporary file
    // is removed when the request ends. Validation caps it at 10 MB, which is
    // also what the chamber's mailbox will accept.
    $content = @file_get_contents($file['tmp_name']);

    if ($content === false) {
        log_error('[upload] temporary file unreadable', ['name' => $file['name']]);
        submission_failed($form, $back, ['The file could not be read. Please try again.']);
        return;
    }

    $data = $v['data'];
    $record = [
        'reference' => reference('DOC'),
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'documentType' => $data['document_type'],
        'note' => $data['note'],
        'originalName' => $file['name'],
        'sizeBytes' => $file['size'],
        'mime' => $file['mime'],
        'createdAt' => new DateTimeImmutable(),
    ];

    $delivered = notify_chamber(
        'Document received — ' . $record['reference'],
        upload_email($record),
        ['address' => $record['email'], 'name' => $record['name']],
        [[
            'filename' => $record['originalName'],
            'content' => $content,
            'contentType' => $record['mime'],
        ]]
    );

    if (!$delivered) {
        submission_failed($form, $back, delivery_failed_error());
        return;
    }

    acknowledge_visitor(
        $record['email'],
        $record['name'],
        'We have received your document',
        'document',
        $record['reference']
    );

    redirect(route('submitted', 'upload') . '?ref=' . rawurlencode($record['reference']));
}

function submit_appointment(): void
{
    $form = 'appointment';
    $back = route('appointment');

    $blocked = submission_guard('appointment');
    if ($blocked !== null) {
        submission_failed($form, $back, $blocked);
        return;
    }

    $v = validate_appointment();

    if (validator_failed($v)) {
        submission_failed($form, $back, $v['errors'], $v['fieldErrors']);
        return;
    }

    $data = $v['data'];
    $record = [
        'reference' => reference('APT'),
        'name' => $data['name'],
        'email' => $data['email'],
        'phone' => $data['phone'],
        'preferredDate' => $data['preferred_date'],
        'preferredSlot' => $data['preferred_slot'],
        'mode' => $data['mode'],
        'area' => $data['area'],
        'note' => $data['note'],
        'createdAt' => new DateTimeImmutable(),
    ];

    $delivered = notify_chamber(
        'Appointment request — ' . $record['reference'],
        appointment_email($record),
        ['address' => $record['email'], 'name' => $record['name']]
    );

    if (!$delivered) {
        submission_failed($form, $back, delivery_failed_error());
        return;
    }

    acknowledge_visitor(
        $record['email'],
        $record['name'],
        'We have received your appointment request',
        'appointment request',
        $record['reference']
    );

    redirect(route('submitted', 'appointment') . '?ref=' . rawurlencode($record['reference']));
}
