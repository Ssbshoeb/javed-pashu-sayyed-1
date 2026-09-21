<?php
/*
|--------------------------------------------------------------------------
| Responsive images
|--------------------------------------------------------------------------
| Helpers over the variant manifest in image_variants.php.
|
| The photographs are hand-made art direction — webp/jpg pairs picked by
| <picture>, and CSS `object-fit` crops the stylesheet's aspect contracts
| depend on — so nothing resizes them at request time. Without these the home
| hero cut-out would arrive on a phone at 1280px wide to render inside 640 CSS
| px at most.
|
| Everything here reads the manifest rather than assuming a width exists: an
| `area-*` photograph starts at 900px and so has a 640 variant and no 1024, and
| a srcset entry pointing at a file that is not there is a broken image rather
| than a smaller one.
*/

defined('APP_RUNNING') or exit('No direct access.');

/** The manifest, loaded once. */
function image_variants(): array
{
    static $variants = null;

    if ($variants === null) {
        $variants = require APP_PATH . '/support/image_variants.php';
    }

    return $variants;
}

/** `/img/hero-1.webp` + 640 -> `/img/hero-1-640.webp`. */
function variant_path(string $path, int $width): string
{
    return (string) preg_replace('/(\.[a-z]+)$/i', '-' . $width . '$1', $path);
}

/** The manifest entry for a path, or null when the file has no variants. */
function image_entry(string $path): ?array
{
    $name = basename($path);
    $variants = image_variants();

    return $variants[$name] ?? null;
}

/** The widths generated for a file, narrowest first. Empty when it has none. */
function variant_widths(string $path): array
{
    $entry = image_entry($path);

    return $entry['variants'] ?? [];
}

/**
 * A `srcset` listing every generated width plus the original, which is always
 * the widest. Returns an empty string when the file has no variants, so a
 * caller can test it and leave the attribute off entirely rather than emit an
 * empty one.
 */
function src_set(string $path): string
{
    $entry = image_entry($path);

    if ($entry === null || empty($entry['variants'])) {
        return '';
    }

    $parts = [];
    foreach ($entry['variants'] as $width) {
        $parts[] = variant_path($path, $width) . ' ' . $width . 'w';
    }
    $parts[] = $path . ' ' . $entry['width'] . 'w';

    return implode(', ', $parts);
}

/**
 * Prints ` srcset="..."` only when the file actually has variants.
 * Used inline in the views so an empty attribute is never emitted.
 */
function srcset_attr(string $path): string
{
    $set = src_set($path);

    return $set === '' ? '' : ' srcset="' . e($set) . '"';
}

/**
 * The inner-page hero band takes its photograph from a CSS custom property
 * (`--hero-image`), which is how the Blade views set it and how site.css still
 * reads it.
 *
 * `--hero-image-sm` and `--hero-image-md` carry the narrow variants for the
 * phone and tablet media queries in site.css. A background that 404s renders as
 * nothing at all — no broken-image marker, no console error the advocate would
 * ever see — so each is set only when that width was actually generated, and
 * the stylesheet falls back through to the full-size image when it was not.
 *
 * Returns the value for a `style` attribute, already escaped.
 */
function hero_image_style(string $path): string
{
    $widths = variant_widths($path);

    $declarations = ["--hero-image: url('" . $path . "')"];

    if (in_array(640, $widths, true)) {
        $declarations[] = "--hero-image-sm: url('" . variant_path($path, 640) . "')";
    }
    if (in_array(1024, $widths, true)) {
        $declarations[] = "--hero-image-md: url('" . variant_path($path, 1024) . "')";
    }

    return e(implode('; ', $declarations));
}
