<?php
/*
|--------------------------------------------------------------------------
| Mail
|--------------------------------------------------------------------------
| Assembles a MIME message and hands it to the SMTP transport.
|
| MAIL_MAILER=log writes the message to the application log instead of sending
| it, exactly as Laravel's log mailer did. It is the default so a fresh
| checkout runs without SMTP credentials — and it is the setting that must
| never survive into production, hence the loud warning in mail_send().
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * Sends one message. Never throws: a submission that reached the server is
 * acknowledged to the visitor even if delivery fails, and the failure is
 * logged for whoever is watching the logs. The caller gets `false` so it can
 * decide what to show.
 *
 * @param array{
 *   to: string,
 *   toName?: string,
 *   subject: string,
 *   html: string,
 *   replyTo?: array{address: string, name?: string},
 *   attachments?: array<int, array{filename: string, path?: string, content?: string, contentType?: string}>
 * } $mail
 */
function mail_send(array $mail): bool
{
    $driver = strtolower((string) env('MAIL_MAILER', 'log'));

    // A live site running the log mailer takes the enquiry, shows the visitor a
    // reference number, and delivers nothing to the chamber. The loss is
    // invisible from both ends, so say it loudly in the log at the moment it
    // happens rather than discovering it weeks later.
    if (IS_PRODUCTION && $driver === 'log') {
        log_critical('Submission NOT delivered: MAIL_MAILER is "log" in production.', [
            'subject' => $mail['subject'],
            'intendedRecipient' => $mail['to'],
        ]);
    }

    if ($driver === 'log') {
        $attachments = array_map(
            static fn (array $a): string => $a['filename'],
            $mail['attachments'] ?? []
        );

        log_info('[mail:log] message not sent', [
            'to' => $mail['to'],
            'subject' => $mail['subject'],
            'attachments' => $attachments,
        ]);

        return true;
    }

    $fromAddress = (string) env('MAIL_FROM_ADDRESS', 'no-reply@localhost');
    $fromName = (string) env('MAIL_FROM_NAME', practice('firm'));

    $recipient = mail_clean_address($mail['to']);

    if ($recipient === null) {
        log_error('[mail] refused: recipient is not a valid address', ['to' => $mail['to']]);
        return false;
    }

    $message = mail_build($mail, $fromAddress, $fromName, $recipient);

    $error = null;
    $sent = send_smtp($fromAddress, [$recipient], $message, $error);

    if (!$sent) {
        log_error('[mail] delivery failed', [
            'subject' => $mail['subject'],
            'to' => $recipient,
            'reason' => $error,
        ]);
    }

    return $sent;
}

/**
 * The complete message: headers, blank line, body.
 *
 * Built by hand rather than with a library, and every part of it matters for
 * whether the message arrives:
 *
 *   - CRLF line endings throughout (RFC 5322 requires them; a bare LF gets
 *     messages rejected by strict servers)
 *   - a Message-ID on the sending domain
 *   - Date in RFC 2822 form
 *   - quoted-printable for the HTML, so an em dash or a rupee sign survives
 *     the 998-octet line limit intact
 *   - a plain-text alternative, because a message with no text part scores
 *     badly with every spam filter there is
 */
