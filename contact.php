<?php
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim((string) ($_POST['first_name'] ?? ''));
    $lastName = trim((string) ($_POST['last_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $company = trim((string) ($_POST['company'] ?? ''));
    $website = trim((string) ($_POST['website'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    $hasProject = !empty($_POST['has_project']);
    $projectDetails = trim((string) ($_POST['project_details'] ?? ''));

    if ($firstName === '' || $lastName === '' || $email === '' || $message === '') {
        app_flash('warning', 'Merci de renseigner au minimum votre prénom, votre nom, votre email et votre message.');
        app_redirect('/contact');
    }

    $uploads = app_handle_uploads('attachments');
    $record = [
        'id' => app_uuid(),
        'created_at' => app_now(),
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
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

    app_flash('success', 'Votre message a bien été enregistré. Une confirmation s’affiche sur cette page et votre demande a été transmise.');
    app_redirect('/contact');
}

$currentPage = 'contact';
$pageTitle = app_page_title('Contact');
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container grid-2">
        <div>
            <div class="eyebrow">Contact</div>
            <h1 class="page-title">Présentez votre besoin, vos contraintes et vos documents.</h1>
            <p class="lead">Le formulaire de contact enregistre toutes les informations du contactant, gère un mode projet détaillé et accepte les formats DOCX, PDF, JPG et WEBP.</p>
            <div class="panel">
                <p><strong>Traitement des demandes :</strong> ce formulaire est le point d’entrée principal pour les demandes commerciales, techniques et les transmissions de documents.</p>
                <p class="copy">Vous décrivez votre besoin ici, vous ajoutez vos pièces jointes, puis la demande est enregistrée avec confirmation sur la page.</p>
            </div>
        </div>
        <div class="form-card">
            <form class="form-grid" method="post" enctype="multipart/form-data">
                <div class="field"><label for="first_name">Prénom</label><input id="first_name" name="first_name" required></div>
                <div class="field"><label for="last_name">Nom</label><input id="last_name" name="last_name" required></div>
                <div class="field"><label for="email">Email</label><input id="email" name="email" type="email" required></div>
                <div class="field"><label for="phone">Téléphone</label><input id="phone" name="phone"></div>
                <div class="field"><label for="company">Organisation</label><input id="company" name="company"></div>
                <div class="field"><label for="website">Site existant</label><input id="website" name="website"></div>
                <div class="field field--full"><label for="message">Message</label><textarea id="message" name="message" required></textarea></div>
                <div class="field field--full">
                    <label class="checkbox-row"><input id="has_project" name="has_project" type="checkbox" value="1" data-toggle-target="#project-details"> Il s’agit d’un projet à cadrer</label>
                </div>
                <div class="field field--full" id="project-details" hidden>
                    <label for="project_details">Précisions projet</label>
                    <textarea id="project_details" name="project_details"></textarea>
                </div>
                <div class="field field--full">
                    <label for="attachments">Documents joints (docx, pdf, jpg, webp)</label>
                    <input id="attachments" name="attachments[]" type="file" multiple accept=".docx,.pdf,.jpg,.jpeg,.webp">
                </div>
                <div class="field field--full">
                    <button class="btn btn--primary" type="submit">Envoyer</button>
                </div>
            </form>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
