<?php
/**
 * AJAX endpoint: checks domain availability via RDAP protocol.
 * GET /domain-check?name=monsite&tld=fr
 * Returns JSON: {available: bool|null, domain: string, price: int, error?: string}
 */
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/domain-pricing.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (!isset($_SESSION['domain_checks'])) {
    $_SESSION['domain_checks'] = [];
}
$_SESSION['domain_checks'] = array_values(array_filter(
    $_SESSION['domain_checks'],
    static fn(int $t): bool => $t > time() - 60
));
if (count($_SESSION['domain_checks']) > 10) {
    http_response_code(429);
    echo json_encode(['error' => 'Trop de requetes, reessayez dans une minute.']);
    exit;
}
$_SESSION['domain_checks'][] = time();

$name = strtolower(trim(preg_replace('/[^a-z0-9\-]/', '', (string) ($_GET['name'] ?? ''))));
$tld = strtolower(trim(preg_replace('/[^a-z0-9\-]/', '', (string) ($_GET['tld'] ?? ''))));

if ($name === '' || strlen($name) < 2) {
    echo json_encode(['error' => 'Nom de domaine trop court (2 caracteres minimum).']);
    exit;
}

$prices = app_domain_selling_prices();
if (!isset($prices[$tld])) {
    echo json_encode(['error' => 'Extension non disponible.']);
    exit;
}

$domain = $name . '.' . $tld;
$sellingPrice = $prices[$tld];

$rdapServers = [
    'com' => 'https://rdap.verisign.com/com/v1/domain/',
    'net' => 'https://rdap.verisign.com/net/v1/domain/',
    'org' => 'https://rdap.publicinterestregistry.org/rdap/domain/',
    'fr' => 'https://rdap.nic.fr/domain/',
    're' => 'https://rdap.nic.fr/domain/',
    'pm' => 'https://rdap.nic.fr/domain/',
    'tf' => 'https://rdap.nic.fr/domain/',
    'wf' => 'https://rdap.nic.fr/domain/',
    'yt' => 'https://rdap.nic.fr/domain/',
    'eu' => 'https://rdap.eu/domain/',
    'be' => 'https://rdap.dns.be/domain/',
    'ch' => 'https://rdap.nic.ch/domain/',
    'it' => 'https://rdap.nic.it/domain/',
    'us' => 'https://rdap.nic.us/domain/',
];

$rdapBase = $rdapServers[$tld] ?? 'https://rdap.org/domain/';
$rdapUrl = $rdapBase . rawurlencode($domain);

$available = null;
$error = null;

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $rdapUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
    CURLOPT_CONNECTTIMEOUT => 5,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 3,
    CURLOPT_HTTPHEADER => ['Accept: application/rdap+json'],
    CURLOPT_USERAGENT => 'AkashaProduction-DomainCheck/1.0',
]);

curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode === 404) {
    $available = true;
} elseif ($httpCode >= 200 && $httpCode < 300) {
    $available = false;
} elseif ($curlError !== '' || $httpCode === 0) {
    $hasDns = checkdnsrr($domain, 'A') || checkdnsrr($domain, 'NS');
    if ($hasDns) {
        $available = false;
    } else {
        $error = 'Verification incertaine. Reessayez ou contactez-nous.';
    }
} else {
    $error = 'Le registre ne repond pas. Reessayez dans quelques instants.';
}

echo json_encode([
    'domain' => $domain,
    'available' => $available,
    'price' => $sellingPrice,
    'error' => $error,
], JSON_UNESCAPED_UNICODE);
