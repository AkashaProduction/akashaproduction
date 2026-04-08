<?php
/**
 * Domain extension pricing from o2switch (HT prices).
 * Selling price formula: ceil(HT * 1.20 + 1) — no decimals.
 * Auto-entrepreneur non assujetti TVA: cost = TTC, margin = +1 EUR, rounded up.
 */

function app_domain_ht_prices(): array
{
    return [
        'fr' => 10.75, 'com' => 14.90, 'net' => 15.75, 'org' => 15.75,
        'eu' => 10.75, 'be' => 10.75, 'it' => 11.00, 'ch' => 22.00,
        're' => 8.25, 'me' => 17.00, 'co' => 37.00, 'io' => 131.00,
        'pro' => 20.00, 'info' => 24.00, 'biz' => 22.00, 'lu' => 37.00,
        'tv' => 34.00, 'name' => 8.25, 'pm' => 8.25, 'pw' => 21.00,
        'tf' => 8.25, 'us' => 11.00, 'wf' => 8.25, 'yt' => 8.25,
        'bzh' => 50.00, 'audio' => 131.00, 'click' => 10.75, 'link' => 10.75,
        'pictures' => 10.75, 'rocks' => 23.75, 'academy' => 43.75,
        'accountant' => 27.00, 'agency' => 21.00, 'associates' => 29.00,
        'band' => 30.00, 'bargains' => 27.60, 'bid' => 25.00, 'bike' => 29.00,
        'blue' => 20.00, 'boutique' => 29.00, 'builders' => 29.00,
        'business' => 17.00, 'cab' => 29.00, 'cafe' => 43.75,
        'camera' => 47.00, 'camp' => 48.00, 'cards' => 29.00,
        'care' => 43.75, 'catering' => 29.00, 'center' => 43.75,
        'chat' => 29.00, 'cheap' => 27.60, 'christmas' => 40.00,
        'church' => 31.00, 'city' => 19.00, 'cleaning' => 47.00,
        'clothing' => 29.00, 'club' => 29.00, 'coffee' => 29.00,
        'community' => 29.00, 'company' => 17.00, 'computer' => 29.00,
        'consulting' => 48.00, 'cool' => 29.00, 'dance' => 21.50,
        'date' => 27.00, 'deals' => 29.00, 'democrat' => 29.00,
        'digital' => 31.00, 'direct' => 29.00, 'directory' => 19.00,
        'dog' => 47.00, 'domains' => 40.00, 'download' => 27.60,
        'education' => 32.00, 'email' => 21.00, 'equipment' => 19.00,
        'estate' => 29.00, 'events' => 53.75, 'exchange' => 29.00,
        'exposed' => 19.00, 'express' => 29.00, 'fail' => 29.00,
        'faith' => 27.00, 'family' => 53.75, 'farm' => 29.00,
        'fish' => 40.00, 'fitness' => 29.00, 'florist' => 29.00,
        'football' => 19.00, 'forsale' => 29.00, 'foundation' => 29.00,
        'futbol' => 12.00, 'fyi' => 19.00, 'gallery' => 26.00,
        'gift' => 19.00, 'gifts' => 29.00, 'glass' => 47.00,
        'graphics' => 19.00, 'gratis' => 19.00, 'gripe' => 29.00,
        'guide' => 29.00, 'guitars' => 131.00, 'guru' => 31.00,
        'haus' => 29.00, 'help' => 27.60, 'hosting' => 391.00,
        'house' => 53.75, 'immo' => 29.00, 'immobilien' => 29.00,
        'industries' => 43.00, 'ink' => 25.00, 'institute' => 26.00,
        'kim' => 18.00, 'kitchen' => 47.00, 'land' => 29.00,
        'life' => 27.60, 'lighting' => 19.00, 'limited' => 29.00,
        'live' => 24.00, 'loan' => 27.00, 'love' => 27.00,
        'management' => 19.00, 'marketing' => 29.00, 'mba' => 29.00,
        'media' => 53.75, 'men' => 27.00, 'moda' => 29.00,
        'money' => 29.00, 'network' => 35.00, 'ninja' => 35.00,
        'online' => 30.00, 'parts' => 29.00, 'party' => 27.00,
        'photo' => 37.00, 'photos' => 19.00, 'pics' => 27.00,
        'pink' => 18.00, 'place' => 21.25, 'plumbing' => 47.00,
        'plus' => 42.50, 'properties' => 29.00, 'property' => 131.00,
        'pub' => 29.00, 'racing' => 27.00, 'red' => 18.00,
        'reisen' => 19.00, 'rentals' => 42.50, 'repair' => 29.00,
        'report' => 19.00, 'rest' => 34.00, 'review' => 27.00,
        'reviews' => 57.00, 'run' => 19.00, 'sale' => 29.00,
        'sarl' => 29.00, 'school' => 29.00, 'science' => 27.00,
        'services' => 27.60, 'sexy' => 42.00, 'shoes' => 47.00,
        'show' => 42.50, 'singles' => 27.60, 'site' => 34.00,
        'soccer' => 19.00, 'social' => 29.00, 'solar' => 47.00,
        'solutions' => 30.00, 'space' => 27.00, 'studio' => 42.00,
        'style' => 29.00, 'supplies' => 19.00, 'supply' => 19.00,
        'support' => 21.00, 'systems' => 32.00, 'tattoo' => 42.00,
        'team' => 29.00, 'tech' => 53.00, 'tips' => 35.00,
        'today' => 21.00, 'tools' => 29.00, 'town' => 29.00,
        'toys' => 47.00, 'trade' => 21.50, 'training' => 29.00,
        'vacations' => 29.00, 'video' => 34.00, 'vision' => 29.00,
        'watch' => 40.00, 'webcam' => 20.00, 'website' => 27.00,
        'wiki' => 25.00, 'win' => 27.00, 'works' => 29.00,
        'world' => 39.00, 'wtf' => 29.00, 'xyz' => 21.00, 'zone' => 29.00,
    ];
}

function app_domain_selling_price(float $htPrice): int
{
    return (int) ceil($htPrice * 1.20 + 1);
}

function app_domain_selling_prices(): array
{
    $result = [];
    foreach (app_domain_ht_prices() as $tld => $ht) {
        $result[$tld] = app_domain_selling_price($ht);
    }
    asort($result);
    return $result;
}

/** Popular extensions shown first in the UI dropdown. */
function app_domain_popular_tlds(): array
{
    return ['fr', 'com', 'net', 'org', 'eu', 'be', 'pro', 'info', 'me', 'co', 'io', 'online', 'site', 'tech', 'world', 'immo', 'agency', 'digital', 'studio', 'boutique'];
}
