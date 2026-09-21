<?php
/*
|--------------------------------------------------------------------------
| Email templates
|--------------------------------------------------------------------------
| The four notification / acknowledgement emails, ported from
| resources/views/emails/*.blade.php through lib/email-templates.ts.
|
| Written as HTML strings rather than view files on purpose: these are
| table-and-inline-style emails for Outlook and Gmail, not pages, and they are
| built inside a POST handler where no layout is running.
|
| Every interpolated value goes through e(). Nothing here may skip it: the name,
| the message and the filename are all typed by the visitor.
*/

defined('APP_RUNNING') or exit('No direct access.');

const HEAD_RULE = 'border-top:4px solid #7A2B34;';
const LABEL_CELL = 'padding:11px 26px;border-top:1px solid #EAE6E4;width:150px;color:#6E6266;font-size:11px;letter-spacing:1px;text-transform:uppercase;vertical-align:top;';
const VALUE_CELL = 'padding:11px 26px 11px 0;border-top:1px solid #EAE6E4;';

/** The outer document every message shares. */
function email_shell(string $inner, string $title, int $maxWidth = 640): string
{
    return '<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>' . e($title) . '</title></head>
<body style="margin:0;padding:24px;background:#F4F2F1;font-family:Arial,Helvetica,sans-serif;color:#191517;">
<div style="max-width:' . $maxWidth . 'px;margin:0 auto;background:#fff;border:1px solid #DFD8D6;">
' . $inner . '
</div>
</body>
</html>';
}

/**
 * The label/value table every notification opens with.
 *
 * @param array<int, array{0: string, 1: string}> $pairs
 */
function email_rows(array $pairs, string $valueStyle = ''): string
{
    $rows = '';

    foreach ($pairs as [$label, $value]) {
        $rows .= '    <tr>
      <td style="' . LABEL_CELL . '">' . e($label) . '</td>
      <td style="' . VALUE_CELL . $valueStyle . '">' . e($value) . '</td>
    </tr>' . "\n";
    }

    return '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;font-size:14px;">
' . $rows . '  </table>';
}

/** A free-text block — the message, or the sender's note. */
function email_block(string $label, string $body): string
{
    return '  <div style="padding:18px 26px;border-top:1px solid #EAE6E4;">
    <div style="color:#6E6266;font-size:11px;letter-spacing:1px;text-transform:uppercase;margin-bottom:8px;">' . e($label) . '</div>
    <div style="white-space:pre-wrap;line-height:1.6;font-size:14px;">' . e($body) . '</div>
  </div>';
}

/** The grey footer strip. The text is a trusted literal from this file. */
function email_foot_note(string $text): string
{
    return '  <div style="padding:16px 26px;background:#F4F2F1;border-top:1px solid #DFD8D6;font-size:12px;color:#6E6266;">
    ' . $text . '
  </div>';
}

/** An area slug rendered as its published title, or an em dash. */
function email_area_title(?string $slug): string
{
    if ($slug === null || $slug === '') {
        return '—';
    }

    return practice('areas.' . $slug . '.title', $slug);
}

/**
 * resources/views/emails/enquiry.blade.php
 *
 * Carries both the general enquiry and the case evaluation request: they hold
 * the same fields, and the ones a plain enquiry has no value for print as an
 * em dash rather than as an empty row.
 */
function enquiry_email(array $record): string
{
    $kindLabel = ($record['kind'] ?? '') === 'case_evaluation'
        ? 'Case evaluation request'
        : 'Website enquiry';

    $inner = '  <div style="' . HEAD_RULE . 'padding:22px 26px 14px;">
    <div style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#7A2B34;font-weight:bold;">' . e($kindLabel) . '</div>
    <div style="font-size:20px;margin-top:6px;">Reference ' . e($record['reference']) . '</div>
    <div style="font-size:13px;color:#6E6266;margin-top:4px;">
      Received ' . e(received_at($record['createdAt'])) . '
    </div>
  </div>

  ' . email_rows([
        ['Name', $record['name']],
        ['Email', $record['email']],
        ['Telephone', $record['phone']],
        ['Area', email_area_title($record['area'] ?? null)],
        ['Subject', $record['subject'] ?: '—'],
        ['Jurisdiction', $record['jurisdiction'] ?? '' ?: '—'],
        ['Urgency', !empty($record['urgency']) ? title_case($record['urgency']) : '—'],
    ]) . '

' . email_block('Message', $record['message']) . '

' . email_foot_note(
        'Reply to this email to answer the sender directly. This submission is not stored anywhere else — this email is the only record of it.'
    );

    return email_shell($inner, 'Website submission');
}

