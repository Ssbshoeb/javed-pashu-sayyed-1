<?php
/*
|--------------------------------------------------------------------------
| robots.txt and sitemap.xml
|--------------------------------------------------------------------------
| Ported from SeoController, through app/robots.ts and app/sitemap.ts.
|
| Both are generated from the same content the site is built from, so a new
| practice area or article appears here without anyone remembering to update a
| file by hand.
*/

defined('APP_RUNNING') or exit('No direct access.');

function serve_robots(): void
{
    header('Content-Type: text/plain; charset=utf-8');

    echo "User-agent: *\n";
    echo "Allow: /\n";
    // Confirmation screens hold no content to index, and their reference
    // numbers should never reach a search result.
    echo "Disallow: /submitted/\n";
    echo "\n";
    echo 'Sitemap: ' . absolute_url('/sitemap.xml') . "\n";
}

/*
| Last-modified date for a page, taken from the template that renders it and
| the content file its text lives in — whichever changed last.
|
| Stamping every URL with today's date, as the Laravel version once did, tells
| a crawler the whole site changed daily. It does not, and a lastmod that is
| provably wrong is worse than none: the crawler stops trusting the field.
*/

/** Content for every page ultimately comes out of this one file. */
const CONTENT_FILE = '/app/config/practice.php';

function file_mtime(string $relative): ?int
{
    $file = BASE_PATH . $relative;

    // A renamed or deleted file simply contributes nothing, exactly as the
    // is_file() guard in the PHP version did.
    return is_file($file) ? (int) filemtime($file) : null;
}

function last_modified(string ...$files): string
{
    $times = [];

    foreach ($files as $file) {
        $time = file_mtime($file);
        if ($time !== null) {
            $times[] = $time;
        }
    }

    if ($times === []) {
        // Falls back to the content file, which always exists.
        $fallback = file_mtime(CONTENT_FILE);
        $times[] = $fallback ?? time();
    }

    return gmdate('Y-m-d', max($times));
}

function serve_sitemap(): void
{
    header('Content-Type: application/xml; charset=utf-8');

    $view = static fn (string $name): string => '/app/views/pages/' . $name . '.php';

    $entries = [
        [route('home'), 1.0, 'monthly', last_modified(CONTENT_FILE, $view('home'))],
        [route('about'), 0.8, 'yearly', last_modified(CONTENT_FILE, $view('about'))],
        [route('achievements'), 0.6, 'yearly', last_modified(CONTENT_FILE, $view('achievements'))],
        [route('whyUs'), 0.7, 'yearly', last_modified(CONTENT_FILE, $view('why-choose-us'))],
        [route('areas'), 0.9, 'monthly', last_modified(CONTENT_FILE, $view('practice-areas'))],
        [route('insights'), 0.7, 'monthly', last_modified(CONTENT_FILE, $view('insights'))],
        [route('media'), 0.5, 'monthly', last_modified(CONTENT_FILE, $view('media'))],
        [route('caseEvaluation'), 0.8, 'yearly', last_modified(CONTENT_FILE, $view('case-evaluation'))],
        [route('upload'), 0.6, 'yearly', last_modified(CONTENT_FILE, $view('upload-document'))],
        [route('appointment'), 0.8, 'yearly', last_modified(CONTENT_FILE, $view('request-appointment'))],
        [route('contact'), 0.9, 'yearly', last_modified(CONTENT_FILE, $view('contact'))],
    ];

    $areaTemplate = $view('practice-area');
    foreach (area_slugs() as $slug) {
        $entries[] = [route('area', $slug), 0.8, 'monthly', last_modified(CONTENT_FILE, $areaTemplate)];
    }

    $articles = practice('articles');
    foreach (article_slugs() as $slug) {
        // An article's own publication date, which is the honest answer for a
        // page whose content is fixed when it is written.
        $entries[] = [route('article', $slug), 0.6, 'yearly', $articles[$slug]['date']];
    }

    foreach (legal_pages() as $page) {
        $entries[] = [route('legal', $page), 0.3, 'yearly', last_modified(CONTENT_FILE, $view('legal/' . $page))];
    }

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

    foreach ($entries as [$path, $priority, $frequency, $modified]) {
        echo "  <url>\n";
        echo '    <loc>' . e(absolute_url($path)) . "</loc>\n";
        echo '    <lastmod>' . e($modified) . "</lastmod>\n";
        echo '    <changefreq>' . e($frequency) . "</changefreq>\n";
        echo '    <priority>' . number_format($priority, 1) . "</priority>\n";
        echo "  </url>\n";
    }

    echo "</urlset>\n";
}
