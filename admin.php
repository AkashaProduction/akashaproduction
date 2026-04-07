<?php
require __DIR__ . '/includes/bootstrap.php';

if (isset($_GET['logout'])) {
    app_admin_logout();
    app_flash('success', 'Session administrateur fermée.');
    app_redirect('/admin');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'login') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (app_admin_login($email, $password)) {
            app_flash('success', 'Connexion administrateur ouverte.');
        } else {
            app_flash('warning', app_admin_is_enabled() ? 'Identifiants invalides.' : 'Le panel admin n’est pas encore activé côté configuration.');
        }
        app_redirect('/admin');
    }

    if ($action === 'update_ticket' && app_admin_logged_in()) {
        $ticketId = (string) ($_POST['ticket_id'] ?? '');
        $status = (string) ($_POST['status'] ?? 'open');
        $priority = (string) ($_POST['priority'] ?? 'normal');
        $replyMessage = trim((string) ($_POST['reply_message'] ?? ''));
        if ($replyMessage !== '') {
            $tickets = app_read_json('tickets.json');
            foreach ($tickets as $ticket) {
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
        app_flash('success', 'Ticket mis à jour.');
        app_redirect('/admin');
    }
}

$currentPage = '';
$pageTitle = app_page_title('Administration');
$support = app_config()['support'];
$tickets = app_admin_logged_in() ? app_read_json('tickets.json') : [];

require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container grid-2">
        <div>
            <div class="eyebrow">Administration</div>
            <h1 class="page-title">Modération des tickets et suivi client</h1>
            <p class="lead">Le panel administrateur permet de modérer les tickets, de répondre aux clients et de piloter les demandes du service commercial et du service technique.</p>
            <div class="panel">
                <ul>
                    <li>Statuts : ouvert, en cours, répondu, clos</li>
                    <li>Priorités : normale, haute, urgente</li>
                    <li>Réponses visibles côté client dans le panel utilisateur</li>
                </ul>
            </div>
        </div>
        <?php if (!app_admin_logged_in()): ?>
            <div class="form-card">
                <form class="form-grid" method="post">
                    <input type="hidden" name="action" value="login">
                    <div class="field field--full"><label for="admin-email">Email administrateur</label><input id="admin-email" name="email" type="email" required></div>
                    <div class="field field--full"><label for="admin-password">Mot de passe</label><input id="admin-password" name="password" type="password" required></div>
                    <div class="field field--full"><button class="btn btn--primary" type="submit">Ouvrir le panel</button></div>
                </form>
            </div>
        <?php else: ?>
            <div class="form-card">
                <div class="notice">Session administrateur ouverte.</div>
                <div class="cta-row">
                    <a class="btn btn--secondary" href="/admin?logout=1">Se déconnecter</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php if (app_admin_logged_in()): ?>
    <section class="section">
        <div class="container grid-2">
            <?php foreach ($tickets as $ticket): ?>
                <article class="panel">
                    <div class="kicker"><?= htmlspecialchars(app_department_label((string) $ticket['department']), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($support['statuses'][$ticket['status']] ?? (string) $ticket['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <h3><?= htmlspecialchars((string) $ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="muted"><?= htmlspecialchars((string) ($ticket['customer']['name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars((string) ($ticket['customer']['email'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="ticket-thread">
                        <?php foreach ($ticket['thread'] as $entry): ?>
                            <div class="ticket-entry">
                                <strong><?= htmlspecialchars((string) $entry['author_label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <p class="copy"><?= nl2br(htmlspecialchars((string) $entry['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form class="form-grid" method="post">
                        <input type="hidden" name="action" value="update_ticket">
                        <input type="hidden" name="ticket_id" value="<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="field">
                            <label for="status-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">Statut</label>
                            <select id="status-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" name="status">
                                <?php foreach ($support['statuses'] as $status => $label): ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?>"<?= $ticket['status'] === $status ? ' selected' : ''; ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label for="priority-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">Priorité</label>
                            <select id="priority-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" name="priority">
                                <?php foreach ($support['priorities'] as $priority => $label): ?>
                                    <option value="<?= htmlspecialchars($priority, ENT_QUOTES, 'UTF-8'); ?>"<?= $ticket['priority'] === $priority ? ' selected' : ''; ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="field field--full">
                            <label for="reply-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">Réponse administrateur</label>
                            <textarea id="reply-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" name="reply_message"></textarea>
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