function mail_build(array $mail, string $fromAddress, string $fromName, string $recipient): string
{
    $boundaryMixed = 'mix_' . bin2hex(random_bytes(12));
    $boundaryAlt = 'alt_' . bin2hex(random_bytes(12));

    $attachments = $mail['attachments'] ?? [];
    $hasAttachments = $attachments !== [];

    $headers = [];
    $headers[] = 'Date: ' . date('r');
    $headers[] = 'Message-ID: <' . bin2hex(random_bytes(16)) . '@' . mail_domain($fromAddress) . '>';
    $headers[] = 'From: ' . mail_address_header($fromAddress, $fromName);
    $headers[] = 'To: ' . mail_address_header($recipient, $mail['toName'] ?? null);
    $headers[] = 'Subject: ' . mail_encode_header($mail['subject']);

    if (!empty($mail['replyTo']['address'])) {
        $replyAddress = mail_clean_address($mail['replyTo']['address']);

        // Reply-To is the visitor's own address, so the advocate can answer
        // straight from the inbox. It is the one header on the message that
        // carries a value the visitor supplied — anything that is not a valid
        // address is dropped rather than injected into the header block.
        if ($replyAddress !== null) {
            $headers[] = 'Reply-To: ' . mail_address_header($replyAddress, $mail['replyTo']['name'] ?? null);
        }
    }

    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'X-Mailer: javedpashusayyed.com';
    // The notification is machine-generated; this stops an out-of-office reply
    // bouncing back and forth with the chamber's own autoresponder.
    $headers[] = 'Auto-Submitted: auto-generated';

    $html = $mail['html'];
    $text = mail_html_to_text($html);

    $alternative = '--' . $boundaryAlt . "\r\n"
        . "Content-Type: text/plain; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: quoted-printable\r\n\r\n"
        . quoted_printable_encode($text) . "\r\n\r\n"
        . '--' . $boundaryAlt . "\r\n"
        . "Content-Type: text/html; charset=UTF-8\r\n"
        . "Content-Transfer-Encoding: quoted-printable\r\n\r\n"
        . quoted_printable_encode($html) . "\r\n\r\n"
        . '--' . $boundaryAlt . "--\r\n";

    if (!$hasAttachments) {
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundaryAlt . '"';

        return implode("\r\n", $headers) . "\r\n\r\n" . $alternative;
    }

    $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundaryMixed . '"';

    $body = '--' . $boundaryMixed . "\r\n"
        . 'Content-Type: multipart/alternative; boundary="' . $boundaryAlt . "\"\r\n\r\n"
        . $alternative;

    foreach ($attachments as $attachment) {
        $content = $attachment['content'] ?? null;

        if ($content === null && !empty($attachment['path'])) {
            $content = @file_get_contents($attachment['path']);
        }

        if ($content === false || $content === null) {
            log_error('[mail] attachment unreadable, message sent without it', [
                'filename' => $attachment['filename'],
            ]);
            continue;
        }

        $type = $attachment['contentType'] ?? 'application/octet-stream';

        $body .= '--' . $boundaryMixed . "\r\n"
            . 'Content-Type: ' . $type . '; name="' . mail_encode_header($attachment['filename']) . "\"\r\n"
            . "Content-Transfer-Encoding: base64\r\n"
            . 'Content-Disposition: attachment; filename="' . mail_encode_header($attachment['filename']) . "\"\r\n\r\n"
            // chunk_split at 76 characters: base64 in a MIME part must be
            // line-wrapped or the message is malformed.
            . chunk_split(base64_encode($content), 76, "\r\n") . "\r\n";
    }

    $body .= '--' . $boundaryMixed . "--\r\n";

    return implode("\r\n", $headers) . "\r\n\r\n" . $body;
}

/**
 * A bare, validated address, or null.
 *
 * This is the guard that makes header injection impossible: every address that
 * reaches a header goes through here first, and anything containing a newline
 * fails FILTER_VALIDATE_EMAIL and is rejected.
 */
function mail_clean_address(string $address): ?string
{
    $address = trim(str_replace(["\r", "\n", "\0"], '', $address));

    return filter_var($address, FILTER_VALIDATE_EMAIL) ? $address : null;
}

/** `"Display Name" <address@example.com>`, or the bare address. */
function mail_address_header(string $address, ?string $name = null): string
{
    if ($name === null || trim($name) === '') {
        return $address;
    }

    return mail_encode_header($name, true) . ' <' . $address . '>';
}

/**
 * RFC 2047 encoding for a header value.
 *
 * A Subject line reading "Website enquiry — ENQ-260830-K4TZ" contains an em
 * dash, which is not ASCII; sent raw it either arrives mangled or gets the
 * message rejected. Pure-ASCII values are left alone so the common case stays
 * readable in a mail log.
 *
 * Also strips CR and LF unconditionally — a name field is visitor-supplied,
 * and a newline in a header is a header-injection attempt.
 */
function mail_encode_header(string $value, bool $quoteIfPlain = false): string
{
    $value = str_replace(["\r", "\n", "\0"], '', $value);

    if (preg_match('/^[\x20-\x7E]*$/', $value)) {
        if (!$quoteIfPlain) {
            return $value;
        }

        // A display name containing a quote or a backslash has to be escaped
        // inside the quoted string, not just wrapped in one.
        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }

    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

/** The domain part of an address, for the Message-ID. */
function mail_domain(string $address): string
{
    $at = strrpos($address, '@');
    $domain = $at === false ? '' : substr($address, $at + 1);

    return $domain !== '' ? $domain : 'localhost';
}

/**
 * A readable plain-text alternative from the HTML body.
 *
 * Not a general HTML-to-text converter: it only has to handle the four
 * templates in email_templates.php, which are tables of label/value rows and a
 * few blocks of prose. Block-level tags become line breaks, everything else is
 * dropped, and entities are decoded back to the characters the visitor typed.
 */
function mail_html_to_text(string $html): string
{
    // Drop anything that is not content.
    $text = preg_replace('#<(head|style|script|title)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;

    // Block boundaries become line breaks before the tags are stripped, so the
    // rows of a table do not run together into one paragraph.
    $text = preg_replace('#<(br|/p|/div|/tr|/h[1-6])[^>]*>#i', "\n", $text) ?? $text;
    $text = preg_replace('#</td>#i', "  ", $text) ?? $text;

    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Collapse the whitespace the templates' indentation leaves behind, without
    // losing the paragraph breaks introduced above.
    $text = preg_replace('/[ \t]+/', ' ', $text) ?? $text;
    $text = preg_replace('/ *\n */', "\n", $text) ?? $text;
    $text = preg_replace('/\n{3,}/', "\n\n", $text) ?? $text;

    return trim($text);
}
