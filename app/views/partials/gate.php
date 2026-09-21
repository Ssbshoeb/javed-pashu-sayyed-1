<?php
/*
| Bar Council of India entry disclaimer.
| Mandatory gate — the site is not readable until a choice is made.
| Wording comes from config/practice.php and must not be edited without
| the advocate's written approval.
|
| Rendered hidden and shown by js/site.js once it has read the visitor's stored
| acceptance, so the markup the server sends and what the browser shows agree
| — the same arrangement the React port used, and it means a crawler sees the
| page rather than the dialog.
*/

defined('APP_RUNNING') or exit('No direct access.');

/** The approved closing line, with :firm substituted here on the server. */
$closing = str_replace(':firm', $firm, $disclaimer['closing']);
?>
<div id="disclaimer-gate" class="gate" role="dialog" aria-modal="true"
     aria-labelledby="gate-title" aria-describedby="gate-text" hidden>
    <div class="gate__panel">
        <div class="gate__head">
            <h2 id="gate-title">Disclaimer</h2>
            <p>Please read before proceeding</p>
        </div>

        <div class="gate__body" id="gate-text">
            <p><?= e($disclaimer['intro']) ?></p>

            <ul>
                <?php foreach ($disclaimer['points'] as $point): ?>
                    <li><?= e($point) ?></li>
                <?php endforeach; ?>
            </ul>

            <p><?= e($closing) ?></p>
        </div>

        <div class="gate__foot">
            <button type="button" class="btn btn--ghost" id="gate-decline">Decline</button>
            <?php /* Label must read exactly as the gate text names it: the approved
                     wording in config/practice.php says 'By clicking on "I AGREE"'. */ ?>
            <button type="button" class="btn btn--primary" id="gate-accept">I AGREE</button>
        </div>
    </div>
</div>

<?php /* Without JavaScript the gate cannot be dismissed, so the site stays closed. */ ?>
<noscript>
    <style>
        #disclaimer-gate { display: flex !important; }
        #disclaimer-gate[hidden] { display: flex !important; }
        .gate__foot { display: none; }
        body { overflow: hidden; }
    </style>
</noscript>
