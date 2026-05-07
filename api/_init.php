<?php
declare(strict_types=1);

require __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/content.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

function api_respond(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_fail(string $message, int $status = 400): never
{
    api_respond(['ok' => false, 'message' => $message], $status);
}

function api_require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        api_fail('Méthode non autorisée.', 405);
    }
}

function api_check_csrf_or_fail(): void
{
    $token = (string) ($_POST['csrf'] ?? '');
    if (!app_check_csrf($token)) {
        api_fail('Jeton de sécurité expiré. Rechargez la page et réessayez.', 419);
    }
}

function api_check_honeypot_or_fail(): void
{
    if (!empty($_POST['company'])) {
        api_respond(['ok' => true, 'message' => 'Reçu.']);
    }
}
