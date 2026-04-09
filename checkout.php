<?php
/**
 * Stripe Checkout Session creator.
 * GET /checkout?order=ORDER_ID
 * Reads order from storage, builds line items, redirects to Stripe.
 */
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/domain-pricing.php';

$orderId = trim((string) ($_GET['order'] ?? ''));
if ($orderId === '') {
    app_flash('warning', t('checkout.order_not_found'));
    app_redirect('/commander');
}

$orders = app_read_json('orders.json');
$order = null;
foreach ($orders as $o) {
    if (($o['id'] ?? '') === $orderId) {
        $order = $o;
        break;
    }
}

if (!$order) {
    app_flash('warning', t('checkout.order_not_found'));
    app_redirect('/commander');
}

if (($order['status'] ?? '') === 'paid') {
    app_flash('info', t('checkout.already_paid'));
    app_redirect('/commande-confirmee?order=' . urlencode($orderId));
}

if (($order['status'] ?? '') === 'quote-requested') {
    app_flash('info', t('checkout.quote_pending'));
    app_redirect('/commander');
}

$stripe = require __DIR__ . '/includes/stripe-config.php';
$secretKey = (string) (app_config()['stripe_secret_key'] ?? '');
if ($secretKey === '') {
    app_flash('warning', t('checkout.payment_unavailable'));
    app_redirect('/commander');
}

$creation = (string) ($order['selection']['creation'] ?? '');
$hosting = (string) ($order['selection']['hosting'] ?? '');
$splitPayment = !empty($order['selection']['split_payment']);
$includeDomain = !empty($order['selection']['include_domain']);
$domainName = (string) ($order['selection']['custom_domain_name'] ?? '');
$domainExt = (string) ($order['selection']['domain_extension'] ?? '');
$domainPrice = (int) ($order['selection']['domain_price'] ?? 0);

$packKey = $creation . ':' . $hosting;
$isPack = isset($stripe['packs'][$packKey]);

$lineItems = [];

if ($isPack && isset($stripe['packs'][$packKey]['product'])) {
    $packProduct = $stripe['packs'][$packKey]['product'];
    $packAmount = $stripe['packs'][$packKey]['amount'];
    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product' => $stripe['products'][$packProduct],
            'unit_amount' => $packAmount,
        ],
        'quantity' => 1,
    ];
} elseif ($isPack) {
    $creationAmount = $stripe['amounts'][$creation] ?? 0;
    $hostingAmount = $stripe['amounts'][$hosting] ?? 0;

    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product' => $stripe['products'][$creation],
            'unit_amount' => $creationAmount,
        ],
        'quantity' => 1,
    ];
    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product' => $stripe['products'][$hosting],
            'unit_amount' => $hostingAmount,
        ],
        'quantity' => 1,
    ];
} else {
    if ($creation !== '' && $creation !== 'custom' && isset($stripe['products'][$creation])) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product' => $stripe['products'][$creation],
                'unit_amount' => $stripe['amounts'][$creation] ?? 0,
            ],
            'quantity' => 1,
        ];
    }
    if ($hosting !== '' && $hosting !== 'cloud' && isset($stripe['products'][$hosting])) {
        $lineItems[] = [
            'price_data' => [
                'currency' => 'eur',
                'product' => $stripe['products'][$hosting],
                'unit_amount' => $stripe['amounts'][$hosting] ?? 0,
            ],
            'quantity' => 1,
        ];
    }
}

if ($includeDomain && $domainName !== '' && $domainPrice > 0) {
    $lineItems[] = [
        'price_data' => [
            'currency' => 'eur',
            'product' => $stripe['products']['domain'],
            'unit_amount' => $domainPrice * 100,
        ],
        'quantity' => 1,
    ];
}

if (empty($lineItems)) {
    app_flash('warning', t('checkout.no_items'));
    app_redirect('/commander');
}

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'akashaproduction.com');

$params = [
    'mode' => 'payment',
    'customer_email' => $order['customer']['email'] ?? '',
    'success_url' => $baseUrl . '/commande-confirmee?order=' . urlencode($orderId) . '&session_id={CHECKOUT_SESSION_ID}',
    'cancel_url' => $baseUrl . '/commander',
    'invoice_creation[enabled]' => 'true',
    'metadata[order_id]' => $orderId,
    'metadata[creation]' => $creation,
    'metadata[hosting]' => $hosting,
];

foreach ($lineItems as $i => $item) {
    $params["line_items[$i][price_data][currency]"] = $item['price_data']['currency'];
    $params["line_items[$i][price_data][product]"] = $item['price_data']['product'];
    $params["line_items[$i][price_data][unit_amount]"] = $item['price_data']['unit_amount'];
    $params["line_items[$i][quantity]"] = $item['quantity'];
}

if ($isPack && !isset($stripe['packs'][$packKey]['product']) && isset($stripe['coupons'][$packKey])) {
    $params['discounts[0][coupon]'] = $stripe['coupons'][$packKey];
}

if ($splitPayment) {
    $params['payment_method_types[0]'] = 'card';
    $params['payment_intent_data[payment_method_options][card][installments][enabled]'] = 'true';
}

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://api.stripe.com/v1/checkout/sessions',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query($params),
    CURLOPT_USERPWD => $secretKey . ':',
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_TIMEOUT => 30,
    CURLOPT_CONNECTTIMEOUT => 10,
]);

$response = curl_exec($ch);
$httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError !== '' || $httpCode === 0) {
    app_flash('warning', t('checkout.connection_error'));
    app_redirect('/commander');
}

$data = json_decode((string) $response, true);

if ($httpCode >= 400 || !isset($data['url'])) {
    $errorMsg = $data['error']['message'] ?? t('checkout.session_error');
    app_flash('warning', $errorMsg);
    app_redirect('/commander');
}

// Update order with Stripe session
$orders = app_read_json('orders.json');
foreach ($orders as &$o) {
    if (($o['id'] ?? '') === $orderId) {
        $o['stripe_session_id'] = $data['id'];
        $o['status'] = 'checkout-started';
        break;
    }
}
unset($o);
app_write_json('orders.json', $orders);

header('Location: ' . $data['url']);
exit;
