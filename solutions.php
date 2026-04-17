<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = 'solutions';
$pageTitle = app_page_title(t('nav.solutions'));
$catalog = app_config()['catalog'];

$packs = [
    ['key' => 'vitrine', 'price' => 120, 'compare_at' => 138, 'query' => 'pack=showcase-shared-yearly'],
    ['key' => 'complexe', 'price' => 550, 'compare_at' => 588, 'query' => 'pack=complex-shared-yearly'],
    ['key' => 'custom', 'price' => null, 'compare_at' => null, 'query' => 'pack=custom'],
];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero solutions-hero">
    <div class="container solutions-hero-grid">
        <div>
            <div class="eyebrow"><?= htmlspecialchars(t('solutions.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h1 class="page-title"><?= htmlspecialchars(t('solutions.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead"><?= htmlspecialchars(t('solutions.lead'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="solutions-scene glass">
            <div class="solutions-bubble solutions-bubble--1">
                <img src="https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=500&q=80" alt="<?= htmlspecialchars(t('solutions.hero_creation'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="solutions-bubble__label"><?= htmlspecialchars(t('solutions.hero_creation'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="solutions-bubble solutions-bubble--2">
                <img src="https://images.unsplash.com/photo-1558494949-ef010cbdcc31?auto=format&fit=crop&w=500&q=80" alt="<?= htmlspecialchars(t('solutions.hero_hosting'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="solutions-bubble__label"><?= htmlspecialchars(t('solutions.hero_hosting'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="solutions-bubble solutions-bubble--3">
                <img src="https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?auto=format&fit=crop&w=500&q=80" alt="<?= htmlspecialchars(t('solutions.hero_mobile'), ENT_QUOTES, 'UTF-8'); ?>">
                <span class="solutions-bubble__label"><?= htmlspecialchars(t('solutions.hero_mobile'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
</section>

<!-- PROMO CMS SOURCE HÉBERGÉ -->
<section class="promo-hero">
    <div class="container">
        <div class="promo-badge">Offre limitée - Stock limité</div>
        <h2>Exclusif : CMS Source Hébergé</h2>
        <p class="promo-subtitle">Nous vous installons une puissante solution paramétrable selon vos goûts et projets</p>

        <div class="modules-grid">
            <div class="module-chip">Actualité</div>
            <div class="module-chip">Marketplace</div>
            <div class="module-chip">Événements</div>
            <div class="module-chip">Carte interactive</div>
            <div class="module-chip">Financement participatif</div>
            <div class="module-chip">Association</div>
            <div class="module-chip">Communauté membres</div>
            <div class="module-chip">Forums</div>
        </div>

        <div class="pricing-box-promo">
            <div class="price-regular">490€ / an</div>
            <div class="price-promo">290€/an</div>
            <div class="pricing-items">
                <div class="pricing-item">Hébergement inclus</div>
                <div class="pricing-item">Installation incluse</div>
                <div class="pricing-item">Maintenance mensuelle</div>
                <div class="pricing-item">Domaine .fr/.net/.org offert</div>
            </div>
        </div>

        <a href="/commander?pack=cms-promo" class="btn btn--primary">Commander cette offre</a>
    </div>
</section>

<!-- CRÉATIONS -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow"><?= htmlspecialchars(t('solutions.creation_eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
                <h2 class="section-title"><?= htmlspecialchars(t('solutions.creation_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <p class="copy section-heading__note"><?= htmlspecialchars(t('solutions.creation_note'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="grid-3 solutions-grid">
            <?php foreach ($catalog['creation'] as $key => $entry): ?>
                <?php $catI18n = ta('catalog.creation.' . $key); ?>
                <a href="/commander?creation=<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" class="solution-card solution-card--clickable solution-card--creation">
                    <div class="solution-card__head">
                        <span class="solution-card__eyebrow"><?= htmlspecialchars($catI18n['headline'] ?? $entry['headline'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="solution-card__title"><?= htmlspecialchars($catI18n['label'] ?? $entry['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="solution-card__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) : t('solutions.on_quote'); ?></div>
                    </div>
                    <ul class="solution-card__features">
                        <?php $features = $catI18n['features'] ?? $entry['features']; ?>
                        <?php foreach ($features as $f): ?>
                            <li><?= htmlspecialchars($f, ENT_QUOTES, 'UTF-8'); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <span class="btn btn--secondary solution-card__cta"><?= htmlspecialchars(t('solutions.select'), ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- HÉBERGEMENTS -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow"><?= htmlspecialchars(t('solutions.hosting_eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
                <h2 class="section-title"><?= htmlspecialchars(t('solutions.hosting_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <p class="copy section-heading__note"><?= htmlspecialchars(t('solutions.hosting_note'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="grid-3 solutions-grid">
            <?php foreach ($catalog['hosting'] as $key => $entry): ?>
                <?php if ($key === 'shared-monthly') continue; ?>
                <?php $catI18n = ta('catalog.hosting.' . $key); ?>
                <a href="/commander?hosting=<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" class="solution-card solution-card--clickable solution-card--hosting">
                    <div class="solution-card__head">
                        <span class="solution-card__eyebrow"><?= htmlspecialchars($catI18n['headline'] ?? $entry['headline'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="solution-card__title"><?= htmlspecialchars($catI18n['label'] ?? $entry['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="solution-card__price"><?= $entry['amount'] > 0 ? app_money((float) $entry['amount']) . '<small>' . htmlspecialchars($entry['suffix'], ENT_QUOTES, 'UTF-8') . '</small>' : t('solutions.on_quote'); ?></div>
                    </div>
                    <span class="btn btn--secondary solution-card__cta"><?= htmlspecialchars(t('solutions.select'), ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- PACKS PRÉ-COMBINÉS -->
<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow"><?= htmlspecialchars(t('solutions.packs_eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
                <h2 class="section-title"><?= htmlspecialchars(t('solutions.packs_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <p class="copy section-heading__note"><?= htmlspecialchars(t('solutions.packs_note'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="grid-3 solutions-grid">
            <?php foreach ($packs as $pack): ?>
                <?php $catI18n = ta('catalog.packs.' . $pack['key']); ?>
                <a href="/commander?<?= htmlspecialchars($pack['query'], ENT_QUOTES, 'UTF-8'); ?>" class="solution-card solution-card--clickable solution-card--pack">
                    <div class="solution-card__head">
                        <span class="solution-card__eyebrow"><?= htmlspecialchars($catI18n['label'] ?? $pack['key'], ENT_QUOTES, 'UTF-8'); ?></span>
                        <h3 class="solution-card__title"><?= htmlspecialchars($catI18n['headline'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="solution-card__price">
                            <?php if ($pack['price'] !== null): ?>
                                <?= app_money((float) $pack['price']); ?>
                                <?php if ($pack['compare_at'] !== null): ?>
                                    <small><s><?= app_money((float) $pack['compare_at']); ?></s></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <?= t('solutions.on_quote'); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <p class="copy"><?= htmlspecialchars($catI18n['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    <span class="btn btn--secondary solution-card__cta"><?= htmlspecialchars(t('solutions.configure'), ENT_QUOTES, 'UTF-8'); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- DOMAINE & PAIEMENT -->
<section class="section">
    <div class="container grid-2">
        <div class="panel">
            <div class="kicker"><?= htmlspecialchars(t('solutions.domain_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h2 class="section-title"><?= htmlspecialchars(t('solutions.domain_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="copy"><?= htmlspecialchars(t('solutions.domain_p1'), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="copy"><?= htmlspecialchars(t('solutions.domain_p2'), ENT_QUOTES, 'UTF-8'); ?></p>
            <a class="btn btn--primary" href="/commander?include_domain=1"><?= htmlspecialchars(t('solutions.domain_cta'), ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
        <div class="panel">
            <div class="kicker"><?= htmlspecialchars(t('solutions.payment_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h2 class="section-title"><?= htmlspecialchars(t('solutions.payment_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="copy"><?= htmlspecialchars(t('solutions.payment_text'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
