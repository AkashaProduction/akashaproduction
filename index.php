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
            <h1 class="hero-title">Nous concevons des plateformes web lisibles, premium et prêtes à porter un écosystème complet de projets.</h1>
            <p class="lead">
                Akasha Production présente nos univers, nos réalisations et nos offres dans une interface plus ambitieuse:
                une base commerciale claire, une direction visuelle forte et un cadre technique capable de faire évoluer vos projets
                proprement dans le temps.
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
            <h2 class="section-title">Nous développons nos propres créations et accompagnons des projets professionnels depuis de nombreuses années.</h2>
            <p class="copy">
                Akasha Production conçoit, réalise et fait évoluer ses propres sites. Cette expérience directe nourrit une approche concrète:
                nous pensons à la fois la structure, l’identité, la lisibilité commerciale et la durabilité technique des projets.
            </p>
            <p class="copy">
                Au fil de notre expérience, nous avons également développé plusieurs sites professionnels pour des entreprises et des activités variées.
                Cela nous permet de proposer des créations web cohérentes, solides et adaptées aux besoins réels de chaque projet.
            </p>
        </div>
        <div class="grid-2">
            <article class="card">
                <div class="kicker">Présentation</div>
                <h3>Une maison mère pour des univers multiples</h3>
                <p class="copy">Chaque sous-projet garde son identité, tout en s’inscrivant dans une architecture cohérente de domaines et de pages commerciales.</p>
            </article>
            <article class="card">
                <div class="kicker">Commande</div>
                <h3>Offres simples, packs lisibles et devis cadrés</h3>
                <p class="copy">Le catalogue permet l’achat unitaire, les combinaisons création + hébergement et une demande de devis claire.</p>
            </article>
            <article class="card">
                <div class="kicker">Évolution</div>
                <h3>Base prête pour un vrai espace client</h3>
                <p class="copy">Le site prépare déjà l’intégration d’un panel technique, d’un support client et d’outils d’hébergement avancés.</p>
            </article>
            <article class="card">
                <div class="kicker">Expérience</div>
                <h3>Créations internes et projets professionnels</h3>
                <p class="copy">Nous réalisons nos propres plateformes et avons développé plusieurs sites professionnels pour diverses entreprises au long de notre expérience.</p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="kicker">Références</div>
        <h2 class="section-title">Une sélection de projets et d’univers déjà en ligne</h2>
        <p class="copy">Les sites s’ouvrent dans une nouvelle fenêtre. Si une capture n’est pas disponible, la carte reste exploitable avec le lien direct.</p>
        <div class="project-grid">
            <?php foreach ($projects as $project): ?>
                <article class="project-card">
                    <div class="project-thumb">
                        <img src="<?= htmlspecialchars(app_screenshot_url($project['url']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.outerHTML='<div class=&quot;project-placeholder&quot;>À venir</div>'">
                    </div>
                    <div class="project-body">
                        <div class="kicker">Référence</div>
                        <h3><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="copy"><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <a class="btn btn--secondary" href="<?= htmlspecialchars($project['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer">Ouvrir le site</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
