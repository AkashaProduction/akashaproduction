<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = '';
$pageTitle = app_page_title(t('legal.eyebrow'));
$site = app_config()['site'];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow"><?= htmlspecialchars(t('legal.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
        <h1 class="page-title"><?= htmlspecialchars(t('legal.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
</section>

<section class="section">
    <div class="container panel legal-copy">
        <div>
            <h2 class="section-title"><?= htmlspecialchars(t('legal.editor'), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><strong><?= htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <p><?= htmlspecialchars(t('legal.responsible'), ENT_QUOTES, 'UTF-8'); ?> : <?= htmlspecialchars($site['responsable'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php foreach ($site['address_lines'] as $line): ?>
                <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <p><?= htmlspecialchars(t('legal.phone'), ENT_QUOTES, 'UTF-8'); ?> : <?= htmlspecialchars($site['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><?= htmlspecialchars(t('legal.email'), ENT_QUOTES, 'UTF-8'); ?> : <a href="<?= htmlspecialchars(app_nav_href('contact'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($site['contact_email_label'], ENT_QUOTES, 'UTF-8'); ?></a></p>
        </div>
        <div>
            <h2 class="section-title"><?= htmlspecialchars(t('legal.hosting'), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><strong><?= htmlspecialchars($site['host']['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <?php foreach ($site['host']['address_lines'] as $line): ?>
                <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <?php foreach ($site['host']['legal_lines'] as $line): ?>
                <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <p><?= htmlspecialchars(t('legal.phone'), ENT_QUOTES, 'UTF-8'); ?> : <?= htmlspecialchars($site['host']['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p><?= htmlspecialchars(t('legal.site_label'), ENT_QUOTES, 'UTF-8'); ?> : <a href="<?= htmlspecialchars($site['host']['website'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($site['host']['website'], ENT_QUOTES, 'UTF-8'); ?></a></p>
        </div>
        <div>
            <h2 class="section-title"><?= htmlspecialchars(t('legal.data_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
            <p><?= htmlspecialchars(t('legal.data_p1'), ENT_QUOTES, 'UTF-8'); ?></p>
            <p><?= htmlspecialchars(t('legal.data_p2'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
