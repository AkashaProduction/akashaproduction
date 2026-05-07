<?php
declare(strict_types=1);

require __DIR__ . '/_init.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    api_fail('Méthode non autorisée.', 405);
}

$offset = max(0, (int) ($_GET['offset'] ?? 0));
$limit  = max(1, min(40, (int) ($_GET['limit'] ?? 8)));

$all = app_user_projects_published();
$total = count($all);
$slice = array_slice($all, $offset, $limit);

$items = array_map(static function (array $p): array {
    return [
        'id'          => (string) ($p['id'] ?? ''),
        'title'       => (string) ($p['title'] ?? ''),
        'description' => (string) ($p['description'] ?? ''),
        'url'         => (string) ($p['url'] ?? ''),
        'image'       => (string) ($p['image'] ?? ''),
        'submitter'   => [
            'first_name' => (string) ($p['submitter']['first_name'] ?? ''),
            'last_name'  => (string) ($p['submitter']['last_name'] ?? ''),
        ],
    ];
}, $slice);

api_respond([
    'ok'       => true,
    'items'    => $items,
    'offset'   => $offset,
    'limit'    => $limit,
    'total'    => $total,
    'has_more' => ($offset + $limit) < $total,
]);
