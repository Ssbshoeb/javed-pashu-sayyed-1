<?php
/*
|--------------------------------------------------------------------------
| Disclaimer acceptance
|--------------------------------------------------------------------------
| Records that the entry disclaimer was accepted.
|
| No IP address is stored — only an anonymised session reference, per the
| privacy notice. The session id itself is PHP's own, kept in a first-party
| cookie; it identifies nothing about the visitor, and only its SHA-256 prefix
| is ever written down.
|
| WHERE THE RECORD GOES. The Laravel app wrote a row to a
| `disclaimer_acceptances` table. This port has no database, so the acceptance
| goes to the application log — which is what /legal/privacy says. If the
| chamber ever needs these as queryable records rather than log lines, this is
| the one place to change: write to whatever store is chosen and leave the rest
| of the site alone.
*/

defined('APP_RUNNING') or exit('No direct access.');

function record_disclaimer_acceptance(): void
{
    header('Content-Type: application/json; charset=utf-8');

    /*
    | The CSRF token, checked here as everywhere else. Server Actions in the
    | Next.js port got an origin check from the framework and this route
    | hand-rolled one; here the token does the same job and does it better,
    | because it does not depend on the Origin header being present.
    */
    if (!csrf_valid()) {
        http_response_code(403);
        echo json_encode(['recorded' => false]);
        return;
    }

    // `throttle:20,1` on the Laravel route.
    if (throttled('disclaimer', 20)) {
        http_response_code(429);
        echo json_encode(['recorded' => false]);
        return;
    }

    $entryPath = (string) post('entry_path', '');

    // `required|string|max:255`. The value is the path the visitor entered on,
    // sent by the browser, so it is length-checked and written escaped — it is
    // never used to build a path or a redirect.
    if ($entryPath === '' || mb_strlen($entryPath, 'UTF-8') > 255) {
        http_response_code(422);
        echo json_encode(['recorded' => false]);
        return;
    }

    log_info('[disclaimer] accepted', [
        'session_reference' => substr(hash('sha256', session_id()), 0, 32),
        'entry_path' => $entryPath,
        'accepted_at' => date(DATE_ATOM),
    ]);

    echo json_encode(['recorded' => true]);
}
