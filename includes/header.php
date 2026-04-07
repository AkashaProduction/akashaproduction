<?php
$flash = app_pull_flash();
$pageTitle = $pageTitle ?? app_config()['site']['name'];
$currentPage = $currentPage ?? 'presentation';
$nav = app_config()['navigation'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="Akasha Production conçoit des créations web, présente ses projets et accompagne des sites professionnels.">
    <link rel="stylesheet" href="/assets/site.css?v=1">
</head>
<body>
<div class="site-shell">
    <header class="site-header">
        <div class="site-header__inner">
            <a class="brand" href="<?= htmlspecialchars(app_nav_href('presentation'), ENT_QUOTES, 'UTF-8'); ?>">
                <strong><?= htmlspecialchars(app_config()['site']['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <span><?= htmlspecialchars(app_config()['site']['tagline'], ENT_QUOTES, 'UTF-8'); ?></span>
            </a>
            <nav class="site-nav">
                <?php foreach ($nav as $key => $entry): ?>
                    <a href="<?= htmlspecialchars($entry['href'], ENT_QUOTES, 'UTF-8'); ?>"<?= $currentPage === $key ? ' data-active="true"' : ''; ?>>
                        <?= htmlspecialchars($entry['label'], ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                <?php endforeach; ?>
                <a href="/admin">Admin</a>
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
