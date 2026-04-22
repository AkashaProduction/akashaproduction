<?php

/**
 * Copier ce fichier en includes/runtime-config.php (gitignored) et remplir.
 *
 * Pour générer un hash admin :
 *   php -r 'echo password_hash("MON_MOT_DE_PASSE", PASSWORD_DEFAULT);'
 *
 * Les clés Stripe (stripe_secret_key, stripe_webhook_secret) se règlent
 * désormais directement depuis le panel administrateur (/admin) — elles sont
 * persistées dans storage/settings.json protégé par htaccess.
 */

return [
    // Hash argon2id/bcrypt du mot de passe administrateur.
    // Vide = panel admin désactivé.
    'admin_password_hash' => '',
];
