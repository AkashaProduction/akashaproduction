<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = 'presentation';
$pageTitle = app_page_title(t('nav.presentation'));
$projects = app_config()['projects'];

require __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="container hero-grid">
        <div class="hero-copy">
            <div class="eyebrow"><?= htmlspecialchars(t('home.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h1 class="hero-title"><?= htmlspecialchars(t('home.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead"><?= htmlspecialchars(t('home.lead'), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="cta-row">
                <a class="btn btn--primary" href="<?= htmlspecialchars(app_nav_href('solutions'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars(t('home.cta_solutions'), ENT_QUOTES, 'UTF-8'); ?></a>
                <a class="btn btn--secondary" href="<?= htmlspecialchars(app_nav_href('commander'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars(t('home.cta_order'), ENT_QUOTES, 'UTF-8'); ?></a>
            </div>
            <div class="hero-stats">
                <article class="stat-card">
                    <strong><?= htmlspecialchars(t('home.stat1_title'), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span><?= htmlspecialchars(t('home.stat1_text'), ENT_QUOTES, 'UTF-8'); ?></span>
                </article>
                <article class="stat-card">
                    <strong><?= htmlspecialchars(t('home.stat2_title'), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span><?= htmlspecialchars(t('home.stat2_text'), ENT_QUOTES, 'UTF-8'); ?></span>
                </article>
                <article class="stat-card">
                    <strong><?= htmlspecialchars(t('home.stat3_title'), ENT_QUOTES, 'UTF-8'); ?></strong>
                    <span><?= htmlspecialchars(t('home.stat3_text'), ENT_QUOTES, 'UTF-8'); ?></span>
                </article>
            </div>
            <div class="meta-list">
                <span class="meta-tag"><?= htmlspecialchars(t('home.tag1'), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="meta-tag"><?= htmlspecialchars(t('home.tag2'), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="meta-tag"><?= htmlspecialchars(t('home.tag3'), ENT_QUOTES, 'UTF-8'); ?></span>
                <span class="meta-tag"><?= htmlspecialchars(t('home.tag4'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
        <div class="glass device-scene">
            <div class="scene-card scene-card--top">
                <span class="scene-card__label"><?= htmlspecialchars(t('home.scene_top_label'), ENT_QUOTES, 'UTF-8'); ?></span>
                <strong class="scene-card__value"><?= htmlspecialchars(t('home.scene_top_value'), ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="scene-card__meta"><?= htmlspecialchars(t('home.scene_top_meta'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <img class="shot shot-desktop" src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=1200&q=80" alt="<?= htmlspecialchars(t('home.scene_top_value'), ENT_QUOTES, 'UTF-8'); ?>">
            <img class="shot shot-tablet" src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=900&q=80" alt="<?= htmlspecialchars(t('home.scene_top_value'), ENT_QUOTES, 'UTF-8'); ?>">
            <img class="shot shot-mobile" src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=600&q=80" alt="<?= htmlspecialchars(t('home.scene_top_value'), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="scene-card scene-card--bottom">
                <span class="scene-card__label"><?= htmlspecialchars(t('home.scene_bottom_label'), ENT_QUOTES, 'UTF-8'); ?></span>
                <strong class="scene-card__value"><?= htmlspecialchars(t('home.scene_bottom_value'), ENT_QUOTES, 'UTF-8'); ?></strong>
                <span class="scene-card__meta"><?= htmlspecialchars(t('home.scene_bottom_meta'), ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container grid-2">
        <div class="panel">
            <div class="kicker"><?= htmlspecialchars(t('home.who_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h2 class="section-title"><?= htmlspecialchars(t('home.who_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p class="copy"><?= htmlspecialchars(t('home.who_p1'), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="copy"><?= htmlspecialchars(t('home.who_p2'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="grid-2">
            <article class="card">
                <h3><?= htmlspecialchars(t('home.card1_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="copy"><?= htmlspecialchars(t('home.card1_text'), ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
            <article class="card">
                <h3><?= htmlspecialchars(t('home.card2_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="copy"><?= htmlspecialchars(t('home.card2_text'), ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
            <article class="card">
                <h3><?= htmlspecialchars(t('home.card3_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="copy"><?= htmlspecialchars(t('home.card3_text'), ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
            <article class="card">
                <h3><?= htmlspecialchars(t('home.card4_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="copy"><?= htmlspecialchars(t('home.card4_text'), ENT_QUOTES, 'UTF-8'); ?></p>
            </article>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <div>
                <div class="eyebrow"><?= htmlspecialchars(t('home.projects_eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
                <h2 class="section-title"><?= htmlspecialchars(t('home.projects_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <p class="copy section-heading__note"><?= htmlspecialchars(t('home.projects_note'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="project-grid">
            <?php foreach ($projects as $project): ?>
                <article class="project-card">
                    <div class="project-thumb">
                        <img src="<?= htmlspecialchars(app_screenshot_url($project['url']), ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?>" onerror="this.outerHTML='<div class=&quot;project-placeholder&quot;><?= htmlspecialchars(t('home.coming_soon'), ENT_QUOTES, 'UTF-8'); ?></div>'">
                        <div class="project-thumb__overlay">
                            <span class="project-domain"><?= htmlspecialchars($project['domain'] ?? parse_url($project['url'], PHP_URL_HOST) ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="project-status"><?= htmlspecialchars($project['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                    <div class="project-body">
                        <h3><?= htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="copy project-copy"><?= htmlspecialchars($project['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="project-actions">
                            <span class="project-link-label"><?= htmlspecialchars($project['domain'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <a class="btn btn--secondary" href="<?= htmlspecialchars($project['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars(t('home.visit'), ENT_QUOTES, 'UTF-8'); ?></a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
