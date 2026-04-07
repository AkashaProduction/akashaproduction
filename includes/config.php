<?php

return [
    'site' => [
        'name' => 'Akasha Production',
        'tagline' => 'Concept, création web et écosystème de projets',
        'responsable' => 'Robin Prevent',
        'address_lines' => [
            '50 avenue Frédéric Mistral',
            '83170 Brignoles, France',
        ],
        'phone' => '078148871',
        'contact_email' => 'contact@akashaproduction.com',
        'contact_email_label' => 'nous contacter',
        'facebook_url' => 'https://www.facebook.com/akashaproduction',
        'host' => [
            'name' => 'o2switch',
            'address_lines' => [
                'Chemin des Pardiaux',
                '63000 Clermont-Ferrand, France',
            ],
            'legal_lines' => [
                'SAS au capital de 100 000 €',
                'RCS Clermont-Ferrand 510 909 807',
            ],
            'phone' => '04 44 44 60 40',
            'website' => 'https://www.o2switch.fr',
        ],
    ],
    'navigation' => [
        'presentation' => ['label' => 'Présentation', 'href' => '/'],
        'solutions' => ['label' => 'Nos solutions', 'href' => '/nos-solutions'],
        'contact' => ['label' => 'Contact', 'href' => '/contact'],
        'commander' => ['label' => 'Commander', 'href' => '/commander'],
        'account' => ['label' => 'Mon compte', 'href' => '/mon-compte'],
    ],
    'projects' => [
        [
            'title' => 'CMS Source',
            'url' => 'https://www.cms-source.org',
            'description' => 'Plateforme éditoriale et technique orientée publication, structure et déploiement de contenus.',
        ],
        [
            'title' => 'Mafiaz World',
            'url' => 'https://www.mafiaz.world',
            'description' => 'Univers de marque avec identité forte, narration visuelle et présence web différenciante.',
        ],
        [
            'title' => 'Permathèque',
            'url' => 'http://www.permatheque.fr',
            'description' => 'Projet de transmission de ressources autour de la permaculture, du vivant et de l’autonomie.',
        ],
        [
            'title' => 'Vivre en Autonomie',
            'url' => 'https://www.vivre-en-autonomie.fr',
            'description' => 'Site thématique pensé pour des contenus structurés, lisibles et durables.',
        ],
        [
            'title' => 'Conseil Ayurveda',
            'url' => 'https://www.conseil-ayurveda.fr',
            'description' => 'Présence professionnelle dédiée au conseil, à la prise de contact et à la valorisation d’expertise.',
        ],
        [
            'title' => 'On Apprend Tous Les Jours',
            'url' => 'https://www.onapprendtouslesjours.fr',
            'description' => 'Projet éditorial conçu pour organiser le savoir, publier et diffuser efficacement.',
        ],
        [
            'title' => 'Eau Dynamisée',
            'url' => 'https://www.eau-dynamisee.com',
            'description' => 'Site orienté pédagogie produit, crédibilité commerciale et clarté d’offre.',
        ],
        [
            'title' => 'Atlas Access Immo',
            'url' => 'https://www.atlas-access.immo',
            'description' => 'Vitrine immobilière professionnelle, structurée pour la lisibilité, la confiance et la conversion.',
        ],
    ],
    'catalog' => [
        'creation' => [
            'showcase' => [
                'label' => 'Site vitrine multilingue 3 pages',
                'amount' => 50,
                'headline' => 'Création vitrine',
                'features' => ['3 pages structurées', 'Version multilingue', 'Design responsive premium'],
            ],
            'complex' => [
                'label' => 'Site complexe 9 pages + 3 modules + base de données',
                'amount' => 500,
                'headline' => 'Création complexe',
                'features' => ['9 pages éditoriales', 'Commerce, newsletter, blog', '1 base de données'],
            ],
            'custom' => [
                'label' => 'Création personnalisée sur devis',
                'amount' => 0,
                'headline' => 'Création personnalisée',
                'features' => ['Étude détaillée', 'Architecture spécifique', 'Proposition sur mesure'],
            ],
        ],
        'hosting' => [
            'shared-monthly' => [
                'label' => 'Serveur mutualisé mensuel',
                'amount' => 8,
                'headline' => 'Mutualisé',
                'suffix' => '/ mois',
            ],
            'shared-yearly' => [
                'label' => 'Serveur mutualisé annuel',
                'amount' => 88,
                'headline' => 'Mutualisé annuel',
                'suffix' => '/ an',
            ],
            'vps' => [
                'label' => 'VPS dédié',
                'amount' => 200,
                'headline' => 'VPS dédié',
                'suffix' => '/ mois',
            ],
            'cloud' => [
                'label' => 'Cloud personnalisé sur devis',
                'amount' => 0,
                'headline' => 'Cloud',
                'suffix' => '',
            ],
        ],
        'domain' => [
            'subdomain' => [
                'label' => 'Sous-domaine offert',
                'amount' => 0,
            ],
            'custom-domain' => [
                'label' => 'Nom de domaine personnalisé',
                'amount' => 18,
            ],
        ],
        'pack_prices' => [
            'showcase:shared-yearly' => 120,
            'complex:shared-yearly' => 550,
            'showcase:vps' => 230,
            'complex:vps' => 675,
        ],
        'quote_questions' => [
            'Objectif principal' => ['Site vitrine', 'E-commerce', 'Blog', 'Espace membre', 'À étudier'],
            'Style recherché' => ['Sobre et premium', 'Nature et bien-être', 'Institutionnel', 'Éditorial', 'À étudier'],
            'Contenus disponibles' => ['J’ai déjà mes contenus', 'J’ai besoin d’aide', 'Traductions à prévoir', 'Photos à produire', 'À étudier'],
            'Délai souhaité' => ['Urgent', '1 mois', '2 à 3 mois', 'Souple', 'À étudier'],
            'Fonctions attendues' => ['Newsletter', 'Blog', 'Paiement en ligne', 'Base de données', 'À étudier'],
        ],
        'parent_domains' => [
            'akashaproduction.com',
            'permatheque.fr',
            'vivre-en-autonomie.fr',
            'conseil-ayurveda.fr',
            'onapprendtouslesjours.fr',
            'harmonie-holistique.fr',
            'mafiaz.world',
            'alasourcedeleau.org',
        ],
    ],
    'support' => [
        'departments' => [
            'commercial' => 'Service commercial',
            'technical' => 'Service technique',
        ],
        'topics' => [
            'commercial' => [
                'Demande de devis',
                'Question sur une commande',
                'Paiement ou facturation',
                'Pack, combo ou promotion',
                'Nom de domaine',
                'Hébergement',
                'Autre demande commerciale',
            ],
            'technical' => [
                'Incident site web',
                'Bug d’affichage',
                'Accès FTP / SSH / base de données',
                'Nom de domaine ou DNS',
                'Email ou délivrabilité',
                'Performance ou sécurité',
                'Autre demande technique',
            ],
        ],
        'priorities' => [
            'normal' => 'Normale',
            'high' => 'Haute',
            'urgent' => 'Urgente',
        ],
        'statuses' => [
            'open' => 'Ouvert',
            'in-progress' => 'En cours',
            'answered' => 'Répondu',
            'closed' => 'Clos',
        ],
    ],
    'subprojects' => [
        'CMS Source',
        'Ombres & Lumières',
        'Permathèque',
        'Vivre en Autonomie',
        'Conseil Ayurveda',
        'On Apprend Tous Les Jours',
        'Location Vente',
        'Université Védique',
        'À La Source De L’Eau',
        'Épanouissement Amoureux',
        'Eau Dynamisée',
        'Harmonie Holistique',
        'Atlas Access Immo',
    ],
    'admin_email' => 'contact@akashaproduction.com',
    'admin_password' => '',
];
