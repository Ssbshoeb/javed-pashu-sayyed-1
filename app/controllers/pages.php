<?php
/*
|--------------------------------------------------------------------------
| Page controllers
|--------------------------------------------------------------------------
| One function per page. Each assembles its metadata, queues any structured
| data the layout should print, and renders its template.
|
| The `sizes` constants below say how wide these photographs actually render,
| for the browser to pick a srcset entry against. Read off site.css —
| .mhero__media is 46% capped at 640px, .ahero__photo 57.5% capped at 1060px,
| and the about-band portrait is a grid column at roughly 40% — and each
| collapses to the full width at the 900px stacking breakpoint. A sizes
| attribute that disagrees with the stylesheet is worse than none: the browser
| trusts it and fetches the wrong file.
*/

defined('APP_RUNNING') or exit('No direct access.');

const MHERO_SIZES = '(max-width: 900px) 100vw, min(46vw, 640px)';
const PORTRAIT_SIZES = '(max-width: 900px) 100vw, 40vw';
const AHERO_SIZES = '(max-width: 900px) 100vw, min(57.5vw, 1060px)';

function page_home(): void
{
    push_json_ld(website_schema());

    /*
        The hero cut-out is the largest thing above the fold, and fetching it
        early is what keeps Largest Contentful Paint respectable. Only this page
        shows it.

        The srcset and sizes have to repeat what the <picture> in the template
        declares. Preloading the bare 1280px file while the markup offers a
        srcset would have a phone fetch that file AND the 640px one it then
        picks — the preload would cost bandwidth rather than save it.
    */
    view_share('preload', [[
        'href' => '/img/advocate-cutout.webp',
        'type' => 'image/webp',
        'srcset' => src_set('/img/advocate-cutout.webp'),
        'sizes' => MHERO_SIZES,
    ]]);

    /*
        Figures for the statistics band. The success rate and free-consultation
        count are published only on the advocate's express instruction — see the
        compliance warning in config/practice.php before changing them.
    */
    $figures = practice('figures');
    $statTiles = [];

    if ($figures['successRate'] !== null) {
        $statTiles[] = ['value' => $figures['successRate'], 'suffix' => '%', 'label' => 'Successful cases'];
    }
    if ($figures['freeConsultations'] !== null) {
        $statTiles[] = ['value' => $figures['freeConsultations'], 'suffix' => '+', 'label' => 'Free consultations'];
    }
    if ($figures['showYears']) {
        $statTiles[] = ['value' => practice('experienceYears'), 'suffix' => '+', 'label' => 'Years of experience'];
    }
    if ($figures['showAreas']) {
        $statTiles[] = ['value' => count(area_slugs()), 'suffix' => '', 'label' => 'Areas of practice'];
    }
    $statTiles[] = ['value' => count(practice('courts')), 'suffix' => '', 'label' => 'Courts & forums'];
    $statTiles[] = ['value' => count(practice('languages')), 'suffix' => '', 'label' => 'Languages'];

    view('home', page_metadata([
        'title' => 'Adv. Javed Pashu Sayyed — Advocate, Bombay High Court & Supreme Court',
        'description' => 'Chamber of Adv. Javed Pashu Sayyed, Advocate practising at the Bombay High Court and Supreme Court of India in Mumbai. Corporate, arbitration, criminal, cyber crime and family law.',
        'path' => route('home'),
    ]), [
        'statTiles' => $statTiles,
        'mheroSizes' => MHERO_SIZES,
        'portraitSizes' => PORTRAIT_SIZES,
    ]);
}

function page_about(): void
{
    push_json_ld(breadcrumb_schema([['About the Advocate', route('about')]]));

    view('about', page_metadata([
        'title' => 'About Adv. Javed Pashu Sayyed — Advocate, Bombay High Court',
        'description' => 'Adv. Javed Pashu Sayyed — Advocate with 20+ years experience before the Supreme Court of India, Bombay High Court, and Courts across India. Practice background, courts and memberships.',
        'path' => route('about'),
    ]), ['aheroSizes' => AHERO_SIZES]);
}

