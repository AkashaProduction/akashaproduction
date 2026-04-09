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
    <link rel="stylesheet" href="/assets/site.css?v=9">
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
