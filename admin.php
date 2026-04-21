<?php
require __DIR__ . '/includes/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    app_csrf_enforce();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'logout') {
        app_admin_logout();
        app_flash('success', 'Session administrateur fermée.');
        app_redirect('/admin');
    }

    if ($action === 'login') {
        if (!app_rate_limit('admin_login', 5, 300)) {
            app_flash('warning', 'Trop de tentatives. Réessayez dans quelques minutes.');
            app_log('warning', 'admin_login_rate_limited', []);
            app_redirect('/admin');
        }
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (app_admin_login($email, $password)) {
            app_rate_reset('admin_login');
            app_flash('success', 'Connexion administrateur ouverte.');
        } else {
            app_flash('warning', app_admin_is_enabled() ? 'Identifiants invalides.' : 'Le panel admin n\'est pas encore activé côté configuration.');
        }
        app_redirect('/admin');
    }

    if (!app_admin_logged_in()) {
        app_redirect('/admin');
    }

    if ($action === 'update_order') {
        $orderId = (string) ($_POST['order_id'] ?? '');
        $newStatus = (string) ($_POST['status'] ?? '');
        $allowedStatuses = ['pending-validation', 'quote-requested', 'checkout-started', 'paid', 'in-progress', 'delivered', 'cancelled', 'payment-failed'];
        if ($orderId !== '' && in_array($newStatus, $allowedStatuses, true)) {
            app_update_order_status($orderId, $newStatus);
            app_log('info', 'admin_order_updated', ['order_id' => $orderId, 'status' => $newStatus]);
            app_flash('success', 'Commande mise à jour.');
        }
        app_redirect('/admin');
    }

    if ($action === 'update_ticket') {
        $ticketId = (string) ($_POST['ticket_id'] ?? '');
        $status = (string) ($_POST['status'] ?? 'open');
        $priority = (string) ($_POST['priority'] ?? 'normal');
        $replyMessage = trim((string) ($_POST['reply_message'] ?? ''));
        if ($replyMessage !== '') {
            foreach (app_read_json('tickets.json') as $ticket) {
                if (($ticket['id'] ?? '') === $ticketId) {
                    app_add_ticket_reply($ticketId, 'admin', 'Akasha Production', $replyMessage);
                    $customerEmail = (string) ($ticket['customer']['email'] ?? '');
                    if ($customerEmail !== '') {
                        app_send_mail_to(
                            $customerEmail,
                            'Réponse ticket Akasha Production',
                            "Réponse au ticket {$ticketId}\n\n{$replyMessage}",
                            (string) app_config()['site']['contact_email']
                        );
                    }
                    break;
                }
            }
        }
        app_update_ticket($ticketId, $status, $priority);
        app_log('info', 'admin_ticket_updated', ['ticket_id' => $ticketId]);
        app_flash('success', 'Ticket mis à jour.');
        app_redirect('/admin');
    }

    if ($action === 'delete_contact') {
        $contactId = (string) ($_POST['contact_id'] ?? '');
        if ($contactId !== '') {
            app_json_mutate('contacts.json', function (array $items) use ($contactId): array {
                return array_values(array_filter($items, static fn ($c) => ($c['id'] ?? '') !== $contactId));
            });
            app_log('info', 'admin_contact_deleted', ['contact_id' => $contactId]);
            app_flash('success', 'Message supprimé.');
        }
        app_redirect('/admin');
    }
}

$currentPage = '';
$pageTitle = app_page_title('Administration');
$support = app_config()['support'];
$orders = app_admin_logged_in() ? app_read_json('orders.json') : [];
$tickets = app_admin_logged_in() ? app_read_json('tickets.json') : [];
$contacts = app_admin_logged_in() ? app_read_json('contacts.json') : [];

