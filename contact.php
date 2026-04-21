<?php
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    app_csrf_enforce();

    // Honeypot : champ caché « website_url » rempli = bot.
    if (trim((string) ($_POST['website_url'] ?? '')) !== '') {
        app_log('info', 'contact_honeypot', []);
        app_flash('success', t('contact.flash_success'));
        app_redirect('/contact');
    }

    if (!app_rate_limit('contact', 5, 300)) {
        app_flash('warning', t('contact.flash_warning'));
        app_redirect('/contact');
    }

    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $company = trim((string) ($_POST['company'] ?? ''));
    $website = trim((string) ($_POST['website'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $hasProject = !empty($_POST['has_project']);
    $projectDetails = trim((string) ($_POST['project_details'] ?? ''));

    if ($firstName === '' || $lastName === '' || $message === '' || !app_valid_email($email)) {
        app_flash('warning', t('contact.flash_warning'));
        app_redirect('/contact');
    }

    $uploads = app_handle_uploads('attachments');
    $record = [
        'id' => app_uuid(),
        'created_at' => app_now(),
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => strtolower($email),
        'phone' => $phone,
        'company' => $company,
        'website' => $website,
        'message' => $message,
        'has_project' => $hasProject,
        'project_details' => $projectDetails,
        'attachments' => $uploads,
    ];
    app_append_json('contacts.json', $record);

    $body = "Nouveau message de contact\n\n"
        . "Nom: {$firstName} {$lastName}\n"
        . "Email: {$email}\n"
        . "Téléphone: {$phone}\n"
        . "Organisation: {$company}\n"
        . "Site existant: {$website}\n"
        . "Projet: " . ($hasProject ? 'Oui' : 'Non') . "\n"
        . "Précisions projet: {$projectDetails}\n\n"
        . "Message:\n{$message}\n\n"
        . 'Pièces jointes: ' . ($uploads ? implode(', ', array_column($uploads, 'original')) : 'Aucune');
    app_send_mail('Nouveau contact Akasha Production', $body, $email);

    app_flash('success', t('contact.flash_success'));
    app_redirect('/contact');
}

$currentPage = 'contact';
$pageTitle = app_page_title(t('contact.eyebrow'));
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container grid-2">
        <div>
            <div class="eyebrow"><?= htmlspecialchars(t('contact.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h1 class="page-title"><?= htmlspecialchars(t('contact.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead"><?= htmlspecialchars(t('contact.lead'), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="panel">
                <p><strong><?= htmlspecialchars(t('contact.processing_title'), ENT_QUOTES, 'UTF-8'); ?></strong> <?= htmlspecialchars(t('contact.processing_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                <p class="copy"><?= htmlspecialchars(t('contact.processing_detail'), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
        <div class="form-card">
            <form class="form-grid" method="post" enctype="multipart/form-data">
                <?= app_csrf_field(); ?>
                <div style="position:absolute;left:-10000px;" aria-hidden="true">
                    <label for="website_url">Laissez vide</label>
                    <input id="website_url" name="website_url" type="text" tabindex="-1" autocomplete="off">
                </div>
                <div class="field"><label for="first_name"><?= htmlspecialchars(t('contact.firstname'), ENT_QUOTES, 'UTF-8'); ?></label><input id="first_name" name="first_name" required></div>
                <div class="field"><label for="last_name"><?= htmlspecialchars(t('contact.lastname'), ENT_QUOTES, 'UTF-8'); ?></label><input id="last_name" name="last_name" required></div>
                <div class="field"><label for="email"><?= htmlspecialchars(t('contact.email'), ENT_QUOTES, 'UTF-8'); ?></label><input id="email" name="email" type="email" required></div>
                <div class="field"><label for="phone"><?= htmlspecialchars(t('contact.phone'), ENT_QUOTES, 'UTF-8'); ?></label><input id="phone" name="phone"></div>
                <div class="field"><label for="company"><?= htmlspecialchars(t('contact.company'), ENT_QUOTES, 'UTF-8'); ?></label><input id="company" name="company"></div>
                <div class="field"><label for="website"><?= htmlspecialchars(t('contact.website'), ENT_QUOTES, 'UTF-8'); ?></label><input id="website" name="website"></div>
                <div class="field field--full"><label for="message"><?= htmlspecialchars(t('contact.message'), ENT_QUOTES, 'UTF-8'); ?></label><textarea id="message" name="message" required></textarea></div>
                <div class="field field--full">
                    <label class="checkbox-row"><input id="has_project" name="has_project" type="checkbox" value="1" data-toggle-target="#project-details"> <?= htmlspecialchars(t('contact.has_project'), ENT_QUOTES, 'UTF-8'); ?></label>
                </div>
                <div class="field field--full" id="project-details" hidden>
                    <label for="project_details"><?= htmlspecialchars(t('contact.project_details'), ENT_QUOTES, 'UTF-8'); ?></label>
                    <textarea id="project_details" name="project_details"></textarea>
                </div>
                <div class="field field--full">
                    <label for="attachments"><?= htmlspecialchars(t('contact.attachments'), ENT_QUOTES, 'UTF-8'); ?></label>
                    <input id="attachments" name="attachments[]" type="file" multiple accept=".docx,.pdf,.jpg,.jpeg,.webp">
                </div>
                <div class="field field--full">
                    <button class="btn btn--primary" type="submit"><?= htmlspecialchars(t('contact.submit'), ENT_QUOTES, 'UTF-8'); ?></button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
