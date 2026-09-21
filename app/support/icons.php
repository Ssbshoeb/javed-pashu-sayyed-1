<?php
/*
|--------------------------------------------------------------------------
| Icons
|--------------------------------------------------------------------------
| Icons for the home page tiles, hero stats, courts strip and the About page.
|
| The approved design draws its tile icons as SOLID gold glyphs, not line work,
| so these are filled shapes. Inline SVG rather than an icon font: no extra
| request, they inherit currentColor, and they stay crisp at any size.
|
| Usage: <?= icon('gavel') ?>
|
| The glyph bodies are trusted, hand-written literals in this file — nothing
| from content or from a request ever reaches the markup below, which is why
| icon() is the one place in the application that prints unescaped HTML.
*/

defined('APP_RUNNING') or exit('No direct access.');

function icon_glyphs(): array
{
    return [
        'gavel' => '<path d="M15.1 1.9l7 7-3.4 3.4-7-7 3.4-3.4Z"/><path d="M10.6 5.6l7.8 7.8-2.4 2.4-7.8-7.8 2.4-2.4Z" opacity=".55"/><path d="M9.4 10.9l3.1 3.1-7.6 7.6H2.4v-3.1l7-7.6Z"/><rect x="1.6" y="21" width="12.8" height="2" rx="1"/>',
        'family' => '<circle cx="6.6" cy="6.4" r="2.4"/><circle cx="17.4" cy="6.4" r="2.4"/><circle cx="12" cy="9.6" r="2"/><path d="M2.6 20.5v-4.2A3.9 3.9 0 0 1 6.5 12.4h.2a3.9 3.9 0 0 1 2 .6 5 5 0 0 0-1.6 3.6v3.9H2.6Z"/><path d="M21.4 20.5v-4.2a3.9 3.9 0 0 0-3.9-3.9h-.2a3.9 3.9 0 0 0-2 .6 5 5 0 0 1 1.6 3.6v3.9h4.5Z"/><path d="M8.6 20.5v-3.4a3.4 3.4 0 0 1 6.8 0v3.4H8.6Z"/>',
        'building' => '<path d="M12 2.6l8.6 5v1.6H3.4V7.6l8.6-5Z"/><path d="M5 10.8h2.4v8H5v-8Zm5.8 0h2.4v8h-2.4v-8Zm5.8 0H19v8h-2.4v-8Z"/><rect x="2.6" y="19.6" width="18.8" height="1.9" rx=".9"/>',
        'handshake' => '<path d="M11 8.4l1.6-1.5 2.1 2 3.1-3 4.2 4.3-5.6 5.5-2.3-2.2-1.4 1.3-1.7-1.6 2.6-2.5-1.1-1.1-1.5 1.4Z"/><path d="M8.9 6.9l2 1.9-4 3.9L2.6 8.3l3.2-3.1 3.1 1.7Z"/><path d="M8.4 13.9l4 3.9-1.7 1.6-4-3.9 1.7-1.6Z"/>',
        'shield' => '<path d="M12 2.4l7.6 3.2v5.2c0 4.7-3.1 8.9-7.6 10.8-4.5-1.9-7.6-6.1-7.6-10.8V5.6L12 2.4Z"/><rect x="9.2" y="10.6" width="5.6" height="5" rx="1" fill="#0B0F16"/><path d="M10.6 10.6V9.4a1.4 1.4 0 0 1 2.8 0v1.2h-1V9.4a.4.4 0 0 0-.8 0v1.2h-1Z" fill="#0B0F16"/>',
        'house' => '<path d="M12 3.1l9 6.6-1.2 1.7-1.3-1V21h-5v-5.4h-3V21h-5V10.4l-1.3 1L3 9.7l9-6.6Z"/>',
        'cart' => '<path d="M2.2 3.4h3.3l.7 3h15L18.8 16H7.7L5 4.9H2.2V3.4Z"/><circle cx="9.4" cy="19.4" r="1.8"/><circle cx="17.4" cy="19.4" r="1.8"/>',
        'hands' => '<circle cx="12" cy="4.4" r="2.3"/><path d="M9 8.2h6l1.1 5.6H7.9L9 8.2Z"/><path d="M2.6 11.5c1.6 0 2.9 1 3.6 2.6l1.9 4.4c.4.9-.1 1.8-1 1.8H4.4c-.8 0-1.4-.4-1.7-1.1L1.2 15c-.5-1.3.1-2.6 1.4-3.5Z"/><path d="M21.4 11.5c-1.6 0-2.9 1-3.6 2.6l-1.9 4.4c-.4.9.1 1.8 1 1.8h2.7c.8 0 1.4-.4 1.7-1.1l1.5-4.2c.5-1.3-.1-2.6-1.4-3.5Z"/>',
        'globe' => '<path d="M12 2.6A9.4 9.4 0 1 0 12 21.4 9.4 9.4 0 0 0 12 2.6Zm0 1.9c1.3 1.4 2.1 3.3 2.4 5.6H9.6c.3-2.3 1.1-4.2 2.4-5.6ZM7.7 10.1a13 13 0 0 1 1.1-4.6 7.6 7.6 0 0 0-3.8 4.6h2.7Zm-2.7 1.9h2.7c.1 1.7.5 3.3 1.1 4.6a7.6 7.6 0 0 1-3.8-4.6Zm4.6 0h4.8c-.3 2.3-1.1 4.2-2.4 5.6-1.3-1.4-2.1-3.3-2.4-5.6Zm5.6 4.6c.6-1.3 1-2.9 1.1-4.6H19a7.6 7.6 0 0 1-3.8 4.6ZM16.3 10.1c-.1-1.7-.5-3.3-1.1-4.6A7.6 7.6 0 0 1 19 10.1h-2.7Z"/>',
        'copyright' => '<path d="M12 2.6A9.4 9.4 0 1 0 12 21.4 9.4 9.4 0 0 0 12 2.6Zm0 1.9a7.5 7.5 0 1 1 0 15 7.5 7.5 0 0 1 0-15Z"/><path d="M14.6 9.9a3.2 3.2 0 1 0 0 4.2l1.4 1.2a5 5 0 1 1 0-6.6l-1.4 1.2Z"/>',
        'scales' => '<rect x="11.2" y="3.4" width="1.7" height="16.2" rx=".8"/><rect x="6.6" y="19.6" width="10.8" height="1.8" rx=".9"/><rect x="3.4" y="7.2" width="17.2" height="1.6" rx=".8"/><path d="M4 8.8l-2.4 5.4h4.8L4 8.8Zm16 0l-2.4 5.4h4.8L20 8.8Z"/>',
        'people' => '<circle cx="9" cy="7.6" r="2.8"/><circle cx="16.8" cy="8.6" r="2.2"/><path d="M3.4 20.6v-2.8A4.2 4.2 0 0 1 7.6 13.6h2.8a4.2 4.2 0 0 1 4.2 4.2v2.8H3.4Z"/><path d="M16.2 20.6v-2.9a5.6 5.6 0 0 0-.8-2.9h1.6a3.6 3.6 0 0 1 3.6 3.6v2.2h-4.4Z"/>',
        'person' => '<circle cx="12" cy="7.6" r="3.2"/><path d="M5.2 20.6v-1.8A5.2 5.2 0 0 1 10.4 13.6h3.2a5.2 5.2 0 0 1 5.2 5.2v1.8H5.2Z"/>',
        'suitperson' => '<circle cx="12" cy="6.8" r="3.2"/><path d="M12 11.4c-1.2 0-2.3.3-3.2.9L12 21l3.2-8.7c-.9-.6-2-.9-3.2-.9Z"/><path d="M7.6 13.2A5.4 5.4 0 0 0 5 17.9v2.7h4.4l-1.8-7.4Zm8.8 0 -1.8 7.4H19v-2.7a5.4 5.4 0 0 0-2.6-4.7Z"/>',
        'shieldcheck' => '<path d="M12 2.4l7.6 3.2v5.2c0 4.7-3.1 8.9-7.6 10.8-4.5-1.9-7.6-6.1-7.6-10.8V5.6L12 2.4Z"/><path d="M10.9 15.4 7.6 12.1l1.6-1.6 1.7 1.7 4-4 1.6 1.6-5.6 5.6Z" fill="#0B0F16"/>',
        'laurel' => '<circle cx="12" cy="9.4" r="1.9"/><path d="M9.4 12.4h5.2l.9 4.4H8.5l.9-4.4Z"/><path d="M7.4 4.6c1.7.5 2.6 1.7 2.6 3.6-1.9-.2-2.8-1.4-2.6-3.6Zm-2 3.4c1.8.2 2.9 1.2 3.2 3.1-1.9.2-3-.8-3.2-3.1Zm-.5 3.9c1.8-.2 3 .6 3.6 2.4-1.8.6-3-.2-3.6-2.4Zm.6 3.9c1.7-.6 3 0 3.9 1.7-1.6.9-2.9.4-3.9-1.7Z"/><path d="M16.6 4.6c-1.7.5-2.6 1.7-2.6 3.6 1.9-.2 2.8-1.4 2.6-3.6Zm2 3.4c-1.8.2-2.9 1.2-3.2 3.1 1.9.2 3-.8 3.2-3.1Zm.5 3.9c-1.8-.2-3 .6-3.6 2.4 1.8.6 3-.2 3.6-2.4Zm-.6 3.9c-1.7-.6-3 0-3.9 1.7 1.6.9 2.9.4 3.9-1.7Z"/>',
        'medal' => '<circle cx="12" cy="9.4" r="6.2"/><path d="M12 5.6l1.2 2.4 2.6.4-1.9 1.9.4 2.7-2.3-1.3-2.3 1.3.4-2.7-1.9-1.9 2.6-.4L12 5.6Z" fill="#0B0F16"/><path d="M8.4 15.2 6.6 22l3.4-1.9L12 22l-1.3-6.4a7.7 7.7 0 0 1-2.3-.4Zm7.2 0a7.7 7.7 0 0 1-2.3.4L12 22l2 -1.9L17.4 22l-1.8-6.8Z"/>',
        'docsearch' => '<path d="M5.4 2.6h8l4.2 4.2v6.5a5.6 5.6 0 0 0-7.2 6.1H5.4V2.6Z"/><path d="M13.4 3.2v3.8h3.8l-3.8-3.8Z" fill="#0B0F16"/><circle cx="15.8" cy="16" r="3.1"/><rect x="18.1" y="18.1" width="4.4" height="1.9" rx=".9" transform="rotate(45 18.1 18.1)"/><circle cx="15.8" cy="16" r="1.5" fill="#0B0F16"/>',
        'trophy' => '<path d="M7.4 3h9.2v4.2a4.6 4.6 0 0 1-9.2 0V3Z"/><path d="M6.2 4.2H3.6v1.6a3.6 3.6 0 0 0 3.1 3.6l-.5-1.9V4.2Zm11.6 0h2.6v1.6a3.6 3.6 0 0 1-3.1 3.6l.5-1.9V4.2Z"/><rect x="10.9" y="11.6" width="2.2" height="4.4"/><path d="M8 16h8l.9 4.8H7.1L8 16Z"/><rect x="5.6" y="20.4" width="12.8" height="1.9" rx=".9"/>',
        'check' => '<path d="M9.9 17.6 4.4 12.1l1.9-1.9 3.6 3.6 8-8 1.9 1.9-9.9 9.9Z"/>',
        'dots' => '<circle cx="5.2" cy="12" r="2.1"/><circle cx="12" cy="12" r="2.1"/><circle cx="18.8" cy="12" r="2.1"/>',
        'briefcase' => '<path d="M9 3.4h6a1.6 1.6 0 0 1 1.6 1.6v1.6h-9.2V5A1.6 1.6 0 0 1 9 3.4Zm-.4 3.2h6.8V5c0-.2-.2-.4-.4-.4H9c-.2 0-.4.2-.4.4v1.6Z"/><path d="M2.6 8.2h18.8a1.4 1.4 0 0 1 1.4 1.4v9.6a1.4 1.4 0 0 1-1.4 1.4H2.6a1.4 1.4 0 0 1-1.4-1.4V9.6a1.4 1.4 0 0 1 1.4-1.4Zm8 4.4v1.9h2.8v-1.9h-2.8Z"/>',
        'monitor' => '<path d="M3 4.2h18a1.4 1.4 0 0 1 1.4 1.4v9.8A1.4 1.4 0 0 1 21 16.8H3a1.4 1.4 0 0 1-1.4-1.4V5.6A1.4 1.4 0 0 1 3 4.2Zm1 2v7.6h16V6.2H4Z"/><rect x="8.6" y="18.4" width="6.8" height="1.9" rx=".9"/><rect x="10.9" y="16.4" width="2.2" height="2.4"/><path d="M9.4 8.2 6.9 10l2.5 1.8v-1H12v-1.4H9.4v-1.2Zm5.2 0v1.2H17v1.4h-2.4v1L17.1 10l-2.5-1.8Z" fill="#0B0F16"/>',
        'scalesdoc' => '<path d="M5.4 2.6h8L17.6 7v14.4H5.4V2.6Z"/><path d="M13.4 3.2v3.8h3.8l-3.8-3.8Z" fill="#0B0F16"/><path d="M11.2 9.4h.9v7.2h-.9V9.4Zm-3.4 1.4h7.7v.9H7.8v-.9Z" fill="#0B0F16"/><path d="M8.2 11.6 6.9 14.4h2.7L8.2 11.6Zm7 0-1.4 2.8h2.8L15.2 11.6Z" fill="#0B0F16"/>',
        'passport' => '<path d="M5.2 2.6h13.6a1.4 1.4 0 0 1 1.4 1.4v16a1.4 1.4 0 0 1-1.4 1.4H5.2A1.4 1.4 0 0 1 3.8 20V4a1.4 1.4 0 0 1 1.4-1.4Z"/><circle cx="12" cy="9.6" r="3.4" fill="#0B0F16"/><path d="M12 7.2c.8.7 1.2 1.6 1.2 2.4s-.4 1.7-1.2 2.4c-.8-.7-1.2-1.6-1.2-2.4s.4-1.7 1.2-2.4Z"/><rect x="8.2" y="15.4" width="7.6" height="1.5" rx=".7" fill="#0B0F16"/><rect x="9.6" y="18" width="4.8" height="1.3" rx=".6" fill="#0B0F16"/>',
        'dove' => '<path d="M20.8 5.4c-1.3.5-2.3.6-3.2.3-1.6-.5-3.3-.4-4.9.4-2 1-3.2 2.7-3.5 4.9L4.9 8.4c-.9-.5-1.9.5-1.4 1.4l2.4 4.2c-1 .4-1.9 1-2.6 1.9l1.5 1.1c1.4-1.6 3.2-2 5.4-1.4l.9 3.3c.3.9 1.6.9 1.8-.1l.7-3.4c3.7-1 6-3.6 6.8-7.7l.4-2.3Z"/><circle cx="15.6" cy="7.2" r=".9" fill="#0B0F16"/>',
        'star' => '<path d="M12 2.4l2.9 5.9 6.5.9-4.7 4.6 1.1 6.5L12 17.3l-5.8 3 1.1-6.5-4.7-4.6 6.5-.9L12 2.4Z"/>',
        'target' => '<path d="M12 2.6a9.4 9.4 0 1 0 9.4 9.4h-1.9A7.5 7.5 0 1 1 12 4.5V2.6Z"/><path d="M12 6.4a5.6 5.6 0 1 0 5.6 5.6h-1.9A3.7 3.7 0 1 1 12 8.3V6.4Z"/><circle cx="12" cy="12" r="1.9"/><path d="M13.4 10.6 19 5h-2.6V2.4l-4.4 4.4 1.4 3.8Z"/>',
    ];
}

/**
 * One glyph, as a complete <svg> element.
 *
 * An unknown name falls back to the gavel rather than rendering an empty box —
 * the same behaviour the React component had, so a typo in the content file is
 * visible as the wrong icon rather than as a hole in the layout.
 */
function icon(string $name, string $class = 'ico'): string
{
    $glyphs = icon_glyphs();
    $body = $glyphs[$name] ?? $glyphs['gavel'];

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="currentColor"'
        . ' aria-hidden="true" focusable="false">' . $body . '</svg>';
}
