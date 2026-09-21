<?php
/*
|--------------------------------------------------------------------------
| Page head
|--------------------------------------------------------------------------
| The per-page <head>, ported from the `@section('title')` / `@yield` block in
| layouts/app.blade.php and from lib/metadata.ts in the Next.js port.
|
| The brand is appended only when the page's own title does not already carry
| it — several titles name the advocate themselves, and the old layout used the
| same `str_contains` rule to avoid printing it twice.
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * Normalises the arguments a page hands the layout into the full set of head
 * values the layout prints.
 *
 * @param array{
 *   title: string,
 *   description?: string,
 *   path: string,
 *   ogType?: string,
 *   noindex?: bool,
 *   article?: array{publishedTime: string, modifiedTime: string, section: string}
 * } $params
 */
function page_metadata(array $params): array
{
    $brand = practice('name');
    $title = $params['title'];

    if (!str_contains($title, $brand)) {
        $title .= ' — ' . $brand;
    }

    $description = $params['description'] ?? practice('shortIntro');
    $url = absolute_url($params['path']);
    $image = absolute_url(practice('seo.shareImage'));

    return [
        'title' => $title,
        'description' => $description,
        'canonical' => $url,
        // Informational site: indexable, but never presented as solicitation.
        // Confirmation screens override this — they hold no content and their
        // reference numbers should never reach a search result.
        'robots' => !empty($params['noindex']) ? 'noindex, follow' : 'index, follow',
        'ogType' => $params['ogType'] ?? 'website',
        // Without an image the link renders as a bare grey box when a client
        // forwards it on WhatsApp, which is where most of these links travel.
        'image' => $image,
        'article' => $params['article'] ?? null,
    ];
}
