<?php
/*
|--------------------------------------------------------------------------
| SMTP transport
|--------------------------------------------------------------------------
| A small, dependency-free SMTP client: connect, EHLO, optional STARTTLS,
| AUTH, MAIL FROM / RCPT TO / DATA, QUIT.
|
| WHY NOT mail(). PHP's mail() hands the message to a local sendmail binary.
| Shared hosts either do not have one, or have one whose envelope sender is a
| server-owned address the receiving side cannot align with the From header —
| which is exactly what puts an enquiry notification in the spam folder or has
| it rejected outright for SPF/DKIM failure. This site's mail has to be
| authenticated against the chamber's own mailbox to arrive at all, and that
| means speaking SMTP.
|
| WHY NOT PHPMailer. It would do this well, and if Composer is available on the
| host it is a perfectly good swap — send_smtp() is the only function to
| replace. It is not used here so that the whole application can be uploaded by
| FTP to any host with PHP and work, with nothing to install and nothing to
| keep updated.
|
| Everything below speaks RFC 5321 line-by-line and fails closed: any
| unexpected response code aborts the conversation and returns false, and the
| caller (mail.php) turns that into the "could not be delivered" message the
| visitor sees.
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * Delivers one already-assembled message.
 *
 * @param string   $envelopeFrom Address for MAIL FROM — must be the authenticated mailbox.
 * @param string[] $recipients   Addresses for RCPT TO.
 * @param string   $data         The complete message: headers, blank line, body.
 * @param string   $error        Set to the reason on failure, for the log.
 */
function send_smtp(string $envelopeFrom, array $recipients, string $data, ?string &$error = null): bool
{
    $host = (string) env('MAIL_HOST', '127.0.0.1');
    $port = (int) env('MAIL_PORT', 587);
    $scheme = strtolower((string) env('MAIL_SCHEME', ''));
    $username = (string) env('MAIL_USERNAME', '');
    $password = (string) env('MAIL_PASSWORD', '');
    $timeout = (int) env('MAIL_TIMEOUT', 20);

    // Implicit TLS on 465, STARTTLS everywhere else — the same rule Laravel's
    // MAIL_SCHEME=smtps encoded and the Node port's `secure` flag repeated.
    $implicitTls = $scheme === 'smtps' || $port === 465;

    $transport = $implicitTls ? 'ssl://' : 'tcp://';

    /*
    | Certificate verification is ON. A mail server whose certificate does not
    | verify is either misconfigured or not the server you think you are
    | talking to, and this connection carries the mailbox password. If a host's
    | own certificate chain is genuinely broken, fix the chain rather than
    | turning this off.
    */
    $context = stream_context_create([
        'ssl' => [
            'verify_peer' => true,
            'verify_peer_name' => true,
            'allow_self_signed' => false,
            'SNI_enabled' => true,
            'peer_name' => $host,
        ],
    ]);

    $socket = @stream_socket_client(
        $transport . $host . ':' . $port,
        $errorNumber,
        $errorMessage,
        $timeout,
        STREAM_CLIENT_CONNECT,
        $context
    );

    if ($socket === false) {
        $error = 'connect failed: ' . $errorMessage . ' (' . $errorNumber . ')';
        return false;
    }

    stream_set_timeout($socket, $timeout);

    $fail = static function (string $reason) use ($socket, &$error): bool {
        $error = $reason;
        @fclose($socket);
        return false;
    };

    // Greeting.
    if (!smtp_expect($socket, 220, $reply)) {
        return $fail('greeting: ' . $reply);
    }

    $ehloName = smtp_client_name();

    if (!smtp_command($socket, 'EHLO ' . $ehloName, 250, $reply)) {
        // Some very old servers only speak HELO. Worth one fallback before
        // giving up, because the failure is otherwise indistinguishable from a
        // credentials problem in the log.
        if (!smtp_command($socket, 'HELO ' . $ehloName, 250, $reply)) {
            return $fail('EHLO refused: ' . $reply);
        }
    }

    $capabilities = strtoupper($reply);

    // STARTTLS: upgrade the plain connection before anything secret is sent.
    if (!$implicitTls && str_contains($capabilities, 'STARTTLS')) {
        if (!smtp_command($socket, 'STARTTLS', 220, $reply)) {
            return $fail('STARTTLS refused: ' . $reply);
        }

        $crypto = @stream_socket_enable_crypto(
            $socket,
            true,
            STREAM_CRYPTO_METHOD_TLS_CLIENT
        );

        if ($crypto !== true) {
            return $fail('TLS negotiation failed');
        }

        // The capability list has to be re-read inside the TLS session: what a
        // server offers before the upgrade is not what it offers after, and
        // AUTH in particular is usually only advertised once encrypted.
        if (!smtp_command($socket, 'EHLO ' . $ehloName, 250, $reply)) {
            return $fail('EHLO after STARTTLS refused: ' . $reply);
        }

        $capabilities = strtoupper($reply);
    }

    // AUTH, when credentials are configured.
    if ($username !== '') {
        if (!smtp_authenticate($socket, $capabilities, $username, $password, $reply)) {
            return $fail('authentication failed: ' . $reply);
        }
    }

    if (!smtp_command($socket, 'MAIL FROM:<' . $envelopeFrom . '>', 250, $reply)) {
        return $fail('MAIL FROM refused: ' . $reply);
    }

    foreach ($recipients as $recipient) {
        // 251 is "user not local; will forward" — an accepted recipient.
        if (!smtp_command($socket, 'RCPT TO:<' . $recipient . '>', [250, 251], $reply)) {
            return $fail('RCPT TO refused for ' . $recipient . ': ' . $reply);
        }
    }

    if (!smtp_command($socket, 'DATA', 354, $reply)) {
        return $fail('DATA refused: ' . $reply);
    }

    // Dot-stuffing: a body line that begins with '.' would otherwise end the
    // message early. Required by RFC 5321 §4.5.2.
    $lines = explode("\r\n", $data);
    foreach ($lines as $line) {
        if (isset($line[0]) && $line[0] === '.') {
            $line = '.' . $line;
        }
        fwrite($socket, $line . "\r\n");
    }

    fwrite($socket, ".\r\n");

    if (!smtp_expect($socket, 250, $reply)) {
        return $fail('message rejected: ' . $reply);
    }

    // Politeness, and it flushes the queue on some servers. A failure here does
    // not matter: the message is already accepted.
    smtp_command($socket, 'QUIT', 221, $reply);
    @fclose($socket);

    return true;
}

