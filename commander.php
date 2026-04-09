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
        app_flash('warning', t('commander.flash_warning'));
        app_redirect('/commander');
    }

    $domainPrice = 0;
    if ($includeDomain && $customDomainName !== '') {
        $allPrices = app_domain_selling_prices();
        if (isset($allPrices[$domainExtension])) {
            $domainPrice = $allPrices[$domainExtension];
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

    if ($isQuote) {
        app_flash('success', t('commander.flash_quote_success'));
        app_redirect('/commander');
    } else {
        app_redirect('/checkout?order=' . urlencode($record['id']));
    }
}

$currentPage = 'commander';
$pageTitle = app_page_title(t('nav.commander'));
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero commander-hero">
    <div class="container solutions-hero-grid">
        <div>
            <div class="eyebrow"><?= htmlspecialchars(t('commander.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h1 class="page-title"><?= htmlspecialchars(t('commander.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead"><?= htmlspecialchars(t('commander.lead'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="solutions-scene glass">
            <div class="solutions-bubble solutions-bubble--1">
                <img src="/assets/img/commander-victory.jpg" alt="Victory">
            </div>
            <div class="solutions-bubble solutions-bubble--2">
                <img src="/assets/img/commander-growth.jpg" alt="Growth">
            </div>
            <div class="solutions-bubble solutions-bubble--3">
                <img src="/assets/img/commander-stars.jpg" alt="Satisfaction">
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2 order-layout">
        <div class="form-card">
            <form class="form-grid" method="post" data-order-form>
                <!-- CREATION -->
                <div class="field field--full">
                    <div class="kicker"><?= htmlspecialchars(t('commander.creation_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="product-tabs" data-product-group="creation">
                        <?php foreach ($catalog['creation'] as $key => $entry): ?>
                            <label class="product-tab">
                                <input type="radio" name="creation" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?= $prefillCreation === $key ? ' checked' : ''; ?>>
                                <span class="product-tab__inner">
                                    <span class="product-tab__name"><?= htmlspecialchars(t('catalog.creation.' . $key . '.label'), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="product-tab__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : htmlspecialchars(t('commander.summary_on_quote'), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="product-tab__features"><?= htmlspecialchars(implode(' · ', ta('catalog.creation.' . $key . '.features') ?: $entry['features']), ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- HEBERGEMENT -->
                <div class="field field--full">
                    <div class="kicker"><?= htmlspecialchars(t('commander.hosting_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="product-tabs" data-product-group="hosting">
                        <?php foreach ($catalog['hosting'] as $key => $entry): ?>
                            <label class="product-tab">
                                <input type="radio" name="hosting" value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?= $prefillHosting === $key ? ' checked' : ''; ?>>
                                <span class="product-tab__inner">
                                    <span class="product-tab__name"><?= htmlspecialchars(t('catalog.hosting.' . $key . '.label'), ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="product-tab__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) . '<small>' . htmlspecialchars($entry['suffix'], ENT_QUOTES, 'UTF-8') . '</small>' : htmlspecialchars(t('commander.summary_on_quote'), ENT_QUOTES, 'UTF-8'); ?></span>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SOUS-DOMAINE OFFERT -->
                <div class="field field--full">
                    <div class="kicker"><?= htmlspecialchars(t('commander.subdomain_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <p class="copy"><?= htmlspecialchars(t('commander.subdomain_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="subdomain-row">
                        <input id="subdomain_prefix" name="subdomain_prefix" placeholder="<?= htmlspecialchars(t('commander.subdomain_placeholder'), ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars($prefillSubdomainPrefix, ENT_QUOTES, 'UTF-8'); ?>">
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
                    <div class="kicker"><?= htmlspecialchars(t('commander.domain_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <label class="checkbox-row">
                        <input id="include-domain" name="include_domain" type="checkbox" value="1" data-toggle-target="#domain-search-block"<?= $prefillIncludeDomain ? ' checked' : ''; ?>>
                        <span><?= htmlspecialchars(t('commander.domain_add'), ENT_QUOTES, 'UTF-8'); ?></span>
                    </label>
                </div>
                <div class="field field--full" id="domain-search-block"<?= $prefillIncludeDomain ? '' : ' hidden'; ?>>
                    <div class="domain-search-box">
                        <div class="domain-search-row">
                            <input id="domain-name-input" name="custom_domain_name" type="text" placeholder="<?= htmlspecialchars(t('commander.domain_placeholder'), ENT_QUOTES, 'UTF-8'); ?>" value="<?= htmlspecialchars($prefillCustomDomainName, ENT_QUOTES, 'UTF-8'); ?>" class="domain-name-field">
                            <span class="subdomain-dot">.</span>
                            <select id="domain-extension" name="domain_extension" class="domain-ext-field">
                                <optgroup label="<?= htmlspecialchars(t('commander.popular_extensions'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php foreach ($popularTlds as $tld): ?>
                                        <?php if (isset($domainPrices[$tld])): ?>
                                            <option value="<?= $tld; ?>">.<?= $tld; ?> — <?= $domainPrices[$tld]; ?> &euro;/an</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                                <optgroup label="<?= htmlspecialchars(t('commander.all_extensions'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php foreach ($domainPrices as $tld => $price): ?>
                                        <?php if (!in_array($tld, $popularTlds, true)): ?>
                                            <option value="<?= $tld; ?>">.<?= $tld; ?> — <?= $price; ?> &euro;/an</option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </optgroup>
                            </select>
                            <button type="button" class="btn btn--secondary domain-search-btn" data-domain-search><?= htmlspecialchars(t('commander.domain_verify'), ENT_QUOTES, 'UTF-8'); ?></button>
                        </div>
                        <div class="domain-result" data-domain-result hidden></div>
                    </div>
                </div>

                <!-- INFORMATIONS CLIENT -->
                <div class="field field--full"><div class="kicker"><?= htmlspecialchars(t('commander.info_kicker'), ENT_QUOTES, 'UTF-8'); ?></div></div>
                <div class="field"><label for="first_name"><?= htmlspecialchars(t('commander.firstname'), ENT_QUOTES, 'UTF-8'); ?></label><input id="first_name" name="first_name" required></div>
                <div class="field"><label for="last_name"><?= htmlspecialchars(t('commander.lastname'), ENT_QUOTES, 'UTF-8'); ?></label><input id="last_name" name="last_name" required></div>
                <div class="field"><label for="email"><?= htmlspecialchars(t('commander.email'), ENT_QUOTES, 'UTF-8'); ?></label><input id="email" name="email" type="email" required></div>
                <div class="field"><label for="phone"><?= htmlspecialchars(t('commander.phone'), ENT_QUOTES, 'UTF-8'); ?></label><input id="phone" name="phone"></div>
                <div class="field"><label for="company"><?= htmlspecialchars(t('commander.company'), ENT_QUOTES, 'UTF-8'); ?></label><input id="company" name="company"></div>
                <div class="field"><label for="country"><?= htmlspecialchars(t('commander.country'), ENT_QUOTES, 'UTF-8'); ?></label><input id="country" name="country"></div>

                <!-- DESCRIPTION PROJET -->
                <div class="field field--full">
                    <label for="project_description"><?= htmlspecialchars(t('commander.description'), ENT_QUOTES, 'UTF-8'); ?></label>
                    <textarea id="project_description" name="project_description" required></textarea>
                </div>

                <!-- DEVIS (conditionnel) -->
                <div class="field field--full" data-quote-block hidden>
                    <div class="panel">
                        <div class="kicker"><?= htmlspecialchars(t('commander.quote_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                        <p class="copy"><?= htmlspecialchars(t('commander.quote_text'), ENT_QUOTES, 'UTF-8'); ?></p>
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
                    <label class="checkbox-row"><input id="split-payment" name="split_payment" type="checkbox" value="1"> <?= htmlspecialchars(t('commander.split_payment'), ENT_QUOTES, 'UTF-8'); ?></label>
                </div>

                <div class="field field--full">
                    <button class="btn btn--primary" type="submit" data-submit-btn><?= htmlspecialchars(t('commander.submit_pay'), ENT_QUOTES, 'UTF-8'); ?></button>
                </div>
            </form>
        </div>

        <!-- SIDEBAR RESUME -->
        <div class="order-summary" data-order-summary>
            <div class="kicker"><?= htmlspecialchars(t('commander.summary_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h2 class="section-title"><?= htmlspecialchars(t('commander.summary_title'), ENT_QUOTES, 'UTF-8'); ?></h2>

            <div class="summary-lines">
                <div class="summary-line">
                    <span><?= htmlspecialchars(t('commander.summary_creation'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <strong data-summary-creation><?= htmlspecialchars(t('catalog.creation.' . $prefillCreation . '.label'), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span data-summary-creation-price><?= $catalog['creation'][$prefillCreation]['amount'] > 0 ? app_money((float) $catalog['creation'][$prefillCreation]['amount']) : htmlspecialchars(t('commander.summary_on_quote'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="summary-line">
                    <span><?= htmlspecialchars(t('commander.summary_hosting'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <strong data-summary-hosting><?= htmlspecialchars(t('catalog.hosting.' . $prefillHosting . '.label'), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span data-summary-hosting-price><?= $catalog['hosting'][$prefillHosting]['amount'] > 0 ? app_money((float) $catalog['hosting'][$prefillHosting]['amount']) : htmlspecialchars(t('commander.summary_on_quote'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <div class="summary-line summary-line--domain" data-summary-domain-line hidden>
                    <span><?= htmlspecialchars(t('commander.summary_domain'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <strong data-summary-domain-name>—</strong>
                    <span data-summary-domain-price>—</span>
                </div>
            </div>

            <div class="summary-promo" data-summary-promo hidden>
                <span><?= htmlspecialchars(t('commander.summary_promo'), ENT_QUOTES, 'UTF-8'); ?></span>
                <strong data-summary-promo-label></strong>
            </div>

            <div class="summary-total-row">
                <span><?= htmlspecialchars(t('commander.summary_total'), ENT_QUOTES, 'UTF-8'); ?></span>
                <div class="order-summary__total" data-order-total><?= $prefillTotal !== null ? app_money((float) $prefillTotal) : htmlspecialchars(t('commander.summary_on_quote'), ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <p class="copy" data-order-detail><?= $prefillIsQuote ? htmlspecialchars(t('commander.summary_quote_detail'), ENT_QUOTES, 'UTF-8') : htmlspecialchars(t('commander.summary_pay_detail'), ENT_QUOTES, 'UTF-8'); ?></p>

            <div class="panel summary-note">
                <p class="copy"><?= htmlspecialchars(t('commander.summary_note'), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
    </div>
</section>
<script>
window.i18n = <?= json_encode([
    'labels' => [
        'creation' => [
            'showcase' => t('catalog.creation.showcase.label'),
            'complex' => t('catalog.creation.complex.label'),
            'custom' => t('catalog.creation.custom.label'),
        ],
        'hosting' => [
            'shared-monthly' => t('catalog.hosting.shared-monthly.label'),
            'shared-yearly' => t('catalog.hosting.shared-yearly.label'),
            'vps' => t('catalog.hosting.vps.label'),
            'cloud' => t('catalog.hosting.cloud.label'),
        ],
    ],
    'on_quote' => t('commander.summary_on_quote'),
    'currency' => t('js.currency'),
    'per_year' => t('js.per_year'),
    'pack_discount' => t('js.pack_discount'),
    'domain_custom' => t('js.domain_custom'),
    'split_format' => t('js.split_format'),
    'quote_detail' => t('commander.summary_quote_detail'),
    'pay_detail' => t('commander.summary_pay_detail'),
    'submit_pay' => t('commander.submit_pay'),
    'submit_quote' => t('commander.submit_quote'),
    'search_min' => t('js.search_min'),
    'searching' => t('js.searching'),
    'verify' => t('js.verify'),
    'domain_available' => t('js.domain_available'),
    'domain_taken' => t('js.domain_taken'),
    'domain_unknown' => t('js.domain_unknown'),
    'domain_error' => t('js.domain_error'),
], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?>;
</script>
<?php require __DIR__ . '/includes/footer.php'; ?>
