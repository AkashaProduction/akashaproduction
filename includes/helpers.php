<?php
declare(strict_types=1);

function app_config(): array
{
    global $config;
    return $config;
}

function app_storage_path(string $file): string
{
    $storageDir = __DIR__ . '/../storage';
    if (!is_dir($storageDir)) {
        mkdir($storageDir, 0775, true);
    }
    return $storageDir . '/' . $file;
}

function app_read_json(string $file): array
{
    $path = app_storage_path($file);
    if (!file_exists($path)) {
        app_atomic_write($path, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function app_write_json(string $file, array $data): void
{
    $path = app_storage_path($file);
    app_atomic_write($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

function app_atomic_write(string $path, string $contents): void
{
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $tmp = tempnam($dir, '.tmp-');
    if ($tmp === false) {
        file_put_contents($path, $contents, LOCK_EX);
        return;
    }
    file_put_contents($tmp, $contents);
    if (!rename($tmp, $path)) {
        @unlink($tmp);
        file_put_contents($path, $contents, LOCK_EX);
        return;
    }
    @chmod($path, 0664);
}

function app_json_mutate(string $file, callable $mutator): void
{
    $path = app_storage_path($file);
    if (!file_exists($path)) {
        app_atomic_write($path, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    $fp = fopen($path, 'c+');
    if ($fp === false) {
        app_log('error', 'json_mutate_open_failed', ['file' => $file]);
        return;
    }
    try {
        if (!flock($fp, LOCK_EX)) {
            app_log('error', 'json_mutate_lock_failed', ['file' => $file]);
            return;
        }
        $raw = stream_get_contents($fp) ?: '[]';
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = [];
        }
        $next = $mutator($data);
        if (!is_array($next)) {
            $next = $data;
        }
        $encoded = json_encode($next, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $encoded);
        fflush($fp);
        flock($fp, LOCK_UN);
    } finally {
        fclose($fp);
    }
}

function app_append_json(string $file, array $record): void
{
    app_json_mutate($file, static function (array $data) use ($record): array {
        array_unshift($data, $record);
        return $data;
    });
}

function app_flash(string $type, string $message): void
{
    $_SESSION['akasha_flash'] = ['type' => $type, 'message' => $message];
}

function app_pull_flash(): ?array
{
    if (!isset($_SESSION['akasha_flash'])) {
        return null;
    }
    $flash = $_SESSION['akasha_flash'];
    unset($_SESSION['akasha_flash']);
    return $flash;
}

function app_redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

function app_uuid(): string
{
    return bin2hex(random_bytes(16));
}

function app_now(): string
{
    return date(DATE_ATOM);
}

/* ------------------------------------------------------------------ */
/* Settings (admin-editable runtime values in storage/settings.json)  */
/* ------------------------------------------------------------------ */

function app_settings_path(): string
{
    return __DIR__ . '/../storage/settings.json';
}

function app_settings_load(): array
{
    $path = app_settings_path();
    if (!file_exists($path)) {
        return [];
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}

function app_settings_save(array $partial): void
{
    $path = app_settings_path();
    $dir = dirname($path);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fp = fopen($path, 'c+');
    if ($fp === false) {
        app_log('error', 'settings_open_failed', []);
        return;
    }
    try {
        if (!flock($fp, LOCK_EX)) {
            return;
        }
        $raw = stream_get_contents($fp) ?: '{}';
        $current = json_decode($raw, true);
        if (!is_array($current)) {
            $current = [];
        }
        $merged = array_replace_recursive($current, $partial);
        $encoded = json_encode($merged, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $encoded);
        fflush($fp);
        flock($fp, LOCK_UN);
    } finally {
        fclose($fp);
    }
    @chmod($path, 0600);
    // Rafraîchir le $config global pour refléter la valeur immédiatement.
    global $config;
    if (is_array($config)) {
        $config = array_replace_recursive($config, $partial);
    }
}

function app_mask_secret(string $value, int $visible = 4): string
{
    $len = strlen($value);
    if ($len === 0) {
        return '';
    }
    if ($len <= $visible) {
        return str_repeat('•', $len);
    }
    return str_repeat('•', max(4, $len - $visible)) . substr($value, -$visible);
}

/* ------------------------------------------------------------------ */
/* Logging                                                            */
/* ------------------------------------------------------------------ */

function app_log(string $level, string $event, array $context = []): void
{
    $dir = __DIR__ . '/../storage/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $line = json_encode([
        'ts' => app_now(),
        'level' => $level,
        'event' => $event,
        'context' => $context,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
        'ua' => substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 200),
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($line === false) {
        return;
    }
    @file_put_contents($dir . '/app.log', $line . "\n", FILE_APPEND | LOCK_EX);
}

/* ------------------------------------------------------------------ */
/* Rate limiting (session-based)                                      */
/* ------------------------------------------------------------------ */

function app_rate_limit(string $key, int $max, int $windowSeconds): bool
{
    $bucket = $_SESSION['akasha_rate'][$key] ?? [];
    $now = time();
    $bucket = array_values(array_filter($bucket, static fn ($t) => ($t + $windowSeconds) > $now));
    if (count($bucket) >= $max) {
        $_SESSION['akasha_rate'][$key] = $bucket;
        return false;
    }
    $bucket[] = $now;
    $_SESSION['akasha_rate'][$key] = $bucket;
    return true;
}

function app_rate_reset(string $key): void
{
    unset($_SESSION['akasha_rate'][$key]);
}

/* ------------------------------------------------------------------ */
/* CSRF                                                               */
/* ------------------------------------------------------------------ */

function app_csrf_token(): string
{
    if (empty($_SESSION['akasha_csrf'])) {
        $_SESSION['akasha_csrf'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['akasha_csrf'];
}

function app_csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(app_csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function app_csrf_check(?string $token = null): bool
{
    $provided = $token ?? (string) ($_POST['_csrf'] ?? '');
    $expected = (string) ($_SESSION['akasha_csrf'] ?? '');
    if ($provided === '' || $expected === '') {
        return false;
    }
    return hash_equals($expected, $provided);
}

function app_csrf_enforce(): void
{
    if (!app_csrf_check()) {
        app_log('warning', 'csrf_rejected', ['uri' => $_SERVER['REQUEST_URI'] ?? '']);
        http_response_code(400);
        echo 'Requête invalide (jeton CSRF).';
        exit;
    }
}

/* ------------------------------------------------------------------ */
/* Email header sanitization + validation                             */
/* ------------------------------------------------------------------ */

function app_clean_header(string $value): string
{
    return trim(preg_replace('/[\r\n\x00]+/', ' ', $value));
}

function app_valid_email(string $email): bool
{
    $email = trim($email);
    if ($email === '' || strlen($email) > 254) {
        return false;
    }
    if (preg_match('/[\r\n\x00]/', $email)) {
        return false;
    }
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/* ------------------------------------------------------------------ */
/* Misc utility                                                       */
/* ------------------------------------------------------------------ */

function app_screenshot_url(string $url): string
{
    return 'https://s.wordpress.com/mshots/v1/' . rawurlencode($url) . '?w=1200';
}

function app_money(float $amount): string
{
    return number_format($amount, 0, ',', ' ') . ' €';
}

function app_nav_href(string $key): string
{
    return (string) (app_config()['navigation'][$key]['href'] ?? '/');
}

function app_department_label(string $key): string
{
    return (string) (app_config()['support']['departments'][$key] ?? $key);
}

function app_compute_total(string $creation, string $hosting, bool $includeCustomDomain): float
{
    $catalog = app_config()['catalog'];
    $packKey = $creation . ':' . $hosting;
    if (isset($catalog['pack_prices'][$packKey])) {
        $total = (float) $catalog['pack_prices'][$packKey];
    } else {
        $total = 0;
        if (isset($catalog['creation'][$creation])) {
            $total += (float) $catalog['creation'][$creation]['amount'];
        }
        if (isset($catalog['hosting'][$hosting])) {
            $total += (float) $catalog['hosting'][$hosting]['amount'];
        }
    }

    if ($includeCustomDomain) {
        $total += (float) $catalog['domain']['custom-domain']['amount'];
    }

    return $total;
}

/* ------------------------------------------------------------------ */
/* Mail                                                               */
/* ------------------------------------------------------------------ */

function app_send_mail_to(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $to = app_clean_header($to);
    $subject = app_clean_header($subject);
    if (!app_valid_email($to)) {
        app_log('error', 'mail_invalid_to', ['to' => $to]);
        return false;
    }
    $from = (string) (app_config()['site']['contact_email'] ?? 'noreply@akashaproduction.com');
    if (!app_valid_email($from)) {
        $from = 'noreply@akashaproduction.com';
    }
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=utf-8',
        'From: Akasha Production <' . $from . '>',
    ];
    if ($replyTo !== null && $replyTo !== '' && app_valid_email($replyTo)) {
        $headers[] = 'Reply-To: ' . app_clean_header($replyTo);
    }
    $ok = mail($to, $subject, $body, implode("\r\n", $headers));
    if (!$ok) {
        app_log('error', 'mail_failed', ['to' => $to, 'subject' => substr($subject, 0, 60)]);
    }
    return $ok;
}

function app_send_mail(string $subject, string $body, ?string $replyTo = null): bool
{
    return app_send_mail_to((string) app_config()['site']['contact_email'], $subject, $body, $replyTo);
}

/* ------------------------------------------------------------------ */
/* Uploads                                                            */
/* ------------------------------------------------------------------ */

function app_handle_uploads(string $field): array
{
    if (!isset($_FILES[$field])) {
        return [];
    }

    $allowed = [
        'pdf' => ['application/pdf'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'webp' => ['image/webp'],
    ];
    $maxBytes = 8 * 1024 * 1024; // 8 MB
    $saved = [];
    $uploadDir = app_storage_path('uploads');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }
    $finfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;

    $files = $_FILES[$field];
    $count = is_array($files['name']) ? count($files['name']) : 0;
    for ($index = 0; $index < $count; $index++) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }
        if (($files['size'][$index] ?? 0) > $maxBytes) {
            app_log('warning', 'upload_too_large', ['size' => $files['size'][$index] ?? 0]);
            continue;
        }

        $original = (string) $files['name'][$index];
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!isset($allowed[$extension])) {
            continue;
        }

        $tmp = (string) $files['tmp_name'][$index];
        if ($finfo !== null) {
            $mime = (string) $finfo->file($tmp);
            if ($mime !== '' && !in_array($mime, $allowed[$extension], true)) {
                app_log('warning', 'upload_mime_mismatch', ['ext' => $extension, 'mime' => $mime]);
                continue;
            }
        }

        $safeName = date('YmdHis') . '-' . app_uuid() . '.' . $extension;
        $target = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($tmp, $target)) {
            @chmod($target, 0644);
            $saved[] = [
                'original' => preg_replace('/[\r\n\x00]/', '', $original),
                'stored' => $safeName,
            ];
        }
    }

    return $saved;
}

/* ------------------------------------------------------------------ */
/* Orders / Tickets                                                   */
/* ------------------------------------------------------------------ */

function app_orders_by_email(string $email): array
{
    return array_values(array_filter(app_read_json('orders.json'), static function (array $record) use ($email): bool {
        return strtolower((string) ($record['customer']['email'] ?? '')) === strtolower($email);
    }));
}

function app_tickets_by_email(string $email): array
{
    return array_values(array_filter(app_read_json('tickets.json'), static function (array $record) use ($email): bool {
        return strtolower((string) ($record['customer']['email'] ?? '')) === strtolower($email);
    }));
}

function app_create_ticket(array $payload): array
{
    $record = [
        'id' => app_uuid(),
        'created_at' => app_now(),
        'updated_at' => app_now(),
        'status' => 'open',
        'department' => $payload['department'],
        'priority' => $payload['priority'],
        'subject' => $payload['subject'],
        'order_id' => $payload['order_id'] ?: null,
        'customer' => [
            'name' => $payload['name'],
            'email' => $payload['email'],
        ],
        'thread' => [[
            'id' => app_uuid(),
            'created_at' => app_now(),
            'author_type' => 'customer',
            'author_label' => $payload['name'],
            'message' => $payload['message'],
        ]],
    ];
    app_append_json('tickets.json', $record);
    return $record;
}

function app_add_ticket_reply(string $ticketId, string $authorType, string $authorLabel, string $message, ?string $email = null): bool
{
    $ok = false;
    app_json_mutate('tickets.json', function (array $tickets) use ($ticketId, $authorType, $authorLabel, $message, $email, &$ok): array {
        foreach ($tickets as &$ticket) {
            if (($ticket['id'] ?? '') !== $ticketId) {
                continue;
            }
            if ($authorType === 'customer' && strtolower((string) ($ticket['customer']['email'] ?? '')) !== strtolower((string) $email)) {
                return $tickets;
            }
            $ticket['thread'][] = [
                'id' => app_uuid(),
                'created_at' => app_now(),
                'author_type' => $authorType,
                'author_label' => $authorLabel,
                'message' => $message,
            ];
            $ticket['updated_at'] = app_now();
            $ticket['status'] = $authorType === 'admin' ? 'answered' : 'open';
            $ok = true;
            return $tickets;
        }
        return $tickets;
    });
    return $ok;
}

function app_update_ticket(string $ticketId, string $status, string $priority): bool
{
    $ok = false;
    app_json_mutate('tickets.json', function (array $tickets) use ($ticketId, $status, $priority, &$ok): array {
        foreach ($tickets as &$ticket) {
            if (($ticket['id'] ?? '') !== $ticketId) {
                continue;
            }
            $ticket['status'] = $status;
            $ticket['priority'] = $priority;
            $ticket['updated_at'] = app_now();
            $ok = true;
            return $tickets;
        }
        return $tickets;
    });
    return $ok;
}

function app_update_order_status(string $orderId, string $status, array $extra = []): bool
{
    $ok = false;
    app_json_mutate('orders.json', function (array $orders) use ($orderId, $status, $extra, &$ok): array {
        foreach ($orders as &$order) {
            if (($order['id'] ?? '') !== $orderId) {
                continue;
            }
            $order['status'] = $status;
            $order['updated_at'] = app_now();
            foreach ($extra as $k => $v) {
                $order[$k] = $v;
            }
            $ok = true;
            return $orders;
        }
        return $orders;
    });
    return $ok;
}

/* ------------------------------------------------------------------ */
/* Admin authentication                                               */
/* ------------------------------------------------------------------ */

function app_admin_password_hash(): string
{
    $config = app_config();
    $hash = (string) ($config['admin_password_hash'] ?? '');
    if ($hash !== '') {
        return $hash;
    }
    // Back-compat: legacy plaintext. Treat as disabled unless explicitly set.
    $legacy = (string) ($config['admin_password'] ?? '');
    if ($legacy === '') {
        return '';
    }
    // One-shot migration: compute a hash for the legacy value so password_verify works.
    return password_hash($legacy, PASSWORD_DEFAULT);
}

function app_admin_is_enabled(): bool
{
    return app_admin_password_hash() !== '';
}

function app_is_admin_email(string $email): bool
{
    $normalized = strtolower(trim($email));
    if ($normalized === '') {
        return false;
    }

    $config = app_config();
    $candidates = $config['admin_aliases'] ?? [];
    if (!in_array((string) ($config['admin_email'] ?? ''), $candidates, true)) {
        $candidates[] = (string) ($config['admin_email'] ?? '');
    }

    $normalizedCandidates = array_map(static fn (string $value): string => strtolower(trim($value)), $candidates);
    return in_array($normalized, $normalizedCandidates, true);
}

function app_admin_login(string $email, string $password): bool
{
    if (!app_admin_is_enabled()) {
        return false;
    }
    if (!app_is_admin_email($email)) {
        app_log('info', 'admin_login_wrong_email', ['email' => $email]);
        return false;
    }
    $hash = app_admin_password_hash();
    if (!password_verify($password, $hash)) {
        app_log('info', 'admin_login_wrong_password', ['email' => $email]);
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['akasha_admin'] = [
        'email' => strtolower(trim($email)),
        'logged_at' => time(),
    ];
    app_log('info', 'admin_login_ok', ['email' => $email]);
    return true;
}

function app_admin_logged_in(): bool
{
    return !empty($_SESSION['akasha_admin']);
}

function app_admin_logout(): void
{
    unset($_SESSION['akasha_admin']);
    session_regenerate_id(true);
}

/* ------------------------------------------------------------------ */
/* Customer authentication (OTP by email)                             */
/* ------------------------------------------------------------------ */

function app_customer_logged_in(): bool
{
    return !empty($_SESSION['akasha_customer']['email']);
}

function app_customer_email(): string
{
    return (string) ($_SESSION['akasha_customer']['email'] ?? '');
}

function app_customer_name(): string
{
    return (string) ($_SESSION['akasha_customer']['name'] ?? '');
}

function app_customer_logout(): void
{
    unset($_SESSION['akasha_customer'], $_SESSION['akasha_customer_otp']);
    session_regenerate_id(true);
}

function app_customer_issue_otp(string $email, string $name = ''): string
{
    $code = (string) random_int(100000, 999999);
    $_SESSION['akasha_customer_otp'] = [
        'email' => strtolower(trim($email)),
        'name' => trim($name),
        'code_hash' => password_hash($code, PASSWORD_DEFAULT),
        'expires_at' => time() + 600, // 10 min
        'attempts' => 0,
    ];
    return $code;
}

function app_customer_pending_email(): string
{
    return (string) ($_SESSION['akasha_customer_otp']['email'] ?? '');
}

function app_customer_verify_otp(string $code): bool
{
    $otp = $_SESSION['akasha_customer_otp'] ?? null;
    if (!is_array($otp)) {
        return false;
    }
    if (($otp['expires_at'] ?? 0) < time()) {
        unset($_SESSION['akasha_customer_otp']);
        return false;
    }
    if (($otp['attempts'] ?? 0) >= 5) {
        unset($_SESSION['akasha_customer_otp']);
        return false;
    }
    $_SESSION['akasha_customer_otp']['attempts'] = ($otp['attempts'] ?? 0) + 1;
    if (!password_verify(trim($code), (string) $otp['code_hash'])) {
        return false;
    }
    session_regenerate_id(true);
    $_SESSION['akasha_customer'] = [
        'email' => $otp['email'],
        'name' => $otp['name'],
        'logged_at' => time(),
    ];
    unset($_SESSION['akasha_customer_otp']);
    return true;
}

/* ------------------------------------------------------------------ */
/* Page title                                                         */
/* ------------------------------------------------------------------ */

function app_page_title(string $title): string
{
    return $title . ' | ' . app_config()['site']['name'];
}
