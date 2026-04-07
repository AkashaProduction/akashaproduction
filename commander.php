<?php
require __DIR__ . '/includes/bootstrap.php';

$catalog = app_config()['catalog'];
$packPresets = [
    'showcase-shared-yearly' => ['creation' => 'showcase', 'hosting' => 'shared-yearly', 'label' => 'Pack vitrine'],
    'complex-shared-yearly' => ['creation' => 'complex', 'hosting' => 'shared-yearly', 'label' => 'Pack complexe'],
    'custom' => ['creation' => 'custom', 'hosting' => 'cloud', 'label' => 'Pack personnalisé'],
];

$prefillCreation = (string) ($_GET['creation'] ?? 'showcase');
$prefillHosting = (string) ($_GET['hosting'] ?? 'shared-yearly');
$prefillPack = (string) ($_GET['pack'] ?? '');
$prefillSubdomainPrefix = trim((string) ($_GET['subdomain_prefix'] ?? ''));
$prefillParentDomain = (string) ($_GET['parent_domain'] ?? 'akashaproduction.com');
$prefillIncludeDomain = !empty($_GET['include_domain']);
$prefillCustomDomainName = trim((string) ($_GET['custom_domain_name'] ?? ''));

if (isset($packPresets[$prefillPack])) {
    $prefillCreation = $packPresets[$prefillPack]['creation'];
    $prefillHosting = $packPresets[$prefillPack]['hosting'];
}

if (!isset($catalog['creation'][$prefillCreation])) {
    $prefillCreation = 'showcase';
}

if (!isset($catalog['hosting'][$prefillHosting])) {
    $prefillHosting = 'shared-yearly';
}

if (!in_array($prefillParentDomain, $catalog['parent_domains'], true)) {
    $prefillParentDomain = 'akashaproduction.com';
}

$prefillIsQuote = $prefillCreation === 'custom' || $prefillHosting === 'cloud';
$prefillTotal = $prefillIsQuote ? null : app_compute_total($prefillCreation, $prefillHosting, $prefillIncludeDomain);
$prefillSummaryLabel = isset($packPresets[$prefillPack])
    ? $packPresets[$prefillPack]['label']
    : ($catalog['creation'][$prefillCreation]['headline'] . ' + ' . $catalog['hosting'][$prefillHosting]['headline']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $company = trim((string) ($_POST['company'] ?? ''));
    $country = trim((string) ($_POST['country'] ?? ''));
    $creation = (string) ($_POST['creation'] ?? 'showcase');
    $hosting = (string) ($_POST['hosting'] ?? 'shared-yearly');
    $subdomainPrefix = trim((string) ($_POST['subdomain_prefix'] ?? ''));
    $parentDomain = (string) ($_POST['parent_domain'] ?? 'akashaproduction.com');
    $projectDescription = trim((string) ($_POST['project_description'] ?? ''));
    $includeDomain = !empty($_POST['include_domain']);
    $customDomainName = trim((string) ($_POST['custom_domain_name'] ?? ''));
    $splitPayment = !empty($_POST['split_payment']);
    $isQuote = $creation === 'custom' || $hosting === 'cloud';

    if ($firstName === '' || $lastName === '' || $email === '' || $projectDescription === '') {
        app_flash('warning', 'Merci de renseigner vos informations essentielles et la description du projet.');
        app_redirect('/commander');
    }

    if ($includeDomain && $customDomainName === '') {
        app_flash('warning', 'Merci de préciser le nom de domaine personnalisé à enregistrer.');
        app_redirect('/commander');
    }

    $quoteAnswers = [];
    foreach ($catalog['quote_questions'] as $question => $options) {
        $key = 'quote_' . md5($question);
        $quoteAnswers[$question] = (string) ($_POST[$key] ?? '');
    }

    $total = app_compute_total($creation, $hosting, $includeDomain);
    $record = [
        'id' => app_uuid(),
        'created_at' => app_now(),
        'status' => $isQuote ? 'quote-requested' : 'pending-validation',
        'customer' => [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'country' => $country,
        ],
        'selection' => [
            'creation' => $creation,
            'hosting' => $hosting,
            'subdomain_prefix' => $subdomainPrefix,
            'parent_domain' => $parentDomain,
            'include_domain' => $includeDomain,
            'custom_domain_name' => $customDomainName,
            'split_payment' => $splitPayment,
        ],
        'project_description' => $projectDescription,
        'quote_answers' => $quoteAnswers,
        'summary' => [
            'total' => $isQuote ? null : $total,
            'installment' => (!$isQuote && $splitPayment) ? round($total / 3, 2) : null,
        ],
    ];
    app_append_json('orders.json', $record);

    $body = "Nouvelle demande Akasha Production\n\n"
        . "Référence: {$record['id']}\n"
        . "Client: {$firstName} {$lastName}\n"
        . "Email: {$email}\n"
        . "Téléphone: {$phone}\n"
        . "Organisation: {$company}\n"
        . "Pays: {$country}\n"
        . "Création: {$creation}\n"
        . "Hébergement: {$hosting}\n"
        . "Sous-domaine demandé: " . ($subdomainPrefix !== '' ? "{$subdomainPrefix}.{$parentDomain}" : "À définir sur {$parentDomain}") . "\n"
        . "Domaine parent: {$parentDomain}\n"
        . "Domaine personnalisé: " . ($includeDomain ? $customDomainName : 'Non') . "\n"
        . "Paiement 3x: " . ($splitPayment ? 'Oui' : 'Non') . "\n"
        . "Total: " . ($isQuote ? 'Sur devis' : app_money($total)) . "\n\n"
        . "Description du projet:\n{$projectDescription}\n\n"
        . "Réponses au devis:\n" . print_r($quoteAnswers, true);
    app_send_mail('Nouvelle commande / demande Akasha Production', $body, $email);

    app_flash('success', $isQuote
        ? 'Votre demande de devis a bien été enregistrée. Nous reviendrons vers vous avec une proposition adaptée.'
        : 'Votre demande de commande a bien été enregistrée. La validation et l’activation vous seront confirmées rapidement.');
    app_redirect('/commander');
}

