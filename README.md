# Adv. Javed Pashu Sayyed — website (PHP)

The chamber website, as a plain PHP application. Ported from the Next.js
project in `../javed-sayyed-nextjs`, which was itself a port of the original
Laravel app — every URL, every rule and every word of copy is the same.

No Composer, no framework, no build step and no database. Upload the files and
it runs.

---

## Requirements

| | |
|---|---|
| PHP | 8.0 or newer |
| Extensions | `openssl` (SMTP over TLS), `fileinfo` (upload sniffing), `mbstring` — all standard, all on by default |
| Web server | Apache or LiteSpeed with `mod_rewrite` (the supplied `.htaccess`), or nginx (see below) |
| Database | none |

---

## Installing on shared hosting (Hostinger, cPanel)

1. **Upload.** Put the *contents* of this folder into the domain's document
   root — `public_html` on Hostinger. The tree should look like:

   ```
   public_html/
     index.php  .htaccess  .user.ini  router.php
     app/  css/  img/  js/  storage/
     .env
   ```

2. **Create `.env`.** Copy `.env.example` to `.env` and fill it in. At minimum
   set `SITE_URL` and the five `MAIL_*` values. See **Mail** below.

3. **Make `storage/` writable.** `chmod 755 storage` and the two folders inside
   it (`storage/logs`, `storage/cache/ratelimit`). The application degrades
   gracefully if it cannot write — it will not take the site down — but the log
   and the rate limiter both need it.

4. **Check it.** Load the site. Then submit the contact form once and confirm
   the mail arrives at the chamber.

That is the whole install.

### If the host cannot serve from this folder

Some panels insist the document root is a subfolder. Either point the domain's
document root at wherever you uploaded these files, or move `app/` and
`storage/` one level *above* the document root and change the two paths at the
top of `app/bootstrap.php` to match. Nothing else needs editing.

---

## Configuration

Everything is in `.env`. The file is refused by `.htaccess`, and it holds the
mailbox password — never commit it and never leave it world-readable.

| Key | What it does |
|---|---|
| `APP_ENV` | `production` hides errors and enables the misconfigured-mailer warning. `local` while developing. |
| `APP_DEBUG` | `true` prints errors to the page. Never `true` in production. |
| `SITE_URL` | The site's absolute origin, no trailing slash. Used for canonical URLs, `og:url`, the sitemap and the JSON-LD `@id` values. Falls back to the request's own host if unset, so the site still works — but set it, because it is what makes the canonical URL the same whether the visitor arrived on `www` or not. |
| `MAIL_*` | See below. |
| `NOTIFY_EMAIL` | Overrides where submissions are delivered. Set it on staging so a test submission does not reach the advocate. |
| `SITE_TIMEZONE` | Stamps the "Received" line on notifications and decides what "tomorrow" means on the appointment form. Defaults to `Asia/Kolkata`. |
| `GA4_MEASUREMENT_ID` | Google Analytics 4 ID. **Leave empty and nothing at all is emitted** — no script tag, no third-party request, and the Content-Security-Policy stays tightened. Analytics only ever starts after the visitor accepts the cookie banner. |

Everything the site *says* — the practice areas, the articles, the biography,
the chamber address, the opening hours — is in **`app/config/practice.php`**.
That one file is the single source of truth. Adding a practice area there
creates its page, its card on the index, its footer link and its sitemap entry
automatically; no other file needs to change.

**Read the compliance notes in that file before editing it.** Several blocks
are flagged for Bar Council of India Rule 36 (which prohibits advertising and
solicitation by advocates), and three lists on the About page are assertions of
fact that need the advocate's written confirmation.

---

## Mail

Submissions are **not stored anywhere**. Each one exists only for as long as it
takes to email it to the chamber — the notification and the visitor's
acknowledgement are the only record, and the privacy notice says so.

Delivery speaks SMTP directly (`app/support/smtp.php`). It does **not** use
PHP's `mail()`, deliberately: `mail()` hands the message to a local sendmail
binary whose envelope sender is a server-owned address, which is exactly what
gets an enquiry notification filed as spam or rejected for SPF failure.

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_SCHEME=smtps          # implicit TLS on 465; leave empty for STARTTLS on 587
MAIL_USERNAME=admin@javedpashusayyed.com
MAIL_PASSWORD=…
MAIL_FROM_ADDRESS="admin@javedpashusayyed.com"
MAIL_FROM_NAME="Adv. Javed Pashu Sayyed"
```

`MAIL_FROM_ADDRESS` **must be the authenticated mailbox**. If it is a different
address the message fails SPF/DKIM alignment at the receiving end.

### `MAIL_MAILER=log`

Writes each message to `storage/logs/` instead of sending it, so the forms can
be exercised end to end with no credentials. **It must never survive into
production**: submissions would be accepted, the visitor shown a reference
number, and nothing delivered. The application logs a `CRITICAL` line every
time this happens in production, but set it properly instead.

### If mail does not arrive

`storage/logs/app-YYYY-MM.log` records every failure with the SMTP server's own
reply — that reply names the problem (bad credentials, blocked port, refused
recipient) far more precisely than any guess.

---

## Local development

```sh
cp .env.local.example .env
php -S localhost:8000 router.php
```

`router.php` exists only for PHP's built-in server, which has no `.htaccess`.
It does what the rewrite rules do. Nothing on the live host uses it, and it can
be deleted before uploading.

---

## nginx

There is no `.htaccess` on nginx. The equivalent server block:

```nginx
root /var/www/javedpashusayyed;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    include fastcgi_params;
    fastcgi_pass unix:/run/php/php8.2-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
}

