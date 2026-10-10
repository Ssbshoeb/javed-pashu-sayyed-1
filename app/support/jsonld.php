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
        // Attorney is a LegalService subtype; the pair lets a single advocate's
        // chamber be read as both the practice and the lawyer.
        '@type' => ['LegalService', 'Attorney'],
        '@id' => $siteUrl . '/#legalservice',
        'name' => practice('firm'),
        'alternateName' => 'Advocate Javed Pashu Sayyed',
        'description' => practice('shortIntro'),
        'url' => $siteUrl . '/',
        'logo' => absolute_url('/img/logo-mark.png'),
        'hasMap' => 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($contact['mapQuery']),
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
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Mumbai'],
            ['@type' => 'City', 'name' => 'Pune'],
            ['@type' => 'State', 'name' => 'Maharashtra'],
            ['@type' => 'Country', 'name' => 'India'],
        ],
        // Each practice area as a named service pointing at its own page.
        'hasOfferCatalog' => [
            '@type' => 'OfferCatalog',
            'name' => 'Areas of practice',
            'itemListElement' => array_values(array_map(
                static fn (string $slug, array $area): array => [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name' => $area['title'],
                        'url' => absolute_url(route('area', $slug)),
                    ],
                ],
                array_keys(practice('areas')),
                practice('areas')
            )),
        ],
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
            '@id' => $siteUrl . '/#advocate',
            'name' => practice('name'),
            'url' => absolute_url(route('about')),
            'image' => absolute_url('/img/advocate-about-hero.jpg'),
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
            '@id' => site_url() . '/#advocate',
            'name' => practice('name'),
            'jobTitle' => 'Advocate',
            // Ties the byline to the About page, so the author is a known
            // entity on this site rather than a bare string.
            'url' => absolute_url(route('about')),
        ],
        'publisher' => [
            '@type' => 'Organization',
            '@id' => site_url() . '/#legalservice',
            'name' => practice('firm'),
            'logo' => ['@type' => 'ImageObject', 'url' => absolute_url('/img/logo-mark.png')],
        ],
    ];
}

/**
 * The page itself, typed (AboutPage, ContactPage, CollectionPage ...), tied to
 * the site and to the advocate so a crawler can see what each URL is for.
 */
function webpage_schema(string $type, string $path, string $name, string $description): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => $type,
        '@id' => absolute_url($path) . '#webpage',
        'url' => absolute_url($path),
        'name' => $name,
        'description' => $description,
        'inLanguage' => 'en-IN',
        'isPartOf' => ['@id' => site_url() . '/#website'],
        'about' => ['@id' => site_url() . '/#legalservice'],
    ];
}

/** One practice area as a Service offered by the chamber. */
function service_schema(string $slug, array $area): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $area['title'],
        'serviceType' => $area['title'],
        'description' => $area['summary'],
        'url' => absolute_url(route('area', $slug)),
        'provider' => ['@id' => site_url() . '/#legalservice'],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Mumbai'],
            ['@type' => 'City', 'name' => 'Pune'],
        ],
    ];
}

/** The /media page: coverage published elsewhere, as a list of articles. */
function media_list_schema(array $items): array
{
    return [
        '@context' => 'https://schema.org',
        '@type' => 'ItemList',
        'itemListElement' => array_map(
            static fn (array $m, int $i): array => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'item' => array_filter([
                    '@type' => 'Article',
                    'headline' => $m['title'],
                    'url' => $m['url'],
                    'datePublished' => $m['date'] ?? null,
                    'image' => !empty($m['image']) ? absolute_url($m['image']) : null,
                    'publisher' => ['@type' => 'Organization', 'name' => $m['source'] ?? 'Media'],
                    'about' => ['@id' => site_url() . '/#advocate'],
                ]),
            ],
            array_values($items),
            array_keys(array_values($items))
        ),
    ];
}
