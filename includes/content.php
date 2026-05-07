<?php
declare(strict_types=1);

function app_content_defaults(): array
{
    return [
        'site' => [
            'meta_title'       => 'Akasha Production — La Créativité Interconnectée',
            'meta_description' => 'Akasha Production conçoit des créations web sur mesure, des univers interactifs et des jeux narratifs à la croisée du sensible et du conceptuel.',
            'enter_label'      => 'Entrer',
            'loader_label'     => 'Akasha Production',
        ],
        'nav' => [
            'featured'  => 'À la une',
            'creations' => 'Nos créations',
            'projects'  => 'Vos projets',
            'contact'   => 'Contact',
        ],
        'hero' => [
            'eyebrow'        => 'Akasha Production',
            'title'          => 'La Créativité Interconnectée',
            'lead'           => 'Akasha Production tisse des productions web tous formats — sites conceptuels, plateformes communautaires, jeux narratifs et œuvres interactives. Chaque projet est pensé comme une constellation : des intentions reliées, une esthétique cohérente, une expérience qui demeure.',
            'cta_creations'  => 'Nos créations',
            'cta_projects'   => 'Vos projets',
        ],
        'featured' => [
            'eyebrow'     => 'À la une',
            'title'       => 'Aletheia — The Mystic Quest',
            'description' => "Aletheia est un escape game paradoxal en ligne — une œuvre vivante où l'on entre par curiosité, et où l'on découvre, en parcourant neuf cercles, qu'il n'y a rien à fuir : la pièce dont on cherche la sortie est présence. Trois passages, vingt formes de cartes, cinq rituels. Une expérience initiatique distribuable, pensée comme une constellation narrative.",
            'image'       => 'assets/img/aletheia-cover.jpg',
            'image_webp'  => 'assets/img/aletheia-cover.webp',
            'image_alt'   => 'Aletheia — The Mystic Quest',
            'cta_label'   => 'Jouer à Aletheia',
            'cta_url'     => 'https://www.akashaproduction.com/aletheia',
        ],
        'creations' => [
            'eyebrow' => 'Portfolio',
            'title'   => 'Nos créations',
            'intro'   => 'Une galerie de projets web livrés ou en orbite — chacun avec sa lumière propre.',
            'cards'   => [
                [
                    'name'        => 'Mafiaz.World',
                    'description' => 'Univers narratif et social autour des "Ombres & Lumières" — gameplay communautaire, économie symbolique et identité graphique signature.',
                    'url'         => 'https://www.mafiaz.world',
                    'image'       => 'assets/img/projects/mafiaz-world.jpg',
                    'image_webp'  => 'assets/img/projects/mafiaz-world.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'CMS-Source.org',
                    'description' => 'CMS modulaire distribuable, pensé pour bâtir des architectures web fluides et configurables — la fondation logicielle des éditions Akasha.',
                    'url'         => 'https://www.cms-source.org',
                    'image'       => 'assets/img/projects/cms-source.jpg',
                    'image_webp'  => 'assets/img/projects/cms-source.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Permatheque.org',
                    'description' => 'Bibliothèque vivante des savoirs de la permaculture et de l\'autonomie sensible — organisée comme une forêt comestible numérique.',
                    'url'         => 'https://www.permatheque.org',
                    'image'       => 'assets/img/projects/permatheque.jpg',
                    'image_webp'  => 'assets/img/projects/permatheque.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Conseil-Ayurveda.fr',
                    'description' => 'Plateforme de consultation ayurvédique en ligne — interface apaisée, parcours guidé, et écriture éditoriale au service du soin.',
                    'url'         => 'https://www.conseil-ayurveda.fr',
                    'image'       => 'assets/img/projects/conseil-ayurveda.jpg',
                    'image_webp'  => 'assets/img/projects/conseil-ayurveda.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Vivre-en-autonomie.fr',
                    'description' => 'Magazine et carnet d\'expériences sur la vie autonome — articles, fiches pratiques, sentiers d\'apprentissage et communauté ouverte.',
                    'url'         => 'https://www.vivre-en-autonomie.fr',
                    'image'       => 'assets/img/projects/vivre-en-autonomie.jpg',
                    'image_webp'  => 'assets/img/projects/vivre-en-autonomie.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Onapprendtouslesjours.fr',
                    'description' => 'Espace pédagogique éclectique — savoirs partagés, micro-formats, exploration libre. Un atelier de curiosité au quotidien.',
                    'url'         => 'https://www.onapprendtouslesjours.fr',
                    'image'       => 'assets/img/projects/onapprendtouslesjours.jpg',
                    'image_webp'  => 'assets/img/projects/onapprendtouslesjours.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Atlas-Access.immo',
                    'description' => 'Atlas immobilier marocain — outils d\'accompagnement à l\'investissement, à l\'installation et à la mise en valeur de patrimoine.',
                    'url'         => 'https://www.atlas-access.immo',
                    'image'       => 'assets/img/projects/atlas-access.jpg',
                    'image_webp'  => 'assets/img/projects/atlas-access.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Harmonie-Holistique.org',
                    'description' => 'Réseau de praticiens et de ressources holistiques — annuaire, agenda, contenus éditoriaux et espace communauté.',
                    'url'         => 'https://www.harmonie-holistique.org',
                    'image'       => 'assets/img/projects/harmonie-holistique.jpg',
                    'image_webp'  => 'assets/img/projects/harmonie-holistique.webp',
                    'available'   => true,
                ],
                [
                    'name'        => 'Relief.Education',
                    'description' => 'Plateforme éducative en cours d\'orbite — design system narratif et architecture pédagogique en construction.',
                    'url'         => '',
                    'image'       => 'assets/img/projects/relief-education.jpg',
                    'image_webp'  => 'assets/img/projects/relief-education.webp',
                    'available'   => false,
                ],
            ],
        ],
        'projects' => [
            'eyebrow'      => 'Vos projets',
            'title'        => 'Une vitrine pour vos créations',
            'empty_state'  => "Aucun projet n'est actuellement mis en avant. Si des internautes viennent à publier, ils s'afficheront ici — du plus récent au plus ancien.",
            'intro'        => "Akasha Production ouvre un espace de promotion des créations web innovantes. Si vous portez un projet — site conceptuel, jeu en ligne, plateforme artistique ou utilitaire singulier — proposez-le ici. Les projets retenus apparaîtront en haut de cette section, le plus récent en grand, suivi des autres en grille.",
            'instructions' => "Soumettez votre projet via le formulaire ci-dessous. Notre équipe le relit, le situe, et — si l'œuvre rejoint la constellation — le publie sur cette page.",
            'form_title'   => 'Soumettre un projet',
            'submit_label' => 'Envoyer le projet',
            'load_more_label' => 'Voir plus',
        ],
        'footer' => [
            'copyright'  => 'Copyright 2026 AkashaProduction.com',
            'legal_link' => 'Informations légales',
            'contact_link' => 'Nous contacter',
            'legal_lines' => [
                'Robin Prevent',
                '50 avenue Frédéric Mistral, 83170 Brignoles',
                'Téléphone : 07 81 42 88 71',
                'Hébergement : o2switch — Chemin des Pardiaux, 63000 Clermont-Ferrand',
            ],
        ],
        'contact' => [
            'title'        => 'Nous contacter',
            'lead'         => "Pour un projet, une collaboration ou une simple question — déposez un message, nous répondons en moins de 48h.",
            'submit_label' => 'Envoyer',
        ],
    ];
}

