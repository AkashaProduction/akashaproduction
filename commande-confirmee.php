<?php
/**
 * Payment confirmation page.
 * GET /commande-confirmee?order=ORDER_ID&session_id=STRIPE_SESSION_ID
 */
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';

$orderId = trim((string) ($_GET['order'] ?? ''));
$sessionId = trim((string) ($_GET['session_id'] ?? ''));

$order = null;
if ($orderId !== '') {
    $orders = app_read_json('orders.json');
    foreach ($orders as &$o) {
        if (($o['id'] ?? '') === $orderId) {
            if (($o['status'] ?? '') !== 'paid' && $sessionId !== '') {
                $o['status'] = 'paid';
                $o['paid_at'] = app_now();
                $o['stripe_session_id'] = $sessionId;
            }
            $order = $o;
            break;
        }
    }
    unset($o);
    if ($order && ($order['status'] ?? '') === 'paid') {
        app_write_json('orders.json', $orders);
    }
}

$currentPage = '';
$pageTitle = app_page_title('Commande confirmee');
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow">Confirmation</div>
        <h1 class="page-title">Merci pour votre commande.</h1>
        <p class="lead">Votre paiement a ete enregistre avec succes. Vous recevrez une facture detaillee par email.</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 640px;">
        <?php if ($order): ?>
            <div class="panel">
                <div class="kicker">Recapitulatif</div>
                <div class="summary-lines">
                    <div class="summary-line">
                        <span>Reference</span>
                        <strong><?= htmlspecialchars(substr($order['id'], 0, 12), ENT_QUOTES, 'UTF-8'); ?>...</strong>
                    </div>
                    <div class="summary-line">
                        <span>Client</span>
                        <strong><?= htmlspecialchars(($order['customer']['first_name'] ?? '') . ' ' . ($order['customer']['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Email</span>
                        <strong><?= htmlspecialchars($order['customer']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <?php if (!empty($order['summary']['total'])): ?>
                        <div class="summary-line">
                            <span>Total</span>
                            <strong><?= app_money((float) $order['summary']['total']); ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="summary-line">
                        <span>Statut</span>
                        <strong style="color: var(--accent);">Paye</strong>
                    </div>
                </div>
            </div>

            <div class="panel" style="margin-top: 1.5rem;">
                <p class="copy">Nous preparons votre projet. Un email de confirmation avec votre facture vous sera envoye sous peu. Pour toute question, rendez-vous sur votre <a href="/mon-compte">espace client</a> ou <a href="/contact">contactez-nous</a>.</p>
            </div>
        <?php else: ?>
            <div class="panel">
                <p class="copy">Commande introuvable ou deja traitee. Consultez votre <a href="/mon-compte">espace client</a> pour le suivi.</p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="/" class="btn btn--primary">Retour a l'accueil</a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