/** resources/views/emails/upload.blade.php */
function upload_email(array $record): string
{
    $inner = '  <div style="' . HEAD_RULE . 'padding:22px 26px 14px;">
    <div style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#7A2B34;font-weight:bold;">Document received</div>
    <div style="font-size:20px;margin-top:6px;">Reference ' . e($record['reference']) . '</div>
    <div style="font-size:13px;color:#6E6266;margin-top:4px;">
      Received ' . e(received_at($record['createdAt'])) . '
    </div>
  </div>

  ' . email_rows(
        [
            ['Name', $record['name']],
            ['Email', $record['email']],
            ['Telephone', $record['phone']],
            ['Type', title_case($record['documentType'])],
            ['File', $record['originalName']],
            ['Size', number_format($record['sizeBytes'] / 1024 / 1024, 2) . ' MB'],
        ],
        'word-break:break-all;'
    ) . '

' . (!empty($record['note']) ? email_block('Note from sender', $record['note']) : '') . '

' . email_foot_note(
        'The uploaded file is attached to this email only. It is not stored on the server — this email is the only copy.'
    );

    return email_shell($inner, 'Document received');
}

/** resources/views/emails/appointment.blade.php */
function appointment_email(array $record): string
{
    $modes = [
        'in_person' => 'At the chamber',
        'telephonic' => 'By telephone',
        'video' => 'Over video',
    ];

    $inner = '  <div style="' . HEAD_RULE . 'padding:22px 26px 14px;">
    <div style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#7A2B34;font-weight:bold;">Appointment request</div>
    <div style="font-size:20px;margin-top:6px;">Reference ' . e($record['reference']) . '</div>
    <div style="font-size:13px;color:#6E6266;margin-top:4px;">
      Received ' . e(received_at($record['createdAt'])) . '
    </div>
  </div>

  ' . email_rows([
        ['Name', $record['name']],
        ['Email', $record['email']],
        ['Telephone', $record['phone']],
        ['Date', long_date_with_weekday($record['preferredDate'])],
        ['Slot', $record['preferredSlot']],
        ['Mode', $modes[$record['mode']] ?? $record['mode']],
        ['Area', email_area_title($record['area'] ?? null)],
    ]) . '

' . (!empty($record['note']) ? email_block('Note', $record['note']) : '') . '

' . email_foot_note(
        'The visitor has been told this is a request, not a confirmed booking. Reply to this email or telephone to confirm or propose another time.'
    );

    return email_shell($inner, 'Appointment request');
}

/** resources/views/emails/acknowledgement.blade.php */
function acknowledgement_email(string $name, string $reference, string $kind): string
{
    $weekdayHours = practice('contact.hours')['Monday to Friday'];
    $firm = practice('firm');

    $inner = '  <div style="' . HEAD_RULE . 'padding:24px 28px 18px;">
    <div style="font-size:19px;">' . e($firm) . '</div>
    <div style="font-size:11px;letter-spacing:1.5px;text-transform:uppercase;color:#6E6266;margin-top:4px;">Advocate</div>
  </div>

  <div style="padding:22px 28px;border-top:1px solid #EAE6E4;font-size:15px;line-height:1.65;">
    <p style="margin:0 0 14px;">Dear ' . e($name) . ',</p>

    <p style="margin:0 0 14px;">
      Your ' . e($kind) . ' has been received at the chamber. It will be read and you will receive a
      reply at this email address.
    </p>

    <p style="margin:0 0 14px;">
      Your reference number is
      <strong style="font-family:Consolas,monospace;">' . e($reference) . '</strong>.
      Please quote it in any reply about this submission.
    </p>

    <p style="margin:0 0 14px;">
      Chamber hours are Monday to Friday, ' . e($weekdayHours) . '.
      If the matter is urgent and you have not already said so, reply to this email stating what
      the deadline is.
    </p>

    <p style="margin:0;">Regards,<br>' . e($firm) . '</p>
  </div>

  <div style="padding:18px 28px;background:#F4F2F1;border-top:1px solid #DFD8D6;font-size:12px;color:#6E6266;line-height:1.6;">
    <strong style="color:#191517;">Please note.</strong> This acknowledgement is automatic. Neither it
    nor your submission creates a lawyer–client relationship, and nothing in it is legal advice. An
    engagement begins only when it is recorded in writing. Do not send privileged or sensitive material
    by email before that point.
  </div>';

    return email_shell($inner, 'We have received your ' . $kind, 600);
}
