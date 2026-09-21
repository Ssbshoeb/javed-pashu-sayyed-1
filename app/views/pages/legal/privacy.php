<?php
/* Ported from src/app/legal/privacy/page.tsx. */

defined('APP_RUNNING') or exit('No direct access.');

$email = practice('contact.email');
$addressLines = practice('contact.addressLines');
?>
<section class="hero" style="<?= hero_image_style('/img/band.webp') ?>">
    <div class="shell">
        <p class="eyebrow">Legal</p>
        <h1>Privacy Policy</h1>
        <p class="lede" style="margin-top:1rem;">
            This policy explains what personal data this website collects and how it is handled, in
            accordance with the Digital Personal Data Protection Act, 2023.
        </p>
    </div>
</section>

<section class="section">
    <div class="shell">
        <div class="prose prose--wide">
            <h2 style="margin-bottom:0.8rem;">Who is responsible for your data</h2>
            <p>
                <?= e(practice('firm')) ?>, at <?= e(implode(', ', $addressLines)) ?>, is the data fiduciary
                for personal data collected through this website. Questions and requests may be sent to
                <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">What is collected</h2>
            <ul class="list-check">
                <li>
                    <strong>Enquiry and case evaluation forms</strong> &mdash; name, email address, telephone
                    number, area of law, jurisdiction, urgency and the description you write.
                </li>
                <li>
                    <strong>Document upload</strong> &mdash; name, email address, telephone number, document
                    type, your note and the file itself.
                </li>
                <li>
                    <strong>Appointment request</strong> &mdash; name, email address, telephone number,
                    preferred date, slot and mode, and any note.
                </li>
                <li>
                    <strong>Disclaimer acceptance</strong> &mdash; a one-way hashed session reference, the page
                    entered and the time. No IP address is stored.
                </li>
                <li>
                    <strong>Analytics</strong> &mdash; only if you accept analytics cookies. Nothing is loaded
                    before that.
                </li>
            </ul>

            <h2 style="margin:2rem 0 0.8rem;">Why it is collected</h2>
            <p>
                Solely to respond to you. Enquiry, case evaluation, document upload and appointment
                submissions are forwarded by email to the chamber at the moment you submit them and are
                not saved in any database or file store on the website &mdash; the notification and
                acknowledgement emails are the only record kept. Your data is not used for marketing,
                is not sold, and is not shared with any third party except where the law requires it or
                where a service provider processes it on the chamber&rsquo;s behalf under an obligation
                of confidentiality (for example, the email service used to deliver notifications).
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Consent</h2>
            <p>
                Every form on this website requires you to tick a consent box before it can be
                submitted. You may withdraw consent at any time by writing to the address above;
                withdrawal does not affect anything already done on the basis of the consent given
                earlier.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">How long it is kept</h2>
            <ul class="list-check">
                <li>
                    Enquiries, case evaluation submissions, uploaded documents and appointment requests &mdash;
                    not kept on the website at all; each exists only for the moment it takes to email it
                    to the chamber. Whatever is kept after that lives in the chamber&rsquo;s own email
                    mailbox, governed by the email provider&rsquo;s retention and, once a matter is taken
                    on, by professional obligations rather than by this policy.
                </li>
                <li>
                    Disclaimer acceptance records &mdash; written to the server log only, and kept for as long
                    as those logs are retained by the hosting provider.
                </li>
            </ul>

            <h2 style="margin:2rem 0 0.8rem;">How it is protected</h2>
            <p>
                The website is served over HTTPS. Because form and upload data is forwarded by email
                rather than saved on the server, there is no on-site database or file store of it to be
                accessed or breached. Access to the email account it is sent to is limited to the
                advocate and, where necessary, the technical service provider maintaining the site.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Your rights</h2>
            <p>
                You may ask what data about you is held and raise a grievance about how it has been
                handled. Because submissions are not stored on the website, a request to correct or
                erase a past submission should be made directly to the chamber&rsquo;s email address
                below, the same way you would ask any correspondent to amend or delete a message. Write
                to <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a>, marking the subject line &ldquo;Data
                request&rdquo;. Requests are answered within a reasonable period, except where retention
                is required by law or by professional obligation.
            </p>

            <h2 style="margin:2rem 0 0.8rem;">Children</h2>
            <p>
                This website is not directed at children, and forms should not be submitted by anyone
                under eighteen without the consent of a parent or guardian.
            </p>

            <div class="notice" style="margin-top:2rem;">
                <strong>A caution about email.</strong> Email and web forms are not secure channels. Do
                not send privileged material, identity documents or anything sensitive through this
                website before an engagement has been confirmed in writing.
            </div>
        </div>
    </div>
</section>
