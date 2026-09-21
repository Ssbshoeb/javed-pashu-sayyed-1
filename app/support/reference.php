<?php
/*
|--------------------------------------------------------------------------
| Submission references
|--------------------------------------------------------------------------
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * A short, human-readable reference the visitor is shown on screen and that
 * appears in the notification email, so a phone call about a submission can be
 * traced without an admin panel.
 *
 * Same shape as the Laravel and Next.js versions: PREFIX-yymmdd-XXXX.
 */
function reference(string $prefix): string
{
    $stamp = date('ymd');

    // Crockford-ish alphabet: no lowercase, and nothing a visitor can misread
    // over the telephone — no I, O, 0 or 1.
    $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $length = strlen($alphabet);

    $suffix = '';
    // random_bytes rather than rand(): the reference is quoted back over the
    // telephone as a weak identifier, and a predictable one invites guessing.
    $bytes = random_bytes(4);
    for ($i = 0; $i < 4; $i++) {
        $suffix .= $alphabet[ord($bytes[$i]) % $length];
    }

    return $prefix . '-' . $stamp . '-' . $suffix;
}

/** The pattern the confirmation screen validates a `?ref=` value against. */
function is_reference(string $value): bool
{
    return (bool) preg_match('/^[A-Z]{2,3}-\d{6}-[A-Z0-9]{4}$/', $value);
}
