<?php
/*
|--------------------------------------------------------------------------
| Bootstrap
|--------------------------------------------------------------------------
| Loaded once by index.php, before anything else. Reads .env, starts the
| session, wires the error handler and pulls in the support layer.
|
| There is no Composer autoloader and no framework: every dependency of this
| application is a plain PHP file required from here, which is what lets it run
| on any shared host with PHP 8.0 or newer and nothing installed.
*/

defined('APP_RUNNING') or exit('No direct access.');

// ---------------------------------------------------------------------------
// Paths
// ---------------------------------------------------------------------------

define('APP_PATH', __DIR__);
define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('VIEW_PATH', APP_PATH . '/views');

// ---------------------------------------------------------------------------
// Environment
// ---------------------------------------------------------------------------

/**
 * Minimal .env reader.
 *
 * Values are put into a module-level array rather than $_ENV / putenv(), so
 * nothing here can leak into a child process or be read back by phpinfo().
 * Supports `KEY=value`, `KEY="quoted value"`, `#` comments and blank lines —
 * which is everything this application's .env actually uses.
 */
function env_load(string $file): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    if (!is_readable($file)) {
        return;
    }

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        $split = strpos($line, '=');
        if ($split === false) {
            continue;
        }

        $key = trim(substr($line, 0, $split));
        $value = trim(substr($line, $split + 1));

        // Strip one layer of matching quotes, exactly as Laravel's reader did.
        $length = strlen($value);
        if ($length >= 2) {
            $first = $value[0];
            $last = $value[$length - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        env_set($key, $value);
    }
}

/**
 * The in-process environment store. Called with a key and value it writes;
 * called bare it reads the whole store back.
 */
function env_store(?string $key = null, ?string $value = null): array
{
    static $values = [];

    if ($key !== null) {
        $values[$key] = (string) $value;
    }

    return $values;
}

/** Writes one environment value into the in-process store. */
function env_set(string $key, string $value): void
{
    env_store($key, $value);
}

/**
 * Reads an environment value.
 *
 * A real server variable (set through the control panel or an Apache SetEnv)
 * wins over the .env file, so a host that supplies credentials that way does
 * not need the file at all.
 */
function env(string $key, $default = null)
{
    $server = getenv($key);
    if ($server !== false && $server !== '') {
        return $server;
    }

    $values = env_store();

    return array_key_exists($key, $values) && $values[$key] !== '' ? $values[$key] : $default;
}

env_load(BASE_PATH . '/.env');

define('APP_ENV', strtolower((string) env('APP_ENV', 'production')));
define('APP_DEBUG', filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOL));
define('IS_PRODUCTION', APP_ENV === 'production');

// ---------------------------------------------------------------------------
// Errors
// ---------------------------------------------------------------------------

/*
| A PHP notice printed into the middle of a page is both an ugly break in the
| design and an information leak. In production nothing is displayed and
| everything is logged; in development the opposite, so a mistake is visible
| while it is being made.
*/
error_reporting(E_ALL);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', STORAGE_PATH . '/logs/php-error.log');

// ---------------------------------------------------------------------------
// Timezone
// ---------------------------------------------------------------------------

/*
| The chamber is in Mumbai, so the "Received" line on a notification email and
| the reference number's date stamp are both IST. The Next.js port ran on UTC
| and had to convert at every use; setting it once here means every date
| function in the application is already in the right zone.
*/
date_default_timezone_set((string) env('SITE_TIMEZONE', 'Asia/Kolkata'));

// ---------------------------------------------------------------------------
// Session
// ---------------------------------------------------------------------------

/*
| Used for two things only: the flash bag that carries validation errors and
| old input back to a form after a failed POST, and the CSRF token. No visitor
| data is kept in it, and it holds nothing after a successful submission.
*/
if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Lax',
    ]);

    session_name('jsa_session');
    @session_start();
}

// ---------------------------------------------------------------------------
// Support layer
// ---------------------------------------------------------------------------

require APP_PATH . '/support/helpers.php';
require APP_PATH . '/support/logger.php';
require APP_PATH . '/support/routes.php';
require APP_PATH . '/support/site.php';
require APP_PATH . '/support/images.php';
require APP_PATH . '/support/dates.php';
require APP_PATH . '/support/icons.php';
require APP_PATH . '/support/metadata.php';
require APP_PATH . '/support/jsonld.php';
require APP_PATH . '/support/reference.php';
require APP_PATH . '/support/rate_limit.php';
require APP_PATH . '/support/form_state.php';
require APP_PATH . '/support/validation.php';
require APP_PATH . '/support/smtp.php';
require APP_PATH . '/support/mail.php';
require APP_PATH . '/support/email_templates.php';
require APP_PATH . '/support/view.php';

// ---------------------------------------------------------------------------
// Content
// ---------------------------------------------------------------------------

/**
 * The practice content, loaded once.
 *
 * Everything the site says about the chamber comes from here — the same single
 * source of truth src/content/practice.ts was.
 */
function practice(?string $key = null, $default = null)
{
    static $practice = null;

    if ($practice === null) {
        $practice = require APP_PATH . '/config/practice.php';
    }

    if ($key === null) {
        return $practice;
    }

    // Dot notation: practice('contact.hours.Saturday').
    $value = $practice;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

/** Slugs of the practice areas that actually have a page, in display order. */
function area_slugs(): array
{
    return array_keys(practice('areas'));
}

/** Slugs of the published articles, in display order. */
function article_slugs(): array
{
    return array_keys(practice('articles'));
}

/** The four pages served under /legal/. */
function legal_pages(): array
{
    return ['disclaimer', 'privacy', 'terms', 'cookies'];
}

/** The four confirmation screens served under /submitted/. */
function submission_types(): array
{
    return ['enquiry', 'case-evaluation', 'upload', 'appointment'];
}
