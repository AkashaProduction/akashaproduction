<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = 'solutions';
$pageTitle = app_page_title('Nos solutions');
$catalog = app_config()['catalog'];

$packPresets = [
    'showcase-shared-yearly' => [
        'label' => 'Pack vitrine',
        'headline' => 'Création vitrine + mutualisé annuel',
        'creation' => 'showcase',
        'hosting' => 'shared-yearly',
        'price' => 120,
        'compare_at' => 138,
        'description' => 'Le pack le plus direct pour lancer une présence professionnelle complète.',
    ],
    'complex-shared-yearly' => [
        'label' => 'Pack complexe',
        'headline' => 'Création complexe + mutualisé annuel',
        'creation' => 'complex',
        'hosting' => 'shared-yearly',
        'price' => 550,
        'compare_at' => 588,
        'description' => 'Pour les projets qui demandent plus de pages, plus de modules et une base de données.',
    ],
    'custom' => [
        'label' => 'Pack personnalisé',
        'headline' => 'Création personnalisée + hébergement sur devis',
        'creation' => 'custom',
        'hosting' => 'cloud',
        'price' => null,
        'compare_at' => null,
        'description' => 'Étude complète et chiffrage sur mesure pour les besoins atypiques ou plus ambitieux.',
    ],
];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Nos solutions</div>
        <h1 class="page-title">Choisissez votre base, puis affinez votre commande.</h1>
        <p class="lead">La page fonctionne comme un vrai sélecteur commercial: vous choisissez une création, un hébergement ou un pack, puis la page Commander récupère directement cette présélection.</p>
    </div>
</section>

<form method="get" action="<?= htmlspecialchars(app_nav_href('commander'), ENT_QUOTES, 'UTF-8'); ?>" class="solution-builder" data-solution-builder>
    <section class="section">
        <div class="container catalog-row">
            <div class="catalog-row__header">
                <div class="eyebrow">Ligne 1</div>
                <h2 class="section-title">Création</h2>
                <p class="copy">Choisissez le niveau de création web correspondant à votre projet.</p>
            </div>
            <div class="catalog-select-grid">
                <?php foreach ($catalog['creation'] as $key => $entry): ?>
                    <label class="select-card-label">
                        <input class="select-card-input" type="radio" name="creation" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?= $key === 'showcase' ? ' checked' : ''; ?>>
                        <span class="select-card">
                            <span class="select-card__eyebrow"><?= htmlspecialchars($entry['headline'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="select-card__title"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="select-card__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : 'Sur devis'; ?></span>
                            <span class="select-card__copy"><?= htmlspecialchars(implode(' · ', $entry['features']), ENT_QUOTES, 'UTF-8'); ?></span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container catalog-row">
            <div class="catalog-row__header">
                <div class="eyebrow">Ligne 2</div>
                <h2 class="section-title">Hébergement</h2>
                <p class="copy">Choisissez l’environnement d’hébergement principal; le mode mensuel ou annuel sera rappelé dans la commande.</p>
            </div>
            <div class="catalog-select-grid">
                <label class="select-card-label">
                    <input class="select-card-input" type="radio" name="hosting" value="shared-yearly" checked>
                    <span class="select-card">
                        <span class="select-card__eyebrow">Mutualisé</span>
                        <span class="select-card__title">Mutualisé annuel</span>
                        <span class="select-card__price">88 €<small>/ an</small></span>
                        <span class="select-card__copy">Alternative mensuelle à 8 €/mois. Solution sobre et efficace pour les sites vitrines et de nombreux projets éditoriaux.</span>
                    </span>
                </label>
                <label class="select-card-label">
                    <input class="select-card-input" type="radio" name="hosting" value="vps">
                    <span class="select-card">
                        <span class="select-card__eyebrow">VPS dédié</span>
                        <span class="select-card__title">Environnement dédié</span>
                        <span class="select-card__price">200 €<small>/ mois</small></span>
                        <span class="select-card__copy">Pour les projets plus exigeants, avec davantage de marge technique et une isolation plus forte.</span>
                    </span>
                </label>
                <label class="select-card-label">
                    <input class="select-card-input" type="radio" name="hosting" value="cloud">
                    <span class="select-card">
                        <span class="select-card__eyebrow">Cloud</span>
                        <span class="select-card__title">Infrastructure personnalisée</span>
                        <span class="select-card__price">Sur devis</span>
                        <span class="select-card__copy">À retenir pour les besoins spécifiques, l’architecture multi-services ou les contraintes d’exploitation plus avancées.</span>
                    </span>
                </label>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container catalog-row">
            <div class="catalog-row__header">
                <div class="eyebrow">Ligne 3</div>
                <h2 class="section-title">Packs</h2>
                <p class="copy">Sélectionnez un pack si vous souhaitez partir directement sur une combinaison déjà pensée.</p>
            </div>
            <div class="catalog-select-grid">
                <?php foreach ($packPresets as $key => $pack): ?>
                    <label class="select-card-label">
                        <input class="select-card-input" type="radio" name="pack" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="select-card select-card--pack">
                            <span class="select-card__eyebrow"><?= htmlspecialchars($pack['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="select-card__title"><?= htmlspecialchars($pack['headline'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="select-card__price">
                                <?= $pack['price'] !== null ? app_money((float) $pack['price']) : 'Sur devis'; ?>
                                <?php if ($pack['compare_at'] !== null): ?><small><s><?= app_money((float) $pack['compare_at']); ?></s></small><?php endif; ?>
                            </span>
                            <span class="select-card__copy"><?= htmlspecialchars($pack['description'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container solution-builder__footer">
            <div class="panel">
                <div class="eyebrow">Options</div>
                <h2 class="section-title">Domaine et préparation</h2>
                <label class="checkbox-row checkbox-row--stacked">
                    <input id="solutions-domain" type="checkbox" name="include_domain" value="1">
                    <span>Ajouter un nom de domaine personnalisé à 18 €/an</span>
                </label>
                <p class="copy">Le sous-domaine personnalisé sur nos domaines parents reste disponible sans surcoût, sous réserve d’acceptation de cohérence avec le projet.</p>
            </div>
            <div class="form-card selection-summary" data-solution-summary>
                <div class="eyebrow">Préparation</div>
                <h2 class="section-title">Votre sélection</h2>
                <p class="copy" data-solution-summary-text>Création vitrine + mutualisé annuel</p>
                <div class="order-summary__total" data-solution-summary-total>120 €</div>
                <div class="cta-row">
                    <button class="btn btn--primary" type="submit">Passer à la commande</button>
                    <a class="btn btn--secondary" href="<?= htmlspecialchars(app_nav_href('contact'), ENT_QUOTES, 'UTF-8'); ?>">Parler du projet</a>
                </div>
            </div>
        </div>
    </section>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>
