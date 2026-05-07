<?php
declare(strict_types=1);

require __DIR__ . '/_init.php';

api_require_post();
api_check_honeypot_or_fail();
api_check_csrf_or_fail();

$firstName = app_clean((string) ($_POST['first_name'] ?? ''));
$lastName  = app_clean((string) ($_POST['last_name'] ?? ''));
$email     = trim((string) ($_POST['email'] ?? ''));
$subject   = app_clean((string) ($_POST['subject'] ?? ''));
$message   = trim((string) ($_POST['message'] ?? ''));

if ($firstName === '' || $lastName === '') {
    api_fail('Votre prénom et nom sont requis.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    api_fail("L'adresse email n'est pas valide.");
}
if ($subject === '' || mb_strlen($subject) > 160) {
    api_fail('Le sujet est requis (160 caractères max).');
}
if ($message === '' || mb_strlen($message) > 5000) {
    api_fail('Le message est requis (5000 caractères max).');
}

$cfg = app_config();
$body = "Nouveau message de contact via akashaproduction.com\n\n"
      . "De      : {$firstName} {$lastName} <{$email}>\n"
      . "Sujet   : {$subject}\n\n"
      . "— Message —\n{$message}\n";

$ok = app_send_mail($cfg['mail']['recipients_contact'], "[Akasha Contact] {$subject}", $body, $email);

$record = [
    'id'         => app_uuid(),
    'created_at' => app_now(),
    'first_name' => $firstName,
    'last_name'  => $lastName,
    'email'      => $email,
    'subject'    => $subject,
    'message'    => $message,
    'mail_sent'  => $ok,
];
$messages = app_read_json('contact-messages.json', []);
array_unshift($messages, $record);
app_write_json('contact-messages.json', $messages);

api_respond([
    'ok'      => true,
    'message' => 'Merci, votre message a été transmis. Nous revenons vers vous sous 48h.',
]);
