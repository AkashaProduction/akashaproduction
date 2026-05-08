<?php
declare(strict_types=1);

/**
 * Akasha Production — i18n helpers.
 *
 * Supported languages (9): fr (default) · en · es · ru · ar · zh · pt · pl · it
 * Storage:
 *   - data/content.json          → French overrides (back-compat with v2 monolingual storage)
 *   - data/content-{lang}.json   → overrides for non-French languages
 *
 * The active language is resolved in this order:
 *   1. ?lang=xx query parameter (also writes the cookie)
 *   2. akasha_lang cookie
 *   3. default = fr
 */

const APP_DEFAULT_LANG = 'fr';

function app_languages(): array
{
    return [
        'fr' => ['name' => 'Français',  'flag' => 'fr', 'dir' => 'ltr', 'native' => 'Français'],
        'en' => ['name' => 'English',   'flag' => 'gb', 'dir' => 'ltr', 'native' => 'English'],
        'es' => ['name' => 'Español',   'flag' => 'es', 'dir' => 'ltr', 'native' => 'Español'],
        'ru' => ['name' => 'Русский',   'flag' => 'ru', 'dir' => 'ltr', 'native' => 'Русский'],
        'ar' => ['name' => 'العربية',   'flag' => 'sa', 'dir' => 'rtl', 'native' => 'العربية'],
        'zh' => ['name' => '中文',       'flag' => 'cn', 'dir' => 'ltr', 'native' => '中文'],
        'pt' => ['name' => 'Português', 'flag' => 'pt', 'dir' => 'ltr', 'native' => 'Português'],
        'pl' => ['name' => 'Polski',    'flag' => 'pl', 'dir' => 'ltr', 'native' => 'Polski'],
        'it' => ['name' => 'Italiano',  'flag' => 'it', 'dir' => 'ltr', 'native' => 'Italiano'],
    ];
}

function app_lang_is_supported(string $lang): bool
{
    return array_key_exists($lang, app_languages());
}

function app_lang_dir(string $lang): string
{
    $langs = app_languages();
    return $langs[$lang]['dir'] ?? 'ltr';
}

function app_lang_resolve(): string
{
    // 1. Explicit ?lang=xx — also persists to cookie
    if (isset($_GET['lang'])) {
        $candidate = strtolower((string) $_GET['lang']);
        if (app_lang_is_supported($candidate)) {
            $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
            // Persist for one year
            setcookie('akasha_lang', $candidate, [
                'expires'  => time() + 31536000,
                'path'     => '/',
                'secure'   => $secure,
                'httponly' => false,
                'samesite' => 'Lax',
            ]);
            $_COOKIE['akasha_lang'] = $candidate;
            return $candidate;
        }
    }

    // 2. Cookie
    if (isset($_COOKIE['akasha_lang'])) {
        $candidate = strtolower((string) $_COOKIE['akasha_lang']);
        if (app_lang_is_supported($candidate)) {
            return $candidate;
        }
    }

    // 3. Default
    return APP_DEFAULT_LANG;
}

/**
 * Returns the storage filename for a given language.
 * French is special-cased to preserve back-compat with the v2 monolingual storage.
 */
function app_content_filename_for_lang(string $lang): string
{
    return $lang === APP_DEFAULT_LANG ? 'content.json' : "content-{$lang}.json";
}
