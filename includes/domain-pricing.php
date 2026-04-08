<?php
/**
 * Domain extension pricing from o2switch (HT prices).
 * Selling price formula: ceil(HT * 1.20 + 1) — no decimals.
 * Auto-entrepreneur non assujetti TVA: cost = TTC, margin = +1 EUR, rounded up.
 */

/**
 * Final selling prices per extension (EUR/an).
 * Calculated once: ceil(o2switch_HT * 1.20 + 1), no decimals.
 */
function app_domain_selling_prices(): array
{
    return [
        're' => 11, 'name' => 11, 'pm' => 11, 'tf' => 11, 'wf' => 11, 'yt' => 11,
        'click' => 14, 'link' => 14, 'pictures' => 14, 'fr' => 14, 'eu' => 14, 'be' => 14,
        'it' => 15, 'us' => 15, 'futbol' => 16,
        'com' => 19,
        'net' => 20, 'org' => 20,
        'business' => 22, 'company' => 22, 'me' => 22, 'kim' => 22, 'pink' => 22, 'red' => 22,
        'city' => 24, 'directory' => 24, 'equipment' => 24, 'exposed' => 24, 'football' => 24,
        'fyi' => 24, 'gift' => 24, 'graphics' => 24, 'gratis' => 24, 'lighting' => 24,
        'management' => 24, 'photos' => 24, 'reisen' => 24, 'report' => 24, 'run' => 24,
        'soccer' => 24, 'supplies' => 24, 'supply' => 24,
        'blue' => 25, 'pro' => 25, 'pw' => 26,
        'agency' => 27, 'email' => 27, 'support' => 27, 'today' => 27, 'xyz' => 27,
        'dance' => 27, 'trade' => 27, 'place' => 27,
        'biz' => 28, 'ch' => 28, 'webcam' => 25,
        'rocks' => 30, 'bid' => 31, 'ink' => 31,
        'info' => 30, 'live' => 30,
        'accountant' => 34, 'date' => 34, 'faith' => 34, 'loan' => 34, 'love' => 34,
        'men' => 34, 'party' => 34, 'pics' => 34, 'racing' => 34, 'review' => 34,
        'science' => 34, 'space' => 34, 'website' => 34, 'win' => 34,
        'bargains' => 35, 'cheap' => 35, 'download' => 35, 'help' => 35,
        'life' => 35, 'services' => 35, 'singles' => 35, 'wiki' => 31,
        'associates' => 36, 'bike' => 36, 'boutique' => 36, 'builders' => 36,
        'cab' => 36, 'cards' => 36, 'catering' => 36, 'chat' => 36, 'clothing' => 36,
        'club' => 36, 'coffee' => 36, 'community' => 36, 'computer' => 36,
        'cool' => 36, 'deals' => 36, 'democrat' => 36, 'direct' => 36,
        'estate' => 36, 'exchange' => 36, 'express' => 36, 'fail' => 36,
        'farm' => 36, 'fitness' => 36, 'florist' => 36, 'forsale' => 36,
        'foundation' => 36, 'gifts' => 36, 'gripe' => 36, 'guide' => 36,
        'haus' => 36, 'immo' => 36, 'immobilien' => 36, 'land' => 36,
        'limited' => 36, 'marketing' => 36, 'mba' => 36, 'moda' => 36,
        'money' => 36, 'parts' => 36, 'properties' => 36, 'pub' => 36,
        'repair' => 36, 'sale' => 36, 'sarl' => 36, 'school' => 36,
        'social' => 36, 'style' => 36, 'team' => 36, 'tools' => 36,
        'town' => 36, 'training' => 36, 'vacations' => 36, 'vision' => 36,
        'works' => 36, 'wtf' => 36, 'zone' => 36,
        'band' => 37, 'online' => 37, 'solutions' => 37,
        'gallery' => 33, 'institute' => 33,
        'church' => 39, 'guru' => 39,
        'digital' => 39, 'systems' => 40, 'education' => 40,
        'network' => 43, 'ninja' => 43, 'tips' => 43,
        'rest' => 42, 'site' => 42, 'tv' => 42, 'video' => 42,
        'christmas' => 49, 'domains' => 49, 'fish' => 49, 'watch' => 49,
        'co' => 46, 'lu' => 46, 'photo' => 46,
        'consulting' => 59, 'camp' => 59,
        'academy' => 54, 'cafe' => 54, 'care' => 54, 'center' => 54,
        'rentals' => 52, 'show' => 52, 'plus' => 52,
        'studio' => 52, 'sexy' => 52, 'tattoo' => 52,
        'industries' => 53,
        'camera' => 58, 'cleaning' => 58, 'dog' => 58, 'glass' => 58,
        'kitchen' => 58, 'plumbing' => 58, 'shoes' => 58, 'solar' => 58, 'toys' => 58,
        'bzh' => 61,
        'events' => 66, 'family' => 66, 'house' => 66, 'media' => 66,
        'tech' => 65,
        'reviews' => 70,
        'audio' => 159, 'guitars' => 159, 'io' => 159, 'property' => 159,
        'hosting' => 471,
        'world' => 48,
    ];
}

/** Popular extensions shown first in the UI dropdown. */
function app_domain_popular_tlds(): array
{
    return ['fr', 'com', 'net', 'org', 'eu', 'be', 'pro', 'info', 'me', 'co', 'io', 'online', 'site', 'tech', 'world', 'immo', 'agency', 'digital', 'studio', 'boutique'];
}
