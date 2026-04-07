<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
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
