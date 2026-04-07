<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = 'presentation';
$pageTitle = app_page_title('Présentation');
$projects = app_config()['projects'];
$subprojects = app_config()['subprojects'];

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="eyebrow">Concept et création web</div>
            <h1 class="hero-title">Des sites clairs. Des bases solides.</h1>
            <p class="lead">
                Akasha Production regroupe nos créations, notre savoir-faire et nos offres de création web dans une base unique:
                présentation, solutions, commande, support client et évolution technique dans un même ensemble cohérent.
            </p>
            <div class="cta-row">
                <a class="btn btn--primary" href="<?= htmlspecialchars(app_nav_href('solutions'), ENT_QUOTES, 'UTF-8'); ?>">Découvrir les solutions</a>
                <a class="btn btn--secondary" href="<?= htmlspecialchars(app_nav_href('commander'), ENT_QUOTES, 'UTF-8'); ?>">Commander</a>
            </div>
            <div class="hero-stats">
                <article class="stat-card">
                    <strong>Depuis 2005</strong>
                    <span>Expérience de développement et de mise en ligne de projets professionnels.</span>
                </article>
                <article class="stat-card">
                    <strong>Sites, packs, support</strong>
                    <span>Une base pensée pour présenter, vendre, accompagner et faire évoluer les services.</span>
                </article>
                <article class="stat-card">
                    <strong>Écosystème propriétaire</strong>
                    <span>Des créations internes et des domaines thématiques structurés autour d’une même vision.</span>
                </article>
            </div>
            <div class="meta-list">
                <?php foreach ($subprojects as $item): ?>
                    <span class="meta-tag"><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="glass device-scene">
            <div class="scene-card scene-card--top">
                <span class="scene-card__label">Akasha Production</span>
                <strong class="scene-card__value">Présence web premium</strong>
                <span class="scene-card__meta">Présentation, services, commande et support dans une même base.</span>
            </div>
            <img class="shot shot-desktop" src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=1200&q=80" alt="Présentation d'une interface web sur écran desktop">
            <img class="shot shot-tablet" src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=900&q=80" alt="Présentation sur tablette">
            <img class="shot shot-mobile" src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=600&q=80" alt="Présentation sur mobile">
            <div class="scene-card scene-card--bottom">
                <span class="scene-card__label">Architecture</span>
                <strong class="scene-card__value">Solutions combinables</strong>
                <span class="scene-card__meta">Création, hébergement, domaine, espace client et ticketing.</span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="panel">
            <div class="kicker">Qui nous sommes</div>
            <h2 class="section-title">Nous réalisons nos propres sites et accompagnons aussi des projets professionnels.</h2>
            <p class="copy">
                Akasha Production conçoit, développe et fait évoluer ses propres créations. Cette pratique directe nous oblige à penser l’ergonomie,
                la structure éditoriale, la cohérence commerciale et la tenue technique sur la durée.
            </p>
            <p class="copy">
                Au fil des années, nous avons également développé plusieurs sites professionnels pour des activités très différentes.
                Le résultat recherché est toujours le même: une présence web sérieuse, lisible, durable et réellement exploitable.
            </p>
        </div>
        <div class="grid-2">
            <article class="card">
                <h3>Une maison mère pour plusieurs univers</h3>
                <p class="copy">Chaque sous-projet garde sa personnalité, tout en restant rattaché à une architecture claire de domaines, contenus et offres.</p>
            </article>
            <article class="card">
                <h3>Des offres cadrées et combinables</h3>
                <p class="copy">Le site permet d’assembler création, hébergement, domaine et packs promotionnels dans un parcours plus cohérent.</p>
            </article>
            <article class="card">
                <h3>Un socle orienté exploitation réelle</h3>
                <p class="copy">Commandes, demandes commerciales et tickets techniques sont pensés pour être traités, suivis et détaillés côté client.</p>
            </article>
            <article class="card">
                <h3>Une expérience nourrie par le terrain</h3>
                <p class="copy">Nos propres projets servent de laboratoire permanent, et les projets professionnels nous imposent un niveau d’exigence concret.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow">Projets en ligne</div>
                <h2 class="section-title">Quelques réalisations et univers déjà publiés</h2>
            </div>
            <p class="copy section-heading__note">Descriptions reprises à partir des contenus, titres ou méta-descriptions réellement visibles sur les sites quand ils étaient accessibles.</p>
        </div>
        <div class="project-grid">
            <?php foreach ($projects as $project): ?>
                <article class="project-card">
                    <div class="project-thumb">
                        <img src="<?= htmlspecialchars(app_screenshot_url($project['url']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.outerHTML='<div class=&quot;project-placeholder&quot;>À venir</div>'">
                        <div class="project-thumb__overlay">
                            <span class="project-domain"><?= htmlspecialchars($project['domain'] ?? parse_url($project['url'], PHP_URL_HOST) ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="project-status"><?= htmlspecialchars($project['status'] ?? 'En ligne', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                    <div class="project-body">
                        <h3><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="copy project-copy"><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="project-actions">
                            <span class="project-link-label"><?= htmlspecialchars($project['domain'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <a class="btn btn--secondary" href="<?= htmlspecialchars($project['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">Visiter</a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
