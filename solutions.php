<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = 'solutions';
$pageTitle = app_page_title('Nos solutions');
$catalog = app_config()['catalog'];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Nos solutions</div>
        <h1 class="page-title">Des offres cadrées, combinables et évolutives.</h1>
        <p class="lead">Les produits peuvent être commandés seuls, combinés entre eux ou basculés sur devis selon le niveau de personnalisation recherché.</p>
    </div>
</section>

<section class="section">
    <div class="container grid-3">
        <?php foreach ($catalog['creation'] as $entry): ?>
            <article class="pricing-card">
                <div class="kicker"><?= htmlspecialchars($entry['headline'], ENT_QUOTES, 'UTF-8'); ?></div>
                <h3><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="price">
                    <strong><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : 'Sur devis'; ?></strong>
                </div>
                <ul>
                    <?php foreach ($entry['features'] as $feature): ?>
                        <li><?= htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="container grid-3">
        <?php foreach ($catalog['hosting'] as $entry): ?>
            <article class="pricing-card">
                <div class="kicker"><?= htmlspecialchars($entry['headline'], ENT_QUOTES, 'UTF-8'); ?></div>
                <h3><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <div class="price">
                    <strong><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : 'Sur devis'; ?></strong>
                    <?php if (!empty($entry['suffix'])): ?><span><?= htmlspecialchars($entry['suffix'], ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="container grid-3">
        <article class="pricing-card">
            <div class="kicker">Sous-domaine offert</div>
            <h3>Sous-domaine personnalisable</h3>
            <div class="price"><strong>Offert</strong></div>
            <p class="copy">Nous pouvons vous attribuer un sous-domaine du type <strong>votre-choix.akashaproduction.com</strong> ou rattaché à l’un de nos domaines thématiques, selon cohérence du projet.</p>
        </article>
        <article class="pricing-card">
            <div class="kicker">Nom de domaine</div>
            <h3>Domaine personnalisé</h3>
            <div class="price"><strong>18 €</strong><span>/ an</span></div>
            <p class="copy">Vous pouvez ajouter un nom de domaine personnalisé indépendant, enregistré et suivi avec votre commande.</p>
        </article>
        <article class="pricing-card">
            <div class="kicker">Domaines parents</div>
            <h3>Répertoires thématiques disponibles</h3>
            <ul>
                <?php foreach ($catalog['parent_domains'] as $domain): ?>
                    <li><?= htmlspecialchars($domain, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
    </div>
</section>

<section class="section">
    <div class="container grid-3">
        <article class="pricing-card">
            <div class="kicker">Pack vitrine</div>
            <h3>Création vitrine + mutualisé annuel</h3>
            <div class="price"><strong>120 €</strong><s>138 €</s></div>
            <p class="copy">Une formule claire pour lancer une présence professionnelle rapidement et proprement.</p>
        </article>
        <article class="pricing-card">
            <div class="kicker">Pack complexe</div>
            <h3>Création complexe + mutualisé annuel</h3>
            <div class="price"><strong>550 €</strong><s>588 €</s></div>
            <p class="copy">Une base plus complète pour les projets nécessitant davantage d’autonomie et de modules.</p>
        </article>
        <article class="pricing-card">
            <div class="kicker">Pack personnalisé</div>
            <h3>Création personnalisée + hébergement personnalisé</h3>
            <div class="price"><strong>Sur devis</strong></div>
            <p class="copy">Architecture, hébergement et accompagnement adaptés selon la nature exacte du projet.</p>
        </article>
    </div>
    <div class="container" style="margin-top:1.25rem;">
        <a class="btn btn--primary" href="<?= htmlspecialchars(app_nav_href('commander'), ENT_QUOTES, 'UTF-8'); ?>">Accéder au configurateur</a>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
