<?php
/*
|--------------------------------------------------------------------------
| Structured data
|--------------------------------------------------------------------------
| The schema.org blocks the site emits.
|
| Every value is built on the server from config/practice.php — never from a
| request — and e_json() escapes `<` as <, so a string containing
| `</script>` can never close the tag early.
*/

defined('APP_RUNNING') or exit('No direct access.');

/** One `<script type="application/ld+json">` block. */
function json_ld(array $data): string
{
    return '<script type="application/ld+json">' . e_json($data) . '</script>';
}

/** The site-wide LegalService entity, emitted on every page. */
function legal_service_schema(): array
{
    $contact = practice('contact');
    $siteUrl = site_url();

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LegalService',
        '@id' => $siteUrl . '/#legalservice',
        'name' => practice('firm'),
        'description' => practice('shortIntro'),
        'url' => $siteUrl . '/',
        'email' => $contact['email'],
        'priceRange' => '₹₹',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => implode(', ', array_slice($contact['addressLines'], 0, 2)),
            'addressLocality' => 'Nariman Point, Mumbai',
            'addressRegion' => 'Maharashtra',
            'postalCode' => '400021',
            'addressCountry' => 'IN',
        ],
        'areaServed' => ['Mumbai', 'Pune', 'Maharashtra', 'India'],
        'image' => absolute_url(practice('seo.shareImage')),
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => $contact['geo']['lat'],
            'longitude' => $contact['geo']['lng'],
        ],
        'openingHoursSpecification' => array_map(
            static fn (array $slot): array => [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => $slot['days'],
                'opens' => $slot['opens'],
                'closes' => $slot['closes'],
            ],
            $contact['hoursSpec']
        ),
        // The practice areas, as subjects the chamber handles. This is the
        // factual list out of content, not a keyword list.
        'knowsAbout' => array_values(array_map(
            static fn (array $area): string => $area['title'],
            practice('areas')
        )),
        'founder' => [
            '@type' => 'Person',
            'name' => practice('name'),
            'jobTitle' => 'Advocate',
            'description' => practice('bio')[0],
            'knowsLanguage' => practice('languages'),
            'memberOf' => ['@type' => 'Organization', 'name' => 'Bar Council of Maharashtra & Goa'],
        ],
    ];

    if (!empty($contact['phone'])) {
        $schema['telephone'] = $contact['phone'];
    }

    // A profile is published only once it exists — a dead sameAs link does the
    // entity more harm than no sameAs at all.
    $profiles = array_values(array_filter(practice('seo.profiles')));
    if ($profiles) {
        $schema['sameAs'] = $profiles;
    }

    return $schema;
}

/** WebSite, home page only. */
function website_schema(): array
{
    $siteUrl = site_url();

    return [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => $siteUrl . '/#website',
        'url' => $siteUrl . '/',
        'name' => practice('firm'),
        'publisher' => ['@id' => $siteUrl . '/#legalservice'],
        'inLanguage' => 'en-IN',
    ];
}

/**
 * Breadcrumbs, for every page below the top level. Pass the trail after Home —
 * breadcrumb_schema([['Practice Areas', route('areas')]]).
 *
 * @param array<int, array{0: string, 1: string}> $trail
 */
function breadcrumb_schema(array $trail): array
{
    $items = array_merge([['Home', route('home')]], $trail);

    return [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(
            static function (array $item, int $index): array {
                [$name, $path] = $item;

                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'name' => $name,
                    'item' => absolute_url($path),
                ];
            },
            $items,
            array_keys($items)
        ),
    ];
}

/** FAQPage, on a practice-area detail page. */
function faq_schema(array $faqs): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(
            static fn (array $faq): array => [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ],
            $faqs
        ),
    ];
}

/** Article, on an insight note. */
function article_schema(string $slug, array $article): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'headline' => $article['title'],
        'description' => $article['excerpt'],
        'datePublished' => $article['date'],
        'articleSection' => $article['category'],
        'inLanguage' => 'en-IN',
        'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => absolute_url(route('article', $slug))],
        // Google wants dateModified alongside datePublished; absent it, the
        // published date is treated as the last-modified date too.
        'dateModified' => $article['updated'] ?? $article['date'],
        'image' => absolute_url(practice('seo.shareImage')),
        'author' => [
            '@type' => 'Person',
            'name' => practice('name'),
            'jobTitle' => 'Advocate',
            // Ties the byline to the About page, so the author is a known
            // entity on this site rather than a bare string.
            'url' => absolute_url(route('about')),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => practice('firm'),
            'logo' => ['@type' => 'ImageObject', 'url' => absolute_url(practice('seo.shareImage'))],
        ],
    ];
}
