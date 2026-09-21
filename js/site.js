/*
|--------------------------------------------------------------------------
| Site behaviour
|--------------------------------------------------------------------------
| Everything the Next.js port did in the browser, in one file:
|
|   - the Bar Council disclaimer gate      (DisclaimerGate.tsx)
|   - the cookie banner and analytics      (CookieBanner.tsx, Analytics.tsx)
|   - the mobile navigation                (Header.tsx)
|   - the testimonial carousel             (Testimonials.tsx)
|   - scroll reveal, the scroll loop,
|     number counters, smooth scrolling    (SiteBehaviour.tsx)
|   - the upload form's file-size hint     (UploadForm.tsx)
|   - the submit button's pending label    (the four form components)
|
| Written as one plain script rather than modules so it is one request with no
| build step, which is the whole point of the PHP port. It is served with
| `defer`, so the DOM is parsed before any of this runs and nothing here needs
| a DOMContentLoaded wrapper.
|
| Nothing below assumes an element exists. Every page shares this file, and a
| page without a carousel or without counters must not throw on load — a
| script that dies at line one takes the gate with it, and the site is then
| unreadable.
*/

(function () {
  'use strict';

  var config = document.getElementById('site-config');
  var read = function (key, fallback) {
    if (!config) return fallback;
    var value = config.getAttribute('data-' + key);
    return value === null || value === '' ? fallback : value;
  };

  var motionOk = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var canObserve = 'IntersectionObserver' in window;

  /* ======================================================================
     Shared: scrolling to an in-page section
     ====================================================================== */

  /*
      Scrolls to an in-page section, clear of the sticky masthead, and moves
      keyboard focus onto it.

      Shared by the nav's same-page links, by the disclaimer gate (which lands
      here directly when accepted on the home page itself — no navigation, so no
      hash change to let the browser do it) and by the global handler for plain
      `#` links.
  */
  function scrollToHash(hash) {
    var target;

    try {
      target = document.querySelector(hash);
    } catch (error) {
      // A malformed hash from a hand-typed URL is not a selector.
      return null;
    }

    if (!target) return null;

    var masthead = document.querySelector('.masthead');
    var offset = masthead ? masthead.offsetHeight + 16 : 16;
    var top = target.getBoundingClientRect().top + window.scrollY - offset;

    window.scrollTo({ top: top, behavior: motionOk ? 'smooth' : 'auto' });

    window.history.replaceState(null, '', hash);

    target.setAttribute('tabindex', '-1');
    target.focus({ preventScroll: true });

    return target;
  }

  /* ======================================================================
     Analytics
     ====================================================================== */

  /*
      Nothing here runs on its own: until loadAnalytics() is called by the cookie
      banner, not a single request goes to Google. And when GA4_MEASUREMENT_ID is
      empty this is inert, so an unconfigured site behaves exactly as one with no
      analytics at all — no script, no third-party connection, and the tighter
      Content-Security-Policy the front controller sends stays in force.
  */
  var GA4_ID = read('ga4-id', '');
  var gaLoaded = false;

  function loadAnalytics() {
    if (!GA4_ID) return;

    // Consent can fire more than once (the click, then the page load that
    // follows it), and gtag must only be injected once.
    if (gaLoaded) return;
    gaLoaded = true;

    var script = document.createElement('script');
    script.async = true;
    script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(GA4_ID);
    document.head.appendChild(script);

    window.dataLayer = window.dataLayer || [];
    function gtag() {
      window.dataLayer.push(arguments);
    }
    window.gtag = gtag;

    gtag('js', new Date());
    // anonymize_ip is on because this site is governed by the DPDP notice in
    // /legal/privacy, which tells visitors no IP is stored.
    gtag('config', GA4_ID, { anonymize_ip: true });
  }

  /* ======================================================================
     Storage, guarded
     ====================================================================== */

  /*
      Browser storage can be blocked outright — a private window, a browser set
      to refuse site data, an embedded webview. Reading or writing then THROWS
      rather than returning null, so every access goes through these.

      A blocked browser gets the safe side of each decision: the gate shows every
      time, and the cookie banner shows until it is answered on this page load.
  */
  function storeGet(store, key) {
    try {
      return window[store].getItem(key);
    } catch (error) {
      return null;
    }
  }

  function storeSet(store, key, value) {
    try {
      window[store].setItem(key, value);
      return true;
    } catch (error) {
      return false;
    }
  }

  /* ======================================================================
     Disclaimer gate
     ====================================================================== */

  /*
      Bar Council of India entry disclaimer. Mandatory — the site is not readable
      until a choice is made.

      validityDays === 0 means "every visit": acceptance is kept in
      sessionStorage, so it survives navigation within the visit but not the
      browser being closed. Any positive number is remembered in localStorage for
      that many days.
  */
  (function gate() {
    var panel = document.getElementById('disclaimer-gate');
    if (!panel) return;

    var KEY = 'jsa_disclaimer_accepted_until';
    var validityDays = Math.max(0, parseInt(read('disclaimer-days', '0'), 10) || 0);
    var store = validityDays === 0 ? 'sessionStorage' : 'localStorage';
    var main = document.getElementById('main');

    function accepted() {
      var until = storeGet(store, KEY);
      if (until === null) return false;

      return validityDays === 0 || Date.now() < parseInt(until, 10);
    }

    function release() {
      panel.hidden = true;
      document.body.classList.remove('is-locked');
      if (main) main.removeAttribute('aria-hidden');
    }

    if (accepted()) {
      release();
      return;
    }

    // The site behind the gate is inert while it stands: not scrollable, and
    // not reachable by a screen reader.
    panel.hidden = false;
    document.body.classList.add('is-locked');

    // Focus stays inside the dialog: nothing behind it is reachable by keyboard.
    var box = panel.querySelector('.gate__panel') || panel;

    function focusable() {
      return Array.prototype.slice.call(
        box.querySelectorAll('a[href], button, [tabindex]:not([tabindex="-1"])')
      );
    }

    var first = focusable()[0];
    if (first) first.focus();

    box.addEventListener('keydown', function (event) {
      if (event.key !== 'Tab') return;

      var items = focusable();
      if (items.length === 0) return;

      var head = items[0];
      var tail = items[items.length - 1];

      if (event.shiftKey && document.activeElement === head) {
        event.preventDefault();
        tail.focus();
      } else if (!event.shiftKey && document.activeElement === tail) {
        event.preventDefault();
        head.focus();
      }
    });

    /*
        Records that the disclaimer was accepted. Best-effort — logging never
        blocks the visitor. No IP address is stored, only an anonymised
        reference; see app/controllers/disclaimer.php.
    */
    function record() {
      var body = new FormData();
      body.append('entry_path', window.location.pathname);
      body.append('_token', read('csrf', ''));

      try {
        fetch('/api/disclaimer/accept', {
          method: 'POST',
          body: body,
          credentials: 'same-origin',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        }).catch(function () {
          /* best-effort */
        });
      } catch (error) {
        /* best-effort */
      }
    }

    var acceptButton = document.getElementById('gate-accept');
    var declineButton = document.getElementById('gate-decline');

    if (acceptButton) {
      acceptButton.addEventListener('click', function () {
        record();

        var until = validityDays === 0 ? Date.now() : Date.now() + validityDays * 86400000;
        storeSet(store, KEY, String(until));

        release();

        // On the home page the target section is already here, so scroll to it
        // directly; anywhere else, navigate. Either way the visitor lands on
        // content rather than at the top of a page they have just unlocked.
        if (window.location.pathname === '/') {
          scrollToHash('#about');
        } else {
          window.location.href = '/#about';
        }
      });
    }

    if (declineButton) {
      declineButton.addEventListener('click', function () {
        window.location.href = read('decline-url', 'https://www.google.com');
      });
    }
  })();

  /* ======================================================================
     Cookie banner
     ====================================================================== */

  (function cookieBanner() {
    var banner = document.getElementById('cookie-banner');
    if (!banner) return;

    var KEY = 'jsa_cookie_choice';
    var choice = storeGet('localStorage', KEY);

    // A visitor who accepted earlier must be counted on every page after that
    // one, not only on the page where they clicked. Without this the banner is
    // simply hidden on later loads, no click event fires, and analytics records
    // a single pageview per visitor for ever.
    if (choice === 'accept') {
      loadAnalytics();
      return;
    }

    if (choice === 'reject') return;

    // `unanswered` is a real state: the banner shows only for it.
    banner.hidden = false;

    function choose(action) {
      storeSet('localStorage', KEY, action);
      banner.hidden = true;

      if (action === 'accept') loadAnalytics();
    }

    var accept = document.getElementById('cookie-accept');
    var reject = document.getElementById('cookie-reject');

    if (accept) accept.addEventListener('click', function () { choose('accept'); });
    if (reject) reject.addEventListener('click', function () { choose('reject'); });
  })();

  /* ======================================================================
     Masthead and mobile navigation
     ====================================================================== */

  (function navigation() {
    var masthead = document.querySelector('.masthead');
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('primary-nav');

    if (!masthead) return;

    /*
        The panel is fixed and starts at the bottom of the bar, so it needs the
        bar's real height rather than a guess: the stacked name makes the header
        taller on some widths, and a hard-coded offset left a gap or an overlap.
    */
    function syncHeight() {
      var height = Math.round(masthead.getBoundingClientRect().height);
      document.documentElement.style.setProperty('--masthead-h', height + 'px');
    }

    syncHeight();
    window.addEventListener('resize', syncHeight);

    if (!toggle || !nav) return;

    var label = toggle.querySelector('.nav-toggle__label');

    function setOpen(open) {
      toggle.classList.toggle('is-open', open);
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      nav.classList.toggle('is-open', open);
      document.body.classList.toggle('nav-open', open);

      // The bars are decorative; the label carries the accessible name.
      if (label) label.textContent = open ? 'Close' : 'Menu';
    }

    toggle.addEventListener('click', function () {
      syncHeight();
      setOpen(!nav.classList.contains('is-open'));
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') setOpen(false);
    });

    // Returning to a desktop width leaves the panel's fixed positioning behind;
    // clear the open state with it so the body does not stay locked.
    window.addEventListener('resize', function () {
      if (window.innerWidth > 1040) setOpen(false);
    });

    /*
        Menu links carry both a real page URL (for search engines and for direct
        visits) and, in `data-scroll`, the id of the matching section on the home
        page. When that section is on the page already, scroll to it instead of
        navigating.

        Tapping any item closes the panel. Most items navigate away, but the
        same-page ones only scroll, and the panel covering the screen would have
        hidden whatever the visitor had just asked for.
    */
    Array.prototype.forEach.call(nav.querySelectorAll('a'), function (link) {
      link.addEventListener('click', function (event) {
        var selector = link.getAttribute('data-scroll');

        if (
          selector &&
          !event.metaKey &&
          !event.ctrlKey &&
          !event.shiftKey &&
          event.button === 0 &&
          document.querySelector(selector)
        ) {
          event.preventDefault();
          scrollToHash(selector);
        }

        setOpen(false);
      });
    });
  })();

  /* ======================================================================
     Scroll reveal
     ====================================================================== */

  if (canObserve) {
    // The class is added only now, so without JS nothing is ever left hidden.
    document.documentElement.classList.add('js-reveal');

    var revealer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-in');
          revealer.unobserve(entry.target);
        });
      },
      { rootMargin: '0px 0px -12% 0px', threshold: 0.08 }
    );

    // Pages that carry no reveal attributes of their own still get motion:
    // each section's direct children are treated as a reveal group.
    if (!document.querySelector('[data-reveal], [data-reveal-group]')) {
      Array.prototype.forEach.call(
        document.querySelectorAll('.section > .shell, .split > *'),
        function (el) {
          el.setAttribute('data-reveal', '');
        }
      );
    }

    Array.prototype.forEach.call(
      document.querySelectorAll('[data-reveal], [data-reveal-group]'),
      function (el) {
        revealer.observe(el);
      }
    );
  }

  /* ======================================================================
     One scroll loop for all
     ====================================================================== */

  /* Every scroll-driven effect is computed in a single rAF-throttled pass, so
     the page does not run four competing scroll listeners. */

  (function scrollLoop() {
    var masthead = document.querySelector('.masthead');
    var progress = document.getElementById('scroll-progress');
    var toTop = document.getElementById('to-top');
    var heroInner = document.querySelector('.hero > .shell');
    var heroBanner = document.querySelector('.hero');
    var plates = Array.prototype.slice.call(document.querySelectorAll('.parallax__plate'));

    var ticking = false;

    function frame() {
      var y = window.scrollY;

      if (masthead) masthead.classList.toggle('is-stuck', y > 40);

      if (progress) {
        var scrollable = document.documentElement.scrollHeight - window.innerHeight;
        var ratio = scrollable > 0 ? Math.min(y / scrollable, 1) : 0;
        progress.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
      }

      if (toTop) toTop.classList.toggle('is-shown', y > window.innerHeight * 0.6);

      // Hero copy drifts upward and dims as the banner leaves the viewport.
      if (heroInner && heroBanner && motionOk) {
        var height = heroBanner.offsetHeight || 1;
        var passed = Math.min(Math.max(y / height, 0), 1);

        heroInner.style.transform = 'translate3d(0, ' + (passed * 56).toFixed(1) + 'px, 0)';
        heroInner.style.opacity = (1 - passed * 0.85).toFixed(3);
      }

      // Photographic plates move slower than the page.
      if (motionOk) {
        plates.forEach(function (plate) {
          var band = plate.parentElement;
          if (!band) return;

          var box = band.getBoundingClientRect();
          if (box.bottom < -80 || box.top > window.innerHeight + 80) return;

          var centred = (box.top + box.height / 2 - window.innerHeight / 2) / window.innerHeight;
          plate.style.transform = 'translate3d(0, ' + (centred * 14).toFixed(2) + '%, 0)';
        });
      }

      ticking = false;
    }

    function request() {
      if (ticking) return;
      ticking = true;
      window.requestAnimationFrame(frame);
    }

    window.addEventListener('scroll', request, { passive: true });
    window.addEventListener('resize', request, { passive: true });
    frame();

    if (toTop) {
      toTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: motionOk ? 'smooth' : 'auto' });
      });
    }
  })();

  /* ======================================================================
     Number counters
     ====================================================================== */

  (function counters() {
    var items = Array.prototype.slice.call(document.querySelectorAll('[data-count]'));
    if (items.length === 0 || !canObserve) return;

    function countUp(el) {
      var target = parseInt(el.getAttribute('data-count'), 10);
      var suffix = el.getAttribute('data-suffix') || '';

      if (!motionOk || isNaN(target)) {
        el.textContent = (isNaN(target) ? el.textContent : String(target)) + suffix;
        return;
      }

      var started = null;
      var duration = 1400;

      function step(now) {
        if (started === null) started = now;

        var progressed = Math.min((now - started) / duration, 1);
        var eased = 1 - Math.pow(1 - progressed, 3);

        el.textContent = Math.round(target * eased) + suffix;

        if (progressed < 1) window.requestAnimationFrame(step);
      }

      window.requestAnimationFrame(step);
    }

    var watcher = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          countUp(entry.target);
          watcher.unobserve(entry.target);
        });
      },
      { threshold: 0.5 }
    );

    items.forEach(function (el) {
      watcher.observe(el);
    });
  })();

  /* ======================================================================
     Testimonial carousel
     ====================================================================== */

  /*
      The page count depends on the viewport, so it is recomputed on resize and
      the current page clamped — otherwise a visitor who paged to the end on a
      phone and then rotated the device landed on empty space.
  */
  (function testimonials() {
    var carousel = document.getElementById('testimonials');
    if (!carousel) return;

    var track = carousel.querySelector('.tcar__track');
    var items = carousel.querySelectorAll('.tcar__item');
    var prev = carousel.querySelector('[data-tcar="prev"]');
    var next = carousel.querySelector('[data-tcar="next"]');

    if (!track || items.length === 0) return;

    var page = 0;
    var pages = 1;

    function perView() {
      var width = window.innerWidth;
      if (width >= 1040) return 3;
      if (width >= 700) return 2;
      return 1;
    }

    function paint() {
      // The track has a 22px gap, so a page is one viewport width plus one gap.
      track.style.transform = 'translateX(calc(' + -page * 100 + '% - ' + page * 22 + 'px))';

      if (prev) prev.disabled = page === 0;
      if (next) next.disabled = page >= pages - 1;
    }

    function measure() {
      pages = Math.max(1, Math.ceil(items.length / perView()));
      page = Math.min(page, pages - 1);
      paint();
    }

    if (prev) {
      prev.addEventListener('click', function () {
        page = Math.max(0, page - 1);
        paint();
      });
    }

    if (next) {
      next.addEventListener('click', function () {
        page = Math.min(pages - 1, page + 1);
        paint();
      });
    }

    measure();
    window.addEventListener('resize', measure);
  })();

  /* ======================================================================
     Smooth scrolling for plain in-page links
     ====================================================================== */

  /* Plain in-page links (`href="#contact"`) scroll instead of jumping, and stop
     clear of the sticky masthead. Links that navigate are left alone, and the
     nav's own same-page items handle themselves above — this only ever sees a
     click they did not already claim. */

  document.addEventListener('click', function (event) {
    if (event.defaultPrevented || event.metaKey || event.ctrlKey || event.shiftKey) return;

    var target = event.target;
    if (!target || !target.closest) return;

    var link = target.closest('a[href]');
    if (!link) return;

    var href = link.getAttribute('href');
    if (!href || href === '#' || href.charAt(0) !== '#') return;
    if (!document.querySelector(href)) return;

    event.preventDefault();
    scrollToHash(href);
  });

  /* ======================================================================
     Upload form: the file-size hint
     ====================================================================== */

  /*
      Tells the visitor the size before the upload starts, so an oversized file
      is caught at the file picker rather than after a long POST that the server
      then rejects.
  */
  (function fileHint() {
    var input = document.querySelector('input[type="file"][data-max-mb]');
    if (!input) return;

    var note = document.getElementById(input.getAttribute('data-note') || '');
    if (!note) return;

    var original = note.textContent;
    var maxMb = parseFloat(input.getAttribute('data-max-mb')) || 10;

    input.addEventListener('change', function () {
      var file = input.files && input.files[0];

      if (!file) {
        note.textContent = original;
        return;
      }

      var mb = file.size / (1024 * 1024);

      note.textContent =
        mb > maxMb
          ? file.name + ' is ' + mb.toFixed(1) + ' MB — larger than the ' + maxMb + ' MB limit.'
          : file.name + ' — ' + mb.toFixed(1) + ' MB';
    });
  })();

  /* ======================================================================
     Forms: the pending state
     ====================================================================== */

  /*
      The React port disabled the submit button and swapped its label while the
      Server Action ran. A plain form POST has the same problem — an upload can
      take seconds, and a visitor with no feedback clicks again and sends the
      message twice.

      The button is disabled AFTER the browser has begun submitting (a disabled
      button's name/value would not be sent, and on some browsers disabling it
      during the event cancels the submit), and `form.requestSubmit` is never
      called here — the browser's own native validation must still get to run
      and stop the submission if a required field is empty.
  */
  Array.prototype.forEach.call(document.querySelectorAll('form.form'), function (form) {
    form.addEventListener('submit', function () {
      var button = form.querySelector('button[type="submit"]');
      if (!button || button.disabled) return;

      var pending = button.getAttribute('data-pending');

      window.setTimeout(function () {
        button.disabled = true;
        if (pending) button.textContent = pending;
      }, 0);
    });
  });
})();