/**
 * The name given in EHLO.
 *
 * Some servers reject a bare or bracketed-IP identity. The site's own hostname
 * is both accurate and what the receiving server can look up.
 */
function smtp_client_name(): string
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $host = preg_replace('/:\d+$/', '', $host) ?? '';

    if ($host !== '' && preg_match('/^[A-Za-z0-9.\-]+$/', $host)) {
        return $host;
    }

    $from = (string) env('MAIL_FROM_ADDRESS', '');
    $at = strrpos($from, '@');

    if ($at !== false) {
        $domain = substr($from, $at + 1);
        if ($domain !== '') {
            return $domain;
        }
    }

    return 'localhost';
}

/** Writes one command and reads the reply. */
function smtp_command($socket, string $command, $expected, ?string &$reply = null): bool
{
    fwrite($socket, $command . "\r\n");

    return smtp_expect($socket, $expected, $reply);
}

/**
 * Reads a complete reply (following `250-` continuation lines) and checks its
 * code against the expected one, or one of several.
 */
function smtp_expect($socket, $expected, ?string &$reply = null): bool
{
    $reply = '';
    $code = null;

    while (true) {
        $line = fgets($socket, 1024);

        if ($line === false) {
            $meta = stream_get_meta_data($socket);
            $reply .= $meta['timed_out'] ? '[timed out]' : '[connection closed]';
            return false;
        }

        $reply .= $line;
        $line = rtrim($line, "\r\n");

        if (strlen($line) < 4) {
            // A reply shorter than "NNN " is malformed; stop rather than loop.
            break;
        }

        $code = (int) substr($line, 0, 3);

        // A hyphen in the fourth column means another line follows.
        if ($line[3] !== '-') {
            break;
        }
    }

    $codes = is_array($expected) ? $expected : [$expected];

    return $code !== null && in_array($code, $codes, true);
}

/**
 * AUTH, preferring the mechanism the server actually advertised.
 *
 * LOGIN and PLAIN are the two every provider supports. CRAM-MD5 is deliberately
 * not implemented: it is weaker than sending the password over the TLS channel
 * that is already established, and no mainstream provider requires it.
 */
function smtp_authenticate($socket, string $capabilities, string $username, string $password, ?string &$reply): bool
{
    if (str_contains($capabilities, 'LOGIN')) {
        if (!smtp_command($socket, 'AUTH LOGIN', 334, $reply)) {
            return false;
        }
        if (!smtp_command($socket, base64_encode($username), 334, $reply)) {
            return false;
        }

        return smtp_command($socket, base64_encode($password), 235, $reply);
    }

    if (str_contains($capabilities, 'PLAIN')) {
        $token = base64_encode("\0" . $username . "\0" . $password);

        return smtp_command($socket, 'AUTH PLAIN ' . $token, 235, $reply);
    }

    // Nothing advertised, but credentials are configured: try LOGIN anyway
    // rather than sending the message unauthenticated, which would be rejected
    // or silently spam-filed.
    if (!smtp_command($socket, 'AUTH LOGIN', 334, $reply)) {
        return false;
    }
    if (!smtp_command($socket, base64_encode($username), 334, $reply)) {
        return false;
    }

    return smtp_command($socket, base64_encode($password), 235, $reply);
}