function page_achievements(): void
{
    push_json_ld(breadcrumb_schema([['Recognition & Engagements', route('achievements')]]));

    view('achievements', page_metadata([
        'title' => 'Recognition & Engagements — Adv. Javed Pashu Sayyed',
        'description' => 'Recognition and professional engagements of Adv. Javed Pashu Sayyed — speaker at Economic Times Global Legal Convention and academic guest lectures.',
        'path' => route('achievements'),
    ]), ['aheroSizes' => AHERO_SIZES]);
}

function page_why_us(): void
{
    push_json_ld(breadcrumb_schema([['Approach', route('whyUs')]]));

    view('why-choose-us', page_metadata([
        'title' => 'Practice Approach & Standards — Adv. Javed Pashu Sayyed',
        'description' => 'Practice standards and approach of Adv. Javed Pashu Sayyed: thorough documentation, direct representation, confidentiality, and candour about legal positions.',
        'path' => route('whyUs'),
    ]));
}

function page_areas(): void
{
    push_json_ld(breadcrumb_schema([['Practice Areas', route('areas')]]));

    view('practice-areas', page_metadata([
        'title' => 'Legal Practice Areas in Mumbai — Adv. Javed Pashu Sayyed',
        'description' => 'Legal practice areas of Adv. Javed Pashu Sayyed: Corporate & Commercial Law, Arbitration, Criminal Litigation, Cyber Crime, Family Law, Property Disputes, and Strategic Advisory in Mumbai.',
        'path' => route('areas'),
    ]));
}

/** One page per entry in the areas list — nothing else is routable. */
function page_area(string $slug): void
{
    $area = practice('areas.' . $slug);

    if (!is_array($area)) {
        page_not_found();
        return;
    }

    push_json_ld(faq_schema($area['faqs']));
    push_json_ld(breadcrumb_schema([
        ['Practice Areas', route('areas')],
        [$area['title'], route('area', $slug)],
    ]));

    view('practice-area', page_metadata([
        'title' => $area['title'] . ' Advocate in Mumbai',
        'description' => $area['summary'],
        'path' => route('area', $slug),
    ]), ['area' => $area, 'slug' => $slug]);
}

function page_insights(): void
{
    push_json_ld(breadcrumb_schema([['Insights', route('insights')]]));

    view('insights', page_metadata([
        'title' => 'Legal Insights & Procedural Notes — Adv. Javed Pashu Sayyed',
        'description' => 'Legal insights, procedure notes and practical legal information by Adv. Javed Pashu Sayyed, Advocate Bombay High Court.',
        'path' => route('insights'),
    ]));
}

function page_article(string $slug): void
{
    $article = practice('articles.' . $slug);

    if (!is_array($article)) {
        page_not_found();
        return;
    }

    push_json_ld(article_schema($slug, $article));
    push_json_ld(breadcrumb_schema([
        ['Insights', route('insights')],
        [$article['title'], route('article', $slug)],
    ]));

    view('article', page_metadata([
        'title' => $article['seoTitle'] ?? $article['title'],
        'description' => $article['excerpt'],
        'path' => route('article', $slug),
        'ogType' => 'article',
        'article' => [
            'publishedTime' => $article['date'],
            'modifiedTime' => $article['updated'] ?? $article['date'],
            'section' => $article['category'],
        ],
    ]), ['article' => $article, 'slug' => $slug]);
}

function page_case_evaluation(): void
{
    push_json_ld(breadcrumb_schema([['Case Evaluation', route('caseEvaluation')]]));

    view('case-evaluation', page_metadata([
        'title' => 'Case Evaluation Request — Adv. Javed Pashu Sayyed',
        'description' => 'Submit a confidential outline of your legal matter for an initial evaluation by the Chamber of Adv. Javed Pashu Sayyed, Advocate Bombay High Court.',
        'path' => route('caseEvaluation'),
    ]));
}

function page_upload(): void
{
    push_json_ld(breadcrumb_schema([['Upload Document', route('upload')]]));

    view('upload-document', page_metadata([
        'title' => 'Secure Document Submission — Adv. Javed Pashu Sayyed',
        'description' => 'Securely submit case documents, agreements, court orders, or affidavits directly to the Chamber of Adv. Javed Pashu Sayyed.',
        'path' => route('upload'),
    ]));
}

