<?php
/*
| The site layout, ported from src/app/layout.tsx.
|
| $meta      — what page_metadata() returned
| $content   — the rendered page
| $bodyClass — extra class for <body>, unused by any current page
*/

defined('APP_RUNNING') or exit('No direct access.');

$practice = practice();
$ga4Id = (string) $practice['analytics']['ga4Id'];
$disclaimer = $practice['disclaimer'];
?>
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php /* Declared here as well as in the stylesheet, so a mobile browser knows
             the page is dark-only before any CSS has loaded. */ ?>
    <meta name="color-scheme" content="only dark">

    <title><?= e($meta['title']) ?></title>
    <meta name="description" content="<?= e($meta['description']) ?>">
    <?php /* No meta keywords: Google ignores the tag, and a 20-term list is the
             keyword stuffing config/practice.php says this site avoids. */ ?>
    <meta name="author" content="<?= e($practice['name']) ?>">
    <meta name="theme-color" content="#010B14">
    <link rel="canonical" href="<?= e($meta['canonical']) ?>">
    <link rel="alternate" hreflang="en-IN" href="<?= e($meta['canonical']) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= e($meta['canonical']) ?>">
    <meta name="robots" content="<?= e($meta['robots']) ?>">

    <?php /* Open Graph. Without an image the link renders as a bare grey box
             when a client forwards it on WhatsApp, which is where most of
             these links travel. */ ?>
    <meta property="og:type" content="<?= e($meta['ogType']) ?>">
    <meta property="og:site_name" content="<?= e($practice['firm']) ?>">
    <meta property="og:locale" content="en_IN">
    <meta property="og:title" content="<?= e($meta['title']) ?>">
    <meta property="og:description" content="<?= e($meta['description']) ?>">
    <meta property="og:url" content="<?= e($meta['canonical']) ?>">
    <meta property="og:image" content="<?= e($meta['image']) ?>">
    <meta property="og:image:alt" content="<?= e($meta['imageAlt']) ?>">
    <?php if (!empty($meta['article'])): ?>
        <meta property="article:published_time" content="<?= e($meta['article']['publishedTime']) ?>">
        <meta property="article:modified_time" content="<?= e($meta['article']['modifiedTime']) ?>">
        <meta property="article:section" content="<?= e($meta['article']['section']) ?>">
        <meta property="article:author" content="<?= e($practice['name']) ?>">
    <?php endif; ?>

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($meta['title']) ?>">
    <meta name="twitter:description" content="<?= e($meta['description']) ?>">
    <meta name="twitter:image" content="<?= e($meta['image']) ?>">
    <meta name="twitter:image:alt" content="<?= e($meta['imageAlt']) ?>">

    <?php /* The emblem carries the dark ground with it here rather than being
             transparent: a browser tab is usually light, and the gold-on-nothing
             mark disappears against it. */ ?>
    <link rel="icon" href="/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="/img/favicon-180.png" sizes="180x180">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <?php /* Allura is the signature face on the footer brand line. It replaced
             Great Vibes, which is formal copperplate - the client asked for the
             look of a handwritten signature, which is thinner and near-monoline. */ ?>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&amp;family=Public+Sans:wght@400;500;600;700&amp;family=Allura&amp;display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= e(asset('/css/site.css')) ?>">

    <?php /* Structured data. Built on the server from config/practice.php —
             never from a request — and e_json() escapes `<`, so a string
             containing `</script>` cannot close the tag early. */ ?>
    <?= json_ld(legal_service_schema()) ?>
    <?php foreach ((view_share('jsonLd') ?? []) as $block): ?>
        <?= json_ld($block) ?>
    <?php endforeach; ?>

    <?php /* Preloads the page asked for — the home hero cut-out is the largest
             thing above the fold there, and fetching it early is what keeps
             Largest Contentful Paint respectable. */ ?>
    <?php foreach ((view_share('preload') ?? []) as $preload): ?>
        <link rel="preload" as="image" href="<?= e($preload['href']) ?>"
              <?= !empty($preload['type']) ? 'type="' . e($preload['type']) . '"' : '' ?>
              <?= !empty($preload['srcset']) ? 'imagesrcset="' . e($preload['srcset']) . '"' : '' ?>
              <?= !empty($preload['sizes']) ? 'imagesizes="' . e($preload['sizes']) . '"' : '' ?>
              fetchpriority="high">
    <?php endforeach; ?>
</head>
<?php /* The home page carries the client's dark design end to end, while the
         inner pages are still on the site's original warm palette. The
         `page-home` class is rendered by the home page itself as a wrapper
         div around its own content — every rule that uses it in site.css is a
         descendant selector (`.page-home .section`, never `.page-home` alone),
         so the class never has to live on <body>. */ ?>
<body<?= $bodyClass !== '' ? ' class="' . e($bodyClass) . '"' : '' ?>>
    <a class="skip-link" href="#main">Skip to content</a>

    <div class="progress" aria-hidden="true">
        <div class="progress__fill" id="scroll-progress"></div>
    </div>

    <?php partial('partials/gate', ['disclaimer' => $disclaimer, 'firm' => $practice['firm']]); ?>
    <?php partial('partials/header'); ?>

    <?php /* aria-hidden until the disclaimer is answered — the gate removes it. */ ?>
    <main id="main" aria-hidden="true">
        <?= $content ?>
    </main>

    <?php partial('partials/footer'); ?>
    <?php partial('partials/cookie'); ?>

    <button type="button" class="totop" id="to-top" aria-label="Back to top">&uarr;</button>

    <?php /*
        Configuration the behaviour script reads. A data attribute rather than an
        inline <script> block: the Content-Security-Policy this site sends allows
        inline script only because of the JSON-LD above, and keeping application
        JavaScript out of the document is what would let 'unsafe-inline' be
        dropped from script-src if the JSON-LD ever moved to a file.
    */ ?>
    <div id="site-config"
         hidden
         data-disclaimer-days="<?= e((string) $disclaimer['validityDays']) ?>"
         data-decline-url="<?= e($disclaimer['declineUrl']) ?>"
         data-ga4-id="<?= e($ga4Id) ?>"
         data-csrf="<?= e(csrf_token()) ?>"></div>

    <script src="<?= e(asset('/js/site.js')) ?>" defer></script>
</body>
</html>
