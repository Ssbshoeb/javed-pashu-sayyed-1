<?php
/*
|--------------------------------------------------------------------------
| Dates
|--------------------------------------------------------------------------
| Article dates are plain YYYY-MM-DD strings in config/practice.php. They are
| parsed as UTC midnight and formatted in UTC so the published date never
| shifts by a day depending on where the server happens to be — which is
| exactly what parsing them in the server's local zone would do.
*/

defined('APP_RUNNING') or exit('No direct access.');

/** Parses a YYYY-MM-DD content date as UTC midnight. */
function utc_date(string $value): ?DateTimeImmutable
{
    $date = DateTimeImmutable::createFromFormat(
        'Y-m-d H:i:s',
        $value . ' 00:00:00',
        new DateTimeZone('UTC')
    );

    return $date === false ? null : $date;
}

/** Blade's `F Y` — "June 2026". */
function month_year(string $value): string
{
    $date = utc_date($value);

    return $date === null ? $value : $date->format('F Y');
}

/** Blade's `j F Y` — "18 June 2026". */
function long_date(string $value): string
{
    $date = utc_date($value);

    return $date === null ? $value : $date->format('j F Y');
}

/**
 * Blade's `l, j F Y` — "Monday, 24 August 2026". Used on the appointment
 * notification, where the day of the week is what the advocate reads first.
 */
function long_date_with_weekday(string $value): string
{
    $date = utc_date($value);

    return $date === null ? $value : $date->format('l, j F Y');
}

/**
 * The "Received" line on a notification email — Blade's `j F Y, g:i a`.
 *
 * Stamped in the application timezone, which bootstrap.php sets to
 * Asia/Kolkata unless SITE_TIMEZONE says otherwise. The chamber is in Mumbai,
 * and a server running on UTC would report an enquiry sent at 9 pm local as
 * having arrived at 3:30 pm.
 */
function received_at(DateTimeInterface $date): string
{
    return strtolower_period($date->format('j F Y, g:i a'));
}

/** PHP's `a` already gives "pm"; kept as a seam in case the format changes. */
function strtolower_period(string $value): string
{
    return str_replace(['AM', 'PM'], ['am', 'pm'], $value);
}

/**
 * Tomorrow, in YYYY-MM-DD — the `after:today` boundary on the appointment
 * form and the `min` attribute the date input carries.
 *
 * Computed in the application (chamber) timezone: a visitor in Mumbai picking
 * "tomorrow" at 11 pm must not be told the date is in the past because the
 * server's own day has not turned over yet.
 */
function earliest_appointment_date(): string
{
    return (new DateTimeImmutable('tomorrow'))->format('Y-m-d');
}