$currentPage = 'commander';
$pageTitle = app_page_title('Commander');
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Commander</div>
        <h1 class="page-title">Composez votre offre, vos options et votre mode de paiement.</h1>
        <p class="lead">Le configurateur permet les achats simples, les packs promotionnels et les demandes de devis offertes. Les choix sont sauvegardés avec la commande.</p>
        <?php if (isset($_GET['creation']) || isset($_GET['hosting']) || isset($_GET['pack'])): ?>
            <div class="notice">Présélection chargée depuis les solutions : <?= htmlspecialchars($prefillSummaryLabel, ENT_QUOTES, 'UTF-8'); ?><?= $prefillTotal !== null ? ' · ' . app_money((float) $prefillTotal) : ' · Sur devis'; ?></div>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container grid-3">
        <article class="pricing-card">
            <div class="kicker">Création</div>
            <h3>Offres de création web</h3>
            <ul>
                <li>Site vitrine multilingue 3 pages : 50 €</li>
                <li>Site complexe 9 pages + 3 modules + base de données : 500 €</li>
                <li>Création personnalisée : sur devis</li>
            </ul>
        </article>
        <article class="pricing-card">
            <div class="kicker">Hébergement</div>
            <h3>Infrastructure</h3>
            <ul>
                <li>Serveur mutualisé : 8 €/mois</li>
                <li>Serveur mutualisé annuel : 88 €/an</li>
                <li>VPS dédié : 200 €/mois</li>
                <li>Cloud : sur devis</li>
            </ul>
        </article>
        <article class="pricing-card">
            <div class="kicker">Packs</div>
            <h3>Combinaisons promotionnelles</h3>
            <div class="price"><strong>120 €</strong><s>138 €</s></div>
            <div class="price"><strong>550 €</strong><s>588 €</s></div>
            <p class="copy">Les combos non affichés sont recalculés de manière cohérente à partir du même barème.</p>
        </article>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="form-card">
            <form class="form-grid" method="post" data-order-form>
                <div class="field"><label for="first_name">Prénom</label><input id="first_name" name="first_name" required></div>
                <div class="field"><label for="last_name">Nom</label><input id="last_name" name="last_name" required></div>
                <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
                <div class="field"><label for="phone">Téléphone</label><input id="phone" name="phone"></div>
                <div class="field"><label for="company">Organisation</label><input id="company" name="company"></div>
                <div class="field"><label for="country">Pays</label><input id="country" name="country"></div>
                <div class="field">
                    <label for="creation">Création</label>
                    <select id="creation" name="creation">
                        <option value="showcase"<?= $prefillCreation === 'showcase' ? ' selected' : ''; ?>>Site vitrine multilingue 3 pages</option>
                        <option value="complex"<?= $prefillCreation === 'complex' ? ' selected' : ''; ?>>Site complexe 9 pages + 3 modules + base de données</option>
                        <option value="custom"<?= $prefillCreation === 'custom' ? ' selected' : ''; ?>>Création personnalisée</option>
                    </select>
                </div>
                <div class="field">
                    <label for="hosting">Hébergement</label>
                    <select id="hosting" name="hosting">
                        <option value="shared-monthly"<?= $prefillHosting === 'shared-monthly' ? ' selected' : ''; ?>>Serveur mutualisé mensuel</option>
                        <option value="shared-yearly"<?= $prefillHosting === 'shared-yearly' ? ' selected' : ''; ?>>Serveur mutualisé annuel</option>
                        <option value="vps"<?= $prefillHosting === 'vps' ? ' selected' : ''; ?>>VPS dédié</option>
                        <option value="cloud"<?= $prefillHosting === 'cloud' ? ' selected' : ''; ?>>Cloud personnalisé</option>
                    </select>
                </div>
                <div class="field"><label for="subdomain_prefix">Préfixe du sous-domaine offert</label><input id="subdomain_prefix" name="subdomain_prefix" placeholder="votre-choix" value="<?= htmlspecialchars($prefillSubdomainPrefix, ENT_QUOTES, 'UTF-8'); ?>"></div>
                <div class="field">
                    <label for="parent_domain">Domaine parent souhaité</label>
                    <select id="parent_domain" name="parent_domain">
                        <?php foreach ($catalog['parent_domains'] as $domain): ?>
                            <option value="<?= htmlspecialchars($domain, ENT_QUOTES, 'UTF-8'); ?>"<?= $prefillParentDomain === $domain ? ' selected' : ''; ?>><?= htmlspecialchars($domain, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field field--full">
                    <p class="copy">Le sous-domaine offert prend la forme <strong>votre-choix.domaine-parent</strong>, sous réserve d’acceptation de cohérence avec votre projet.</p>
                </div>
                <div class="field field--full">
                    <label for="project_description">Description du projet</label>
                    <textarea id="project_description" name="project_description" required></textarea>
                </div>
                <div class="field field--full">
                    <label class="checkbox-row"><input id="include-domain" name="include_domain" type="checkbox" value="1" data-toggle-target="#custom-domain-fields"<?= $prefillIncludeDomain ? ' checked' : ''; ?>> Ajouter un nom de domaine personnalisé à 18 €/an</label>
                </div>
                <div class="field field--full" id="custom-domain-fields"<?= $prefillIncludeDomain ? '' : ' hidden'; ?>>
                    <label for="custom_domain_name">Nom de domaine personnalisé souhaité</label>
                    <input id="custom_domain_name" name="custom_domain_name" placeholder="exemple.fr" value="<?= htmlspecialchars($prefillCustomDomainName, ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <div class="field field--full">
                    <label class="checkbox-row"><input id="split-payment" name="split_payment" type="checkbox" value="1"> Payer en 3x sans frais sur 12 mois</label>
                </div>

                <div class="field field--full" data-quote-block hidden>
                    <div class="panel">
                        <div class="kicker">Devis</div>
                        <p class="copy">Ce bloc apparaît pour les créations personnalisées et les hébergements cloud.</p>
                        <div class="form-grid">
                            <?php foreach ($catalog['quote_questions'] as $question => $options): ?>
                                <div class="field">
                                    <label for="<?= htmlspecialchars('q-' . md5($question), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($question, ENT_QUOTES, 'UTF-8'); ?></label>
                                    <select id="<?= htmlspecialchars('q-' . md5($question), ENT_QUOTES, 'UTF-8'); ?>" name="<?= htmlspecialchars('quote_' . md5($question), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php foreach ($options as $option): ?>
                                            <option value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="field field--full">
                    <button class="btn btn--primary" type="submit">Enregistrer la demande</button>
                </div>
            </form>
        </div>

        <div class="form-card order-summary">
            <div class="kicker">Résumé instantané</div>
            <div data-order-total class="order-summary__total"><?= $prefillTotal !== null ? app_money((float) $prefillTotal) : 'Sur devis'; ?></div>
            <p class="copy" data-order-detail><?= $prefillIsQuote ? 'Étude commerciale personnalisée' : 'Paiement à l’activation'; ?></p>
            <div class="panel">
                <p class="copy">Le paiement Stripe reste à raccorder avec les clés réelles. En attendant, la demande est enregistrée de manière propre et exploitable.</p>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
