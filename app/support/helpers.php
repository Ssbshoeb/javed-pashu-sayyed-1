<?php
/*
|--------------------------------------------------------------------------
| General helpers
|--------------------------------------------------------------------------
| The handful of functions every view and controller reaches for.
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * HTML-escape.
 *
 * Blade's `{{ }}` did this for every value it printed and the React port did it
 * implicitly; in a plain PHP template nothing is escaped unless it is asked for,
 * so this is called at EVERY interpolation in every view. The only exceptions
 * are the icon glyphs and the JSON-LD block, both of which are trusted literals
 * built on the server and documented as such where they are emitted.
 */
function e($value): string
{
    if ($value === null || $value === false) {
        return '';
    }

    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * JSON encoded for embedding directly inside a `<script>` element.
 *
 * JSON_HEX_TAG turns `<` and `>` into < / >, so a string containing
 * `</script>` can never close the tag early — the same guard the React port's
 * `.replace(/</g, '\\u003c')` provided.
 */
function e_json($value): string
{
    return (string) json_encode(
        $value,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
            | JSON_HEX_APOS | JSON_HEX_QUOT
    );
}

/**
 * Cache-busting asset URL: `/css/site.css?v=<mtime>`.
 *
 * A stylesheet change that a visitor's browser keeps serving from its cache is
 * the single most common "the site did not update" report, and a shared host
 * has no build step to fingerprint filenames. The file's modification time
 * costs one stat and solves it.
 */
function asset(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $file = BASE_PATH . $path;

    $version = is_file($file) ? filemtime($file) : null;

    return $version ? $path . '?v=' . $version : $path;
}

/** The current request path, without the query string. Always starts with '/'. */
function request_path(): string
{
    static $path = null;

    if ($path === null) {
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $position = strpos($uri, '?');

        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }

        $path = '/' . trim(rawurldecode($uri), '/');
        $path = $path === '/' ? '/' : rtrim($path, '/');
    }

    return $path;
}

/** The request method, upper-cased. */
function request_method(): string
{
    return strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
}

/** One value from the POST body, trimmed, or the given default. */
function post(string $key, ?string $default = null): ?string
{
    $value = $_POST[$key] ?? null;

    if (!is_string($value)) {
        return $default;
    }

    // Strip the null bytes and control characters a scripted submission can
    // carry; they have no business in any field this site accepts.
    $value = str_replace(["\0", "\r"], ['', ''], $value);

    return trim($value);
}

/** One value from the query string, or the given default. */
function query(string $key, ?string $default = null): ?string
{
    $value = $_GET[$key] ?? null;

    return is_string($value) ? $value : $default;
}

/**
 * Send a redirect and stop.
 *
 * 303 rather than 302 after a POST, so the browser is told in as many words to
 * follow it with a GET — which is what stops a refresh on the confirmation
 * screen re-submitting the form.
 */
function redirect(string $url, int $status = 303): void
{
    header('Location: ' . $url, true, $status);
    exit;
}

/** Redirect back to the page the form was submitted from. */
function redirect_back(string $fallback, string $fragment = ''): void
{
    $referer = (string) ($_SERVER['HTTP_REFERER'] ?? '');
    $target = $fallback;

    // Only ever follow a Referer that points at this same site: an open
    // redirect here would let a phishing page bounce a visitor off this domain.
    if ($referer !== '') {
        $parts = parse_url($referer);
        $host = $parts['host'] ?? '';

        if ($host !== '' && $host === (string) ($_SERVER['HTTP_HOST'] ?? '')) {
            $target = ($parts['path'] ?? $fallback) . (isset($parts['query']) ? '?' . $parts['query'] : '');
        }
    }

    redirect($target . $fragment);
}

/** True when the string is a well-formed slug this app would route to. */
function is_slug(string $value): bool
{
    return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value);
}

/**
 * Title Case from a snake_case value — "court_order" becomes "Court order".
 * Matches the `titleCase` helper the email templates carried.
 */
function title_case(string $value): string
{
    $spaced = str_replace('_', ' ', $value);

    return mb_strtoupper(mb_substr($spaced, 0, 1)) . mb_substr($spaced, 1);
}

/**
 * The CSRF token for this session, minted on first use.
 *
 * Server Actions in the Next.js port got an origin check from the framework.
 * A plain PHP form gets nothing for free, so every POST on this site carries
 * this token and every handler verifies it.
 */
function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['_csrf'];
}

/** The hidden input every form on the site renders. */
function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

/** True when the submitted token matches the session's. */
function csrf_valid(): bool
{
    $submitted = $_POST['_token'] ?? '';

    return is_string($submitted)
        && $submitted !== ''
        && !empty($_SESSION['_csrf'])
        && hash_equals((string) $_SESSION['_csrf'], $submitted);
}

/**
 * Best-effort client address.
 *
 * Behind a proxy or CDN the socket address is the proxy's, so the forwarded
 * headers are preferred — the same reason the Laravel app trusted proxies
 * at '*'.
 *
 * Only ever used as a rate-limit bucket key, never stored: the privacy notice
 * says no IP address is retained, and nothing here writes one anywhere.
 */
function client_key(): string
{
    $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($forwarded !== '') {
        $first = explode(',', $forwarded)[0];
        return trim($first);
    }

    foreach (['HTTP_X_REAL_IP', 'HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR'] as $key) {
        if (!empty($_SERVER[$key])) {
            return (string) $_SERVER[$key];
        }
    }

    return 'unknown';
}
