<?php
declare(strict_types=1);

require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/content.php';

if (isset($_GET['logout'])) {
    app_admin_logout();
    app_flash('success', 'Session administrateur fermée.');
    app_redirect('/admin');
}

$flash = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $needsCsrf = $action !== 'login';

    if ($needsCsrf && !app_check_csrf((string) ($_POST['csrf'] ?? ''))) {
        app_flash('error', 'Jeton de sécurité expiré, recommencez.');
        app_redirect('/admin?' . http_build_query(['section' => $_POST['section'] ?? 'content']));
    }

    if ($action === 'login') {
        $email    = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (app_admin_login($email, $password)) {
            app_flash('success', 'Connexion établie.');
        } else {
            app_flash('error', 'Identifiants invalides.');
        }
        app_redirect('/admin');
    }

    if (!app_admin_logged_in()) {
        app_flash('error', 'Vous devez être connecté.');
        app_redirect('/admin');
    }

    $section = (string) ($_POST['section'] ?? 'content');
    $editLang = (string) ($_POST['edit_lang'] ?? 'fr');
    if (!app_lang_is_supported($editLang)) { $editLang = 'fr'; }
    $defaults = app_content_defaults();
    $stored   = app_content_stored($editLang);
    $editLangQS = '&edit_lang=' . urlencode($editLang);

    switch ($action) {
        case 'save_site':
            $stored['site'] = [
                'meta_title'       => app_clean((string) ($_POST['meta_title'] ?? '')),
                'meta_description' => app_clean((string) ($_POST['meta_description'] ?? '')),
                'enter_label'      => app_clean((string) ($_POST['enter_label'] ?? 'Entrer')),
                'loader_label'     => app_clean((string) ($_POST['loader_label'] ?? 'Akasha Production')),
            ];
            app_content_save($stored, $editLang);
            app_flash('success', 'En-tête du site mis à jour.');
            app_redirect('/admin?section=content' . $editLangQS);

        case 'save_hero':
            $stored['hero'] = [
                'eyebrow'       => app_clean((string) ($_POST['eyebrow'] ?? '')),
                'title'         => app_clean((string) ($_POST['title'] ?? '')),
                'lead'          => trim((string) ($_POST['lead'] ?? '')),
                'cta_creations' => app_clean((string) ($_POST['cta_creations'] ?? '')),
                'cta_projects'  => app_clean((string) ($_POST['cta_projects'] ?? '')),
            ];
            app_content_save($stored, $editLang);
            app_flash('success', 'Section Hero mise à jour.');
            app_redirect('/admin?section=content' . $editLangQS);

        case 'save_featured':
            $stored['featured'] = [
                'eyebrow'     => app_clean((string) ($_POST['eyebrow'] ?? '')),
                'title'       => app_clean((string) ($_POST['title'] ?? '')),
                'description' => trim((string) ($_POST['description'] ?? '')),
                'image'       => app_clean((string) ($_POST['image'] ?? '')),
                'image_webp'  => app_clean((string) ($_POST['image_webp'] ?? '')),
                'image_alt'   => app_clean((string) ($_POST['image_alt'] ?? '')),
                'cta_label'   => app_clean((string) ($_POST['cta_label'] ?? '')),
                'cta_url'     => trim((string) ($_POST['cta_url'] ?? '')),
            ];
            app_content_save($stored, $editLang);
            app_flash('success', 'Section À la une mise à jour.');
            app_redirect('/admin?section=content' . $editLangQS);

        case 'save_creations':
            $eyebrow = app_clean((string) ($_POST['eyebrow'] ?? ''));
            $title   = app_clean((string) ($_POST['title'] ?? ''));
            $intro   = trim((string) ($_POST['intro'] ?? ''));
            $cards   = [];
            $names = $_POST['card_name'] ?? [];
            if (is_array($names)) {
                foreach ($names as $i => $name) {
                    $name = app_clean((string) $name);
                    if ($name === '') continue;
                    $cards[] = [
                        'name'        => $name,
                        'description' => trim((string) ($_POST['card_description'][$i] ?? '')),
                        'url'         => trim((string) ($_POST['card_url'][$i] ?? '')),
                        'image'       => app_clean((string) ($_POST['card_image'][$i] ?? '')),
                        'image_webp'  => app_clean((string) ($_POST['card_image_webp'][$i] ?? '')),
                        'available'   => !empty($_POST['card_available'][$i]),
                    ];
                }
            }
            $stored['creations'] = [
                'eyebrow' => $eyebrow,
                'title'   => $title,
                'intro'   => $intro,
                'cards'   => $cards,
            ];
            app_content_save($stored, $editLang);
            app_flash('success', count($cards) . ' cartes enregistrées.');
            app_redirect('/admin?section=creations' . $editLangQS);

        case 'save_projects_section':
            $stored['projects'] = [
                'eyebrow'           => app_clean((string) ($_POST['eyebrow'] ?? '')),
                'title'             => app_clean((string) ($_POST['title'] ?? '')),
                'empty_state'       => trim((string) ($_POST['empty_state'] ?? '')),
                'intro'             => trim((string) ($_POST['intro'] ?? '')),
                'cms_source_lead'   => trim((string) ($_POST['cms_source_lead'] ?? '')),
                'cms_source_link'   => app_clean((string) ($_POST['cms_source_link'] ?? '')),
                'cms_source_url'    => trim((string) ($_POST['cms_source_url'] ?? '')),
                'instructions'      => trim((string) ($_POST['instructions'] ?? '')),
                'form_title'        => app_clean((string) ($_POST['form_title'] ?? '')),
                'submit_label'      => app_clean((string) ($_POST['submit_label'] ?? '')),
                'load_more_label'   => app_clean((string) ($_POST['load_more_label'] ?? '')),
            ];
            app_content_save($stored, $editLang);
            app_flash('success', 'Section Vos projets mise à jour.');
            app_redirect('/admin?section=content' . $editLangQS);

        case 'save_footer':
            $lines = preg_split("/\r?\n/", (string) ($_POST['legal_lines'] ?? '')) ?: [];
            $lines = array_values(array_filter(array_map('trim', $lines), static fn ($l) => $l !== ''));
            $stored['footer'] = [
                'copyright'    => app_clean((string) ($_POST['copyright'] ?? '')),
                'legal_link'   => app_clean((string) ($_POST['legal_link'] ?? '')),
                'contact_link' => app_clean((string) ($_POST['contact_link'] ?? '')),
                'legal_lines'  => $lines,
            ];
            $stored['contact'] = [
                'title'        => app_clean((string) ($_POST['contact_title'] ?? '')),
                'lead'         => trim((string) ($_POST['contact_lead'] ?? '')),
                'submit_label' => app_clean((string) ($_POST['contact_submit_label'] ?? '')),
            ];
            $stored['nav'] = [
                'featured'  => app_clean((string) ($_POST['nav_featured'] ?? '')),
                'creations' => app_clean((string) ($_POST['nav_creations'] ?? '')),
                'projects'  => app_clean((string) ($_POST['nav_projects'] ?? '')),
                'contact'   => app_clean((string) ($_POST['nav_contact'] ?? '')),
            ];
            app_content_save($stored, $editLang);
            app_flash('success', 'Pied de page et navigation mis à jour.');
            app_redirect('/admin?section=content' . $editLangQS);

        case 'reset_content':
            app_content_save([], $editLang);
            app_flash('success', 'Contenu réinitialisé aux valeurs par défaut (' . $editLang . ').');
            app_redirect('/admin?section=content' . $editLangQS);

        case 'change_password':
            $oldPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');
            if ($newPassword !== $confirmPassword) {
                app_flash('error', 'La confirmation ne correspond pas au nouveau mot de passe.');
                app_redirect('/admin?section=security' . $editLangQS);
            }
            $result = app_admin_change_password($oldPassword, $newPassword);
            if ($result['ok']) {
                app_flash('success', 'Mot de passe administrateur mis à jour.');
            } else {
                app_flash('error', $result['error']);
            }
            app_redirect('/admin?section=security' . $editLangQS);

        case 'project_status':
            $id = (string) ($_POST['id'] ?? '');
            $status = (string) ($_POST['status'] ?? 'pending');
            if (!in_array($status, ['pending', 'approved', 'rejected'], true)) {
                $status = 'pending';
            }
            app_user_projects_update($id, function (array &$p) use ($status): void {
                $p['status'] = $status;
                $p['moderated_at'] = app_now();
            });
            app_flash('success', "Projet mis à jour ({$status}).");
            app_redirect('/admin?section=projects');

        case 'project_delete':
            $id = (string) ($_POST['id'] ?? '');
            app_user_projects_delete($id);
            app_flash('success', 'Projet supprimé.');
            app_redirect('/admin?section=projects');

        case 'project_edit':
            $id = (string) ($_POST['id'] ?? '');
            $title = app_clean((string) ($_POST['title'] ?? ''));
            $description = trim((string) ($_POST['description'] ?? ''));
            $url = trim((string) ($_POST['url'] ?? ''));
            $image = app_clean((string) ($_POST['image'] ?? ''));
            app_user_projects_update($id, function (array &$p) use ($title, $description, $url, $image): void {
                if ($title !== '') $p['title'] = $title;
                if ($description !== '') $p['description'] = $description;
                if ($url !== '') $p['url'] = $url;
                if ($image !== '') $p['image'] = $image;
                $p['moderated_at'] = app_now();
            });
            app_flash('success', 'Projet édité.');
            app_redirect('/admin?section=projects');
    }
}

