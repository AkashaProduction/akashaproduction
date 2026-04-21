<?php
declare(strict_types=1);

/**
 * Stripe webhook endpoint.
 * Vérifie Stripe-Signature (HMAC SHA-256) puis met à jour orders.json.
 * Événements traités : checkout.session.completed, checkout.session.async_payment_succeeded,
 * checkout.session.async_payment_failed.
 *
 * Configurer le webhook dans Stripe Dashboard → Developers → Webhooks,
 * URL : https://<domaine>/stripe-webhook
 * Copier le signing secret dans runtime-config.php → stripe_webhook_secret.
 */

require __DIR__ . '/includes/bootstrap.php';

header('Content-Type: application/json');

$secret = (string) (app_config()['stripe_webhook_secret'] ?? '');
if ($secret === '') {
    http_response_code(500);
    app_log('error', 'stripe_webhook_no_secret', []);
    echo json_encode(['error' => 'webhook secret not configured']);
    exit;
}

$payload = file_get_contents('php://input') ?: '';
$sigHeader = (string) ($_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '');
if ($sigHeader === '' || $payload === '') {
    http_response_code(400);
    app_log('warning', 'stripe_webhook_no_sig', []);
    echo json_encode(['error' => 'missing signature or payload']);
    exit;
}

// Parse header: t=123,v1=hash[,v1=hash]...
$timestamp = null;
$signatures = [];
foreach (explode(',', $sigHeader) as $chunk) {
    [$k, $v] = array_pad(explode('=', trim($chunk), 2), 2, '');
    if ($k === 't') {
        $timestamp = (int) $v;
    } elseif ($k === 'v1') {
        $signatures[] = $v;
    }
}

if ($timestamp === null || !$signatures) {
    http_response_code(400);
    app_log('warning', 'stripe_webhook_bad_header', ['header' => $sigHeader]);
    echo json_encode(['error' => 'invalid signature header']);
    exit;
}

// Tolérance 5 minutes
if (abs(time() - $timestamp) > 300) {
    http_response_code(400);
    app_log('warning', 'stripe_webhook_stale', ['delta' => time() - $timestamp]);
    echo json_encode(['error' => 'timestamp outside tolerance']);
    exit;
}

$expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
$match = false;
foreach ($signatures as $sig) {
    if (hash_equals($expected, $sig)) {
        $match = true;
        break;
    }
}
if (!$match) {
    http_response_code(400);
    app_log('warning', 'stripe_webhook_bad_signature', []);
    echo json_encode(['error' => 'signature mismatch']);
    exit;
}

$event = json_decode($payload, true);
if (!is_array($event) || !isset($event['type'], $event['data']['object'])) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid event']);
    exit;
}

// Idempotence : on garde la trace des event IDs traités
$eventId = (string) ($event['id'] ?? '');
if ($eventId !== '') {
    $processed = app_read_json('stripe-events.json');
    if (in_array($eventId, $processed, true)) {
        echo json_encode(['status' => 'already_processed']);
        exit;
    }
}

$type = (string) $event['type'];
$obj = $event['data']['object'];
$orderId = (string) ($obj['metadata']['order_id'] ?? '');
$sessionId = (string) ($obj['id'] ?? '');

$handled = false;

if ($orderId !== '') {
    switch ($type) {
        case 'checkout.session.completed':
        case 'checkout.session.async_payment_succeeded':
            $paymentStatus = (string) ($obj['payment_status'] ?? 'paid');
            if ($paymentStatus === 'paid') {
                app_update_order_status($orderId, 'paid', [
                    'paid_at' => app_now(),
                    'stripe_session_id' => $sessionId,
                    'stripe_payment_intent' => (string) ($obj['payment_intent'] ?? ''),
                    'stripe_amount_total' => (int) ($obj['amount_total'] ?? 0),
                ]);
                app_log('info', 'stripe_order_paid', ['order_id' => $orderId, 'session_id' => $sessionId]);
                $handled = true;
            }
            break;

        case 'checkout.session.async_payment_failed':
            app_update_order_status($orderId, 'payment-failed', [
                'stripe_session_id' => $sessionId,
                'failed_at' => app_now(),
            ]);
            app_log('warning', 'stripe_order_failed', ['order_id' => $orderId]);
            $handled = true;
            break;

        case 'checkout.session.expired':
            app_update_order_status($orderId, 'cancelled', [
                'stripe_session_id' => $sessionId,
                'cancelled_at' => app_now(),
            ]);
            $handled = true;
            break;
    }
}

if ($eventId !== '') {
    app_json_mutate('stripe-events.json', function (array $items) use ($eventId): array {
        if (!in_array($eventId, $items, true)) {
            array_unshift($items, $eventId);
        }
        return array_slice($items, 0, 500);
    });
}

http_response_code(200);
echo json_encode(['status' => $handled ? 'handled' : 'ignored', 'type' => $type]);
