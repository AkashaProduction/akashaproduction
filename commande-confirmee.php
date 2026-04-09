<?php
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
$pageTitle = app_page_title(t('confirmation.eyebrow'));
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container">
        <div class="eyebrow"><?= htmlspecialchars(t('confirmation.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
        <h1 class="page-title"><?= htmlspecialchars(t('confirmation.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="lead"><?= htmlspecialchars(t('confirmation.lead'), ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width: 640px;">
        <?php if ($order): ?>
            <div class="panel">
                <div class="kicker"><?= htmlspecialchars(t('confirmation.summary_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="summary-lines">
                    <div class="summary-line">
                        <span><?= htmlspecialchars(t('confirmation.reference'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <strong><?= htmlspecialchars(substr($order['id'], 0, 12), ENT_QUOTES, 'UTF-8'); ?>...</strong>
                    </div>
                    <div class="summary-line">
                        <span><?= htmlspecialchars(t('confirmation.client'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <strong><?= htmlspecialchars(($order['customer']['first_name'] ?? '') . ' ' . ($order['customer']['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <div class="summary-line">
                        <span><?= htmlspecialchars(t('confirmation.email'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <strong><?= htmlspecialchars($order['customer']['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                    <?php if (!empty($order['summary']['total'])): ?>
                        <div class="summary-line">
                            <span><?= htmlspecialchars(t('confirmation.total'), ENT_QUOTES, 'UTF-8'); ?></span>
                            <strong><?= app_money((float) $order['summary']['total']); ?></strong>
                        </div>
                    <?php endif; ?>
                    <div class="summary-line">
                        <span><?= htmlspecialchars(t('confirmation.status'), ENT_QUOTES, 'UTF-8'); ?></span>
                        <strong style="color: var(--accent);"><?= htmlspecialchars(t('confirmation.status_paid'), ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </div>
            </div>

            <div class="panel" style="margin-top: 1.5rem;">
                <p class="copy"><?= htmlspecialchars(t('confirmation.next_text'), ENT_QUOTES, 'UTF-8'); ?> <a href="/mon-compte"><?= htmlspecialchars(t('confirmation.account_link'), ENT_QUOTES, 'UTF-8'); ?></a> <?= htmlspecialchars(t('confirmation.or'), ENT_QUOTES, 'UTF-8'); ?> <a href="/contact"><?= htmlspecialchars(t('confirmation.contact_link'), ENT_QUOTES, 'UTF-8'); ?></a>.</p>
            </div>
        <?php else: ?>
            <div class="panel">
                <p class="copy"><?= htmlspecialchars(t('confirmation.not_found'), ENT_QUOTES, 'UTF-8'); ?> <a href="/mon-compte"><?= htmlspecialchars(t('confirmation.account_link'), ENT_QUOTES, 'UTF-8'); ?></a> <?= htmlspecialchars(t('confirmation.for_tracking'), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endif; ?>

        <div style="margin-top: 2rem; text-align: center;">
            <a href="/" class="btn btn--primary"><?= htmlspecialchars(t('confirmation.back'), ENT_QUOTES, 'UTF-8'); ?></a>
        </div>
    </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
