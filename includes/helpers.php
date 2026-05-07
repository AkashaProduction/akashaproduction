<?php
declare(strict_types=1);

function app_config(): array
{
    global $config;
    return $config;
}

function app_data_path(string $file): string
{
    $config = app_config();
    return rtrim($config['paths']['data'], '/') . '/' . $file;
}

function app_uploads_path(string $file = ''): string
{
    $config = app_config();
    $base = rtrim($config['paths']['uploads'], '/');
    return $file === '' ? $base : $base . '/' . $file;
}

function app_uploads_url(string $file = ''): string
{
    $config = app_config();
    $base = rtrim($config['paths']['uploads_url_path'], '/');
    return $file === '' ? $base : $base . '/' . $file;
}

function app_read_json(string $file, $default = []): array
{
    $path = app_data_path($file);
    if (!file_exists($path)) {
        return is_array($default) ? $default : [];
    }
    $raw = file_get_contents($path);
    if ($raw === false || $raw === '') {
        return is_array($default) ? $default : [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : (is_array($default) ? $default : []);
}

function app_write_json(string $file, array $data): bool
{
    $path = app_data_path($file);
    $dir = dirname($path);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $payload = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    return file_put_contents($path, $payload, LOCK_EX) !== false;
}

function app_uuid(): string
{
    return bin2hex(random_bytes(8));
}

function app_now(): string
{
    return date(DATE_ATOM);
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

function app_csrf_token(): string
{
    if (empty($_SESSION['akasha_csrf'])) {
        $_SESSION['akasha_csrf'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['akasha_csrf'];
}

function app_check_csrf(string $token): bool
{
    return !empty($_SESSION['akasha_csrf']) && hash_equals($_SESSION['akasha_csrf'], $token);
}

function app_admin_login(string $email, string $password): bool
{
    $config = app_config();
    $expectedEmail = strtolower(trim((string) ($config['admin']['email'] ?? '')));
    $hash = (string) ($config['admin']['password_hash'] ?? '');
    if ($expectedEmail === '' || $hash === '') {
        return false;
    }
    if (strtolower(trim($email)) !== $expectedEmail) {
        return false;
    }
    if (!password_verify($password, $hash)) {
        return false;
    }
    $_SESSION['akasha_admin'] = true;
    session_regenerate_id(true);
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

function app_send_mail(array $recipients, string $subject, string $body, ?string $replyTo = null): bool
{
    $config = app_config();
    $from = (string) ($config['site']['contact_email'] ?? 'noreply@akashaproduction.com');
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/plain; charset=utf-8',
        'From: Akasha Production <' . $from . '>',
    ];
    if ($replyTo) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    $ok = true;
    foreach ($recipients as $to) {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            continue;
        }
        $ok = @mail($to, $subject, $body, implode("\r\n", $headers)) && $ok;
    }
    return $ok;
}

function app_save_uploaded_image(string $field, string $prefix = 'project'): ?array
{
    if (empty($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];

    $tmp = (string) ($_FILES[$field]['tmp_name'] ?? '');
    if (!is_uploaded_file($tmp)) {
        return null;
    }
    $size = (int) ($_FILES[$field]['size'] ?? 0);
    if ($size <= 0 || $size > 6 * 1024 * 1024) {
        return null;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? (string) finfo_file($finfo, $tmp) : (string) ($_FILES[$field]['type'] ?? '');
    if ($finfo) {
        finfo_close($finfo);
    }

    if (!isset($allowed[$mime])) {
        return null;
    }
    $ext = $allowed[$mime];

    $dir = app_uploads_path();
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    $name = $prefix . '-' . date('Ymd-His') . '-' . substr(app_uuid(), 0, 6) . '.' . $ext;
    $target = $dir . '/' . $name;
    if (!@move_uploaded_file($tmp, $target)) {
        return null;
    }
    @chmod($target, 0644);

    return [
        'name'     => $name,
        'mime'     => $mime,
        'size'     => $size,
        'path'     => $target,
        'url_path' => app_uploads_url($name),
    ];
}

function app_e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_clean(string $value): string
{
    return trim(strip_tags($value));
}
