<?php
/*
| The client's own emblem, taken from Adv_Javed_Sayyed_Logo_High_Resolution.pdf
| (supplied 23 Aug 2026) and cut out of the white page it is drawn on.
|
| KNOWN LIMITATION - do not "fix" this by re-keying with a different
| threshold. That PDF is a print deliverable: the artwork sits on a white
| background AND contains white/near-white parts (the ribbon under the S, the
| highlights on the S, a soft glow inside the arc). Nothing can tell those
| apart from the page they are printed on by pixel value alone. Cutting
| tightly enough to drop the glow also costs some of the ribbon; cutting
| loosely enough to keep the ribbon leaves a white disc behind the whole mark.
| This is the tight cut, chosen because the header is near-black.
|
| The real fix is a transparent-background or dark-background export from
| whoever made the logo. Ask for SVG - the mark is then sharp at any size and
| this file, the two favicons and the print artwork all come from one source.
*/

defined('APP_RUNNING') or exit('No direct access.');
?>
<picture>
    <source srcset="/img/logo-mark.webp" type="image/webp">
    <img class="logomark" src="/img/logo-mark.png" alt="<?= e(practice('name')) ?>" width="64" height="52">
</picture>
