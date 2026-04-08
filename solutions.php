<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = 'solutions';
$pageTitle = app_page_title('Nos solutions');
$catalog = app_config()['catalog'];

$packs = [
    ['label' => 'Pack vitrine', 'headline' => 'Creation vitrine + mutualise annuel', 'price' => 120, 'compare_at' => 138, 'query' => 'pack=showcase-shared-yearly', 'description' => 'Le pack le plus direct pour lancer une presence professionnelle complete.'],
    ['label' => 'Pack complexe', 'headline' => 'Creation complexe + mutualise annuel', 'price' => 550, 'compare_at' => 588, 'query' => 'pack=complex-shared-yearly', 'description' => 'Pour les projets qui demandent plus de pages, plus de modules et une base de donnees.'],
    ['label' => 'Pack personnalise', 'headline' => 'Creation personnalisee + hebergement sur devis', 'price' => null, 'compare_at' => null, 'query' => 'pack=custom', 'description' => 'Etude complete et chiffrage sur mesure pour les besoins atypiques.'],
];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Nos solutions</div>
        <h1 class="page-title">Des offres cadrees, combinables et evolutives.</h1>
        <p class="lead">Chaque produit peut etre achete seul ou combine avec un autre. Cliquez sur un produit pour le pre-selectionner dans le configurateur de commande.</p>
    </div>
</section>

<!-- CREATION -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow">Ligne 1</div>
                <h2 class="section-title">Creation web</h2>
            </div>
            <p class="copy section-heading__note">Choisissez le niveau de creation correspondant a votre projet.</p>
        </div>
        <div class="grid-3 solutions-grid">
            <?php foreach ($catalog['creation'] as $key => $entry): ?>
                <a href="/commander?creation=<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" class="solution-card solution-card--clickable">
                    <div class="solution-card__head">
                        <span class="solution-card__eyebrow"><?= htmlspecialchars($entry['headline'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="solution-card__title"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="solution-card__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : 'Sur devis'; ?></div>
                    </div>
                    <ul class="solution-card__features">
                        <?php foreach ($entry['features'] as $f): ?>
                            <li><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <span class="btn btn--secondary solution-card__cta">Selectionner</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- HEBERGEMENT -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow">Ligne 2</div>
                <h2 class="section-title">Hebergement</h2>
            </div>
            <p class="copy section-heading__note">Choisissez l'environnement serveur adapte a votre charge et vos besoins.</p>
        </div>
        <div class="grid-3 solutions-grid">
            <?php foreach ($catalog['hosting'] as $key => $entry): ?>
                <?php if ($key === 'shared-monthly') continue; ?>
                <a href="/commander?hosting=<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" class="solution-card solution-card--clickable">
                    <div class="solution-card__head">
                        <span class="solution-card__eyebrow"><?= htmlspecialchars($entry['headline'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="solution-card__title"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="solution-card__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) . '<small>' . htmlspecialchars($entry['suffix'], ENT_QUOTES, 'UTF-8') . '</small>' : 'Sur devis'; ?></div>
                    </div>
                    <span class="btn btn--secondary solution-card__cta">Selectionner</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PACKS -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow">Ligne 3</div>
                <h2 class="section-title">Packs</h2>
            </div>
            <p class="copy section-heading__note">Combinaisons pre-assemblees avec tarification avantageuse.</p>
        </div>
        <div class="grid-3 solutions-grid">
            <?php foreach ($packs as $pack): ?>
                <a href="/commander?<?= htmlspecialchars($pack['query'], ENT_QUOTES, 'UTF-8'); ?>" class="solution-card solution-card--clickable solution-card--pack">
                    <div class="solution-card__head">
                        <span class="solution-card__eyebrow"><?= htmlspecialchars($pack['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="solution-card__title"><?= htmlspecialchars($pack['headline'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="solution-card__price">
                            <?php if ($pack['price'] !== null): ?>
                                <?= app_money((float) $pack['price']); ?>
                                <?php if ($pack['compare_at'] !== null): ?>
                                    <small><s><?= app_money((float) $pack['compare_at']); ?></s></small>
                                <?php endif; ?>
                            <?php else: ?>
                                Sur devis
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="copy"><?= htmlspecialchars($pack['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <span class="btn btn--secondary solution-card__cta">Configurer</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DOMAINE -->
<section class="section">
    <div class="container grid-2">
        <div class="panel">
            <div class="kicker">Nom de domaine</div>
            <h2 class="section-title">Sous-domaine offert ou domaine personnalise</h2>
            <p class="copy">Chaque commande inclut un sous-domaine gratuit sur nos domaines parents (votre-choix.akashaproduction.com, etc.), sous reserve de coherence avec votre projet.</p>
            <p class="copy">Vous pouvez egalement ajouter un nom de domaine personnalise. Le prix varie selon l'extension choisie et la disponibilite est verifiee en temps reel dans le configurateur de commande.</p>
            <a class="btn btn--primary" href="/commander?include_domain=1">Ajouter un domaine</a>
        </div>
        <div class="panel">
            <div class="kicker">Paiement en 3x</div>
            <h2 class="section-title">Souscription sans frais</h2>
            <p class="copy">Pour les commandes a prix fixe, vous pouvez diviser le montant total en 3 prelevements espaces de 4 mois chacun, sans frais supplementaires. Cette option est selectionnable lors de la commande.</p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
