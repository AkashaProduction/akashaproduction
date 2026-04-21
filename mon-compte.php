<?php
require __DIR__ . '/includes/bootstrap.php';

$support = app_config()['support'];
$catalog = app_config()['catalog'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    app_csrf_enforce();
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'logout') {
        app_customer_logout();
        app_flash('success', t('account.flash_logout'));
        app_redirect('/mon-compte');
    }

    if ($action === 'request_otp') {
        if (trim((string) ($_POST['website_url'] ?? '')) !== '') {
            app_log('info', 'otp_honeypot', []);
            app_flash('success', t('account.flash_otp_sent'));
            app_redirect('/mon-compte');
        }
        if (!app_rate_limit('otp_request', 5, 600)) {
            app_flash('warning', t('account.flash_otp_rate'));
            app_redirect('/mon-compte');
        }
        $email = trim((string) ($_POST['email'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        if (!app_valid_email($email)) {
            app_flash('warning', t('account.flash_otp_invalid'));
            app_redirect('/mon-compte');
        }
        if (app_is_admin_email($email)) {
            app_redirect('/admin');
        }
        $code = app_customer_issue_otp($email, $name);
        app_send_mail_to(
            $email,
            'Votre code d\'accès Akasha Production',
            "Votre code d'accès : {$code}\n\nIl expire dans 10 minutes.\n\nSi vous n'êtes pas à l'origine de cette demande, ignorez ce message.",
            (string) app_config()['site']['contact_email']
        );
        app_log('info', 'otp_issued', ['email' => strtolower($email)]);
        app_flash('success', t('account.flash_otp_sent'));
        app_redirect('/mon-compte');
    }

    if ($action === 'verify_otp') {
        if (!app_rate_limit('otp_verify', 10, 600)) {
            app_flash('warning', t('account.flash_otp_rate'));
            app_redirect('/mon-compte');
        }
        $code = trim((string) ($_POST['code'] ?? ''));
        if (app_customer_verify_otp($code)) {
            app_rate_reset('otp_verify');
            app_rate_reset('otp_request');
            app_flash('success', t('account.flash_login_success'));
        } else {
            app_flash('warning', t('account.flash_otp_invalid'));
        }
        app_redirect('/mon-compte');
    }

    // Toutes les actions suivantes exigent un client authentifié.
    if (!app_customer_logged_in()) {
        app_flash('warning', t('account.flash_login_required'));
        app_redirect('/mon-compte');
    }

    $email = app_customer_email();
    $name = app_customer_name();

    if ($action === 'create_ticket') {
        $department = (string) ($_POST['department'] ?? 'commercial');
        $priority = (string) ($_POST['priority'] ?? 'normal');
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        $orderId = trim((string) ($_POST['order_id'] ?? ''));

        if ($subject !== '' && $message !== '') {
            $ticket = app_create_ticket([
                'name' => $name !== '' ? $name : 'Client',
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
            app_flash('success', t('account.flash_ticket_success'));
        } else {
            app_flash('warning', t('account.flash_ticket_warning'));
        }
        app_redirect('/mon-compte');
    }

    if ($action === 'reply_ticket') {
        $ticketId = trim((string) ($_POST['ticket_id'] ?? ''));
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($ticketId !== '' && $message !== '') {
            app_add_ticket_reply($ticketId, 'customer', $name !== '' ? $name : 'Client', $message, $email);
            app_flash('success', t('account.flash_reply_success'));
        }
        app_redirect('/mon-compte');
    }
}

$isAuth = app_customer_logged_in();
$email = $isAuth ? app_customer_email() : '';
$name = $isAuth ? app_customer_name() : '';
$pendingEmail = app_customer_pending_email();
$orders = $isAuth ? app_orders_by_email($email) : [];
$tickets = $isAuth ? app_tickets_by_email($email) : [];

$currentPage = 'account';
$pageTitle = app_page_title(t('nav.account'));
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero account-hero">
    <div class="container grid-2">
        <div>
            <div class="eyebrow"><?= htmlspecialchars(t('account.eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
            <h1 class="page-title"><?= htmlspecialchars(t('account.title'), ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="lead"><?= htmlspecialchars(t('account.lead'), ENT_QUOTES, 'UTF-8'); ?></p>
            <div class="grid-3 account-points">
                <article class="card compact-card">
                    <h3><?= htmlspecialchars(t('account.orders_card_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="copy"><?= htmlspecialchars(t('account.orders_card_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
                <article class="card compact-card">
                    <h3><?= htmlspecialchars(t('account.commercial_card_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="copy"><?= htmlspecialchars(t('account.commercial_card_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
                <article class="card compact-card">
                    <h3><?= htmlspecialchars(t('account.technical_card_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="copy"><?= htmlspecialchars(t('account.technical_card_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            </div>
        </div>
        <div class="form-card">
            <div class="kicker"><?= htmlspecialchars(t('account.access_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
            <?php if ($isAuth): ?>
                <h2 class="section-title"><?= htmlspecialchars(t('account.access_welcome'), ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="copy"><?= htmlspecialchars(t('account.access_signed_as', ['email' => $email]), ENT_QUOTES, 'UTF-8'); ?></p>
                <form method="post">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="logout">
                    <button class="btn btn--secondary" type="submit"><?= htmlspecialchars(t('account.logout'), ENT_QUOTES, 'UTF-8'); ?></button>
                </form>
            <?php elseif ($pendingEmail !== ''): ?>
                <h2 class="section-title"><?= htmlspecialchars(t('account.otp_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="copy"><?= htmlspecialchars(t('account.otp_text', ['email' => $pendingEmail]), ENT_QUOTES, 'UTF-8'); ?></p>
                <form class="form-grid" method="post">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="verify_otp">
                    <div class="field field--full"><label for="otp-code"><?= htmlspecialchars(t('account.otp_code_label'), ENT_QUOTES, 'UTF-8'); ?></label><input id="otp-code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required></div>
                    <div class="field field--full"><button class="btn btn--primary" type="submit"><?= htmlspecialchars(t('account.otp_verify'), ENT_QUOTES, 'UTF-8'); ?></button></div>
                </form>
                <form method="post" style="margin-top:.8rem;">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="logout">
                    <button class="btn btn--secondary" type="submit"><?= htmlspecialchars(t('account.otp_cancel'), ENT_QUOTES, 'UTF-8'); ?></button>
                </form>
            <?php else: ?>
                <h2 class="section-title"><?= htmlspecialchars(t('account.access_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="copy"><?= htmlspecialchars(t('account.access_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                <form class="form-grid" method="post">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="request_otp">
                    <div style="position:absolute;left:-10000px;" aria-hidden="true">
                        <label for="website_url_a">Laissez vide</label>
                        <input id="website_url_a" name="website_url" type="text" tabindex="-1" autocomplete="off">
                    </div>
                    <div class="field field--full"><label for="lookup-name"><?= htmlspecialchars(t('account.name_label'), ENT_QUOTES, 'UTF-8'); ?></label><input id="lookup-name" name="name" required></div>
                    <div class="field field--full"><label for="lookup-email"><?= htmlspecialchars(t('account.email_label'), ENT_QUOTES, 'UTF-8'); ?></label><input id="lookup-email" name="email" type="email" required></div>
                    <div class="field field--full"><button class="btn btn--primary" type="submit"><?= htmlspecialchars(t('account.access_submit'), ENT_QUOTES, 'UTF-8'); ?></button></div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($isAuth): ?>
    <section class="section">
        <div class="container">
            <div class="notice">
                <?= htmlspecialchars(t('account.notice', ['count_orders' => count($orders), 'count_tickets' => count($tickets), 'email' => $email]), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container grid-2">
            <div class="section-heading section-heading--tight">
                <div>
                    <div class="eyebrow"><?= htmlspecialchars(t('account.orders_eyebrow'), ENT_QUOTES, 'UTF-8'); ?></div>
                    <h2 class="section-title"><?= htmlspecialchars(t('account.orders_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
                </div>
            </div>
        </div>
        <div class="container grid-2">
            <?php if (!$orders): ?>
                <article class="panel">
                    <h3><?= htmlspecialchars(t('account.no_orders_title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="copy"><?= htmlspecialchars(t('account.no_orders_text'), ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            <?php endif; ?>
            <?php foreach ($orders as $order): ?>
                <article class="panel">
                    <?php
                    $rawStatus = (string) ($order['status'] ?? '');
                    $statusLabels = ta('account.order_statuses') ?: [];
                    $statusLabel = $statusLabels[$rawStatus] ?? t('account.order_status_default');
                    $statusClass = in_array($rawStatus, ['paid', 'delivered'], true) ? 'status-badge--success' : (in_array($rawStatus, ['cancelled', 'payment-failed'], true) ? 'status-badge--danger' : 'status-badge--pending');
                    ?>
                    <div class="kicker"><span class="status-badge <?= $statusClass; ?>"><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8'); ?></span></div>
                    <h3><?= htmlspecialchars(t('catalog.creation.' . ($order['selection']['creation'] ?? 'showcase') . '.headline'), ENT_QUOTES, 'UTF-8'); ?> / <?= htmlspecialchars(t('catalog.hosting.' . ($order['selection']['hosting'] ?? 'shared-yearly') . '.headline'), ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="muted"><?= htmlspecialchars(t('account.order_ref'), ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars(date('d/m/Y', strtotime((string) $order['created_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
                    <ul>
                        <li><?= htmlspecialchars(t('account.order_creation'), ENT_QUOTES, 'UTF-8'); ?> : <?= htmlspecialchars(t('catalog.creation.' . ($order['selection']['creation'] ?? 'showcase') . '.label'), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?= htmlspecialchars(t('account.order_hosting'), ENT_QUOTES, 'UTF-8'); ?> : <?= htmlspecialchars(t('catalog.hosting.' . ($order['selection']['hosting'] ?? 'shared-yearly') . '.label'), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?= htmlspecialchars(t('account.order_subdomain'), ENT_QUOTES, 'UTF-8'); ?> : <?= htmlspecialchars((string) (($order['selection']['subdomain_prefix'] ?? '') !== '' ? $order['selection']['subdomain_prefix'] . '.' . ($order['selection']['parent_domain'] ?? '') : t('account.order_subdomain_pending', ['domain' => ($order['selection']['parent_domain'] ?? 'akashaproduction.com')])), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?= htmlspecialchars(t('account.order_total'), ENT_QUOTES, 'UTF-8'); ?> : <?= isset($order['summary']['total']) && $order['summary']['total'] !== null ? app_money((float) $order['summary']['total']) : htmlspecialchars(t('account.on_quote'), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?= htmlspecialchars(t('account.order_split'), ENT_QUOTES, 'UTF-8'); ?> : <?= !empty($order['selection']['split_payment']) ? htmlspecialchars(t('account.yes'), ENT_QUOTES, 'UTF-8') : htmlspecialchars(t('account.no'), ENT_QUOTES, 'UTF-8'); ?></li>
                        <li><?= htmlspecialchars(t('account.order_domain'), ENT_QUOTES, 'UTF-8'); ?> : <?= !empty($order['selection']['include_domain']) ? htmlspecialchars((string) ($order['selection']['custom_domain_name'] ?? t('account.yes')), ENT_QUOTES, 'UTF-8') : htmlspecialchars(t('account.no'), ENT_QUOTES, 'UTF-8'); ?></li>
                    </ul>
                    <p class="copy"><?= nl2br(htmlspecialchars((string) ($order['project_description'] ?? ''), ENT_QUOTES, 'UTF-8')); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section">
        <div class="container grid-2">
            <div class="form-card">
                <div class="kicker"><?= htmlspecialchars(t('account.ticket_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                <h2 class="section-title"><?= htmlspecialchars(t('account.ticket_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
                <form class="form-grid" method="post">
                    <?= app_csrf_field(); ?>
                    <input type="hidden" name="action" value="create_ticket">
                    <div class="field">
                        <label for="department"><?= htmlspecialchars(t('account.department'), ENT_QUOTES, 'UTF-8'); ?></label>
                        <select id="department" name="department">
                            <?php foreach ($support['departments'] as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label for="priority"><?= htmlspecialchars(t('account.priority'), ENT_QUOTES, 'UTF-8'); ?></label>
                        <select id="priority" name="priority">
                            <?php foreach ($support['priorities'] as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label for="subject"><?= htmlspecialchars(t('account.subject'), ENT_QUOTES, 'UTF-8'); ?></label>
                        <select id="subject" name="subject" data-support-subject data-support-topics="<?= htmlspecialchars(json_encode($support['topics'], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?>"></select>
                    </div>
                    <div class="field field--full">
                        <label for="order_id"><?= htmlspecialchars(t('account.linked_order'), ENT_QUOTES, 'UTF-8'); ?></label>
                        <select id="order_id" name="order_id">
                            <option value=""><?= htmlspecialchars(t('account.none'), ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?= htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?= htmlspecialchars(substr((string) $order['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> - <?= htmlspecialchars(t('catalog.creation.' . ($order['selection']['creation'] ?? 'showcase') . '.label'), ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field field--full">
                        <label for="message"><?= htmlspecialchars(t('account.ticket_message'), ENT_QUOTES, 'UTF-8'); ?></label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <div class="field field--full">
                        <button class="btn btn--primary" type="submit"><?= htmlspecialchars(t('account.create_ticket_submit'), ENT_QUOTES, 'UTF-8'); ?></button>
                    </div>
                </form>
            </div>

            <div class="form-card">
                <div class="kicker"><?= htmlspecialchars(t('account.tickets_kicker'), ENT_QUOTES, 'UTF-8'); ?></div>
                <h2 class="section-title"><?= htmlspecialchars(t('account.tickets_title'), ENT_QUOTES, 'UTF-8'); ?></h2>
                <?php if (!$tickets): ?>
                    <p class="copy"><?= htmlspecialchars(t('account.no_tickets'), ENT_QUOTES, 'UTF-8'); ?></p>
                <?php endif; ?>
                <?php foreach ($tickets as $ticket): ?>
                    <article class="panel" id="ticket-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" style="margin-bottom:1rem;">
                        <div class="kicker"><?= htmlspecialchars(app_department_label((string) $ticket['department']), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars($support['statuses'][$ticket['status']] ?? (string) $ticket['status'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <h3><?= htmlspecialchars((string) $ticket['subject'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="muted"><?= htmlspecialchars(t('account.ticket_ref'), ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars(substr((string) $ticket['id'], 0, 8), ENT_QUOTES, 'UTF-8'); ?> · <?= htmlspecialchars(t('account.ticket_priority'), ENT_QUOTES, 'UTF-8'); ?> <?= htmlspecialchars($support['priorities'][$ticket['priority']] ?? (string) $ticket['priority'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="ticket-thread">
                            <?php foreach ($ticket['thread'] as $entry): ?>
                                <div class="ticket-entry ticket-entry--<?= htmlspecialchars((string) ($entry['author_type'] ?? 'customer'), ENT_QUOTES, 'UTF-8'); ?>">
                                    <strong><?= htmlspecialchars((string) $entry['author_label'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                    <p class="copy"><?= nl2br(htmlspecialchars((string) $entry['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <form class="form-grid" method="post">
                            <?= app_csrf_field(); ?>
                            <input type="hidden" name="action" value="reply_ticket">
                            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="field field--full">
                                <label for="reply-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>"><?= htmlspecialchars(t('account.reply_label'), ENT_QUOTES, 'UTF-8'); ?></label>
                                <textarea id="reply-<?= htmlspecialchars((string) $ticket['id'], ENT_QUOTES, 'UTF-8'); ?>" name="message" required></textarea>
                            </div>
                            <div class="field field--full">
                                <button class="btn btn--secondary" type="submit"><?= htmlspecialchars(t('account.reply_submit'), ENT_QUOTES, 'UTF-8'); ?></button>
                            </div>
                        </form>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