# Must be denied explicitly: these are real files, so try_files would serve them.
location ~ ^/(app|storage)/ { deny all; }
location ~ /\.           { deny all; }

client_max_body_size 12M;
```

Every PHP file under `app/` also carries a `defined('APP_RUNNING') or exit`
guard, so even a misconfigured server exposes no source and no credentials —
the two measures are belt and braces.

---

## How it is put together

```
index.php                  front controller: security headers, then routing
app/
  bootstrap.php            .env, session, error handling, timezone
  config/practice.php      ALL content and chamber facts — the single source of truth
  controllers/
    pages.php              one function per page
    submissions.php        the four write paths
    seo.php                robots.txt and sitemap.xml
    disclaimer.php         the acceptance-recording endpoint
  support/                 helpers, validation, mail, SMTP, icons, images, JSON-LD
  views/
    layout.php             the <head> and page chrome
    partials/              header, footer, gate, cookie banner, the four forms
    pages/                 one template per page
css/site.css               unchanged from the Next.js project
js/site.js                 every browser behaviour, in one file, no build step
storage/                   log and rate-limit buckets (writable; never served)
```

### Routing

A plain `switch` on the request path in `index.php`. The site has fourteen page
routes, two SEO endpoints and one JSON endpoint, all known at author time, and
a table anyone can read top to bottom is worth more here than a router.

Slugs are matched against the list of pages that actually exist, so an unknown
one is a 404 rather than a lookup — the same guarantee `dynamicParams = false`
gave in the Next.js port.

### Forms

Classic post/redirect/get, which is what the Laravel original did and what the
error wording was written against. A form POSTs to the page it sits on; on
failure the errors and the old input are flashed to the session and the visitor
is redirected back; on success they are redirected to the confirmation screen
with the reference in the query string.

That means a refresh never re-sends a submission, and a visitor who fails
validation gets their typing back.

Each form is named in the flash bag, so the two enquiry forms (home page and
`/contact`) never show each other's errors.

### Security

- **CSRF** on every POST, including the disclaimer endpoint.
- **Rate limiting** at 8 submissions per minute per address, matching the
  `throttle:8,1` middleware the Laravel routes carried. Buckets are files under
  `storage/cache/ratelimit`, named by a hash of the address so no IP is ever
  written to disk.
- **Honeypot** field on all four forms.
- **Uploads** are checked by extension *and* by `finfo` sniffing of the file's
  own bytes, and the two must agree — a `.php` renamed to `.pdf` is refused.
  The file is never written to a public path; it goes straight from PHP's
  temporary upload file into the notification email.
- **Header injection** is impossible: every address is validated with
  `FILTER_VALIDATE_EMAIL` and every header value has CR/LF stripped.
- **Output escaping** — `e()` at every interpolation. The only unescaped output
  is the icon glyphs and the JSON-LD block, both trusted literals built on the
  server, and both documented as such where they are emitted.
- **Security headers** (CSP, HSTS, `X-Frame-Options`, `Referrer-Policy`,
  `Permissions-Policy`) are sent from `index.php` so they survive a move to
  nginx, and duplicated in `.htaccess` for static files, which never reach PHP.

---

## Differences from the Next.js version

Behaviour is the same everywhere it can be. Three things genuinely differ:

1. **Rate-limit storage.** Next kept counters in one long-lived Node process's
   memory. PHP shares nothing between requests, so they are files. This is
   *stronger* on a single host — it survives worker restarts, which the
   in-memory map did not.

2. **Upload MIME checking is stricter.** The Node port could only check the
   `Content-Type` the browser attached. PHP's `finfo` sniffs the file's actual
   bytes, which is what Laravel's `mimes` rule did originally.

3. **Invalid fields are marked.** `site.css` has always carried
   `input[aria-invalid="true"]` rules, but the React components computed
   `fieldErrors` and never put them on the markup, so those rules were dead.
   They are now applied. The wording still appears once, in the error box above
   the form — not repeated beside each input.

---

## Before launch

- [ ] `.env` created, with real SMTP credentials and `MAIL_MAILER=smtp`
- [ ] `APP_DEBUG=false` and `APP_ENV=production`
- [ ] `SITE_URL` set to the canonical origin
- [ ] TLS certificate issued, then uncomment the HTTPS redirect in `.htaccess`
- [ ] Pick `www` or the bare domain and uncomment the matching canonical-host
      rule in `.htaccess`
- [ ] `storage/` writable
- [ ] One test submission through each of the four forms, confirmed received
- [ ] The `TODO` items in `app/config/practice.php` resolved with the advocate
- [ ] The three factual lists on the About page confirmed **in writing**
      (the ten courts, the eight memberships, the MNC/international/NGO claim)
- [ ] `/sitemap.xml` and `/robots.txt` load, and the sitemap URLs use the real
      domain
