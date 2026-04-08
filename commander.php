<?php
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/domain-pricing.php';

$catalog = app_config()['catalog'];
$domainPrices = app_domain_selling_prices();
$popularTlds = app_domain_popular_tlds();

$packPresets = [
    'showcase-shared-yearly' => ['creation' => 'showcase', 'hosting' => 'shared-yearly', 'label' => 'Pack vitrine'],
    'complex-shared-yearly' => ['creation' => 'complex', 'hosting' => 'shared-yearly', 'label' => 'Pack complexe'],
    'custom' => ['creation' => 'custom', 'hosting' => 'cloud', 'label' => 'Pack personnalise'],
];

$prefillCreation = (string) ($_GET['creation'] ?? '');
$prefillHosting = (string) ($_GET['hosting'] ?? '');
$prefillPack = (string) ($_GET['pack'] ?? '');
$prefillSubdomainPrefix = trim((string) ($_GET['subdomain_prefix'] ?? ''));
$prefillParentDomain = (string) ($_GET['parent_domain'] ?? 'akashaproduction.com');
$prefillIncludeDomain = !empty($_GET['include_domain']);
$prefillCustomDomainName = trim((string) ($_GET['custom_domain_name'] ?? ''));

if (isset($packPresets[$prefillPack])) {
    $prefillCreation = $packPresets[$prefillPack]['creation'];
    $prefillHosting = $packPresets[$prefillPack]['hosting'];
}
if ($prefillCreation === '' && $prefillHosting === '' && $prefillPack === '') {
    $prefillCreation = 'showcase';
    $prefillHosting = 'shared-yearly';
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
$prefillTotal = $prefillIsQuote ? null : app_compute_total($prefillCreation, $prefillHosting, false);

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
    $domainExtension = strtolower(trim((string) ($_POST['domain_extension'] ?? '')));
    $splitPayment = !empty($_POST['split_payment']);
    $isQuote = $creation === 'custom' || $hosting === 'cloud';

    if ($firstName === '' || $lastName === '' || $email === '' || $projectDescription === '') {
        app_flash('warning', 'Merci de renseigner vos informations essentielles et la description du projet.');
        app_redirect('/commander');
    }

    $domainPrice = 0;
    if ($includeDomain && $customDomainName !== '') {
        $htPrices = app_domain_ht_prices();
        if (isset($htPrices[$domainExtension])) {
            $domainPrice = app_domain_selling_price($htPrices[$domainExtension]);
        }
    }

    $quoteAnswers = [];
    foreach ($catalog['quote_questions'] as $question => $options) {
        $key = 'quote_' . md5($question);
        $quoteAnswers[$question] = (string) ($_POST[$key] ?? '');
    }

    $baseTotal = app_compute_total($creation, $hosting, false);
    $total = $baseTotal + $domainPrice;

    $record = [
        'id' => app_uuid(),
        'created_at' => app_now(),
        'status' => $isQuote ? 'quote-requested' : 'pending-validation',
        'customer' => [
            'first_name' => $firstName, 'last_name' => $lastName,
            'email' => $email, 'phone' => $phone,
            'company' => $company, 'country' => $country,
        ],
        'selection' => [
            'creation' => $creation, 'hosting' => $hosting,
            'subdomain_prefix' => $subdomainPrefix,
            'parent_domain' => $parentDomain,
            'include_domain' => $includeDomain,
            'custom_domain_name' => $includeDomain ? $customDomainName . '.' . $domainExtension : '',
            'domain_extension' => $domainExtension,
            'domain_price' => $domainPrice,
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
        . "Reference: {$record['id']}\n"
        . "Client: {$firstName} {$lastName}\nEmail: {$email}\nTelephone: {$phone}\n"
        . "Organisation: {$company}\nPays: {$country}\n"
        . "Creation: {$creation}\nHebergement: {$hosting}\n"
        . "Sous-domaine: " . ($subdomainPrefix !== '' ? "{$subdomainPrefix}.{$parentDomain}" : "A definir sur {$parentDomain}") . "\n"
        . "Domaine personnalise: " . ($includeDomain ? $customDomainName . '.' . $domainExtension . " ({$domainPrice} EUR)" : 'Non') . "\n"
        . "Paiement 3x: " . ($splitPayment ? 'Oui' : 'Non') . "\n"
        . "Total: " . ($isQuote ? 'Sur devis' : app_money($total)) . "\n\n"
        . "Description:\n{$projectDescription}\n\n"
        . "Reponses devis:\n" . print_r($quoteAnswers, true);
    app_send_mail('Nouvelle commande / demande Akasha Production', $body, $email);

    app_flash('success', $isQuote
        ? 'Votre demande de devis a bien ete enregistree. Nous reviendrons vers vous avec une proposition adaptee.'
        : 'Votre demande de commande a bien ete enregistree. La validation et l\'activation vous seront confirmees rapidement.');
    app_redirect('/commander');
}

$currentPage = 'commander';
$pageTitle = app_page_title('Commander');
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Commander</div>
        <h1 class="page-title">Composez votre offre sur mesure.</h1>
        <p class="lead">Combinez creation, hebergement et nom de domaine. Les promotions s'appliquent automatiquement sur les combinaisons eligibles.</p>
    </div>
</section>

<section class="section">
    <div class="container grid-2 order-layout">
        <div class="form-card">
            <form class="form-grid" method="post" data-order-form>
                <!-- CREATION -->
                <div class="field field--full">
                    <div class="kicker">Creation</div>
                    <div class="product-tabs" data-product-group="creation">
                        <?php foreach ($catalog['creation'] as $key => $entry): ?>
                            <label class="product-tab">
                                <input type="radio" name="creation" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?= $prefillCreation === $key ? ' checked' : ''; ?>>
                                <span class="product-tab__inner">
                                    <span class="product-tab__name"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="product-tab__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : 'Sur devis'; ?></span>
                                    <span class="product-tab__features"><?= htmlspecialchars(implode(' · ', $entry['features']), ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- HEBERGEMENT -->
                <div class="field field--full">
                    <div class="kicker">Hebergement</div>
                    <div class="product-tabs" data-product-group="hosting">
                        <?php foreach ($catalog['hosting'] as $key => $entry): ?>
                            <label class="product-tab">
                                <input type="radio" name="hosting" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?= $prefillHosting === $key ? ' checked' : ''; ?>>
                                <span class="product-tab__inner">
                                    <span class="product-tab__name"><?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="product-tab__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) . '<small>' . htmlspecialchars($entry['suffix'], ENT_QUOTES, 'UTF-8') . '</small>' : 'Sur devis'; ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SOUS-DOMAINE OFFERT -->
                <div class="field field--full">
                    <div class="kicker">Sous-domaine offert</div>
                    <p class="copy">Un sous-domaine gratuit vous est propose sur nos domaines parents, sous reserve de coherence avec votre projet.</p>
                    <div class="subdomain-row">
                        <input id="subdomain_prefix" name="subdomain_prefix" placeholder="votre-choix" value="<?= htmlspecialchars($prefillSubdomainPrefix, ENT_QUOTES, 'UTF-8'); ?>">
                        <span class="subdomain-dot">.</span>
                        <select id="parent_domain" name="parent_domain">
                            <?php foreach ($catalog['parent_domains'] as $domain): ?>
                                <option value="<?= htmlspecialchars($domain, ENT_QUOTES, 'UTF-8'); ?>"<?= $prefillParentDomain === $domain ? ' selected' : ''; ?>><?= htmlspecialchars($domain, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- NOM DE DOMAINE PERSONNALISE -->
                <div class="field field--full">
                    <div class="kicker">Nom de domaine personnalise</div>
                    <label class="checkbox-row">
                        <input id="include-domain" name="include_domain" type="checkbox" value="1" data-toggle-target="#domain-search-block"<?= $prefillIncludeDomain ? ' checked' : ''; ?>>
                        <span>Ajouter un nom de domaine dedie (tarif selon extension)</span>
                    </label>
                </div>
                <div class="field field--full" id="domain-search-block"<?= $prefillIncludeDomain ? '' : ' hidden'; ?>>
                    <div class="domain-search-box">
                        <div class="domain-search-row">
                            <input id="domain-name-input" name="custom_domain_name" type="text" placeholder="ex: monsite (sans www.)" value="<?= htmlspecialchars($prefillCustomDomainName, ENT_QUOTES, 'UTF-8'); ?>" class="domain-name-field">
                            <span class="subdomain-dot">.</span>
                            <select id="domain-extension" name="domain_extension" class="domain-ext-field">
                                <optgroup label="Extensions populaires">
                                    <?php foreach ($popularTlds as $tld): ?>
                                        <?php if (isset($domainPrices[$tld])): ?>
                                            <option value="<?= $tld; ?>">.<?= $tld; ?> — <?= $domainPrices[$tld]; ?> &euro;/an</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="Toutes les extensions">
                                    <?php foreach ($domainPrices as $tld => $price): ?>
                                        <?php if (!in_array($tld, $popularTlds, true)): ?>
                                            <option value="<?= $tld; ?>">.<?= $tld; ?> — <?= $price; ?> &euro;/an</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <button type="button" class="btn btn--secondary domain-search-btn" data-domain-search>Verifier</button>
                        </div>
                        <div class="domain-result" data-domain-result hidden></div>
                    </div>
                </div>

                <!-- INFORMATIONS CLIENT -->
                <div class="field field--full"><div class="kicker">Vos informations</div></div>
                <div class="field"><label for="first_name">Prenom</label><input id="first_name" name="first_name" required></div>
                <div class="field"><label for="last_name">Nom</label><input id="last_name" name="last_name" required></div>
                <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
                <div class="field"><label for="phone">Telephone</label><input id="phone" name="phone"></div>
                <div class="field"><label for="company">Organisation</label><input id="company" name="company"></div>
                <div class="field"><label for="country">Pays</label><input id="country" name="country"></div>

                <!-- DESCRIPTION PROJET -->
                <div class="field field--full">
                    <label for="project_description">Description du projet</label>
                    <textarea id="project_description" name="project_description" required></textarea>
                </div>

                <!-- DEVIS (conditionnel) -->
                <div class="field field--full" data-quote-block hidden>
                    <div class="panel">
                        <div class="kicker">Formulaire devis</div>
                        <p class="copy">Completez ces informations pour permettre un chiffrage adapte a votre besoin.</p>
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

                <!-- PAIEMENT -->
                <div class="field field--full">
                    <label class="checkbox-row"><input id="split-payment" name="split_payment" type="checkbox" value="1"> Payer en 3x sans frais (souscription sur 12 mois)</label>
                </div>

                <div class="field field--full">
                    <button class="btn btn--primary" type="submit">Enregistrer la demande</button>
                </div>
            </form>
        </div>

        <!-- SIDEBAR RESUME -->
        <div class="order-summary" data-order-summary>
            <div class="kicker">Resume de commande</div>
            <h2 class="section-title">Votre selection</h2>

            <div class="summary-lines">
                <div class="summary-line">
                    <span>Creation</span>
                    <strong data-summary-creation><?= htmlspecialchars($catalog['creation'][$prefillCreation]['label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span data-summary-creation-price><?= $catalog['creation'][$prefillCreation]['amount'] > 0 ? app_money((float) $catalog['creation'][$prefillCreation]['amount']) : 'Sur devis'; ?></span>
                </div>
                <div class="summary-line">
                    <span>Hebergement</span>
                    <strong data-summary-hosting><?= htmlspecialchars($catalog['hosting'][$prefillHosting]['label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span data-summary-hosting-price><?= $catalog['hosting'][$prefillHosting]['amount'] > 0 ? app_money((float) $catalog['hosting'][$prefillHosting]['amount']) : 'Sur devis'; ?></span>
                </div>
                <div class="summary-line summary-line--domain" data-summary-domain-line hidden>
                    <span>Nom de domaine</span>
                    <strong data-summary-domain-name>—</strong>
                    <span data-summary-domain-price>—</span>
                </div>
            </div>

            <div class="summary-promo" data-summary-promo hidden>
                <span>Promotion pack</span>
                <strong data-summary-promo-label></strong>
            </div>

            <div class="summary-total-row">
                <span>Total</span>
                <div class="order-summary__total" data-order-total><?= $prefillTotal !== null ? app_money((float) $prefillTotal) : 'Sur devis'; ?></div>
            </div>
            <p class="copy" data-order-detail><?= $prefillIsQuote ? 'Etude commerciale personnalisee' : 'Paiement a l\'activation'; ?></p>

            <div class="panel summary-note">
                <p class="copy">Les prix combines beneficient automatiquement de la tarification pack quand elle existe. Le paiement Stripe sera connecte prochainement.</p>
            </div>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
