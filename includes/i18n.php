<?php
declare(strict_types=1);

function app_supported_langs(): array
{
    return ['fr', 'en', 'es'];
}

function app_lang(): string
{
    return $_SESSION['akasha_lang'] ?? 'fr';
}

function app_set_lang(string $lang): void
{
    if (in_array($lang, app_supported_langs(), true)) {
        $_SESSION['akasha_lang'] = $lang;
    }
}

function app_translations(): array
{
    static $cache = [];
    $lang = app_lang();
    if (!isset($cache[$lang])) {
        $path = __DIR__ . '/../lang/' . $lang . '.json';
        if (file_exists($path)) {
            $raw = file_get_contents($path);
            $cache[$lang] = json_decode($raw ?: '{}', true) ?? [];
        } else {
            $fallback = __DIR__ . '/../lang/fr.json';
            $raw = file_exists($fallback) ? file_get_contents($fallback) : '{}';
            $cache[$lang] = json_decode($raw ?: '{}', true) ?? [];
        }
    }
    return $cache[$lang];
}

function t(string $key, array $params = []): string
{
    $translations = app_translations();
    $parts = explode('.', $key);
    $value = $translations;
    foreach ($parts as $part) {
        if (!is_array($value) || !isset($value[$part])) {
            return $key;
        }
        $value = $value[$part];
    }
    if (!is_string($value)) {
        return $key;
    }
    foreach ($params as $k => $v) {
        $value = str_replace(':' . $k, (string) $v, $value);
    }
    return $value;
}

function ta(string $key): array
{
    $translations = app_translations();
    $parts = explode('.', $key);
    $value = $translations;
    foreach ($parts as $part) {
        if (!is_array($value) || !isset($value[$part])) {
            return [];
        }
        $value = $value[$part];
    }
    return is_array($value) ? $value : [];
}

function app_lang_flag(string $lang): string
{
    $flags = [
        'fr' => '<svg viewBox="0 0 640 480" width="20" height="15"><rect width="213.3" height="480" fill="#002654"/><rect x="213.3" width="213.4" height="480" fill="#fff"/><rect x="426.7" width="213.3" height="480" fill="#ce1126"/></svg>',
        'en' => '<svg viewBox="0 0 640 480" width="20" height="15"><rect width="640" height="480" fill="#012169"/><path d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 302 81 480H0v-60l239-178L0 64V0z" fill="#fff"/><path d="m424 281 216 159v40L369 281zm-184 20 6 35L54 480H0zM640 0v3L391 191l2-44L590 0zM0 0l239 176h-60L0 42z" fill="#C8102E"/><path d="M241 0v480h160V0zM0 160v160h640V160z" fill="#fff"/><path d="M0 193v96h640v-96zM273 0v480h96V0z" fill="#C8102E"/></svg>',
        'es' => '<svg viewBox="0 0 640 480" width="20" height="15"><rect width="640" height="480" fill="#c60b1e"/><rect y="120" width="640" height="240" fill="#ffc400"/></svg>',
    ];
    return $flags[$lang] ?? '';
}

function app_lang_labels(): array
{
    return [
        'fr' => 'Français',
        'en' => 'English',
        'es' => 'Español',
    ];
}
