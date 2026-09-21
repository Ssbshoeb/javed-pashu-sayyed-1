<?php
/*
|--------------------------------------------------------------------------
| Routes
|--------------------------------------------------------------------------
| Every route on the site, named once so a path change does not have to be
| chased through the views. These are the same URLs the Laravel app and the
| Next.js port both served, so no redirect table is needed on migration:
|
|   /, /about, /achievements, /why-choose-us, /practice-areas,
|   /practice-areas/{slug}, /insights, /insights/{slug}, /case-evaluation,
|   /upload-document, /request-appointment, /contact, /legal/{page},
|   /submitted/{type}, /sitemap.xml and /robots.txt.
*/

defined('APP_RUNNING') or exit('No direct access.');

function route(string $name, ?string $param = null): string
{
    switch ($name) {
        case 'home':
            return '/';
        case 'about':
            return '/about';
        case 'achievements':
            return '/achievements';
        case 'whyUs':
            return '/why-choose-us';
        case 'areas':
            return '/practice-areas';
        case 'area':
            return '/practice-areas/' . rawurlencode((string) $param);
        case 'insights':
            return '/insights';
        case 'article':
            return '/insights/' . rawurlencode((string) $param);
        case 'caseEvaluation':
            return '/case-evaluation';
        case 'upload':
            return '/upload-document';
        case 'appointment':
            return '/request-appointment';
        case 'contact':
            return '/contact';
        case 'legal':
            return '/legal/' . rawurlencode((string) $param);
        case 'submitted':
            return '/submitted/' . rawurlencode((string) $param);
        default:
            return '/';
    }
}
