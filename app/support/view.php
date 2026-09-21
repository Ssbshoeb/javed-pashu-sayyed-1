<?php
/*
|--------------------------------------------------------------------------
| Views
|--------------------------------------------------------------------------
| Plain PHP templates with one layout wrapped around them.
|
| The page view is rendered into a buffer first and the layout printed around
| the result, which is what lets a page declare its own <head> values (title,
| canonical, JSON-LD blocks) even though they are printed before its body.
| Blade's `@section`/`@yield` did the same thing.
*/

defined('APP_RUNNING') or exit('No direct access.');

/**
 * Values a page sets for the layout to print — the head block, and any
 * structured data. Called with a key/value it writes; called bare it reads.
 */
function view_share(?string $key = null, $value = null)
{
    static $shared = [];

    if ($key === null) {
        return $shared;
    }

    if (func_num_args() > 1) {
        $shared[$key] = $value;
        return $value;
    }

    return $shared[$key] ?? null;
}

/** Queues one JSON-LD block for the layout to print in <head>. */
function push_json_ld(array $data): void
{
    $blocks = view_share('jsonLd') ?? [];
    $blocks[] = $data;
    view_share('jsonLd', $blocks);
}

/**
 * Renders a template and returns the markup.
 *
 * `extract` puts each key of $data in scope as a variable, so a template reads
 * `$area` rather than `$data['area']`. EXTR_SKIP means nothing here can
 * clobber $__template or $data themselves.
 */
function render(string $template, array $data = []): string
{
    $file = VIEW_PATH . '/' . $template . '.php';

    if (!is_file($file)) {
        throw new RuntimeException('View not found: ' . $template);
    }

    extract($data, EXTR_SKIP);

    ob_start();
    require $file;

    return (string) ob_get_clean();
}

/** Renders a partial straight to the output. */
function partial(string $template, array $data = []): void
{
    echo render($template, $data);
}

/**
 * Renders a page inside the site layout and sends it.
 *
 * @param string $page     Template under views/pages, e.g. 'home' or 'legal/terms'.
 * @param array  $metadata What page_metadata() returned.
 * @param array  $data     Anything the page template needs.
 */
function view(string $page, array $metadata, array $data = [], int $status = 200): void
{
    http_response_code($status);

    $content = render('pages/' . $page, $data);

    echo render('layout', [
        'meta' => $metadata,
        'content' => $content,
        'bodyClass' => view_share('bodyClass') ?? '',
    ]);
}