function page_appointment(): void
{
    push_json_ld(breadcrumb_schema([['Request Appointment', route('appointment')]]));

    view('request-appointment', page_metadata([
        'title' => 'Request Consultation Appointment — Adv. Javed Pashu Sayyed',
        'description' => 'Request a legal consultation appointment with Adv. Javed Pashu Sayyed at Nariman Point, Mumbai, or schedule a telephone/video consultation.',
        'path' => route('appointment'),
    ]));
}

function page_contact(): void
{
    push_json_ld(breadcrumb_schema([['Contact', route('contact')]]));

    view('contact', page_metadata([
        'title' => 'Contact Chamber at Nariman Point, Mumbai — Adv. Javed Pashu Sayyed',
        'description' => 'Contact the Chamber of Adv. Javed Pashu Sayyed at 2nd Floor, WeWork, Express Towers, Nariman Point, Mumbai. Legal consultation, appointment booking and general enquiry.',
        'path' => route('contact'),
    ]));
}

function page_legal(string $page): void
{
    if (!in_array($page, legal_pages(), true)) {
        page_not_found();
        return;
    }

    $meta = [
        'disclaimer' => [
            'title' => 'Bar Council Disclaimer — Adv. Javed Pashu Sayyed',
            'description' => 'The Bar Council of India disclaimer governing the use of this website, and why no information on it amounts to legal advice or solicitation.',
            'crumb' => 'Disclaimer',
        ],
        'privacy' => [
            'title' => 'Privacy Policy — Adv. Javed Pashu Sayyed',
            'description' => 'Privacy policy and data protection under the DPDP Act 2023 for the website of Adv. Javed Pashu Sayyed.',
            'crumb' => 'Privacy Policy',
        ],
        'terms' => [
            'title' => 'Terms of Use — Adv. Javed Pashu Sayyed',
            'description' => 'The terms on which this website may be used, and the limits of the information published on it. Nothing here is legal advice.',
            'crumb' => 'Terms of Use',
        ],
        'cookies' => [
            'title' => 'Cookie Policy — Adv. Javed Pashu Sayyed',
            'description' => 'Cookie policy explaining browser storage and user privacy choices for the website of Adv. Javed Pashu Sayyed.',
            'crumb' => 'Cookie Policy',
        ],
    ][$page];

    push_json_ld(breadcrumb_schema([[$meta['crumb'], route('legal', $page)]]));

    view('legal/' . $page, page_metadata([
        'title' => $meta['title'],
        'description' => $meta['description'],
        'path' => route('legal', $page),
    ]));
}

function page_submitted(string $type): void
{
    if (!in_array($type, submission_types(), true)) {
        page_not_found();
        return;
    }

    $copy = [
        'enquiry' => [
            'heading' => 'Your enquiry has been received',
            'body' => 'It has been delivered to the chamber and you will receive a reply at the email address you provided.',
        ],
        'case-evaluation' => [
            'heading' => 'Your case evaluation request has been received',
            'body' => 'The details will be read at the chamber and you will be told whether a consultation is appropriate.',
        ],
        'upload' => [
            'heading' => 'Your document has been received',
            'body' => 'The file has been delivered to the chamber by email. It is not stored on the server and is not published anywhere.',
        ],
        'appointment' => [
            'heading' => 'Your appointment request has been received',
            'body' => 'This is a request, not a confirmed booking. The chamber will confirm the slot or propose an alternative.',
        ],
    ][$type];

    $ref = (string) query('ref', '');
    $reference = is_reference($ref) ? $ref : null;

    view('submitted', page_metadata([
        'title' => 'Submission Received',
        'path' => route('submitted', $type),
        // These screens hold no content and their reference numbers should
        // never reach a search result.
        'noindex' => true,
    ]), ['copy' => $copy, 'reference' => $reference]);
}

/**
 * The site's own 404, in the site's design.
 *
 * $status is a parameter because the same page is what a 405 shows: view()
 * sets the response code as it renders, so a status set by the caller
 * beforehand would be overwritten here.
 */
function page_not_found(int $status = 404): void
{
    view('not-found', page_metadata([
        'title' => 'Page Not Found',
        'path' => request_path(),
        'noindex' => true,
    ]), [], $status);
}
