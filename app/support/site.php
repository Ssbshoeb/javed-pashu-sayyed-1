<?php
/*
|--------------------------------------------------------------------------
| The site's own absolute origin
|--------------------------------------------------------------------------
| Needed for canonical URLs, og:url, the sitemap and the JSON-LD @id values.
|
| SITE_URL in .env is authoritative. Unlike the Next.js port — which built the
| sitemap and the metadata without a request to read a host from — PHP always
| has one, so the request is a genuine fallback here rather than a guess: a
| deploy that forgets to set SITE_URL still produces correct absolute URLs for
| whatever host it is being served on.
*/

defined('APP_RUNNING') or exit('No direct access.');

function site_url(): string
{
    static $url = null;

    if ($url !== null) {
        return $url;
    }

    $explicit = trim((string) env('SITE_URL', ''));

    if ($explicit !== '') {
        return $url = rtrim($explicit, '/');
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || ((int) ($_SERVER['SERVER_PORT'] ?? 80) === 443);

    $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');

    // Host is attacker-controlled on a misconfigured server. Anything that is
    // not a plain host[:port] is refused rather than reflected into a canonical
    // tag, where it would be a redirect for every crawler that read the page.
    if (!preg_match('/^[A-Za-z0-9.\-]+(?::\d{1,5})?$/', $host)) {
        $host = 'localhost';
    }

    return $url = ($secure ? 'https://' : 'http://') . $host;
}

/** Absolute URL for a site-relative path, e.g. absolute_url('/about'). */
function absolute_url(string $path = '/'): string
{
    return $path === '/' ? site_url() . '/' : site_url() . $path;
}
