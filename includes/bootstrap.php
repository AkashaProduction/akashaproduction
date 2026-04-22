<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (int) ($_SERVER['SERVER_PORT'] ?? 0) === 443
        || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_name('akasha_sid');
    session_start();
}

$config = require __DIR__ . '/config.php';
$runtimeConfigPath = __DIR__ . '/runtime-config.php';
if (file_exists($runtimeConfigPath)) {
    $runtime = require $runtimeConfigPath;
    if (is_array($runtime)) {
        $config = array_replace_recursive($config, $runtime);
    }
}

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/i18n.php';

// Les réglages éditables depuis l'admin (clés Stripe) écrasent config + runtime.
$settingsStored = app_settings_load();
if ($settingsStored) {
    $config = array_replace_recursive($config, $settingsStored);
}

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

if (isset($_GET['lang'])) {
    app_set_lang((string) $_GET['lang']);
    $redir = strtok((string) ($_SERVER['REQUEST_URI'] ?? '/'), '?');
    $qs = $_GET;
    unset($qs['lang']);
    if ($qs) {
        $redir .= '?' . http_build_query($qs);
    }
    header('Location: ' . $redir);
    exit;
}