$section = (string) ($_GET['section'] ?? 'content');
$editLang = (string) ($_GET['edit_lang'] ?? 'fr');
if (!app_lang_is_supported($editLang)) { $editLang = 'fr'; }
$content = app_content($editLang);
$cfg     = app_config();
$languages = app_languages();
$flash   = app_pull_flash();
$loggedIn = app_admin_logged_in();
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin · Akasha Production</title>
<meta name="robots" content="noindex,nofollow">
<link rel="icon" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><circle cx='16' cy='16' r='14' fill='none' stroke='%235be8ff' stroke-width='2'/></svg>">
<link rel="stylesheet" href="assets/site.css?v=<?= filemtime(__DIR__ . '/assets/site.css'); ?>">
</head>
<body>

<div class="admin-layout">
  <div class="admin-shell">

    <header class="admin-header">
      <h1>Panel d'administration</h1>
      <?php if ($loggedIn): ?>
        <div style="display:flex;gap:0.6rem;align-items:center;">
          <span style="color:var(--text-muted);font-size:0.85rem;"><?= app_e($cfg['admin']['email']); ?></span>
          <a class="btn btn--small" href="/" target="_blank" rel="noopener">Voir le site</a>
          <a class="btn btn--small btn--ghost" href="/admin?logout=1">Déconnexion</a>
        </div>
      <?php endif; ?>
    </header>

    <?php if ($flash): ?>
      <div class="admin-flash admin-flash--<?= $flash['type'] === 'error' ? 'error' : 'success'; ?>"><?= app_e($flash['message']); ?></div>
    <?php endif; ?>

    <?php if (!$loggedIn): ?>

      <div class="admin-card" style="max-width:480px;margin:0 auto;">
        <h2>Connexion administrateur</h2>
        <form method="post" class="admin-form-grid" autocomplete="off">
          <input type="hidden" name="action" value="login">
          <label>
            <span>Email</span>
            <input type="email" name="email" required autocomplete="username" placeholder="admin@akashaproduction.com">
          </label>
          <label>
            <span>Mot de passe</span>
            <input type="password" name="password" required autocomplete="current-password">
          </label>
          <div style="display:flex;justify-content:flex-end;">
            <button class="btn btn--primary" type="submit">Entrer</button>
          </div>
        </form>
      </div>

    <?php else: ?>

      <nav class="admin-tabs">
        <a class="admin-tab<?= $section === 'content' ? ' is-active' : ''; ?>" href="/admin?section=content&amp;edit_lang=<?= app_e($editLang); ?>">Contenu</a>
        <a class="admin-tab<?= $section === 'creations' ? ' is-active' : ''; ?>" href="/admin?section=creations&amp;edit_lang=<?= app_e($editLang); ?>">Nos créations</a>
        <a class="admin-tab<?= $section === 'projects' ? ' is-active' : ''; ?>" href="/admin?section=projects">Vos projets <?php $pending = count(array_filter(app_user_projects(), fn($p) => ($p['status'] ?? '') === 'pending')); echo $pending ? "({$pending})" : ''; ?></a>
        <a class="admin-tab<?= $section === 'contacts' ? ' is-active' : ''; ?>" href="/admin?section=contacts">Messages</a>
        <a class="admin-tab<?= $section === 'security' ? ' is-active' : ''; ?>" href="/admin?section=security">Sécurité</a>
      </nav>

      <?php if (in_array($section, ['content', 'creations'], true)): ?>
        <div class="admin-lang-selector" style="display:flex;align-items:center;gap:0.6rem;margin:0 0 1.2rem 0;padding:0.7rem 1rem;background:rgba(91,232,255,0.05);border:1px solid rgba(91,232,255,0.18);border-radius:10px;">
          <span style="color:var(--text-muted);font-size:0.85rem;">Langue à éditer :</span>
          <select onchange="window.location.href=this.value" style="background:rgba(3,2,10,0.5);border:1px solid rgba(91,232,255,0.3);color:var(--neon-cyan);padding:0.4rem 0.7rem;border-radius:6px;font-family:var(--font-display);">
            <?php foreach ($languages as $code => $info): ?>
              <option value="/admin?section=<?= app_e($section); ?>&amp;edit_lang=<?= app_e($code); ?>"<?= $code === $editLang ? ' selected' : ''; ?>><?= app_e($info['native']); ?> (<?= strtoupper(app_e($code)); ?>)</option>
            <?php endforeach; ?>
          </select>
          <span style="color:var(--text-muted);font-size:0.78rem;">— les modifications s'appliquent uniquement à cette langue.</span>
        </div>
      <?php endif; ?>

      <?php if ($section === 'content'): ?>
        <?php $hero = $content['hero']; $featured = $content['featured']; $proj = $content['projects']; $foot = $content['footer']; $contact = $content['contact']; $nav = $content['nav']; $site = $content['site']; ?>

        <article class="admin-card">
          <h2>Métadonnées du site</h2>
          <form method="post" class="admin-form-grid">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="save_site">
            <input type="hidden" name="section" value="content">
            <label><span>Meta title</span><input type="text" name="meta_title" value="<?= app_e($site['meta_title']); ?>" maxlength="160"></label>
            <label><span>Meta description</span><textarea name="meta_description" maxlength="240"><?= app_e($site['meta_description']); ?></textarea></label>
            <label><span>Libellé bouton « Entrer »</span><input type="text" name="enter_label" value="<?= app_e($site['enter_label']); ?>" maxlength="40"></label>
            <label><span>Libellé loader</span><input type="text" name="loader_label" value="<?= app_e($site['loader_label']); ?>" maxlength="60"></label>
            <div style="display:flex;justify-content:flex-end;"><button class="btn btn--primary" type="submit">Enregistrer</button></div>
          </form>
        </article>

        <article class="admin-card">
          <h2>Hero</h2>
          <form method="post" class="admin-form-grid">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="save_hero">
            <input type="hidden" name="section" value="content">
            <label><span>Eyebrow</span><input type="text" name="eyebrow" value="<?= app_e($hero['eyebrow']); ?>" maxlength="60"></label>
            <label><span>Titre</span><input type="text" name="title" value="<?= app_e($hero['title']); ?>" maxlength="120"></label>
            <label><span>Lead (paragraphe)</span><textarea name="lead" maxlength="800"><?= app_e($hero['lead']); ?></textarea></label>
            <div class="admin-grid">
              <label><span>CTA Créations</span><input type="text" name="cta_creations" value="<?= app_e($hero['cta_creations']); ?>" maxlength="40"></label>
              <label><span>CTA Projets</span><input type="text" name="cta_projects" value="<?= app_e($hero['cta_projects']); ?>" maxlength="40"></label>
            </div>
            <div style="display:flex;justify-content:flex-end;"><button class="btn btn--primary" type="submit">Enregistrer</button></div>
          </form>
        </article>

        <article class="admin-card">
          <h2>À la une (Aletheia)</h2>
          <form method="post" class="admin-form-grid">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="save_featured">
            <input type="hidden" name="section" value="content">
            <label><span>Eyebrow</span><input type="text" name="eyebrow" value="<?= app_e($featured['eyebrow']); ?>" maxlength="40"></label>
            <label><span>Titre</span><input type="text" name="title" value="<?= app_e($featured['title']); ?>" maxlength="120"></label>
            <label><span>Description</span><textarea name="description" maxlength="2000"><?= app_e($featured['description']); ?></textarea></label>
            <div class="admin-grid">
              <label><span>Image (chemin)</span><input type="text" name="image" value="<?= app_e($featured['image']); ?>"></label>
              <label><span>Image WebP (chemin)</span><input type="text" name="image_webp" value="<?= app_e($featured['image_webp']); ?>"></label>
            </div>
            <label><span>Alt image</span><input type="text" name="image_alt" value="<?= app_e($featured['image_alt']); ?>" maxlength="160"></label>
            <div class="admin-grid">
              <label><span>CTA libellé</span><input type="text" name="cta_label" value="<?= app_e($featured['cta_label']); ?>" maxlength="60"></label>
              <label><span>CTA URL</span><input type="url" name="cta_url" value="<?= app_e($featured['cta_url']); ?>"></label>
            </div>
            <div style="display:flex;justify-content:flex-end;"><button class="btn btn--primary" type="submit">Enregistrer</button></div>
          </form>
        </article>

        <article class="admin-card">
          <h2>Section « Vos projets »</h2>
          <form method="post" class="admin-form-grid">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="save_projects_section">
            <input type="hidden" name="section" value="content">
            <div class="admin-grid">
              <label><span>Eyebrow</span><input type="text" name="eyebrow" value="<?= app_e($proj['eyebrow']); ?>" maxlength="40"></label>
              <label><span>Titre</span><input type="text" name="title" value="<?= app_e($proj['title']); ?>" maxlength="120"></label>
            </div>
            <label><span>État vide (texte si aucun projet publié)</span><textarea name="empty_state" maxlength="500"><?= app_e($proj['empty_state']); ?></textarea></label>
            <label><span>Intro</span><textarea name="intro" maxlength="800"><?= app_e($proj['intro']); ?></textarea></label>
            <h3 style="margin:1rem 0 0;font-family:var(--font-display);color:var(--neon-cyan);">Lien CMS-Source.org</h3>
            <p class="form__hint">Affiché sous l'intro pour rediriger les visiteurs vers l'espace création.</p>
            <label><span>Phrase d'amorce</span><input type="text" name="cms_source_lead" value="<?= app_e($proj['cms_source_lead'] ?? ''); ?>" maxlength="160"></label>
            <div class="admin-grid">
              <label><span>Texte du lien</span><input type="text" name="cms_source_link" value="<?= app_e($proj['cms_source_link'] ?? ''); ?>" maxlength="120"></label>
              <label><span>URL du lien</span><input type="url" name="cms_source_url" value="<?= app_e($proj['cms_source_url'] ?? ''); ?>"></label>
            </div>
            <label><span>Instructions au-dessus du formulaire (laisser vide pour masquer)</span><textarea name="instructions" maxlength="800"><?= app_e($proj['instructions']); ?></textarea></label>
            <div class="admin-grid">
              <label><span>Titre formulaire</span><input type="text" name="form_title" value="<?= app_e($proj['form_title']); ?>" maxlength="80"></label>
              <label><span>Bouton envoi</span><input type="text" name="submit_label" value="<?= app_e($proj['submit_label']); ?>" maxlength="40"></label>
              <label><span>Bouton « Voir plus »</span><input type="text" name="load_more_label" value="<?= app_e($proj['load_more_label']); ?>" maxlength="40"></label>
            </div>
            <div style="display:flex;justify-content:flex-end;"><button class="btn btn--primary" type="submit">Enregistrer</button></div>
          </form>
        </article>

        <article class="admin-card">
          <h2>Pied de page · navigation · contact</h2>
          <form method="post" class="admin-form-grid">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="save_footer">
            <input type="hidden" name="section" value="content">
            <div class="admin-grid">
              <label><span>Copyright</span><input type="text" name="copyright" value="<?= app_e($foot['copyright']); ?>" maxlength="120"></label>
              <label><span>Lien légal</span><input type="text" name="legal_link" value="<?= app_e($foot['legal_link']); ?>" maxlength="60"></label>
              <label><span>Lien contact</span><input type="text" name="contact_link" value="<?= app_e($foot['contact_link']); ?>" maxlength="60"></label>
            </div>
            <label><span>Lignes légales (une par ligne)</span><textarea name="legal_lines" maxlength="1200"><?= app_e(implode("\n", $foot['legal_lines'])); ?></textarea></label>

            <h3 style="margin:1.2rem 0 0;font-family:var(--font-display);color:var(--neon-cyan);">Lightbox Contact</h3>
            <div class="admin-grid">
              <label><span>Titre</span><input type="text" name="contact_title" value="<?= app_e($contact['title']); ?>" maxlength="120"></label>
              <label><span>Bouton envoi</span><input type="text" name="contact_submit_label" value="<?= app_e($contact['submit_label']); ?>" maxlength="40"></label>
            </div>
            <label><span>Texte d'intro</span><textarea name="contact_lead" maxlength="600"><?= app_e($contact['lead']); ?></textarea></label>

            <h3 style="margin:1.2rem 0 0;font-family:var(--font-display);color:var(--neon-cyan);">Navigation</h3>
            <div class="admin-grid">
              <label><span>À la une</span><input type="text" name="nav_featured" value="<?= app_e($nav['featured']); ?>" maxlength="40"></label>
              <label><span>Créations</span><input type="text" name="nav_creations" value="<?= app_e($nav['creations']); ?>" maxlength="40"></label>
              <label><span>Projets</span><input type="text" name="nav_projects" value="<?= app_e($nav['projects']); ?>" maxlength="40"></label>
              <label><span>Contact</span><input type="text" name="nav_contact" value="<?= app_e($nav['contact']); ?>" maxlength="40"></label>
            </div>

            <div style="display:flex;justify-content:flex-end;"><button class="btn btn--primary" type="submit">Enregistrer</button></div>
          </form>
        </article>

        <article class="admin-card">
          <h2>Réinitialisation</h2>
          <p style="color:var(--text-soft);">Restaure tous les contenus aux valeurs par défaut. Les projets soumis ne sont pas effacés.</p>
          <form method="post" onsubmit="return confirm('Réinitialiser tous les contenus ? Vos projets soumis sont conservés.');">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="reset_content">
            <input type="hidden" name="section" value="content">
            <button class="btn btn--ghost" type="submit">Réinitialiser le contenu</button>
          </form>
        </article>

      <?php elseif ($section === 'creations'): ?>
        <?php $cre = $content['creations']; ?>

        <article class="admin-card">
          <h2>Nos créations</h2>
          <form method="post" class="admin-form-grid">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="save_creations">
            <input type="hidden" name="section" value="creations">
            <div class="admin-grid">
              <label><span>Eyebrow</span><input type="text" name="eyebrow" value="<?= app_e($cre['eyebrow']); ?>" maxlength="40"></label>
              <label><span>Titre</span><input type="text" name="title" value="<?= app_e($cre['title']); ?>" maxlength="120"></label>
            </div>
            <label><span>Intro</span><textarea name="intro" maxlength="600"><?= app_e($cre['intro']); ?></textarea></label>

            <h3 style="margin:1.4rem 0 0.4rem;font-family:var(--font-display);color:var(--neon-cyan);">Cartes</h3>
            <p class="form__hint">Renseigne les sites présentés. Une carte avec « disponible » désactivé sera floutée et marquée « À venir ».</p>
            <div class="admin-card-grid">
              <?php foreach ($cre['cards'] as $i => $card): ?>
                <details<?= $i < 2 ? ' open' : ''; ?>>
                  <summary><?= app_e($card['name'] ?: 'Carte ' . ($i + 1)); ?></summary>
                  <div class="admin-form-grid" style="padding-top:0.8rem;">
                    <label><span>Nom</span><input type="text" name="card_name[]" value="<?= app_e($card['name']); ?>" maxlength="80"></label>
                    <label><span>Description</span><textarea name="card_description[]" maxlength="400"><?= app_e($card['description']); ?></textarea></label>
                    <div class="admin-grid">
                      <label><span>URL</span><input type="text" name="card_url[]" value="<?= app_e($card['url']); ?>"></label>
                      <label><span>Disponible</span>
                        <select name="card_available[<?= $i ?>]">
                          <option value="1"<?= !empty($card['available']) ? ' selected' : ''; ?>>Oui — visible</option>
                          <option value=""<?= empty($card['available']) ? ' selected' : ''; ?>>Non — floutée + « À venir »</option>
                        </select>
                      </label>
                    </div>
                    <div class="admin-grid">
                      <label><span>Image (jpg)</span><input type="text" name="card_image[]" value="<?= app_e($card['image']); ?>"></label>
                      <label><span>Image (webp)</span><input type="text" name="card_image_webp[]" value="<?= app_e($card['image_webp'] ?? ''); ?>"></label>
                    </div>
                  </div>
                </details>
              <?php endforeach; ?>

              <details>
                <summary>+ Ajouter une nouvelle carte</summary>
                <div class="admin-form-grid" style="padding-top:0.8rem;">
                  <label><span>Nom</span><input type="text" name="card_name[]" value="" maxlength="80"></label>
                  <label><span>Description</span><textarea name="card_description[]" maxlength="400"></textarea></label>
                  <div class="admin-grid">
                    <label><span>URL</span><input type="text" name="card_url[]" value=""></label>
                    <label><span>Disponible</span>
                      <select name="card_available[<?= count($cre['cards']) ?>]">
                        <option value="1">Oui — visible</option>
                        <option value="">Non — floutée + « À venir »</option>
                      </select>
                    </label>
                  </div>
                  <div class="admin-grid">
                    <label><span>Image (jpg)</span><input type="text" name="card_image[]" value=""></label>
                    <label><span>Image (webp)</span><input type="text" name="card_image_webp[]" value=""></label>
                  </div>
                </div>
              </details>
            </div>

            <div style="display:flex;justify-content:flex-end;margin-top:1rem;">
              <button class="btn btn--primary" type="submit">Enregistrer les cartes</button>
            </div>
          </form>
        </article>

      <?php elseif ($section === 'projects'): ?>
        <?php $allProjects = app_user_projects(); ?>
        <article class="admin-card">
          <h2>Modération des projets soumis</h2>
          <p style="color:var(--text-soft);">Approuver un projet le rend visible sur la page publique. Le plus récent en grand, puis les autres en grille.</p>
          <?php if (!$allProjects): ?>
            <p class="form__hint">Aucun projet soumis pour l'instant.</p>
          <?php else: ?>
            <div class="admin-list">
              <?php foreach ($allProjects as $p): ?>
                <article class="admin-row">
                  <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
                    <div style="min-width:0;flex:1;">
                      <p class="admin-row__title"><?= app_e((string) $p['title']); ?></p>
                      <p class="admin-row__meta">
                        <?= app_e((string) ($p['submitter']['first_name'] ?? '')); ?>
                        <?= app_e((string) ($p['submitter']['last_name'] ?? '')); ?>
                        · <?= app_e((string) ($p['submitter']['email'] ?? '')); ?>
                        <?= !empty($p['submitter']['phone']) ? ' · ' . app_e((string) $p['submitter']['phone']) : ''; ?>
                        · <?= app_e(date('d/m/Y H:i', strtotime((string) $p['submitted_at']))); ?>
                      </p>
                    </div>
                    <span class="card__badge"><?= app_e((string) ($p['status'] ?? 'pending')); ?></span>
                  </div>
                  <p style="white-space:pre-wrap;color:var(--text-soft);font-size:0.92rem;"><?= app_e((string) ($p['description'] ?? '')); ?></p>
                  <?php if (!empty($p['url'])): ?>
                    <p><a href="<?= app_e((string) $p['url']); ?>" target="_blank" rel="noopener"><?= app_e((string) $p['url']); ?></a></p>
                  <?php endif; ?>
                  <?php if (!empty($p['image'])): ?>
                    <img src="<?= app_e((string) $p['image']); ?>" alt="" style="max-width:240px;border-radius:8px;">
                  <?php endif; ?>
                  <?php if (!empty($p['note'])): ?>
                    <p class="admin-row__meta">Note auteur·rice : <?= app_e((string) $p['note']); ?></p>
                  <?php endif; ?>

                  <details>
                    <summary style="cursor:pointer;color:var(--neon-cyan);font-family:var(--font-display);">Éditer</summary>
                    <form method="post" class="admin-form-grid" style="padding-top:0.6rem;">
                      <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
                      <input type="hidden" name="action" value="project_edit">
                      <input type="hidden" name="section" value="projects">
                      <input type="hidden" name="id" value="<?= app_e((string) $p['id']); ?>">
                      <label><span>Titre</span><input type="text" name="title" value="<?= app_e((string) $p['title']); ?>"></label>
                      <label><span>Description</span><textarea name="description" rows="3"><?= app_e((string) $p['description']); ?></textarea></label>
                      <div class="admin-grid">
                        <label><span>URL</span><input type="text" name="url" value="<?= app_e((string) ($p['url'] ?? '')); ?>"></label>
                        <label><span>Image (chemin)</span><input type="text" name="image" value="<?= app_e((string) ($p['image'] ?? '')); ?>"></label>
                      </div>
                      <div style="display:flex;justify-content:flex-end;"><button class="btn btn--small" type="submit">Sauvegarder</button></div>
                    </form>
                  </details>

                  <div class="admin-actions">
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
                      <input type="hidden" name="action" value="project_status">
                      <input type="hidden" name="section" value="projects">
                      <input type="hidden" name="id" value="<?= app_e((string) $p['id']); ?>">
                      <input type="hidden" name="status" value="approved">
                      <button class="btn btn--small btn--primary" type="submit">Approuver</button>
                    </form>
                    <form method="post" style="display:inline;">
                      <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
                      <input type="hidden" name="action" value="project_status">
                      <input type="hidden" name="section" value="projects">
                      <input type="hidden" name="id" value="<?= app_e((string) $p['id']); ?>">
                      <input type="hidden" name="status" value="rejected">
                      <button class="btn btn--small btn--ghost" type="submit">Refuser</button>
                    </form>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Supprimer définitivement ce projet ?');">
                      <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
                      <input type="hidden" name="action" value="project_delete">
                      <input type="hidden" name="section" value="projects">
                      <input type="hidden" name="id" value="<?= app_e((string) $p['id']); ?>">
                      <button class="btn btn--small btn--ghost" type="submit">Supprimer</button>
                    </form>
                  </div>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>

      <?php elseif ($section === 'contacts'): ?>
        <?php $messages = app_read_json('contact-messages.json', []); ?>
        <article class="admin-card">
          <h2>Messages reçus</h2>
          <?php if (!$messages): ?>
            <p class="form__hint">Aucun message reçu pour l'instant.</p>
          <?php else: ?>
            <div class="admin-list">
              <?php foreach ($messages as $m): ?>
                <article class="admin-row">
                  <p class="admin-row__title"><?= app_e((string) $m['subject']); ?></p>
                  <p class="admin-row__meta">
                    <?= app_e((string) $m['first_name']); ?> <?= app_e((string) $m['last_name']); ?>
                    · <a href="mailto:<?= app_e((string) $m['email']); ?>"><?= app_e((string) $m['email']); ?></a>
                    · <?= app_e(date('d/m/Y H:i', strtotime((string) $m['created_at']))); ?>
                  </p>
                  <p style="white-space:pre-wrap;color:var(--text-soft);font-size:0.92rem;"><?= app_e((string) $m['message']); ?></p>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </article>

      <?php elseif ($section === 'security'): ?>
        <article class="admin-card">
          <h2>Sécurité — mot de passe administrateur</h2>
          <p class="form__hint">Compte connecté : <strong><?= app_e($cfg['admin']['email']); ?></strong>.<br>Le nouveau hash bcrypt sera écrit dans <code>data/admin-credentials.json</code> et prendra effet immédiatement, sans toucher à <code>includes/config.php</code>. Pour réinitialiser, supprime ce fichier et le hash de <code>config.php</code> reprend la main.</p>
          <form method="post" class="admin-form-grid" autocomplete="off" style="max-width:540px;">
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>"><input type="hidden" name="edit_lang" value="<?= app_e($editLang); ?>">
            <input type="hidden" name="action" value="change_password">
            <input type="hidden" name="section" value="security">
            <label><span>Mot de passe actuel</span><input type="password" name="current_password" required autocomplete="current-password"></label>
            <label><span>Nouveau mot de passe (10 caractères minimum)</span><input type="password" name="new_password" required minlength="10" autocomplete="new-password"></label>
            <label><span>Confirmation du nouveau mot de passe</span><input type="password" name="confirm_password" required minlength="10" autocomplete="new-password"></label>
            <div style="display:flex;justify-content:flex-end;"><button class="btn btn--primary" type="submit">Mettre à jour le mot de passe</button></div>
          </form>
        </article>

      <?php endif; ?>

    <?php endif; ?>

  </div>
</div>

</body>
</html>
