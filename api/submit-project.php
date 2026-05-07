<?php
declare(strict_types=1);

require __DIR__ . '/_init.php';

api_require_post();
api_check_honeypot_or_fail();
api_check_csrf_or_fail();

$title       = app_clean((string) ($_POST['title'] ?? ''));
$description = app_clean((string) ($_POST['description'] ?? ''));
$url         = trim((string) ($_POST['url'] ?? ''));
$firstName   = app_clean((string) ($_POST['first_name'] ?? ''));
$lastName    = app_clean((string) ($_POST['last_name'] ?? ''));
$email       = trim((string) ($_POST['email'] ?? ''));
$phone       = app_clean((string) ($_POST['phone'] ?? ''));
$note        = app_clean((string) ($_POST['note'] ?? ''));

if ($title === '' || mb_strlen($title) > 120) {
    api_fail('Le titre du projet est requis (120 caractères max).');
}
if ($description === '' || mb_strlen($description) > 2000) {
    api_fail('La description est requise (2000 caractères max).');
}
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    api_fail("L'URL du projet n'est pas valide.");
}
if ($firstName === '' || $lastName === '') {
    api_fail('Votre prénom et nom sont requis.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_fail("L'adresse email n'est pas valide.");
}

$image = null;
if (!empty($_FILES['image']['name'])) {
    $image = app_save_uploaded_image('image', 'project');
    if ($image === null) {
        api_fail("L'image n'a pas pu être enregistrée. Format accepté : JPG, PNG, WebP, max 6 Mo.");
    }
}

$id = app_uuid();
$record = [
    'id'           => $id,
    'submitted_at' => app_now(),
    'status'       => 'pending',
    'title'        => $title,
    'description'  => $description,
    'url'          => $url,
    'image'        => $image['url_path'] ?? '',
    'image_meta'   => $image ? ['name' => $image['name'], 'size' => $image['size']] : null,
    'submitter'    => [
        'first_name' => $firstName,
        'last_name'  => $lastName,
        'email'      => $email,
        'phone'      => $phone,
    ],
    'note'         => $note,
];

app_user_projects_add($record);

$cfg = app_config();
$body = "Nouveau projet soumis sur akashaproduction.com\n\n"
      . "Titre : {$title}\n"
      . "URL   : {$url}\n"
      . "Auteur: {$firstName} {$lastName}\n"
      . "Email : {$email}\n"
      . "Tel   : " . ($phone !== '' ? $phone : '—') . "\n"
      . ($image ? "Image : " . $image['url_path'] . "\n" : "")
      . "\n— Description —\n{$description}\n"
      . ($note !== '' ? "\n— Note complémentaire —\n{$note}\n" : "")
      . "\n— Modération —\nhttps://{$cfg['site']['domain']}/admin?section=projects&id={$id}\n";

app_send_mail($cfg['mail']['recipients_project'], "[Akasha] Nouveau projet : {$title}", $body, $email);

api_respond([
    'ok'      => true,
    'message' => 'Merci, votre projet est transmis. Il sera publié après relecture.',
    'id'      => $id,
]);
