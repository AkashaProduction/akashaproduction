<?php
$flash = app_pull_flash();
$pageTitle = $pageTitle ?? app_config()['site']['name'];
$currentPage = $currentPage ?? 'presentation';
$nav = app_config()['navigation'];
$currentLang = app_lang();
$langLabels = app_lang_labels();
?>
<!DOCTYPE html>
<html lang="<?= $currentLang; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?= htmlspecialchars(t('site.meta_description'), ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#08101d">
    <link rel="stylesheet" href="/assets/site.css?v=20">
</head>
<body>
<div class="site-shell">
    <header class="site-header">
        <div class="site-header__inner">
            <a class="brand" href="<?= htmlspecialchars(app_nav_href('presentation'), ENT_QUOTES, 'UTF-8'); ?>">
                <strong><?= htmlspecialchars(t('site.name'), ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?= htmlspecialchars(t('site.tagline'), ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
            <nav class="site-nav">
                <?php foreach ($nav as $key => $entry): ?>
                    <a href="<?= htmlspecialchars($entry['href'], ENT_QUOTES, 'UTF-8'); ?>"<?= $currentPage === $key ? ' data-active="true"' : ''; ?>>
                        <?= htmlspecialchars(t('nav.' . $key), ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endforeach; ?>
                <div class="lang-selector">
                    <button type="button" class="lang-btn" aria-label="<?= htmlspecialchars($langLabels[$currentLang] ?? 'FR', ENT_QUOTES, 'UTF-8'); ?>">
                        <?= app_lang_flag($currentLang); ?>
                        <span><?= strtoupper($currentLang); ?></span>
                        <svg width="10" height="6" viewBox="0 0 10 6" fill="none"><path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                    <div class="lang-dropdown">
                        <?php foreach (app_supported_langs() as $lang): ?>
                            <a href="?lang=<?= $lang; ?>" class="lang-option<?= $lang === $currentLang ? ' lang-option--active' : ''; ?>">
                                <?= app_lang_flag($lang); ?>
                                <span><?= htmlspecialchars($langLabels[$lang] ?? strtoupper($lang), ENT_QUOTES, 'UTF-8'); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button id="theme-toggle" class="theme-toggle" aria-label="Basculer le thème jour/nuit">
                    <svg id="sun-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg id="moon-icon" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            </nav>
        </div>
    </header>
    <main>
        <?php if ($flash): ?>
            <div class="container">
                <div class="<?= $flash['type'] === 'success' ? 'flash flash--success' : 'flash flash--warning'; ?>">
                    <?= nl2br(htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8')); ?>
                </div>
            </div>
        <?php endif; ?>