$orderStatuses = [
    'pending-validation' => 'En attente de validation',
    'quote-requested' => 'Devis demandé',
    'checkout-started' => 'Paiement initié',
    'paid' => 'Payé',
    'in-progress' => 'En cours de réalisation',
    'delivered' => 'Livré',
    'cancelled' => 'Annulé',
    'payment-failed' => 'Paiement échoué',
];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container grid-2">
        <div>
            <div class="eyebrow">Administration</div>
            <h1 class="page-title">Panel d'administration Akasha Production</h1>
            <p class="lead">Gérez vos commandes, modérez les tickets et suivez l'activité de vos clients depuis ce panel centralisé.</p>
        </div>
        <?php if (!app_admin_logged_in()): ?>
            <div class="form-card">
                <div class="kicker">Connexion</div>
                <h2 class="section-title">Accès administrateur</h2>
                <?php if (!app_admin_is_enabled()): ?>
                    <p class="copy"><strong>Admin désactivé.</strong> Définissez <code>admin_password_hash</code> dans <code>includes/runtime-config.php</code>.</p>
                <?php endif; ?>
                <form class="form-grid" method="post">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="login">
                    <div class="field field--full"><label for="admin-email">Email administrateur</label><input id="admin-email" name="email" type="email" required></div>
                    <div class="field field--full"><label for="admin-password">Mot de passe</label><input id="admin-password" name="password" type="password" required></div>
                    <div class="field field--full"><button class="btn btn--primary" type="submit">Ouvrir le panel</button></div>
                </form>
            </div>
        <?php else: ?>
            <div class="form-card">
                <div class="kicker">Tableau de bord</div>
                <h2 class="section-title">Activité</h2>
                <div class="grid-3 admin-stats">
                    <div class="admin-stat"><strong><?= count($orders); ?></strong><span>Commande<?= count($orders) > 1 ? 's' : ''; ?></span></div>
                    <div class="admin-stat"><strong><?= count($tickets); ?></strong><span>Ticket<?= count($tickets) > 1 ? 's' : ''; ?></span></div>
                    <div class="admin-stat"><strong><?= count($contacts); ?></strong><span>Message<?= count($contacts) > 1 ? 's' : ''; ?></span></div>
                </div>
                <form method="post" style="margin-top:1.2rem;">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="logout">
                    <button class="btn btn--secondary" type="submit">Se déconnecter</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (app_admin_logged_in()): ?>
    <!-- COMMANDES -->
    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <div class="eyebrow">Gestion</div>
                    <h2 class="section-title">Commandes</h2>
                </div>
            </div>
        </div>
        <div class="container grid-2">
            <?php if (!$orders): ?>
                <article class="panel"><p class="copy">Aucune commande enregistrée.</p></article>
            <?php endif; ?>
            <?php foreach ($orders as $order): ?>
                <article class="panel">
                    <?php
                    $rawStatus = (string) ($order['status'] ?? '');
                    $statusLabel = $orderStatuses[$rawStatus] ?? $rawStatus;
                    $statusClass = in_array($rawStatus, ['paid', 'delivered'], true) ? 'status-badge--success' : (in_array($rawStatus, ['cancelled', 'payment-failed'], true) ? 'status-badge--danger' : 'status-badge--pending');
                    ?>
                    <div class="kicker"><span class="status-badge <?= $statusClass; ?>"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <h3><?= htmlspecialchars(t('catalog.creation.' . ($order['selection']['creation'] ?? 'showcase') . '.headline'), ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars(t('catalog.hosting.' . ($order['selection']['hosting'] ?? 'shared-yearly') . '.headline'), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="muted">Réf. <?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars(date('d/m/Y', strtotime((string) $order['created_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
                    <ul>
                        <li><strong>Client :</strong> <?= htmlspecialchars(($order['customer']['first_name'] ?? '') . ' ' . ($order['customer']['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Email :</strong> <?= htmlspecialchars((string) ($order['customer']['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Téléphone :</strong> <?= htmlspecialchars((string) ($order['customer']['phone'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Organisation :</strong> <?= htmlspecialchars((string) ($order['customer']['company'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><strong>Total :</strong> <?= isset($order['summary']['total']) && $order['summary']['total'] !== null ? app_money((float) $order['summary']['total']) : 'Sur devis'; ?></li>
                        <li><strong>Paiement 3x :</strong> <?= !empty($order['selection']['split_payment']) ? 'Oui' : 'Non'; ?></li>
                    </ul>
                    <?php if (!empty($order['project_description'])): ?>
                        <p class="copy"><?= nl2br(htmlspecialchars((string) $order['project_description'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php endif; ?>
                    <form class="form-grid" method="post" style="margin-top:1rem;">
                        <?= app_csrf_field(); ?>
                        <input type="hidden" name="action" value="update_order">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="field">
                            <label for="order-status-<?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>">Statut</label>
                            <select id="order-status-<?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>" name="status">
                                <?php foreach ($orderStatuses as $key => $label): ?>
                                    <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"<?= $rawStatus === $key ? ' selected' : ''; ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field" style="align-self:end;">
                            <button class="btn btn--primary" type="submit">Mettre à jour</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- MESSAGES DE CONTACT -->
    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <div class="eyebrow">Messages</div>
                    <h2 class="section-title">Contacts reçus</h2>
                </div>
            </div>
        </div>
        <div class="container grid-2">
            <?php if (!$contacts): ?>
                <article class="panel"><p class="copy">Aucun message reçu.</p></article>
            <?php endif; ?>
            <?php foreach ($contacts as $contact): ?>
                <article class="panel">
                    <div class="kicker"><?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) ($contact['created_at'] ?? 'now'))), ENT_QUOTES, 'UTF-8'); ?></div>
                    <h3><?= htmlspecialchars(($contact['first_name'] ?? '') . ' ' . ($contact['last_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="muted"><a href="mailto:<?= htmlspecialchars((string) ($contact['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars((string) ($contact['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></a>
                        <?php if (!empty($contact['phone'])): ?> · <?= htmlspecialchars((string) $contact['phone'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                        <?php if (!empty($contact['company'])): ?> · <?= htmlspecialchars((string) $contact['company'], ENT_QUOTES, 'UTF-8'); ?><?php endif; ?>
                    </p>
                    <?php if (!empty($contact['message'])): ?>
                        <p class="copy"><?= nl2br(htmlspecialchars((string) $contact['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($contact['has_project']) && !empty($contact['project_details'])): ?>
                        <p class="copy"><strong>Projet :</strong> <?= nl2br(htmlspecialchars((string) $contact['project_details'], ENT_QUOTES, 'UTF-8')); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($contact['attachments'])): ?>
                        <p class="muted">Pièces jointes : <?= htmlspecialchars(implode(', ', array_column((array) $contact['attachments'], 'original')), ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php endif; ?>
                    <form method="post" style="margin-top:.8rem;">
                        <?= app_csrf_field(); ?>
                        <input type="hidden" name="action" value="delete_contact">
                        <input type="hidden" name="contact_id" value="<?= htmlspecialchars((string) ($contact['id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
                        <button class="btn btn--secondary" type="submit" onclick="return confirm('Supprimer ce message ?');">Supprimer</button>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- TICKETS -->
    <section class="section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <div class="eyebrow">Support</div>
                    <h2 class="section-title">Tickets</h2>
                </div>
            </div>
        </div>
        <div class="container grid-2">
            <?php if (!$tickets): ?>
                <article class="panel"><p class="copy">Aucun ticket enregistré.</p></article>
            <?php endif; ?>
            <?php foreach ($tickets as $ticket): ?>
                <article class="panel">
                    <?php
                    $ticketStatusClass = in_array($ticket['status'] ?? '', ['answered', 'closed'], true) ? 'status-badge--success' : 'status-badge--pending';
                    ?>
                    <div class="kicker">
                        <span class="status-badge <?= $ticketStatusClass; ?>"><?= htmlspecialchars($support['statuses'][$ticket['status']] ?? (string) $ticket['status'], ENT_QUOTES, 'UTF-8'); ?></span>
                        · <?= htmlspecialchars(app_department_label((string) $ticket['department']), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <h3><?= htmlspecialchars((string) $ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="muted"><?= htmlspecialchars((string) ($ticket['customer']['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars((string) ($ticket['customer']['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="ticket-thread">
                        <?php foreach ($ticket['thread'] as $entry): ?>
                            <div class="ticket-entry ticket-entry--<?= htmlspecialchars((string) ($entry['author_type'] ?? 'customer'), ENT_QUOTES, 'UTF-8'); ?>">
                                <strong><?= htmlspecialchars((string) $entry['author_label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <p class="copy"><?= nl2br(htmlspecialchars((string) $entry['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form class="form-grid" method="post" style="margin-top:1rem;">
                        <?= app_csrf_field(); ?>
                        <input type="hidden" name="action" value="update_ticket">
                        <input type="hidden" name="ticket_id" value="<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="field">
                            <label for="status-<?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>">Statut</label>
                            <select id="status-<?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>" name="status">
                                <?php foreach ($support['statuses'] as $status => $label): ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>"<?= ($ticket['status'] ?? '') === $status ? ' selected' : ''; ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label for="priority-<?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>">Priorité</label>
                            <select id="priority-<?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>" name="priority">
                                <?php foreach ($support['priorities'] as $priority => $label): ?>
                                    <option value="<?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8'); ?>"<?= ($ticket['priority'] ?? '') === $priority ? ' selected' : ''; ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field field--full">
                            <label for="reply-<?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>">Réponse administrateur</label>
                            <textarea id="reply-<?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?>" name="reply_message"></textarea>
                        </div>
                        <div class="field field--full">
                            <button class="btn btn--primary" type="submit">Mettre à jour le ticket</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
