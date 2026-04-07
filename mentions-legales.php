<?php
require __DIR__ . '/includes/bootstrap.php';

$currentPage = '';
$pageTitle = app_page_title('Mentions légales');
$site = app_config()['site'];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Mentions légales</div>
        <h1 class="page-title">Informations légales du site</h1>
    </div>
</section>

<section class="section">
    <div class="container panel legal-copy">
        <div>
            <h2 class="section-title">Éditeur</h2>
            <p><strong><?= htmlspecialchars($site['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <p>Responsable : <?= htmlspecialchars($site['responsable'], ENT_QUOTES, 'UTF-8'); ?></p>
            <?php foreach ($site['address_lines'] as $line): ?>
                <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <p>Tél : <?= htmlspecialchars($site['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Email : <a href="<?= htmlspecialchars(app_nav_href('contact'), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($site['contact_email_label'], ENT_QUOTES, 'UTF-8'); ?></a></p>
        </div>
        <div>
            <h2 class="section-title">Hébergement</h2>
            <p><strong><?= htmlspecialchars($site['host']['name'], ENT_QUOTES, 'UTF-8'); ?></strong></p>
            <?php foreach ($site['host']['address_lines'] as $line): ?>
                <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <?php foreach ($site['host']['legal_lines'] as $line): ?>
                <p><?= htmlspecialchars($line, ENT_QUOTES, 'UTF-8'); ?></p>
            <?php endforeach; ?>
            <p>Tél : <?= htmlspecialchars($site['host']['phone'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Site : <a href="<?= htmlspecialchars($site['host']['website'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noreferrer"><?= htmlspecialchars($site['host']['website'], ENT_QUOTES, 'UTF-8'); ?></a></p>
        </div>
        <div>
            <h2 class="section-title">Données et formulaires</h2>
            <p>Les informations transmises via les formulaires de contact, commande et support sont enregistrées pour permettre le traitement de la demande et le suivi de la relation client.</p>
            <p>Le client peut demander la suppression ou la rectification de ses données en utilisant la page de contact.</p>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
