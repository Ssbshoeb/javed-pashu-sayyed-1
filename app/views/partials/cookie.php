<?php
/*
| Cookie banner, ported from src/components/CookieBanner.tsx.
|
| Rendered hidden. js/site.js reads the visitor's stored choice and shows it
| only when the question is still unanswered — the same "unanswered is a real
| state" arrangement the React component had.
|
| Nothing analytics-related loads until "Accept analytics" is clicked, and when
| GA4_MEASUREMENT_ID is empty the loader is inert, so an unconfigured site
| behaves exactly as one with no analytics at all.
*/

defined('APP_RUNNING') or exit('No direct access.');
?>
<div class="cookie" id="cookie-banner" hidden>
    <div class="cookie__inner">
        <p>
            This website uses essential cookies to function. Analytics cookies are loaded only if you
            accept them. See the <a href="<?= e(route('legal', 'cookies')) ?>">cookie policy</a>.
        </p>
        <div class="btn-row">
            <button type="button" class="btn btn--ghost" id="cookie-reject">Essential only</button>
            <button type="button" class="btn btn--primary" id="cookie-accept">Accept analytics</button>
        </div>
    </div>
</div>
