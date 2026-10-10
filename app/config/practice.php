<?php
/*
|--------------------------------------------------------------------------
| Practice details
|--------------------------------------------------------------------------
| Single source of truth for every fact shown on the website.
| Biography and areas of practice are as supplied by the advocate.
| Items marked TODO still need to be confirmed before launch.
|
| Ported from src/content/practice.ts. Everything that file held is here, in
| the same order, with the same warnings.
*/

defined('APP_RUNNING') or exit('No direct access.');

return [
    'name' => 'Adv. Javed Pashu Sayyed',
    // The site runs under the advocate's own name, not a firm name. This value
    // also fills the disclaimer's liability clause and the footer copyright.
    'firm' => 'Adv. Javed Pashu Sayyed',
    'tagline' => 'Advocate in Mumbai & Pune — Corporate & Commercial Law · Arbitration · Criminal Law · Cyber Crime',
    'shortIntro' => 'Advocate near you practising before the High Court of Judicature at Bombay, Pune Courts, and the Supreme Court of India, combining over two decades of senior corporate legal leadership with a litigation and dispute-resolution practice.',

    'strapline' => '20+ Years of Corporate & Legal Experience · Advocate in Mumbai & Pune · Litigation & Dispute Resolution',

    'enrolment' => 'Enrolled with the Bar Council of Maharashtra & Goa', // TODO add enrolment number
    'qualifications' => ['LL.B.'], // TODO confirm full qualifications
    'experienceYears' => 20,
    'languages' => ['English', 'Hindi', 'Marathi', 'Urdu'], // TODO confirm

    'courts' => [
        'High Court of Judicature at Bombay',
        'Supreme Court of India',
        'Mumbai Sessions Court',
        'Pune Courts',
        'High Courts across India',
        'Tribunals and other authorities',
    ],

    /*
    | Biography, as supplied by the advocate.
    */
    'bio' => [
        'Adv. Javed Pashu Sayyed is a seasoned legal professional with over two decades of corporate and legal experience, combining extensive corporate leadership exposure with a strong litigation and dispute-resolution practice.',
        'Over the course of his professional career, he has worked with multinational and international companies in senior legal and corporate leadership roles, including as Legal Head and Vice President, handling complex legal, regulatory, contractual, commercial and dispute-management matters. This corporate experience provides him with a practical understanding of business operations, risk management, negotiations and strategic decision-making in addition to conventional legal practice.',
        'His practice encompasses Corporate & Commercial Law, Arbitration and Dispute Resolution, Criminal Law, Cyber Crime and Cyber Fraud, Family & Matrimonial Law, Property and Commercial Disputes, and Constitutional and Writ matters. He has handled and advised on complex and high-stakes matters requiring strategic litigation, detailed legal research and effective representation before courts and other authorities.',
        'He currently practises before the High Court of Judicature at Bombay, the Supreme Court of India, Mumbai Sessions Court, Pune Courts, and other courts and tribunals, and is involved in matters before various High Courts across India.',
        'Having worked with corporate management, businesses, professionals and individual clients, Adv. Sayyed brings a distinctive combination of corporate insight, litigation strategy and courtroom experience to his legal practice. His approach focuses on understanding the commercial and practical implications of a dispute while developing an effective legal strategy suited to each matter.',
        'Beyond professional practice, he has also contributed to legal education by delivering lectures and interacting with students at various law faculties and academic institutions, sharing practical perspectives on law, litigation and the evolving legal profession.',
    ],

    /*
    | Figures shown in the statistics band. These animate from zero when the
    | band scrolls into view.
    |
    | COMPLIANCE WARNING — read before editing.
    | Bar Council of India Rule 36 prohibits advertising and solicitation.
    | A quantified success claim ("98% successful cases") or a count of free
    | services is the kind of statement that has drawn disciplinary complaints
    | against advocates, and every figure must be one the advocate can
    | substantiate if asked. Only verifiable facts are published below.
    | To add a success rate or consultation count, set the values here — but
    | only on the advocate's written instruction, with figures he can prove.
    */
    'figures' => [
        'successRate' => null,       // deliberately not published — see warning above
        'freeConsultations' => null, // deliberately not published — see warning above
        // The hero states this already; repeating it put the same figure on the
        // home page twice.
        'showYears' => false,
        'showAreas' => true,
    ],

    /*
    | Search-engine and social-sharing details.
    | Everything here is factual information Rule 36 permits a website to carry
    | (name, address, telephone, enrolment, areas of practice, qualifications).
    | No keyword stuffing and no claim of superiority — that is what keeps the
    | site indexable without becoming advertising.
    */
    'seo' => [
        // 1200x630 image used when a page is shared on WhatsApp, LinkedIn, X.
        // TODO replace with a purpose-made card carrying the name and chamber.
        'shareImage' => '/img/hero-1.jpg',
        // Filled in only once the profiles actually exist. An empty value is
        // omitted from the structured data rather than published as a dead link.
        'profiles' => [
            'linkedin' => '', // TODO advocate's LinkedIn profile URL
            'google' => '',   // TODO Google Business Profile URL, once claimed
        ],
    ],

    /*
    | Analytics.
    |
    | Set GA4_MEASUREMENT_ID in .env to the property's ID (G-XXXXXXXXXX) to turn
    | analytics on. While it is empty NOTHING is emitted - no script tag, no
    | third-party request - so the site stays exactly as it is until the ID is
    | supplied. It is read from the environment rather than hard-coded here so
    | the same code can run against a test property without an edit.
    |
    | Nothing loads until the visitor accepts analytics cookies on the banner.
    | See js/site.js and views/partials/cookie.php.
    */
    'analytics' => [
        'ga4Id' => env('GA4_MEASUREMENT_ID', ''),
    ],

    'contact' => [
        // From the advocate's card.
        'addressLines' => [
            'Adv. Javed Pashu Sayyed',
            '2nd Floor, WeWork, Express Towers',
            'Nariman Point, Mumbai, Maharashtra 400021',
        ],
        // Withdrawn on the advocate's instruction, 18 Aug 2026. Setting both values
        // again restores the telephone row, the footer entry and the structured data:
        // every view that shows a number is guarded on this being non-empty.
        'phone' => null,
        'phoneLink' => null,
        'email' => 'adv.syedhc@gmail.com',
        // Where every enquiry, upload and appointment request is delivered.
        // NOTIFY_EMAIL overrides this at run time (see controllers/submissions.php)
        // so a staging deploy does not mail the chamber.
        // Domain is live — delivered to the Hostinger mailbox on javedpashusayyed.com.
        'notifyEmail' => 'admin@javedpashusayyed.com',
        'mapQuery' => 'Express Towers, Nariman Point, Mumbai 400021',
        // Used for the LegalService geo coordinates, which is what lets the
        // chamber appear for "advocate near me" style local searches.
        // Confirmed 18 Aug 2026 against the Google Maps pin supplied by the
        // advocate (Express Towers, Ramnath Goenka Marg, Nariman Point).
        'geo' => ['lat' => '18.9281438', 'lng' => '72.8221543'],
        // Machine-readable form of the hours below, for structured data.
        // Keep the two in step if the hours change.
        'hoursSpec' => [
            [
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens' => '10:00',
                'closes' => '19:00',
            ],
            ['days' => ['Saturday'], 'opens' => '10:00', 'closes' => '14:00'],
        ],
        // TODO have the advocate confirm these before launch.
        'hours' => [
            'Monday to Friday' => '10:00 am – 7:00 pm',
            'Saturday' => '10:00 am – 2:00 pm',
            'Sunday' => 'Closed',
        ],
    ],

    /*
    | Bookable hours on the appointment form.
    |
    | ONE list, read by both the form and the validation rules. They used to be
    | two hardcoded arrays - the form offered them and the controller accepted
    | them - so changing the labels in the view alone made every submission fail
    | validation.
    |
    | Written to match the 12-hour form used by `hours` above; the form previously
    | showed 24-hour times beside a 12-hour opening-hours hint.
    */
    'appointmentSlots' => [
        '10:00 – 11:00 am',
        '11:00 am – 12:00 pm',
        '12:00 – 1:00 pm',
        '3:00 – 4:00 pm',
        '4:00 – 5:00 pm',
        '5:00 – 6:00 pm',
    ],

    /*
    | Disclaimer gate. Text is reproduced as supplied and must be approved
    | by the advocate in writing before launch. Do not edit without approval.
    */
    'disclaimer' => [
        /*
        | 0 = show the disclaimer on every visit. Acceptance lasts only for the
        | current browsing session, so moving between pages does not re-prompt,
        | but closing the browser and returning does.
        | Any positive number remembers the acceptance for that many days.
        */
        'validityDays' => 0,
        'declineUrl' => 'https://www.google.com',
        'intro' => 'The rules of the Bar Council of India prohibit law firms from soliciting work or advertising in any manner. By clicking on "I AGREE", the user acknowledges that:',
        'points' => [
            'The user wishes to gain more information about its practice areas and attorneys, for his/her own information.',
            'The information is made available/provided to the user only on his/her specific request and any information obtained or material downloaded from this website is completely at the user’s volition and any transmission, receipt or use of this site is not intended to, and will not, create any lawyer-client relationship.',
            'None of the information contained on the website is in the nature of a legal opinion or otherwise amounts to any legal advice.',
        ],
        'closing' => ':firm is not liable for any consequence of any action taken by the user relying on material/information provided under this website. In cases where the user has any legal issues, he/she in all cases must seek independent legal advice.',
    ],

    /*
    | About page, reproduced from the PDF design the client supplied on
    | 19 Aug 2026, which supersedes the earlier About mock-up.
    |
    | READ THIS BEFORE ANY OF IT GOES LIVE.
    |
    | 1. VERIFY, IN WRITING. Three lists below are assertions of fact about the
    |    advocate, not marketing language, and a wrong entry is far more serious
    |    than a Rule 36 slip:
    |      - `courts`        : ten courts, including five High Courts outside
    |                          Maharashtra. He must actually appear in them.
    |      - `memberships`   : eight organisations. Each membership must be real
    |                          and current, and each body must exist.
    |      - the MNC / international / NGO client claim in `advocateCopy`.
    |
    | 2. RULE 36. The heading 'Areas of Expertise' is the single riskiest label
    |    on the site: the Bar Council permits stating areas of PRACTICE and
    |    objects to claims of expertise or specialisation. 'distinguished',
    |    'excellence', 'result-oriented', 'highest standards' and the closing
    |    slogan are the same category of problem. All kept as supplied, on the
    |    client's instruction, and flagged in the view where they appear.
    |
    | 3. The area list here is a THIRD, different list - it does not match the
    |    home page tiles or the eight practice-area pages that actually exist.
    |    Only five of these ten have a page behind them.
    */
    'aboutPage' => [
        'eyebrow' => 'About Us',
        'heading1' => 'About the',
        'heading2' => 'Advocate',
        // "Excellence" is a superlative claim.
        'strap' => 'Dedication. Integrity. Excellence.',
        // The last two lines were added 29 Aug 2026 to give the hero enough copy
        // to stand against the photograph - it ended halfway up the band and left
        // the column empty below.
        //
        // NEEDS THE ADVOCATE'S SIGN-OFF, like anything else on this page. They
        // state no new fact: the courts are the ones already listed in
        // `advocateCopy` below, and the areas are the ones in `expertise`. They
        // add no superlative and no claim about outcomes, so they carry no RULE 36
        // problem of their own - unlike the two lines above them, which are the
        // client's own wording and are flagged where they sit.
        'intro' => [
            'Law is not just a profession, it is a responsibility.',
            // "result-oriented" and "highest standards" both overstate.
            'At the Chambers of Adv. Javed Pashu Sayyed, we are committed to delivering strategic, practical and result-oriented legal solutions with the highest standards of integrity and professionalism.',
            'The chamber appears for individuals, businesses and institutions before the Supreme Court of India, the High Court of Judicature at Bombay, and courts, tribunals and forums across the country.',
            'Its work runs from corporate and commercial advice and arbitration through to criminal, family, property and cyber matters, at every stage from the first consultation to the final hearing.',
        ],

        // Not "About the Advocate" - that is the page's own H1, and repeating it
        // as the next heading reads as a duplicate to a screen reader and to a
        // crawler reading the outline.
        'advocateHeading' => 'Background and experience',
        'advocateCopy' => [
            // "distinguished experience" is a superlative.
            'Adv. Javed Pashu Sayyed is an Advocate with huge experience in the field of law. He has appeared and argued before the Supreme Court of India, Bombay High Court, various High Courts across India, Sessions Courts, JMFC Courts and other Tribunals and Forums.',
            'His practice spans Corporate Law, Criminal Law, Arbitration (Domestic & International), Cyber Crime, Family & Divorce Matters, Property Disputes, Human Rights, Consumer Protection and Passport & Criminal Case Permissions.',
            // TODO the MNC, international company and NGO claim needs the
            // advocate's written confirmation. "result-oriented" flagged.
            'He has advised and represented a wide spectrum of clients including Individuals, Corporates, MNCs, International Companies and NGOs. His client-centric approach, ethical practice and result-oriented strategy have earned him trust, respect and long-standing professional relationships.',
            'He continues to work towards protecting rights, promoting justice and contributing to the legal fraternity and society at large.',
        ],

        // The design's own heading. "Expertise" is the Rule 36 problem noted above.
        'expertiseHeading' => 'Areas of Expertise',
        'expertise' => [
            ['icon' => 'briefcase', 'title' => 'Corporate Law', 'slug' => 'corporate-commercial'],
            // `img` overrides `icon`: the artwork the client supplied for this one.
            ['icon' => 'gavel', 'title' => 'Criminal Law', 'slug' => 'criminal', 'img' => 'icons/gavel-criminal.png'],
            ['icon' => 'handshake', 'title' => 'Arbitration & Conciliation (Domestic & International)', 'slug' => 'arbitration'],
            ['icon' => 'monitor', 'title' => 'Cyber Crime & IT Law', 'slug' => 'cyber-crime'],
            ['icon' => 'scalesdoc', 'title' => 'Litigation & Dispute Resolution', 'slug' => null],
            ['icon' => 'family', 'title' => 'Family & Divorce Matters', 'slug' => 'family'],
            ['icon' => 'house', 'title' => 'Property & Real Estate Disputes', 'slug' => 'property'],
            ['icon' => 'shieldcheck', 'title' => 'Consumer Protection Matters', 'slug' => null],
            ['icon' => 'passport', 'title' => 'Passport & Criminal Case Permissions', 'slug' => null],
            ['icon' => 'dove', 'title' => 'Human Rights Advocacy', 'slug' => null],
        ],

        'courtsHeading' => 'Courts of Practice',
        // TODO every entry below must be confirmed by the advocate in writing.
        'courts' => [
            'Supreme Court of India',
            'Bombay High Court (Mumbai)',
            'Kolkata High Court',
            'Karnataka High Court',
            'Andhra Pradesh High Court',
            'Rajasthan High Court',
            'Delhi High Court',
            'Mumbai Courts',
            'Pune Court',
            'Delhi Courts',
        ],

        'membershipsHeading' => 'Member of Human Rights & Social Organisations',
        // TODO each of these must be a real, current membership of a body that
        // exists. This is the list most likely to be checked by anyone hostile.
        // `img` is the artwork lifted from the design PDF and overrides `icon`;
        // `icon` stays as the fallback if an image is ever removed.
        'memberships' => [
            ['icon' => 'globe', 'name' => 'International Human Rights Council', 'img' => 'icons/hr-international-council.png'],
            ['icon' => 'laurel', 'name' => 'Human Rights Protection Council', 'img' => 'icons/hr-protection-council.png'],
            ['icon' => 'hands', 'name' => 'All India Human Rights Association', 'img' => 'icons/hr-all-india-association.png'],
            ['icon' => 'scales', 'name' => "People's Rights Protection Forum", 'img' => 'icons/hr-peoples-rights-forum.png'],
            ['icon' => 'globe', 'name' => 'Global Human Rights Foundation', 'img' => 'icons/hr-global-foundation.png'],
            ['icon' => 'dove', 'name' => 'Justice & Rights Awareness Initiative', 'img' => 'icons/hr-justice-awareness.png'],
            ['icon' => 'shieldcheck', 'name' => 'Law & Society Welfare Council', 'img' => 'icons/hr-law-society-council.png'],
            ['icon' => 'people', 'name' => 'Bharat Nyaya Suraksha Sansthan', 'img' => 'icons/hr-bharat-nyaya.png'],
        ],
        'membershipsNote' => 'Actively involved in promoting legal awareness, protecting fundamental rights and working for social justice, equality and the welfare of the underprivileged.',

        'valuesHeading' => 'Our Core Values',
        'values' => [
            ['icon' => 'shieldcheck', 'title' => 'Integrity', 'body' => 'We believe in honesty, transparency and ethical practice.'],
            // "We pursue excellence" - superlative.
            ['icon' => 'star', 'title' => 'Excellence', 'body' => 'We pursue excellence in every case we handle.'],
            ['icon' => 'handshake', 'title' => 'Dedication', 'body' => 'We are committed to our clients and their goals.'],
            ['icon' => 'scales', 'title' => 'Justice', 'body' => 'We work relentlessly to uphold rights and ensure justice.'],
            ['icon' => 'people', 'title' => 'Client Confidence', 'body' => 'Built on trust, respect and long-standing relationships.'],
            // "delivering effective and lasting outcomes" promises a result.
            ['icon' => 'target', 'title' => 'Result Oriented', 'body' => 'Focused on delivering effective and lasting outcomes.'],
        ],

        /*
        | Achievements block, carrying the Economic Times Global Legal Convention
        | photograph.
        |
        | RULE 36. Publishing achievements is the riskiest kind of content on an
        | advocate's website - it is the closest thing to advertising. What is
        | here is deliberately limited to two verifiable facts about where he has
        | spoken, with no case results, no awards and no comparison to anyone
        | else. Do not add "landmark judgments", "success stories" or anything of
        | that kind without advice.
        |
        | TODO confirm the convention year and the institutions before launch.
        */
        'achievementsHeading' => 'Recognition & Engagements',
        'achievementsNote' => 'Speaking and teaching engagements. No case results are published on this website.',
        'achievements' => [
            [
                'icon' => 'star',
                'title' => 'The Economic Times Global Legal Convention',
                'body' => 'Invited speaker at the convention, addressing practitioners on litigation and dispute resolution.',
            ],
            [
                'icon' => 'building',
                'title' => 'Guest lectures at law faculties',
                'body' => 'Lectures and sessions with students at law faculties and academic institutions on law, litigation and the evolving profession.',
            ],
        ],

        // Closing slogan. Advertising in Rule 36 terms; kept as instructed.
        'quote' => 'Your case. Our commitment. Justice is our mission.',
    ],

    /*
    | Home page tile grid, reproduced from the design the client approved on
    | 18 Aug 2026.
    |
    | READ THIS BEFORE EDITING.
    | These taglines are the client's marketing copy, not the site's own voice.
    | Four of the ten tiles - Consumer, Human Rights, Immigration and
    | Intellectual Property - have NO page behind them, because the chamber does
    | not list those areas. Their `slug` is null and they render without a link.
    | Either the advocate confirms he practises them and content is written, or
    | they should come out of this list.
    |
    | Two areas the chamber DOES practise are absent from the design and so from
    | this grid: Constitutional & Writ Litigation, and Strategic Legal Advisory.
    | Both are still reachable from /practice-areas.
    */
    'homeTiles' => [
        ['title' => 'Criminal Law', 'tagline' => 'Defending Rights. Ensuring Justice.', 'icon' => 'gavel', 'slug' => 'criminal'],
        ['title' => 'Family Law', 'tagline' => 'Protecting Families. Resolving Disputes.', 'icon' => 'family', 'slug' => 'family'],
        ['title' => 'Corporate Law', 'tagline' => 'Advising Businesses. Driving Growth.', 'icon' => 'building', 'slug' => 'corporate-commercial'],
        ['title' => 'Arbitration & Dispute Resolution', 'tagline' => 'Resolving Conflicts. Delivering Solutions.', 'icon' => 'handshake', 'slug' => 'arbitration'],
        ['title' => 'Cyber Crime Law', 'tagline' => 'Combating Cyber Threats. Protecting You.', 'icon' => 'shield', 'slug' => 'cyber-crime'],
        ['title' => 'Property Law', 'tagline' => 'Property Matters. Secured Solutions.', 'icon' => 'house', 'slug' => 'property'],
        ['title' => 'Consumer Law', 'tagline' => 'Your Rights. Our Commitment.', 'icon' => 'cart', 'slug' => null],
        ['title' => 'Human Rights Law', 'tagline' => 'Upholding Dignity. Defending Liberties.', 'icon' => 'hands', 'slug' => null],
        ['title' => 'Immigration Law', 'tagline' => 'Guiding You. Across Borders.', 'icon' => 'globe', 'slug' => null],
        ['title' => 'Intellectual Property Law', 'tagline' => 'Protecting Ideas. Securing Innovation.', 'icon' => 'copyright', 'slug' => null],
    ],

    /*
    | Courts strip under the About band, as per the same design.
    */
    'courtStrip' => [
        ['name' => 'Supreme Court of India', 'note' => 'Regular Appearances', 'icon' => 'building'],
        ['name' => 'Bombay High Court', 'note' => 'Regular Appearances', 'icon' => 'building'],
        ['name' => 'Sessions Courts', 'note' => 'Across Maharashtra', 'icon' => 'person'],
        ['name' => 'JMFC Courts', 'note' => 'Extensive Experience', 'icon' => 'gavel'],
    ],

    /*
    | Hero figures from the design.
    */
    'heroStats' => [
        // "Huge" is an unverifiable claim of scale, and it replaced "20+ Years of
        // Experience", which was a plain verifiable fact. That is the wrong way
        // round for Rule 36 - it is the same class of wording as "expert" and
        // "best outcomes" flagged elsewhere in this file. Changed on the
        // client's explicit instruction, 29 Aug 2026. experienceYears is still
        // the source for the prose that states the figure in sentences.
        ['value' => 'Huge', 'label' => 'Experience', 'sub' => '', 'icon' => 'scales'],
        ['value' => 'Across', 'label' => 'India', 'sub' => 'Appearances', 'icon' => 'building'],
        ['value' => 'Client-Centric', 'label' => 'Approach', 'sub' => 'Personalised Solutions', 'icon' => 'people'],
    ],

    /*
    | Practice areas. Adding an entry here creates its detail page and its
    | card on the index automatically — no other file needs to change.
    | `image` names a file in img/ (area-<image>.webp).
    */
    'areas' => [
        'corporate-commercial' => [
            'title' => 'Corporate & Commercial Law',
            'image' => 'corporate',
            'summary' => 'Contracts, commercial arrangements, regulatory compliance and corporate advisory, informed by two decades inside corporate legal departments.',
            'intro' => 'Corporate work in this chamber draws on years spent as Legal Head and Vice President within multinational and international companies. That vantage point matters: an agreement is read not only for its legal effect but for how it will operate commercially, where the risk actually sits, and what a business will need from it when the relationship is under strain.',
            'matters' => [
                'Drafting, vetting and negotiation of commercial agreements',
                'Shareholder agreements, joint ventures and business transfer arrangements',
                'Regulatory and statutory compliance advisory',
                'Contractual and commercial disputes',
                'Legal due diligence and written opinions',
                'Risk management and dispute-avoidance advisory for businesses',
            ],
            'process' => [
                'Understanding the commercial objective behind the transaction or dispute',
                'Review of the documents, obligations and regulatory position',
                'Advice on structure, risk allocation and the practical consequences of each option',
                'Drafting or negotiation support with the counterparty',
                'Execution, stamping and registration formalities',
                'Continuing advisory as the arrangement operates',
            ],
            'statutes' => [
                'Companies Act, 2013',
                'Indian Contract Act, 1872',
                'Insolvency and Bankruptcy Code, 2016',
                'Limited Liability Partnership Act, 2008',
                'Indian Stamp Act, 1899',
            ],
            'forums' => [
                'National Company Law Tribunal',
                'Commercial Courts',
                'High Court of Judicature at Bombay',
            ],
            'bring' => [
                'The agreement or draft in question',
                'Incorporation documents and board resolutions',
                'Correspondence recording the commercial understanding',
                'Any notice of default already issued or received',
            ],
            'faqs' => [
                [
                    'q' => 'Do you review agreements drafted by the other side?',
                    'a' => 'Yes. Vetting an agreement before signature is among the most common instructions the chamber receives, and it is considerably less expensive than the dispute that follows a defective one.',
                ],
                [
                    'q' => 'What does corporate experience add to a litigation practice?',
                    'a' => 'It changes what is asked of a matter. Having sat on the client side of legal decisions, the chamber weighs commercial consequence, timelines and cost alongside the strictly legal position.',
                ],
                [
                    'q' => 'Can a commercial dispute be resolved without going to court?',
                    'a' => 'Frequently — through negotiation, mediation, or an arbitration clause already in the contract. The available routes are set out at the first consultation.',
                ],
            ],
        ],

        'arbitration' => [
            'title' => 'Arbitration & Dispute Resolution',
            'image' => 'arbitration',
            'summary' => 'Arbitration clauses, domestic arbitrations, Section 9 and Section 34 proceedings, mediation and negotiated settlement.',
            'intro' => 'Arbitration offers a private, contractually chosen route to resolving commercial disputes, and its usefulness is often decided long before a dispute arises — in how the clause was drafted. The chamber advises on clauses, acts as counsel in arbitral proceedings, and appears in the court applications that support and follow them.',
            'matters' => [
                'Drafting and review of arbitration clauses',
                'Notice invoking arbitration and appointment of arbitrators',
                'Representation in domestic arbitral proceedings',
                'Interim measures under Section 9',
                'Challenge to awards under Section 34',
                'Enforcement of arbitral awards',
                'Mediation and negotiated settlement of commercial disputes',
            ],
            'process' => [
                'Examination of the arbitration clause and its scope',
                'Notice invoking arbitration',
                'Constitution of the tribunal',
                'Statement of claim, defence and documents',
                'Hearing and award',
                'Enforcement, or challenge where grounds exist',
            ],
            'statutes' => [
                'Arbitration and Conciliation Act, 1996',
                'Commercial Courts Act, 2015',
                'Indian Contract Act, 1872',
            ],
            'forums' => [
                'Arbitral tribunals, institutional and ad hoc',
                'Commercial Courts',
                'High Court of Judicature at Bombay — Sections 9, 11 and 34',
            ],
            'bring' => [
                'The contract containing the arbitration clause',
                'Notice invoking arbitration, if issued',
                'Correspondence on appointment of the arbitrator',
                'The award, where a challenge or enforcement is contemplated',
            ],
            'faqs' => [
                [
                    'q' => 'Our contract has an arbitration clause. Can we still go to court?',
                    'a' => 'Ordinarily the dispute must go to arbitration, though the courts retain a supporting role for interim relief and for enforcement of the award.',
                ],
                [
                    'q' => 'Is an arbitral award final?',
                    'a' => 'An award is binding and can be challenged only on the limited grounds set out in Section 34. It is not a general appeal on the merits.',
                ],
                [
                    'q' => 'Is arbitration confidential?',
                    'a' => 'Arbitral proceedings are private, which is often why commercial parties prefer them to litigation.',
                ],
            ],
        ],

        'criminal' => [
            'title' => 'Criminal Litigation',
            'image' => 'criminal',
            'summary' => 'Bail and anticipatory bail, quashing petitions, trial representation, criminal appeals and revisions.',
            'intro' => 'Criminal matters are time-sensitive, and the first days after registration of an FIR frequently determine the course of the case. The chamber appears at the investigation stage, through trial, and in appeal and revision, under the Bharatiya Nyaya Sanhita and allied legislation.',
            'matters' => [
                'Anticipatory bail and regular bail applications',
                'Quashing of FIRs and criminal proceedings',
                'Representation through investigation and trial',
                'Complaints under Section 138 of the Negotiable Instruments Act',
                'Economic offences and white-collar matters',
                'Criminal appeals, revisions and discharge applications',
            ],
            'process' => [
                'Immediate review of the FIR, remand papers and the position of the investigation',
                'Advice on custody, bail and interim protection',
                'Filing and arguing of the appropriate application',
                'Preparation for framing of charge and trial',
                'Cross-examination and final arguments',
                'Appeal or revision where required',
            ],
            'statutes' => [
                'Bharatiya Nyaya Sanhita, 2023',
                'Bharatiya Nagarik Suraksha Sanhita, 2023',
                'Bharatiya Sakshya Adhiniyam, 2023',
                'Negotiable Instruments Act, 1881 — Section 138',
                'Prevention of Money Laundering Act, 2002',
            ],
            'forums' => [
                'Metropolitan Magistrate Courts',
                'Mumbai Sessions Court and Pune Courts',
                'High Court of Judicature at Bombay',
                'Supreme Court of India',
            ],
            'bring' => [
                'Copy of the FIR or complaint',
                'Remand or bail orders, if any',
                'Summons or notice received',
                'List of witnesses and documents relied on',
            ],
            'faqs' => [
                [
                    'q' => 'An FIR has been registered against me. What should I do first?',
                    'a' => 'Obtain a copy of the FIR and take advice before making any statement. Advice on anticipatory bail is best taken immediately, as the position changes once an arrest is made.',
                ],
                [
                    'q' => 'Can this website be used to discuss the facts of my criminal case?',
                    'a' => 'No. Do not send details of a pending investigation or privileged material through the website. Use the contact details to arrange a consultation.',
                ],
                [
                    'q' => 'Will I have to appear in court personally?',
                    'a' => 'In most criminal proceedings personal appearance is required, though exemption can be applied for in certain circumstances.',
                ],
            ],
        ],

        'cyber-crime' => [
            'title' => 'Cyber Crime & Cyber Fraud',
            'image' => 'cyber',
            'summary' => 'Online fraud, financial cyber crime, data and identity misuse, and proceedings under the Information Technology Act.',
            'intro' => 'Cyber matters combine urgency with an evidentiary problem: the money moves quickly and the proof is electronic. Early action matters more here than in almost any other area, because the window for freezing funds and preserving digital records is short. The chamber advises both those defrauded and those facing allegations.',
            'matters' => [
                'Online financial fraud, UPI and banking fraud complaints',
                'Complaints to the cyber crime cell and the National Cyber Crime Reporting Portal',
                'Offences under the Information Technology Act, 2000',
                'Data theft, identity misuse and impersonation',
                'Defamation, harassment and objectionable content online',
                'Defence in cyber crime prosecutions',
                'Advisory on electronic evidence and its admissibility',
            ],
            'process' => [
                'Immediate steps to report and, where possible, freeze the transaction',
                'Preservation of electronic evidence in an admissible form',
                'Complaint to the cyber cell or the appropriate authority',
                'Follow-up with banks, intermediaries and the investigating agency',
                'Proceedings before the Magistrate or the appropriate forum',
                'Writ or appellate remedies where the matter requires them',
            ],
            'statutes' => [
                'Information Technology Act, 2000',
                'Bharatiya Nyaya Sanhita, 2023 — offences relating to cheating and forgery',
                'Bharatiya Sakshya Adhiniyam, 2023 — electronic evidence',
                'Digital Personal Data Protection Act, 2023',
                'Reserve Bank of India directions on unauthorised electronic transactions',
            ],
            'forums' => [
                'Cyber Crime Police Stations and cyber cells',
                'Metropolitan Magistrate Courts',
                'High Court of Judicature at Bombay',
            ],
            'bring' => [
                'Transaction records, bank statements and UPI or card references',
                'Screenshots, messages, emails and call records, unedited',
                'Any complaint already lodged and its acknowledgement number',
                'Device details and account particulars involved',
            ],
            'faqs' => [
                [
                    'q' => 'I have been defrauded online. What is the first thing to do?',
                    'a' => 'Report it immediately — to the bank and on the National Cyber Crime Reporting Portal — because the prospect of recovering funds falls sharply with time. Preserve every message and record without editing it.',
                ],
                [
                    'q' => 'Are screenshots accepted as evidence?',
                    'a' => 'Electronic records are admissible, but they must be produced in the form the law requires. How the material is preserved at the outset often decides whether it can be used later.',
                ],
                [
                    'q' => 'Can something posted about me online be taken down?',
                    'a' => 'Depending on the content, remedies exist against the person responsible and against the intermediary hosting it. The appropriate route depends on the material and the platform.',
                ],
            ],
        ],

        'family' => [
            'title' => 'Family & Matrimonial Law',
            'image' => 'family',
            'summary' => 'Matrimonial proceedings, maintenance, custody and guardianship, domestic violence matters and succession.',
            'intro' => 'Family matters are conducted with discretion and, wherever the parties permit, with an attempt at settlement before contest. The chamber appears before the Family Court and in related proceedings under personal and secular law.',
            'matters' => [
                'Divorce by mutual consent and contested petitions',
                'Maintenance and interim maintenance',
                'Child custody, visitation and guardianship',
                'Domestic violence proceedings',
                'Restitution of conjugal rights and judicial separation',
                'Succession certificates, wills and probate',
            ],
            'process' => [
                'Confidential consultation on the circumstances and objectives',
                'Advice on jurisdiction, applicable personal law and available relief',
                'Attempt at settlement or mediation where appropriate',
                'Filing of the petition and interim applications',
                'Evidence and hearing',
                'Decree, and enforcement or appeal as required',
            ],
            'statutes' => [
                'Hindu Marriage Act, 1955',
                'Special Marriage Act, 1954',
                'Muslim personal law and allied statutes',
                'Protection of Women from Domestic Violence Act, 2005',
                'Guardians and Wards Act, 1890',
                'Indian Succession Act, 1925',
            ],
            'forums' => [
                'Family Court, Mumbai and Pune',
                'Metropolitan Magistrate Courts',
                'High Court of Judicature at Bombay',
            ],
            'bring' => [
                'Marriage certificate and proof of solemnisation',
                'Birth certificates of children, where custody is involved',
                'Income and asset particulars of both parties',
                'Any earlier orders, notices or complaints',
            ],
            'faqs' => [
                [
                    'q' => 'Is a mutual consent divorce faster than a contested one?',
                    'a' => 'Generally yes, as it avoids a trial, but statutory waiting periods still apply and the court must be satisfied that consent is free.',
                ],
                [
                    'q' => 'Are family court proceedings public?',
                    'a' => 'Family Court proceedings are conducted in camera, and the chamber treats all information shared as confidential.',
                ],
                [
                    'q' => 'Can maintenance be claimed while the case is pending?',
                    'a' => 'Interim maintenance can be applied for during the proceedings. Entitlement and quantum depend on the facts.',
                ],
            ],
        ],

        'constitutional-writ' => [
            'title' => 'Constitutional & Writ Litigation',
            'image' => 'constitutional',
            'summary' => 'Writ petitions before the High Court and the Supreme Court against the State, public authorities and statutory bodies.',
            'intro' => 'Writ jurisdiction is the remedy where a public authority has acted without power, unfairly, or in breach of a fundamental right. It is a discretionary jurisdiction and a demanding one: the petition must show not merely that a decision was wrong, but that it was one the authority was not entitled to make in that manner.',
            'matters' => [
                'Writ petitions under Article 226 before the High Court',
                'Petitions under Article 32 before the Supreme Court',
                'Challenges to administrative and regulatory action',
                'Service and employment matters against public employers',
                'Licensing, permissions and statutory approvals',
                'Special Leave Petitions before the Supreme Court',
            ],
            'process' => [
                'Examination of the impugned order and the authority acting',
                'Assessment of alternative remedies, delay and maintainability',
                'Representation to the authority where that route remains open',
                'Drafting and filing of the petition with supporting affidavit',
                'Admission, interim relief and affidavits in reply',
                'Final hearing, and appeal where the outcome requires it',
            ],
            'statutes' => [
                'Constitution of India — Articles 14, 19, 21, 226 and 32',
                'Administrative Tribunals Act, 1985',
                'Applicable service rules and regulations',
                'Right to Information Act, 2005',
            ],
            'forums' => [
                'High Court of Judicature at Bombay',
                'Supreme Court of India',
                'Administrative and statutory tribunals',
            ],
            'bring' => [
                'The order or decision being challenged',
                'Every representation made and reply received',
                'Rules, circulars or notifications relied on by the authority',
                'Proof of the date of communication of the order',
            ],
            'faqs' => [
                [
                    'q' => 'When is a writ petition the right remedy?',
                    'a' => 'Broadly, where a public authority has acted beyond its powers, unfairly, or in breach of a fundamental right, and where no adequate alternative remedy is available.',
                ],
                [
                    'q' => 'Does delay matter?',
                    'a' => 'Considerably. Writ jurisdiction is discretionary and unexplained delay is a common reason for a petition being declined.',
                ],
                [
                    'q' => 'Can a private dispute be brought by writ petition?',
                    'a' => 'Ordinarily not. The jurisdiction runs against the State and public authorities, though bodies performing public functions may fall within it.',
                ],
            ],
        ],

        'property' => [
            'title' => 'Property & Commercial Disputes',
            'image' => 'civil',
            'summary' => 'Title and possession disputes, recovery suits, injunctions, specific performance, landlord and tenant matters.',
            'intro' => 'Property disputes turn on documents, and most of them are decided long before the hearing — by what the record shows about title, possession and the conduct of the parties. The chamber advises at the pre-litigation stage as often as it appears in court, because a well-drafted notice frequently resolves a dispute without a suit.',
            'matters' => [
                'Suits for possession, partition and declaration of title',
                'Recovery of money and enforcement of contractual obligations',
                'Specific performance of agreements to sell',
                'Temporary and permanent injunctions',
                'Landlord and tenant disputes',
                'Redevelopment and society disputes',
                'Execution proceedings and appeals',
            ],
            'process' => [
                'Consultation and review of the documents on record',
                'Opinion on the cause of action, limitation and appropriate forum',
                'Statutory or legal notice where the matter permits settlement',
                'Institution of the suit and interim applications',
                'Evidence, arguments and judgment',
                'Appeal or execution as the outcome requires',
            ],
            'statutes' => [
                'Code of Civil Procedure, 1908',
                'Specific Relief Act, 1963',
                'Transfer of Property Act, 1882',
                'Maharashtra Rent Control Act, 1999',
                'Limitation Act, 1963',
            ],
            'forums' => [
                'Civil Judge (Junior and Senior Division)',
                'City Civil Court, Mumbai',
                'High Court of Judicature at Bombay, original and appellate side',
            ],
            'bring' => [
                'Title deeds or the agreement in dispute',
                'Correspondence between the parties',
                'Notices issued or received',
                'Any earlier orders or decrees',
            ],
            'faqs' => [
                [
                    'q' => 'How long does a property suit usually take?',
                    'a' => 'It depends on the forum, the complexity of the evidence and the conduct of the parties. No reliable estimate can be given without seeing the papers, and a figure quoted without them would be misleading.',
                ],
                [
                    'q' => 'What is examined when an agreement to sell is vetted?',
                    'a' => 'Title and its chain, encumbrances, possession, and the default clauses read from both sides. It is a documentary exercise carried out before signature.',
                ],
                [
                    'q' => 'Is limitation a concern in my matter?',
                    'a' => 'Limitation periods differ by cause of action and are strictly applied. It is examined at the first consultation, because delay can extinguish an otherwise sound claim.',
                ],
            ],
        ],

        'strategic-advisory' => [
            'title' => 'Strategic Legal Advisory',
            'image' => 'advisory',
            'summary' => 'Retained advisory for businesses and professionals — risk assessment, dispute strategy and second opinions.',
            'intro' => "Not every instruction is a case. A good deal of the chamber's work is advisory: assessing exposure before a decision is taken, choosing between forums when a dispute is unavoidable, or giving a second opinion on a matter already running elsewhere. This is where the years spent inside corporate legal departments are most directly useful.",
            'matters' => [
                'Legal risk assessment before a commercial decision',
                'Dispute strategy — forum, sequencing and cost',
                'Second opinions on matters already in progress',
                'Retained advisory for businesses and professionals',
                'Regulatory and compliance advisory',
                'Internal policy and contract framework review',
            ],
            'process' => [
                'Understanding the commercial context and what the client actually needs decided',
                'Review of the documents and the position under the law',
                'A written opinion setting out options with their consequences',
                'Discussion of the practical and cost implications of each',
                'Implementation support where instructed',
            ],
            'statutes' => ['Advice is given under the statutes governing the matter at hand'],
            'forums' => ['Advisory work; no forum unless the matter proceeds to litigation'],
            'bring' => [
                'The documents relevant to the decision',
                'Any opinion already obtained',
                'The commercial timeline you are working to',
            ],
            'faqs' => [
                [
                    'q' => 'Can a second opinion be given on a matter another advocate is conducting?',
                    'a' => 'Yes, and it is a common instruction. Professional courtesy requires that the position be disclosed, and the chamber will not interfere with an existing engagement.',
                ],
                [
                    'q' => 'Do you accept retained advisory arrangements?',
                    'a' => 'Yes, for businesses and professionals who need legal input available as decisions are taken rather than only when disputes arise.',
                ],
                [
                    'q' => 'Is advisory work charged differently from litigation?',
                    'a' => 'Fees are discussed and recorded in writing before any engagement. They are not published on this website, as the rules do not permit it.',
                ],
            ],
        ],
    ],

    /*
    | Home page FAQ. General questions about how the chamber works - no fees,
    | no outcomes, nothing that reads as solicitation (Rule 36). Also emitted
    | as FAQPage structured data. Have the advocate confirm the answers.
    */
    'homeFaqs' => [
        [
            'q' => 'Where does the chamber practise?',
            'a' => 'The chamber appears before the High Court of Judicature at Bombay, the Supreme Court of India, the Mumbai Sessions Court and the Pune Courts, and in matters before other High Courts, tribunals and forums. The office is at Express Towers, Nariman Point, Mumbai.',
        ],
        [
            'q' => 'Which areas of law does the chamber handle?',
            'a' => 'Corporate and commercial law, arbitration and dispute resolution, criminal litigation, cyber crime, family and matrimonial law, constitutional and writ matters, and property and commercial disputes, along with strategic legal advisory. Each is described on the Practice Areas page.',
        ],
        [
            'q' => 'How do I request a consultation?',
            'a' => 'Use the Request Appointment page to choose a time slot, or the Contact page to send an enquiry. The chamber confirms the slot or proposes an alternative. A request is not a confirmed booking until it is confirmed by the chamber.',
        ],
        [
            'q' => 'What should I bring to the first consultation?',
            'a' => 'The documents relevant to the matter: notices, orders, agreements, FIR copies or correspondence, as applicable. Each practice area page lists what is usually useful for that kind of matter.',
        ],
        [
            'q' => 'Can I send documents through the website?',
            'a' => 'Yes, through the Upload Document page, which delivers the file to the chamber by email. Do not send details of a pending investigation or privileged material through the website; arrange a consultation instead.',
        ],
        [
            'q' => 'Does using this website create a lawyer-client relationship?',
            'a' => 'No. The information on this website is general and is not legal advice. A lawyer-client relationship arises only when the chamber agrees to act in a matter. See the Disclaimer for the full terms.',
        ],
    ],

    /*
    | Client feedback. Only publish an entry where written consent has been
    | recorded. Set `attribution` to null to display anonymously.
    | TODO replace with genuine, consented feedback before launch.
    */
    'testimonials' => [
        [
            'quote' => 'Every step was explained before it was taken, and the papers were always ready well before the date.',
            'attribution' => null,
            'matter' => 'Commercial dispute, 2024',
        ],
        [
            'quote' => 'Reachable when it mattered and straightforward about what the law did and did not allow.',
            'attribution' => null,
            'matter' => 'Corporate advisory, 2023',
        ],
        [
            'quote' => 'The matter was handled with discretion from beginning to end.',
            'attribution' => null,
            'matter' => 'Family matter, 2024',
        ],
    ],

    /*
    | Media: articles and coverage published ABOUT or BY the advocate elsewhere.
    | Add one entry per item; they are sorted by date on output. Shown on /media
    | and (latest three) on the home page.
    |
    |   'title'  => headline as published
    |   'source' => publication / channel name
    |   'date'   => 'YYYY-MM-DD'
    |   'url'    => full external link (opens in a new tab)
    |   'type'   => 'Article' | 'Interview' | 'Video' ... (optional)
    |   'image'  => optional thumbnail path, e.g. '/img/media/x.webp'
    |
    | RULE 36: list only what was published; no comparisons, rankings or results.
    */
    // Brand colour + initials for each source's badge. Unlisted sources fall back to gold.
    'mediaSources' => [
        'Mid-Day' => ['mark' => 'MD', 'color' => '#C8102E'],
        'Global Cover Story' => ['mark' => 'GC', 'color' => '#1F6F8B'],
        'Business News This Week' => ['mark' => 'BN', 'color' => '#2E7D4F'],
        'Business Upturn' => ['mark' => 'BU', 'color' => '#C2410C'],
        'The Eastern Herald' => ['mark' => 'EH', 'color' => '#6D3FA0'],
    ],
    'media' => [
        [
            'topic' => 'Criminal Law',
            'title' => 'Illegal Arrest in India: Advocate Javed Pashu Sayyed Explains Constitutional Rights and BNSS Safeguards',
            'source' => 'Mid-Day',
            'excerpt' => 'Understand illegal arrest safeguards in India, including constitutional rights, BNSS provisions and legal remedies.',
            'image' => '/img/media/mid-day-illegal-arrest.webp',
            'date' => '2026-09-30',
            'url' => 'https://www.mid-day.com/buzzfeed/article/illegal-arrest-in-india-advocate-javed-pashu-sayyed-explains-constitutional-rights-and-bnss-safeguards-11043',
            'type' => 'Article',
        ],
        [
            'topic' => 'Profile',
            'title' => 'Adv. Javed Pashu Sayyed: From Corporate Boardrooms to the Courtroom',
            'source' => 'Global Cover Story',
            'excerpt' => 'A Mumbai-based advocate practising since 2009 across criminal law, arbitration and commercial disputes, on his journey from corporate roles to the Bombay High Court.',
            'image' => '/img/media/global-cover-story-profile.webp',
            'date' => '2026-09-30',
            'url' => 'https://globalcoverstory.com/articles/javed-pashu-sayyed-advocate-corporate-to-courtroom',
            'type' => 'Profile',
        ],
        [
            'title' => 'Cyber Crime in India 2026: What Victims Should Do After Online Fraud and What Happens When a Bank Account Is Frozen',
            'topic' => 'Cyber Crime',
            'source' => 'Business News This Week',
            'excerpt' => 'From digital arrest and fake investment platforms to UPI fraud, mule accounts and bank-account freezes, the first legal steps a victim can take.',
            'image' => '/img/media/business-news-this-week-cyber-crime.webp',
            'date' => '2026-10-06',
            'url' => 'https://businessnewsthisweek.com/news/cyber-crime-in-india-2026-what-victims-should-do-after-online-fraud-and-what-happens-when-a-bank-account-is-frozen/',
            'type' => 'Article',
        ],
        [
            'title' => 'Cyber Crime in India 2026: What Victims Should Do After Online Fraud and What Happens When a Bank Account Is Frozen',
            'topic' => 'Cyber Crime',
            'source' => 'Business Upturn',
            'excerpt' => 'From digital arrest and fake investment platforms to UPI fraud, mule accounts and bank-account freezes, the first legal steps a victim can take.',
            'image' => '/img/media/business-upturn-cyber-crime.webp',
            'date' => '2026-10-06',
            'url' => 'https://businessupturn.com/brand-post/cyber-crime-in-india-2026-what-victims-should-do-after-online-fraud-and-what-happens-when-a-bank-account-is-frozen/',
            'type' => 'Article',
        ],
        [
            'title' => 'Cyber Crime in India 2026: What Victims Should Do After Online Fraud and What Happens When a Bank Account Is Frozen',
            'topic' => 'Cyber Crime',
            'source' => 'The Eastern Herald',
            'excerpt' => 'From digital arrest and fake investment platforms to UPI fraud, mule accounts and bank-account freezes, the first legal steps a victim can take.',
            'image' => '/img/media/eastern-herald-cyber-crime.webp',
            'date' => '2026-10-06',
            'url' => 'https://easternherald.com/2026/10/06/cyber-crime-in-india-2026-what-victims-should-do-after-online-fraud-and-what-happens-when-a-bank-account-is-frozen/',
            'type' => 'Article',
        ],
    ],

    /*
    | Articles. No CMS in this scope — add an entry to publish.
    */
    'articles' => [
        'what-to-do-when-an-fir-is-registered' => [
            'title' => 'What to do in the first 48 hours after an FIR is registered',
            // Shorter form for the <title> tag only — the page heading uses `title`.
            'seoTitle' => 'The first 48 hours after an FIR',
            'category' => 'Criminal',
            'date' => '2026-06-18',
            'excerpt' => 'The steps available immediately after registration, and why the choice between anticipatory and regular bail is often decided by timing.',
            'body' => [
                'Registration of a First Information Report sets a statutory process in motion, and the options open to a person named in it narrow quickly. The purpose of this note is to describe the sequence, not to advise on any particular case.',
                'The first step is to obtain a certified copy of the FIR. It is a public document in most cases and can be applied for at the police station or downloaded from the state police portal. Until the allegations are read in their exact terms, no useful assessment is possible.',
                'Where arrest is apprehended, an application for anticipatory bail may be made to the Sessions Court or the High Court. Once an arrest has taken place, the remedy shifts to a regular bail application, and the considerations before the court change. This is the reason timing matters more here than in most areas of practice.',
                'A person called for questioning is entitled to legal advice before making a statement. Statements made without it frequently become the most difficult part of the record to explain later.',
                'This note is general information and is not legal advice. The appropriate course in any matter depends on its own facts and requires independent advice.',
            ],
        ],
        'first-hours-of-an-online-fraud' => [
            'title' => 'The first hours of an online fraud, and why they decide the outcome',
            'seoTitle' => 'The first hours of an online fraud',
            'category' => 'Cyber Crime',
            'date' => '2026-05-09',
            'excerpt' => 'Reporting, freezing and preserving evidence — the three things that determine whether money is recovered and whether the record survives.',
            'body' => [
                'In online financial fraud the money moves through several accounts within minutes, and the prospect of recovery falls with every hour that passes. Three things matter at the outset, and all three are time-sensitive.',
                'The first is reporting. A complaint on the National Cyber Crime Reporting Portal and a simultaneous report to the bank start the process by which a transaction can be flagged and, in some cases, held before it is withdrawn further down the chain.',
                'The second is preservation. Screenshots, messages, call records and transaction references should be kept exactly as they are, without cropping, editing or forwarding in a way that strips their metadata. Electronic records are admissible, but they must be produced in the form the law requires, and material handled carelessly at the start is difficult to rehabilitate later.',
                'The third is the account of events. Write down the sequence while it is fresh — what was received, what was clicked, what was authorised and when. Investigating agencies work from this account, and gaps in it become gaps in the case.',
                "Reserve Bank directions on unauthorised electronic transactions also place obligations on banks, and the customer's position under them depends partly on how promptly the transaction was reported.",
                'This note is general information and is not legal advice.',
            ],
        ],
        'reading-an-arbitration-clause' => [
            'title' => 'Reading an arbitration clause before you sign it',
            'seoTitle' => 'Reading an arbitration clause',
            'category' => 'Arbitration',
            'date' => '2026-04-02',
            'excerpt' => 'Seat, venue, number of arbitrators and scope — four terms that decide how a future dispute will actually be conducted.',
            'body' => [
                'An arbitration clause is usually the last clause negotiated and the first one that matters when a commercial relationship breaks down. Four terms in it carry most of the consequence.',
                'The seat of arbitration determines which court supervises the proceedings. It is distinct from the venue, which is merely where hearings are held. Clauses that confuse the two produce preliminary litigation before the dispute itself is heard.',
                'The number of arbitrators affects both cost and speed. A three-member tribunal for a modest claim can cost more than the claim is worth.',
                'The scope of the clause decides what can be referred. A clause covering disputes "arising out of" the contract is read differently from one covering disputes "in connection with" it.',
                'Finally, the appointment mechanism should be capable of operating when one party is uncooperative, since that is precisely when it will be needed.',
                'This note is general information and is not legal advice.',
            ],
        ],
    ],
];
