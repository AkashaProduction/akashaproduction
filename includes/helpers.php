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
        file_put_contents($path, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
    }
    $raw = file_get_contents($path);
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
}

function app_write_json(string $file, array $data): void
{
    file_put_contents($path = app_storage_path($file), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function app_append_json(string $file, array $record): void
{
    $data = app_read_json($file);
    array_unshift($data, $record);
    app_write_json($file, $data);
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

function app_send_mail_to(string $to, string $subject, string $body, ?string $replyTo = null): bool
{
    $from = (string) (app_config()['site']['contact_email'] ?? 'noreply@akashaproduction.com');
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=utf-8',
        'From: Akasha Production <' . $from . '>',
    ];
    if ($replyTo) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    return @mail($to, $subject, $body, implode("\r\n", $headers));
}

function app_send_mail(string $subject, string $body, ?string $replyTo = null): bool
{
    return app_send_mail_to((string) app_config()['site']['contact_email'], $subject, $body, $replyTo);
}

function app_handle_uploads(string $field): array
{
    if (!isset($_FILES[$field])) {
        return [];
    }

    $allowed = ['pdf', 'docx', 'jpg', 'jpeg', 'webp'];
    $saved = [];
    $uploadDir = app_storage_path('uploads');
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0775, true);
    }

    $files = $_FILES[$field];
    $count = is_array($files['name']) ? count($files['name']) : 0;
    for ($index = 0; $index < $count; $index++) {
        if (($files['error'][$index] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            continue;
        }

        $original = (string) $files['name'][$index];
        $extension = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowed, true)) {
            continue;
        }

        $safeName = date('YmdHis') . '-' . preg_replace('/[^a-zA-Z0-9._-]/', '-', $original);
        $target = $uploadDir . '/' . $safeName;
        if (move_uploaded_file($files['tmp_name'][$index], $target)) {
            $saved[] = [
                'original' => $original,
                'stored' => $safeName,
            ];
        }
    }

    return $saved;
}

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
    $tickets = app_read_json('tickets.json');
    foreach ($tickets as &$ticket) {
        if (($ticket['id'] ?? '') !== $ticketId) {
            continue;
        }
        if ($authorType === 'customer' && strtolower((string) ($ticket['customer']['email'] ?? '')) !== strtolower((string) $email)) {
            return false;
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
        app_write_json('tickets.json', $tickets);
        return true;
    }
    return false;
}

function app_update_ticket(string $ticketId, string $status, string $priority): bool
{
    $tickets = app_read_json('tickets.json');
    foreach ($tickets as &$ticket) {
        if (($ticket['id'] ?? '') !== $ticketId) {
            continue;
        }
        $ticket['status'] = $status;
        $ticket['priority'] = $priority;
        $ticket['updated_at'] = app_now();
        app_write_json('tickets.json', $tickets);
        return true;
    }
    return false;
}

function app_admin_is_enabled(): bool
{
    $config = app_config();
    return !empty($config['admin_password']);
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
    $config = app_config();
    if (!app_admin_is_enabled()) {
        return false;
    }
    if ($email === $config['admin_email'] && hash_equals((string) $config['admin_password'], $password)) {
        $_SESSION['akasha_admin'] = true;
        return true;
    }
    return false;
}

function app_admin_logged_in(): bool
{
    return !empty($_SESSION['akasha_admin']);
}

function app_admin_logout(): void
{
    unset($_SESSION['akasha_admin']);
}

function app_page_title(string $title): string
{
    return $title . ' | ' . app_config()['site']['name'];
}
