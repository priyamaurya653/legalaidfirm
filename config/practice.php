<?php

/*
|--------------------------------------------------------------------------
| Practice Areas – static data
|--------------------------------------------------------------------------
| Header, footer, home page aur sabhi practice pages yahi se data lete hain.
| Ye file  config/practice.php  me honi chahiye (views folder me NAHI).
| 'group' => 'litigation' (court cases) ya 'services' (documentation work)
*/

return [

    'criminal' => [
        'title' => 'Criminal Cases',
        'group' => 'litigation',
        'icon'  => 'fa-solid fa-gavel',
        'image' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=900&q=80',
        'short' => 'Bail, FIR quashing, trial defence and criminal appeals before every court.',
        'intro' => 'A criminal charge can change your life overnight. Our criminal defence team acts fast — from the first FIR to the final appeal — protecting your liberty, reputation and rights at every step.',
        'services' => [
            'Anticipatory & regular bail applications',
            'FIR quashing petitions before the High Court',
            'White-collar crime & economic offences',
            'Cyber crime & IT Act matters',
            'Cheque bounce cases (Section 138 NI Act)',
            'Trial defence & cross-examination',
            'Criminal appeals & revisions',
        ],
        'process' => [
            ['Case Review', 'We study the FIR, charge sheet and evidence within 24 hours.'],
            ['Immediate Relief', 'Bail or protection from arrest is filed on priority.'],
            ['Defence Strategy', 'Witnesses, evidence and legal arguments are prepared in detail.'],
            ['Trial & Appeal', 'We represent you till the final judgment and beyond.'],
        ],
    ],

    'civil-law' => [
        'title' => 'Civil Cases',
        'group' => 'litigation',
        'icon'  => 'fa-solid fa-scale-balanced',
        'image' => 'https://images.unsplash.com/photo-1505664194779-8beaceb93744?w=900&q=80',
        'short' => 'Property, recovery, contract and injunction disputes handled end-to-end.',
        'intro' => 'Civil disputes need patience, documentation and sharp strategy. We help individuals and businesses recover what is theirs and protect what they own.',
        'services' => [
            'Property & land title disputes',
            'Partition and possession suits',
            'Recovery of money suits',
            'Injunctions & stay orders',
            'Landlord–tenant disputes',
            'Consumer protection cases',
            'Execution of decrees',
        ],
        'process' => [
            ['Document Audit', 'Title deeds, agreements and records are verified.'],
            ['Legal Notice', 'A strong notice often settles the matter without court.'],
            ['Filing & Interim Relief', 'Suit is filed with urgent stay / injunction if needed.'],
            ['Resolution', 'Settlement, mediation or full trial — whichever serves you best.'],
        ],
    ],

    'corporate-law' => [
        'title' => 'Corporate Cases',
        'group' => 'litigation',
        'icon'  => 'fa-solid fa-building',
        'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=900&q=80',
        'short' => 'Company formation, contracts, compliance, M&A and commercial disputes.',
        'intro' => 'From start-ups to established enterprises, we are the legal partner businesses trust to grow safely — structuring deals, drafting contracts and keeping you compliant.',
        'services' => [
            'Company, LLP & partnership registration',
            'Shareholder & founder agreements',
            'Commercial contract drafting & review',
            'Mergers, acquisitions & due diligence',
            'ROC & regulatory compliance',
            'Trademark & intellectual property',
            'Arbitration & commercial litigation',
        ],
        'process' => [
            ['Business Understanding', 'We learn your model, risks and goals first.'],
            ['Structuring', 'The right entity, contracts and governance are set up.'],
            ['Compliance Calendar', 'Every filing and renewal is tracked for you.'],
            ['Dispute Shield', 'If a dispute arises, we defend you swiftly.'],
        ],
    ],

    'family-law' => [
        'title' => 'Family Cases',
        'group' => 'litigation',
        'icon'  => 'fa-solid fa-people-roof',
        'image' => 'https://images.unsplash.com/photo-1511895426328-dc8714191300?w=900&q=80',
        'short' => 'Confidential support in divorce, custody, maintenance and succession.',
        'intro' => 'Family matters are personal. We handle them with complete confidentiality, empathy and firm legal guidance — always keeping your peace of mind and your children first.',
        'services' => [
            'Mutual & contested divorce',
            'Child custody & visitation rights',
            'Maintenance & alimony',
            'Domestic violence protection',
            'Marriage registration',
            'Succession & inheritance disputes',
            'Adoption & guardianship',
        ],
        'process' => [
            ['Private Consultation', 'A confidential, judgment-free first meeting.'],
            ['Options Explained', 'Mediation, mutual consent or litigation — clearly compared.'],
            ['Representation', 'Family court, mediation centre or High Court.'],
            ['Closure', 'Final orders that protect your future.'],
        ],
    ],

    'tax-law' => [
        'title' => 'Tax Cases',
        'group' => 'litigation',
        'icon'  => 'fa-solid fa-file-invoice-dollar',
        'image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=900&q=80',
        'short' => 'Income tax, GST notices, assessments, appeals and tax planning.',
        'intro' => 'Tax notices and assessments can freeze accounts and businesses. Our tax lawyers respond precisely, argue effectively and plan ahead so you stay protected.',
        'services' => [
            'Income tax notices & scrutiny',
            'GST registration, notices & appeals',
            'Search, seizure & survey matters',
            'Appeals before CIT(A) & ITAT',
            'Tax planning for individuals & businesses',
            'International taxation & DTAA',
            'Prosecution defence under tax laws',
        ],
        'process' => [
            ['Notice Analysis', 'We decode the notice and the exact exposure.'],
            ['Reply & Documentation', 'A well-documented, timely response is filed.'],
            ['Hearing & Appeal', 'Representation before officers and tribunals.'],
            ['Future Planning', 'Structuring to avoid the same issue again.'],
        ],
    ],

    'challan' => [
        'title' => 'Challan',
        'group' => 'services',
        'icon'  => 'fa-solid fa-car-burst',
        'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=900&q=80',
        'short' => 'Traffic challan disposal, virtual court and vehicle release matters.',
        'intro' => 'Pending traffic challans can lead to vehicle seizure and licence trouble. We help you settle challans quickly through virtual courts, Lok Adalat and the traffic court — without wasting your day.',
        'services' => [
            'E-challan & court challan disposal',
            'Virtual court representation',
            'Lok Adalat settlement of challans',
            'Vehicle release (superdari) from police',
            'Driving licence suspension matters',
            'Drunken driving (Section 185 MV Act) cases',
        ],
        'process' => [
            ['Share Challan', 'Send us the challan number or a photo.'],
            ['Status Check', 'We verify court, amount and hearing date.'],
            ['Representation', 'Our advocate appears or files online for you.'],
            ['Disposal', 'Challan is closed and receipt shared with you.'],
        ],
    ],

    'police-verification' => [
        'title' => 'Police Verification',
        'group' => 'services',
        'icon'  => 'fa-solid fa-user-shield',
        'image' => 'https://images.unsplash.com/photo-1589994965851-a8f479c573a9?w=900&q=80',
        'short' => 'Tenant, employee, passport and character verification support.',
        'intro' => 'Police verification is mandatory for tenants, domestic help and many jobs. We prepare the documents, file the application and follow up until your verification certificate is issued.',
        'services' => [
            'Tenant verification for landlords',
            'Domestic help & driver verification',
            'Employee background verification',
            'Police clearance certificate (PCC)',
            'Character certificate assistance',
            'Follow-up with the local police station',
        ],
        'process' => [
            ['Documents', 'We list and collect the exact documents needed.'],
            ['Application', 'Form is filed online or at the police station.'],
            ['Follow-up', 'We track the request with the concerned officer.'],
            ['Certificate', 'Verified report or certificate is handed over.'],
        ],
    ],

    'rent-sale-agreement' => [
        'title' => 'Rent & Sale Agreement',
        'group' => 'services',
        'icon'  => 'fa-solid fa-house-circle-check',
        'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=900&q=80',
        'short' => 'Rent agreements, sale deeds and property agreements drafted & registered.',
        'intro' => 'A badly drafted agreement causes disputes later. We draft clear, legally sound rent and sale agreements, arrange stamp duty and help with registration at the Sub-Registrar office.',
        'services' => [
            'Residential & commercial rent agreements',
            'Leave & licence agreements',
            'Agreement to sell & sale deeds',
            'Stamp duty calculation & e-stamp',
            'Registration at Sub-Registrar office',
            'Property title verification',
        ],
        'process' => [
            ['Details', 'Share party and property details with us.'],
            ['Drafting', 'Agreement is drafted with protective clauses.'],
            ['Stamp & Sign', 'E-stamp paper is arranged and signatures done.'],
            ['Registration', 'Registered / notarised copy is delivered.'],
        ],
    ],

    'will-gpa-spa' => [
        'title' => 'WILL / GPA / SPA',
        'group' => 'services',
        'icon'  => 'fa-solid fa-file-signature',
        'image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=900&q=80',
        'short' => 'Will drafting, General & Special Power of Attorney and registration.',
        'intro' => 'A properly made Will or Power of Attorney protects your family and your property. We draft it in clear language, ensure correct witnesses and get it registered so it stands in court.',
        'services' => [
            'Will drafting & registration',
            'Codicil & Will modification',
            'General Power of Attorney (GPA)',
            'Special Power of Attorney (SPA)',
            'NRI Power of Attorney & attestation',
            'Revocation of Power of Attorney',
        ],
        'process' => [
            ['Consultation', 'We understand your assets and wishes.'],
            ['Drafting', 'Clear, dispute-proof document is prepared.'],
            ['Execution', 'Signing with proper witnesses is arranged.'],
            ['Registration', 'Document is registered and safely handed over.'],
        ],
    ],

    'affidavit-notary' => [
        'title' => 'Affidavit & Notary',
        'group' => 'services',
        'icon'  => 'fa-solid fa-stamp',
        'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=900&q=80',
        'short' => 'All types of affidavits, notarisation and document attestation.',
        'intro' => 'Need an affidavit for a name change, address proof, gap year or lost documents? We draft and notarise it the same day with the correct format and stamp paper.',
        'services' => [
            'Name change affidavit & gazette',
            'Address & income affidavits',
            'Gap year & education affidavits',
            'Lost document affidavit',
            'Notarisation & attestation',
            'Apostille & embassy attestation support',
        ],
        'process' => [
            ['Requirement', 'Tell us the purpose of the affidavit.'],
            ['Drafting', 'Correct format is drafted on stamp paper.'],
            ['Notarise', 'Signed and notarised by a notary public.'],
            ['Delivery', 'Ready the same day — pickup or courier.'],
        ],
    ],

    'legal-notice' => [
        'title' => 'Legal Notice',
        'group' => 'services',
        'icon'  => 'fa-solid fa-envelope-open-text',
        'image' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=900&q=80',
        'short' => 'Drafting, sending and replying to legal notices on advocate letterhead.',
        'intro' => 'A strong legal notice on an advocate’s letterhead often resolves disputes without going to court. We draft, send and reply to notices for recovery, property, employment and consumer matters.',
        'services' => [
            'Money recovery notice',
            'Cheque bounce notice (Section 138)',
            'Property & tenant eviction notice',
            'Employment & salary dues notice',
            'Consumer complaint notice',
            'Reply to legal notice received',
        ],
        'process' => [
            ['Facts', 'Share the dispute details and documents.'],
            ['Drafting', 'Notice is drafted on advocate letterhead.'],
            ['Dispatch', 'Sent by registered post / email with proof.'],
            ['Next Step', 'We guide you on reply or court action.'],
        ],
    ],

    'registration' => [
        'title' => 'Registration Services',
        'group' => 'services',
        'icon'  => 'fa-solid fa-id-card',
        'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=900&q=80',
        'short' => 'Marriage, company, GST, trademark, FSSAI and MSME registrations.',
        'intro' => 'Registrations are the foundation of a safe personal and business life. We complete them end-to-end — documents, online filing and follow-up — so you can focus on what matters.',
        'services' => [
            'Court & Arya Samaj marriage registration',
            'Company / LLP / partnership registration',
            'GST & MSME (Udyam) registration',
            'Trademark registration & objections',
            'FSSAI food licence',
            'Society & trust registration',
        ],
        'process' => [
            ['Choose Service', 'We suggest the right registration for you.'],
            ['Documents', 'A simple checklist is shared and collected.'],
            ['Filing', 'Application is filed with the authority.'],
            ['Certificate', 'Registration certificate is delivered.'],
        ],
    ],
];