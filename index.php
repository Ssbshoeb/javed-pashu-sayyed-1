<?php
/*
|--------------------------------------------------------------------------
| Front controller
|--------------------------------------------------------------------------
| Every request that is not a real file on disk arrives here (see .htaccess),
| and this file decides what to do with it.
|
| Routing is a plain match on the request path rather than a pattern engine:
| the site has fourteen page routes, two SEO endpoints and one JSON endpoint,
| all known at author time, and a table anyone can read top to bottom is worth
| more here than a router.
|
| Every URL the Laravel app and the Next.js port served is served here at the
| same path, so no redirects are needed on migration.
*/

define('APP_RUNNING', true);

require __DIR__ . '/app/bootstrap.php';
require APP_PATH . '/controllers/pages.php';
require APP_PATH . '/controllers/submissions.php';
require APP_PATH . '/controllers/seo.php';
require APP_PATH . '/controllers/disclaimer.php';

// ---------------------------------------------------------------------------
// Security headers
// ---------------------------------------------------------------------------

/*
| Ported from app/Http/Middleware/SecurityHeaders.php via next.config.ts.
|
| Sent from PHP rather than only from .htaccess so they survive a move to
| nginx, LiteSpeed or a host that ignores per-directory Apache config — the
| .htaccess copy is a belt-and-braces duplicate for static files, which never
| reach this file.
*/
function send_security_headers(): void
{
    /*
    | Google Analytics is only allowed through the policy when a measurement ID
    | is actually configured. Without this the tag loads, reports no error the
    | visitor or the advocate would ever see, and silently records nothing — the
    | policy blocks it. Kept conditional so a site with no analytics keeps the
    | tighter policy.
    */
    $gaEnabled = (string) practice('analytics.ga4Id') !== '';

    $gaScript = $gaEnabled ? ' https://www.googletagmanager.com' : '';
    $gaConnect = $gaEnabled
        ? ' https://www.google-analytics.com https://*.google-analytics.com'
            . ' https://*.analytics.google.com https://www.googletagmanager.com'
        : '';
    $gaImg = $gaEnabled ? ' https://www.google-analytics.com https://www.googletagmanager.com' : '';

    /*
    | script-src needs 'unsafe-inline' because the site ships inline JSON-LD
    | (schema.org) <script> blocks. style-src needs it because the pages carry
    | inline style attributes throughout — hero background plates, parallax
    | bands, the per-page spacing overrides. Tightening either further would
    | mean reworking those rather than just changing a header.
    |
    | Note this port has NO inline application JavaScript at all: js/site.js is
    | an external file and reads its configuration from a data attribute. That
    | is one of the two things that would have to change for script-src to drop
    | 'unsafe-inline'; moving the JSON-LD to a served file is the other.
    */
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline'" . $gaScript,
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com",
        "img-src 'self' data:" . $gaImg,
        'frame-src https://maps.google.com https://www.google.com',
        "connect-src 'self'" . $gaConnect,
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'self'",
    ]);

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');
    header('Content-Security-Policy: ' . $csp);

    // HSTS is sent only when the request itself arrived over HTTPS. Promising
    // it over plain HTTP (local dev, a tunnel without TLS) would be a lie the
    // browser then enforces on every future visit to the host.
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    if ($secure) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }

    // Pages are personalised only by the flash bag, which is one-shot: a cached
    // copy would show one visitor's validation errors to the next.
    header('Cache-Control: no-store, must-revalidate');
}

send_security_headers();

// ---------------------------------------------------------------------------
// Routing
// ---------------------------------------------------------------------------

$path = request_path();
$method = request_method();

/*
| Two page paths accept a POST as well as a GET, because the enquiry form lives
| on both and posts to the page it sits on. The three dedicated form pages do
| the same. Everything else is GET only.
*/
if ($method === 'POST') {
    switch ($path) {
        case route('home'):
            // `#contact` puts the visitor back on the form and its error box
            // rather than at the top of a long page.
            submit_enquiry('enquiry-h', route('home'), '#contact');
            exit;

        case route('contact'):
            submit_enquiry('enquiry-c', route('contact'), '#contact');
            exit;

        case route('caseEvaluation'):
            submit_case_evaluation();
            exit;

        case route('upload'):
            submit_upload();
            exit;

        case route('appointment'):
            submit_appointment();
            exit;

        case '/api/disclaimer/accept':
            record_disclaimer_acceptance();
            exit;
    }

    // A POST to any other path is not a route this site has. 405 rather than
    // 404, because the path may well exist as a GET.
    header('Allow: GET');
    page_not_found(405);
    exit;
}

if ($method !== 'GET' && $method !== 'HEAD') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit;
}

switch ($path) {
    case route('home'):
        page_home();
        exit;

    case route('about'):
        page_about();
        exit;

    case route('achievements'):
        page_achievements();
        exit;

    case route('whyUs'):
        page_why_us();
        exit;

    case route('areas'):
        page_areas();
        exit;

    case route('insights'):
        page_insights();
        exit;

    case route('caseEvaluation'):
        page_case_evaluation();
        exit;

    case route('upload'):
        page_upload();
        exit;

    case route('appointment'):
        page_appointment();
        exit;

    case route('contact'):
        page_contact();
        exit;

    case '/robots.txt':
        serve_robots();
        exit;

    case '/sitemap.xml':
        serve_sitemap();
        exit;
}

/*
| The four parameterised routes. The slug is matched against the list of pages
| that actually exist rather than being used to look anything up — an unknown
| slug is a 404, exactly as `dynamicParams = false` made it in the Next.js port.
*/
$segments = array_values(array_filter(explode('/', $path), static fn (string $part): bool => $part !== ''));

if (count($segments) === 2) {
    [$prefix, $slug] = $segments;

    if (is_slug($slug)) {
        switch ($prefix) {
            case 'practice-areas':
                page_area($slug);
                exit;

            case 'insights':
                page_article($slug);
                exit;

            case 'legal':
                page_legal($slug);
                exit;

            case 'submitted':
                page_submitted($slug);
                exit;
        }
    }
}

page_not_found();
