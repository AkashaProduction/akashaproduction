<?php
require __DIR__ . '/includes/bootstrap.php';

$support = app_config()['support'];
$catalog = app_config()['catalog'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'lookup') {
        $email = trim((string) ($_POST['email'] ?? ''));
        if (app_is_admin_email($email)) {
            app_redirect('/admin');
        }
        app_redirect('/mon-compte?email=' . rawurlencode($email) . '&name=' . rawurlencode((string) ($_POST['name'] ?? '')));
    }

    if ($action === 'create_ticket') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $department = (string) ($_POST['department'] ?? 'commercial');
        $priority = (string) ($_POST['priority'] ?? 'normal');
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        $orderId = trim((string) ($_POST['order_id'] ?? ''));

        if ($name !== '' && $email !== '' && $subject !== '' && $message !== '') {
            $ticket = app_create_ticket([
                'name' => $name,
                'email' => $email,
                'department' => $department,
                'priority' => $priority,
                'subject' => $subject,
                'message' => $message,
                'order_id' => $orderId,
            ]);
            app_send_mail(
                'Nouveau ticket ' . app_department_label((string) $ticket['department']) . ' - ' . $ticket['subject'],
                "Ticket {$ticket['id']}\nClient: {$name}\nEmail: {$email}\n\n{$message}",
                $email
            );
            app_flash('success', 'Votre ticket a bien été créé.');
        } else {
            app_flash('warning', 'Merci de renseigner votre nom, votre email, le sujet et votre message.');
        }

        app_redirect('/mon-compte?email=' . rawurlencode($email) . '&name=' . rawurlencode($name));
    }

    if ($action === 'reply_ticket') {
        $email = trim((string) ($_POST['email'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $ticketId = trim((string) ($_POST['ticket_id'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($ticketId !== '' && $message !== '') {
            app_add_ticket_reply($ticketId, 'customer', $name !== '' ? $name : 'Client', $message, $email);
            app_flash('success', 'Votre complément a été ajouté au ticket.');
        }
        app_redirect('/mon-compte?email=' . rawurlencode($email) . '&name=' . rawurlencode($name));
    }
}

$email = trim((string) ($_GET['email'] ?? ''));
$name = trim((string) ($_GET['name'] ?? ''));
$orders = $email !== '' ? app_orders_by_email($email) : [];
$tickets = $email !== '' ? app_tickets_by_email($email) : [];

$currentPage = 'account';
$pageTitle = app_page_title('Mon compte');
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero">
    <div class="container grid-2">
        <div>
            <div class="eyebrow">Mon compte</div>
            <h1 class="page-title">Retrouver vos commandes et votre support.</h1>
            <p class="lead">Renseignez le nom et l’email utilisés lors de votre commande pour accéder à votre dossier client, consulter vos demandes et ouvrir un ticket si nécessaire.</p>
            <div class="grid-3 account-points">
                <article class="card compact-card">
                    <h3>Commandes</h3>
                    <p class="copy">Consultez les services enregistrés, les options choisies et le détail de votre demande.</p>
                </article>
                <article class="card compact-card">
                    <h3>Commercial</h3>
                    <p class="copy">Ouvrez un ticket pour un devis, une question de commande, un paiement ou un domaine.</p>
                </article>
                <article class="card compact-card">
                    <h3>Technique</h3>
                    <p class="copy">Signalez un incident, un problème d’accès, de DNS, d’email ou de performance.</p>
                </article>
            </div>
        </div>
        <div class="form-card">
            <div class="kicker">Accès client</div>
            <h2 class="section-title">Connexion à votre dossier</h2>
            <p class="copy">Ce formulaire permet de retrouver votre espace à partir des informations déjà utilisées dans votre commande.</p>
            <form class="form-grid" method="post">
                <input type="hidden" name="action" value="lookup">
                <div class="field field--full"><label for="lookup-name">Nom utilisé lors de la commande</label><input id="lookup-name" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="field field--full"><label for="lookup-email">Email utilisé lors de la commande</label><input id="lookup-email" name="email" type="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required></div>
                <div class="field field--full"><button class="btn btn--primary" type="submit">Accéder à mon espace client</button></div>
            </form>
        </div>
    </div>
</section>

<?php if ($email !== ''): ?>
    <section class="section">
        <div class="container">
            <div class="notice"><?= count($orders); ?> commande(s) et <?= count($tickets); ?> ticket(s) retrouvés pour <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>.</div>
        </div>
    </section>

    <section class="section">
        <div class="container grid-2">
            <div class="section-heading section-heading--tight">
                <div>
                    <div class="eyebrow">Dossier client</div>
                    <h2 class="section-title">Vos commandes</h2>
                </div>
            </div>
        </div>
        <div class="container grid-2">
            <?php if (!$orders): ?>
                <article class="panel">
                    <h3>Aucune commande retrouvée</h3>
                    <p class="copy">Aucune commande n’est actuellement associée à cet email. Vérifiez l’adresse utilisée lors de la demande ou contactez-nous si nécessaire.</p>
                </article>
            <?php endif; ?>
            <?php foreach ($orders as $order): ?>
                <article class="panel">
                    <div class="kicker"><?= htmlspecialchars((string) ($order['status'] ?? 'Commande'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <h3><?= htmlspecialchars((string) ($catalog['creation'][$order['selection']['creation']]['headline'] ?? ($order['selection']['creation'] ?? 'création')), ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars((string) ($catalog['hosting'][$order['selection']['hosting']]['headline'] ?? ($order['selection']['hosting'] ?? 'hébergement')), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="muted">Dossier <?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars(date('d/m/Y', strtotime((string) $order['created_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
                    <ul>
                        <li>Création : <?= htmlspecialchars((string) ($catalog['creation'][$order['selection']['creation']]['label'] ?? ($order['selection']['creation'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li>Hébergement : <?= htmlspecialchars((string) ($catalog['hosting'][$order['selection']['hosting']]['label'] ?? ($order['selection']['hosting'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li>Sous-domaine demandé : <?= htmlspecialchars((string) (($order['selection']['subdomain_prefix'] ?? '') !== '' ? $order['selection']['subdomain_prefix'] . '.' . ($order['selection']['parent_domain'] ?? '') : 'À définir sur ' . ($order['selection']['parent_domain'] ?? 'akashaproduction.com')), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li>Total : <?= isset($order['summary']['total']) && $order['summary']['total'] !== null ? app_money((float) $order['summary']['total']) : 'Sur devis'; ?></li>
                        <li>Paiement 3x : <?= !empty($order['selection']['split_payment']) ? 'Oui' : 'Non'; ?></li>
                        <li>Domaine personnalisé : <?= !empty($order['selection']['include_domain']) ? htmlspecialchars((string) ($order['selection']['custom_domain_name'] ?? 'Oui'), ENT_QUOTES, 'UTF-8') : 'Non'; ?></li>
                    </ul>
                    <p class="copy"><?= nl2br(htmlspecialchars((string) ($order['project_description'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section">
        <div class="container grid-2">
            <div class="form-card">
                <div class="kicker">Support client</div>
                <h2 class="section-title">Créer un ticket</h2>
                <form class="form-grid" method="post">
                    <input type="hidden" name="action" value="create_ticket">
                    <input type="hidden" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="field">
                        <label for="department">Service</label>
                        <select id="department" name="department">
                            <?php foreach ($support['departments'] as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="priority">Priorité</label>
                        <select id="priority" name="priority">
                            <?php foreach ($support['priorities'] as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label for="subject">Sujet</label>
                        <select id="subject" name="subject" data-support-subject data-support-topics="<?= htmlspecialchars(json_encode($support['topics'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"></select>
                    </div>
                    <div class="field field--full">
                        <label for="order_id">Commande liée</label>
                        <select id="order_id" name="order_id">
                            <option value="">Aucune</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?= htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> - <?= htmlspecialchars((string) ($order['selection']['creation'] ?? 'commande'), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <div class="field field--full">
                        <button class="btn btn--primary" type="submit">Créer le ticket</button>
                    </div>
                </form>
            </div>

            <div class="form-card">
                <div class="kicker">Tickets</div>
                <h2 class="section-title">Historique de support</h2>
                <?php if (!$tickets): ?>
                    <p class="copy">Aucun ticket pour le moment.</p>
                <?php endif; ?>
                <?php foreach ($tickets as $ticket): ?>
                    <article class="panel" id="ticket-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" style="margin-bottom:1rem;">
                        <div class="kicker"><?= htmlspecialchars(app_department_label((string) $ticket['department']), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($support['statuses'][$ticket['status']] ?? (string) $ticket['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <h3><?= htmlspecialchars((string) $ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="muted">Ticket <?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> · priorité <?= htmlspecialchars($support['priorities'][$ticket['priority']] ?? (string) $ticket['priority'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="ticket-thread">
                            <?php foreach ($ticket['thread'] as $entry): ?>
                                <div class="ticket-entry">
                                    <strong><?= htmlspecialchars((string) $entry['author_label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <p class="copy"><?= nl2br(htmlspecialchars((string) $entry['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form class="form-grid" method="post">
                            <input type="hidden" name="action" value="reply_ticket">
                            <input type="hidden" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="field field--full">
                                <label for="reply-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">Ajouter un message</label>
                                <textarea id="reply-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" name="message" required></textarea>
                            </div>
                            <div class="field field--full">
                                <button class="btn btn--secondary" type="submit">Envoyer le complément</button>
                            </div>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