function app_content(): array
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }

    $defaults = app_content_defaults();
    $stored = app_read_json('content.json', []);
    $cache = $stored ? app_array_replace_recursive_keep_lists($defaults, $stored) : $defaults;
    return $cache;
}

function app_content_save(array $content): bool
{
    return app_write_json('content.json', $content);
}

function app_content_invalidate(): void
{
    // No-op: cache is request-scoped, but kept as a hook for future caching layers.
}

function app_array_replace_recursive_keep_lists(array $base, array $override): array
{
    foreach ($override as $key => $value) {
        if (is_array($value) && isset($base[$key]) && is_array($base[$key]) && app_is_assoc_array($base[$key]) && app_is_assoc_array($value)) {
            $base[$key] = app_array_replace_recursive_keep_lists($base[$key], $value);
        } else {
            $base[$key] = $value;
        }
    }
    return $base;
}

function app_is_assoc_array(array $array): bool
{
    if ($array === []) {
        return true;
    }
    return array_keys($array) !== range(0, count($array) - 1);
}

function app_user_projects(): array
{
    $records = app_read_json('user-projects.json', []);
    usort($records, static function (array $a, array $b): int {
        return strcmp((string) ($b['submitted_at'] ?? ''), (string) ($a['submitted_at'] ?? ''));
    });
    return $records;
}

function app_user_projects_published(): array
{
    return array_values(array_filter(app_user_projects(), static function (array $record): bool {
        return ($record['status'] ?? 'pending') === 'approved';
    }));
}

function app_user_projects_save(array $records): bool
{
    return app_write_json('user-projects.json', $records);
}

function app_user_projects_add(array $record): array
{
    $records = app_user_projects();
    array_unshift($records, $record);
    app_user_projects_save($records);
    return $record;
}

function app_user_projects_update(string $id, callable $mutator): bool
{
    $records = app_user_projects();
    $changed = false;
    foreach ($records as &$record) {
        if (($record['id'] ?? '') === $id) {
            $mutator($record);
            $changed = true;
            break;
        }
    }
    unset($record);
    if ($changed) {
        app_user_projects_save($records);
    }
    return $changed;
}

function app_user_projects_delete(string $id): bool
{
    $records = app_user_projects();
    $next = array_values(array_filter($records, static fn (array $r): bool => ($r['id'] ?? '') !== $id));
    if (count($next) === count($records)) {
        return false;
    }
    return app_user_projects_save($next);
}
