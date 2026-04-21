<?php

/**
 * Copier ce fichier en includes/runtime-config.php (gitignored) et remplir.
 *
 * Pour générer un hash admin :
 *   php -r 'echo password_hash("MON_MOT_DE_PASSE", PASSWORD_DEFAULT), PHP_EOL;'
 */

return [
    'site' => [
        'contact_email' => 'contact@akashaproduction.com',
    ],
    'admin_email' => 'contact@akashaproduction.com',
    // Laisser vide pour désactiver le panel admin.
    'admin_password_hash' => '',
    // Clé secrète Stripe (sk_live_... ou sk_test_...)
    'stripe_secret_key' => '',
    // Signing secret du webhook Stripe (whsec_...)
    'stripe_webhook_secret' => '',
];
