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

    if (empty($params['noBrand']) && !str_contains($title, $brand)) {
        $title .= ' — ' . $brand;
    }

    $description = seo_trim($params['description'] ?? practice('shortIntro'), 160);
    $url = absolute_url($params['path']);
    $image = absolute_url($params['image'] ?? practice('seo.shareImage'));

    return [
        'title' => $title,
        'description' => $description,
        'canonical' => $url,
        // Informational site: indexable, but never presented as solicitation.
        // Confirmation screens override this — they hold no content and their
        // reference numbers should never reach a search result.
        // The preview directives let Google show a full-width image and an
        // uncut snippet instead of picking conservative defaults.
        'robots' => !empty($params['noindex'])
            ? 'noindex, follow'
            : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
        'ogType' => $params['ogType'] ?? 'website',
        // Without an image the link renders as a bare grey box when a client
        // forwards it on WhatsApp, which is where most of these links travel.
        'image' => $image,
        'imageAlt' => $params['imageAlt'] ?? practice('name') . ', Advocate in Mumbai & Pune',
        'article' => $params['article'] ?? null,
    ];
}

/**
 * Cuts text to a search-snippet length at a word boundary. Google rewrites a
 * description that runs past ~160 characters, so a trimmed one we chose is
 * better than a truncated one it chose.
 */
function seo_trim(string $text, int $max): string
{
    $text = trim((string) preg_replace('/\s+/', ' ', $text));

    if (mb_strlen($text) <= $max) {
        return $text;
    }

    $cut = mb_substr($text, 0, $max - 1);
    $space = mb_strrpos($cut, ' ');

    return rtrim($space !== false ? mb_substr($cut, 0, $space) : $cut, ' ,;:-—.') . '…';
}
