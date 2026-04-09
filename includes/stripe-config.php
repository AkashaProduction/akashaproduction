<?php
/**
 * Stripe product/price/coupon IDs (public, non-secret).
 * Secret key goes in runtime-config.php (gitignored).
 */
return [
    'public_key' => 'pk_live_KQTcKVNw95CYdZtebOJUxdZz',
    'products' => [
        'showcase'       => 'prod_UIfzCB8f2XpTEW',
        'complex'        => 'prod_UIfz0yFPnfD5Wo',
        'custom'         => 'prod_UIfzQwJr1dfwGu',
        'shared-monthly' => 'prod_UIfznoqsddqvp6',
        'shared-yearly'  => 'prod_UIfzr4jeuSgqxZ',
        'vps'            => 'prod_UIfzixV71V1VFL',
        'pack-vitrine'   => 'prod_UIfzIKn0KOkKGS',
        'pack-complexe'  => 'prod_UIfzEWiT9lTCao',
        'domain'         => 'prod_UIfzu1wbCmCbRF',
    ],
    'prices' => [
        'showcase'       => 'price_1TK4kmKUPS0HBuaZJvNgpTwM',
        'complex'        => 'price_1TK4knKUPS0HBuaZWQDHsToD',
        'shared-monthly' => 'price_1TK4koKUPS0HBuaZxwlWudbW',
        'shared-yearly'  => 'price_1TK4kpKUPS0HBuaZf8tMpvXG',
        'vps'            => 'price_1TK4krKUPS0HBuaZCffMZ7pI',
        'pack-vitrine'   => 'price_1TK4ksKUPS0HBuaZAjxVmoY8',
        'pack-complexe'  => 'price_1TK4ktKUPS0HBuaZEPwgM3T3',
    ],
    'packs' => [
        'showcase:shared-yearly' => ['product' => 'pack-vitrine',  'amount' => 12000],
        'complex:shared-yearly'  => ['product' => 'pack-complexe', 'amount' => 55000],
        'showcase:vps'           => ['amount' => 23000],
        'complex:vps'            => ['amount' => 67500],
    ],
    'coupons' => [
        'showcase:shared-yearly' => 'db5Nqp9d',
        'complex:shared-yearly'  => 'Az4oLKI3',
        'showcase:vps'           => 'nQnYZxx0',
        'complex:vps'            => 'kjaeonVL',
    ],
    'amounts' => [
        'showcase' => 5000, 'complex' => 50000,
        'shared-monthly' => 800, 'shared-yearly' => 8800, 'vps' => 20000,
    ],
];
