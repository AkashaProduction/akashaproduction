<?php
require __DIR__ . '/includes/bootstrap.php';
require __DIR__ . '/includes/content.php';

$lang     = app_lang_resolve();
$langDir  = app_lang_dir($lang);
$languages = app_languages();
$content  = app_content($lang);
$cfg      = app_config();
$projects = app_user_projects_published();
$initialBatch = 6;
$published = $projects;
$featured = array_shift($published);
$row3a = array_slice($published, 0, 3);
$row2  = array_slice($published, 3, 2);
$row3b = array_slice($published, 5, 3);
$remaining = array_slice($published, 8);
?><!DOCTYPE html>
<html lang="<?= app_e($lang); ?>" dir="<?= app_e($langDir); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= app_e($content['site']['meta_title']); ?></title>
<meta name="description" content="<?= app_e($content['site']['meta_description']); ?>">
<meta name="theme-color" content="#03020a">
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><defs><linearGradient id='g' x1='0' x2='1' y1='0' y2='1'><stop offset='0' stop-color='%235be8ff'/><stop offset='0.5' stop-color='%23a87bff'/><stop offset='1' stop-color='%23ff5cf0'/></linearGradient></defs><circle cx='16' cy='16' r='14' fill='none' stroke='url(%23g)' stroke-width='2'/><circle cx='16' cy='16' r='3' fill='url(%23g)'/></svg>">
<link rel="preload" as="image" href="assets/img/akasha-logo.webp" type="image/webp">
<link rel="preload" as="image" href="assets/img/cosmic-bg.webp" type="image/webp">
<link rel="stylesheet" href="assets/site.css?v=<?= filemtime(__DIR__ . '/assets/site.css'); ?>">
<meta property="og:title" content="<?= app_e($content['site']['meta_title']); ?>">
<meta property="og:description" content="<?= app_e($content['site']['meta_description']); ?>">
<meta property="og:image" content="/assets/img/akasha-logo.jpg">
<meta property="og:type" content="website">
</head>
<body class="is-locked">

<!-- =========================================================
     LOADER — countdown 3,2,1 with three dots tracing a circle
     ========================================================= -->
<div class="loader" id="loader" aria-hidden="false" role="status" aria-label="<?= app_e($content['forms']['loader_aria']); ?>">
  <div class="loader__inner">
    <svg class="loader__svg" viewBox="0 0 200 200" aria-hidden="true">
      <defs>
        <linearGradient id="loaderGradient" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%"   stop-color="#5be8ff"/>
          <stop offset="35%"  stop-color="#a87bff"/>
          <stop offset="70%"  stop-color="#ff5cf0"/>
          <stop offset="100%" stop-color="#ffa15c"/>
        </linearGradient>
        <linearGradient id="loaderGradientText" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%"   stop-color="#5be8ff"/>
          <stop offset="50%"  stop-color="#a87bff"/>
          <stop offset="100%" stop-color="#ff5cf0"/>
        </linearGradient>
        <filter id="loaderGlow" x="-50%" y="-50%" width="200%" height="200%">
          <feGaussianBlur stdDeviation="3" result="blur"/>
          <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
      </defs>

      <!-- faint orbital ring -->
      <circle cx="100" cy="100" r="68" fill="none" stroke="url(#loaderGradient)" stroke-width="0.6" opacity="0.32"/>
      <circle cx="100" cy="100" r="78" fill="none" stroke="url(#loaderGradient)" stroke-width="0.3" opacity="0.18"/>

      <!-- three orbiting dots -->
      <g class="loader__dots" style="transform-origin:100px 100px; animation: loaderSpin 3s linear infinite;">
        <circle cx="100" cy="32" r="6" fill="url(#loaderGradient)" filter="url(#loaderGlow)"/>
        <circle cx="158.88" cy="134" r="6" fill="url(#loaderGradient)" filter="url(#loaderGlow)" opacity="0.85"/>
        <circle cx="41.12" cy="134" r="6" fill="url(#loaderGradient)" filter="url(#loaderGlow)" opacity="0.7"/>
      </g>

      <!-- countdown text inside the ring -->
      <text id="loaderCount" x="100" y="120" text-anchor="middle" font-family="Cinzel, serif" font-size="64" font-weight="700" fill="url(#loaderGradientText)" filter="url(#loaderGlow)" letter-spacing="2">3</text>
    </svg>
    <span class="loader__brand"><?= app_e($content['site']['loader_label']); ?></span>
  </div>
</div>

<style>
@keyframes loaderSpin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
@keyframes loaderPulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.45; } }
.loader__svg #loaderCount { animation: loaderPulse 1s ease-in-out infinite; }
</style>

<!-- =========================================================
     WELCOME — image 1 fullscreen with "Entrer" CTA
     ========================================================= -->
<div class="welcome" id="welcome" aria-hidden="true">
  <picture>
    <source srcset="assets/img/akasha-logo.webp" type="image/webp">
    <img class="welcome__image" src="assets/img/akasha-logo.jpg" alt="Akasha Production — entrée dans l'univers" id="welcomeImage" decoding="async">
  </picture>
  <div class="welcome__veil"></div>
  <button class="btn btn--primary welcome__cta" id="welcomeEnter" type="button">
    <?= app_e($content['site']['enter_label']); ?>
  </button>
</div>

<!-- =========================================================
     COSMIC STAGE — image 2 fixed background
     ========================================================= -->
<div class="cosmic-stage" aria-hidden="true">
  <picture>
    <source srcset="assets/img/cosmic-bg.webp" type="image/webp">
    <img class="cosmic-stage__image" id="cosmicBg" src="assets/img/cosmic-bg.jpg" alt="" decoding="async" loading="eager">
  </picture>
</div>

<!-- =========================================================
     APP — main page content
     ========================================================= -->
<div class="app-shell" id="appShell">

  <!-- NAVBAR -->
  <header class="nav" id="mainNav">
    <div class="nav__row">
      <a class="nav__brand" href="#top" aria-label="Akasha Production">
        <svg class="nav__brand-glyph" viewBox="0 0 32 32" aria-hidden="true">
          <defs>
            <linearGradient id="navGlyph" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0%" stop-color="#5be8ff"/>
              <stop offset="60%" stop-color="#a87bff"/>
              <stop offset="100%" stop-color="#ff5cf0"/>
            </linearGradient>
          </defs>
          <circle cx="16" cy="16" r="13" fill="none" stroke="url(#navGlyph)" stroke-width="1.4"/>
          <circle cx="16" cy="16" r="2.5" fill="url(#navGlyph)"/>
        </svg>
        <span><?= app_e($cfg['site']['name']); ?></span>
      </a>
      <button class="nav__toggle" type="button" id="navToggle" aria-expanded="false" aria-controls="navMenu">
        <span class="sr-only"><?= app_e($content['forms']['open_menu']); ?></span>
        <svg viewBox="0 0 20 20" width="18" height="18" aria-hidden="true">
          <path d="M3 6h14M3 10h14M3 14h14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
      </button>
      <nav>
        <ul class="nav__menu" id="navMenu">
          <li><a class="nav__link" href="#featured"><?= app_e($content['nav']['featured']); ?></a></li>
          <li><a class="nav__link" href="#creations"><?= app_e($content['nav']['creations']); ?></a></li>
          <li><a class="nav__link" href="#projects"><?= app_e($content['nav']['projects']); ?></a></li>
          <li><button class="nav__link" type="button" data-lightbox="contact"><?= app_e($content['nav']['contact']); ?></button></li>
        </ul>
      </nav>
      <div class="lang-switcher" id="langSwitcher">
        <button class="lang-switcher__current" type="button" aria-haspopup="listbox" aria-expanded="false" aria-label="<?= app_e($languages[$lang]['name']); ?>">
          <img class="lang-switcher__flag" src="assets/img/flags/<?= app_e($languages[$lang]['flag']); ?>.svg" alt="" width="20" height="15">
          <span class="lang-switcher__code"><?= strtoupper(app_e($lang)); ?></span>
          <svg class="lang-switcher__chev" viewBox="0 0 12 8" width="10" height="7" aria-hidden="true">
            <path d="M1 1.5l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <ul class="lang-switcher__menu" role="listbox">
          <?php foreach ($languages as $code => $info): ?>
            <li role="option" aria-selected="<?= $code === $lang ? 'true' : 'false'; ?>">
              <a class="lang-switcher__item<?= $code === $lang ? ' is-active' : ''; ?>" href="?lang=<?= app_e($code); ?>" hreflang="<?= app_e($code); ?>">
                <img class="lang-switcher__flag" src="assets/img/flags/<?= app_e($info['flag']); ?>.svg" alt="" width="20" height="15">
                <span><?= app_e($info['native']); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </header>

  <main id="top">

    <!-- HERO -->
    <section class="section hero" id="hero">
      <div class="shell hero__grid">
        <div class="hero__copy halo-text">
          <span class="eyebrow"><?= app_e($content['hero']['eyebrow']); ?></span>
          <h1 class="hero__title"><?= app_e($content['hero']['title']); ?></h1>
          <p class="hero__lead"><?= app_e($content['hero']['lead']); ?></p>
          <div class="hero__ctas">
            <a class="btn btn--primary" href="#creations"><?= app_e($content['hero']['cta_creations']); ?></a>
            <a class="btn" href="#projects"><?= app_e($content['hero']['cta_projects']); ?></a>
          </div>
        </div>
        <figure class="hero__visual">
          <picture>
            <source srcset="assets/img/akasha-logo.webp" type="image/webp">
            <img src="assets/img/akasha-logo.jpg" alt="Identité Akasha Production — constellation" loading="lazy" decoding="async">
          </picture>
        </figure>
      </div>
    </section>

    <!-- FEATURED -->
    <section class="section section--featured" id="featured">
      <div class="shell">
        <div class="section-heading halo-text">
          <span class="eyebrow"><?= app_e($content['featured']['eyebrow']); ?></span>
          <h2 class="section-title"><?= app_e($content['featured']['title']); ?></h2>
        </div>
        <article class="featured">
          <figure class="featured__media">
            <picture>
              <?php if (!empty($content['featured']['image_webp'])): ?>
                <source srcset="<?= app_e($content['featured']['image_webp']); ?>" type="image/webp">
              <?php endif; ?>
              <img src="<?= app_e($content['featured']['image']); ?>" alt="<?= app_e($content['featured']['image_alt']); ?>" loading="lazy" decoding="async">
            </picture>
          </figure>
          <div class="featured__body">
            <div class="halo-text">
              <p><?= app_e($content['featured']['description']); ?></p>
            </div>
            <div>
              <a class="btn btn--primary" href="<?= app_e($content['featured']['cta_url']); ?>" target="_blank" rel="noopener">
                <?= app_e($content['featured']['cta_label']); ?>
                <svg viewBox="0 0 20 20" width="16" height="16" aria-hidden="true">
                  <path d="M5 5h10v10M5 15L15 5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            </div>
          </div>
        </article>
      </div>
    </section>

    <!-- CREATIONS -->
    <section class="section" id="creations">
      <div class="shell">
        <div class="section-heading halo-text">
          <span class="eyebrow"><?= app_e($content['creations']['eyebrow']); ?></span>
          <h2 class="section-title"><?= app_e($content['creations']['title']); ?></h2>
          <p><?= app_e($content['creations']['intro']); ?></p>
        </div>
        <div class="creations__grid">
          <?php foreach ($content['creations']['cards'] as $card): ?>
            <?php
              $available = !empty($card['available']);
              $url = (string) ($card['url'] ?? '');
              $domain = $url ? preg_replace('#^https?://(www\.)?#', '', $url) : '';
              $domain = $domain ? rtrim((string) strtok($domain, '/'), '/') : '';
            ?>
            <article class="card<?= $available ? '' : ' card--unavailable'; ?>">
              <div class="card__thumb">
                <picture>
                  <?php if (!empty($card['image_webp'])): ?>
                    <source srcset="<?= app_e($card['image_webp']); ?>" type="image/webp">
                  <?php endif; ?>
                  <img src="<?= app_e($card['image']); ?>" alt="<?= app_e($card['name']); ?>" loading="lazy" decoding="async">
                </picture>
                <div class="card__thumb-overlay">
                  <?php if (!$available): ?>
                    <span class="card__badge">À venir</span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="card__body">
                <h3 class="card__title"><?= app_e($card['name']); ?></h3>
                <p class="card__copy"><?= app_e($card['description']); ?></p>
                <div class="card__footer">
                  <span class="card__domain"><?= app_e($domain); ?></span>
                  <?php if ($available && $url !== ''): ?>
                    <a class="btn btn--small btn--ghost" href="<?= app_e($url); ?>" target="_blank" rel="noopener">Visiter</a>
                  <?php else: ?>
                    <span class="btn btn--small btn--disabled" aria-disabled="true">À venir</span>
                  <?php endif; ?>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- VOS PROJETS -->
    <section class="section" id="projects">
      <div class="shell">
        <div class="section-heading halo-text">
          <span class="eyebrow"><?= app_e($content['projects']['eyebrow']); ?></span>
          <h2 class="section-title"><?= app_e($content['projects']['title']); ?></h2>
          <p><?= app_e($content['projects']['intro']); ?></p>
          <?php if (!empty($content['projects']['cms_source_link']) && !empty($content['projects']['cms_source_url'])): ?>
            <p>
              <?= app_e($content['projects']['cms_source_lead'] ?? ''); ?>
              <a href="<?= app_e($content['projects']['cms_source_url']); ?>" target="_blank" rel="noopener"><?= app_e($content['projects']['cms_source_link']); ?></a>.
            </p>
          <?php endif; ?>
        </div>

        <div class="user-projects" id="userProjects">
          <?php if (!$projects): ?>
            <div class="halo-text user-projects__empty">
              <p><?= app_e($content['projects']['empty_state']); ?></p>
            </div>
          <?php else: ?>
            <?php if ($featured): ?>
              <div class="user-projects__featured">
                <?= render_user_project_card($featured, 'featured'); ?>
              </div>
            <?php endif; ?>
            <?php if ($row3a): ?>
              <div class="user-projects__row user-projects__row--3">
                <?php foreach ($row3a as $p): echo render_user_project_card($p); endforeach; ?>
              </div>
            <?php endif; ?>
            <?php if ($row2): ?>
              <div class="user-projects__row user-projects__row--2">
                <?php foreach ($row2 as $p): echo render_user_project_card($p); endforeach; ?>
              </div>
            <?php endif; ?>
            <?php if ($row3b): ?>
              <div class="user-projects__row user-projects__row--3">
                <?php foreach ($row3b as $p): echo render_user_project_card($p); endforeach; ?>
              </div>
            <?php endif; ?>
            <div class="user-projects__more" id="userProjectsMore"></div>
            <?php if ($remaining): ?>
              <div style="text-align:center;">
                <button class="btn" type="button" id="userProjectsLoadMore" data-batch="8"><?= app_e($content['projects']['load_more_label']); ?></button>
              </div>
            <?php endif; ?>
          <?php endif; ?>
        </div>

        <?php if (!empty(trim((string) ($content['projects']['instructions'] ?? '')))): ?>
          <div class="halo-text user-projects__intro" style="margin-top:2.4rem;">
            <p><?= app_e($content['projects']['instructions']); ?></p>
          </div>
        <?php endif; ?>

        <div class="user-projects__form-wrap">
          <form class="form" id="projectForm" enctype="multipart/form-data" novalidate>
            <h3 class="form__title"><?= app_e($content['projects']['form_title']); ?></h3>
            <div id="projectFormFeedback" class="form__feedback" role="status" aria-live="polite"></div>
            <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>">

            <label class="form__field">
              <span class="form__label"><?= app_e($content['forms']['project_title']); ?></span>
              <input class="form__control" type="text" name="title" required maxlength="120" placeholder="<?= app_e($content['forms']['project_title_ph']); ?>">
            </label>

            <label class="form__field">
              <span class="form__label"><?= app_e($content['forms']['project_image']); ?> <span class="form__optional"><?= app_e($content['forms']['project_image_hint']); ?></span></span>
              <input class="form__control" type="file" name="image" accept="image/jpeg,image/png,image/webp">
            </label>

            <label class="form__field">
              <span class="form__label"><?= app_e($content['forms']['project_description']); ?></span>
              <textarea class="form__textarea" name="description" required maxlength="800" placeholder="<?= app_e($content['forms']['project_description_ph']); ?>"></textarea>
            </label>

            <label class="form__field">
              <span class="form__label"><?= app_e($content['forms']['project_url']); ?></span>
              <input class="form__control" type="url" name="url" required placeholder="<?= app_e($content['forms']['project_url_ph']); ?>">
            </label>

            <div class="form__row">
              <label class="form__field">
                <span class="form__label"><?= app_e($content['forms']['first_name']); ?></span>
                <input class="form__control" type="text" name="first_name" required maxlength="80">
              </label>
              <label class="form__field">
                <span class="form__label"><?= app_e($content['forms']['last_name']); ?></span>
                <input class="form__control" type="text" name="last_name" required maxlength="80">
              </label>
            </div>

            <div class="form__row">
              <label class="form__field">
                <span class="form__label"><?= app_e($content['forms']['email']); ?></span>
                <input class="form__control" type="email" name="email" required maxlength="120">
              </label>
              <label class="form__field">
                <span class="form__label"><?= app_e($content['forms']['phone']); ?> <span class="form__optional"><?= app_e($content['forms']['optional']); ?></span></span>
                <input class="form__control" type="tel" name="phone" maxlength="40">
              </label>
            </div>

            <label class="form__field">
              <span class="form__label"><?= app_e($content['forms']['project_note']); ?> <span class="form__optional"><?= app_e($content['forms']['optional']); ?></span></span>
              <textarea class="form__textarea" name="note" maxlength="600" placeholder="<?= app_e($content['forms']['project_note_ph']); ?>"></textarea>
            </label>

            <!-- honeypot -->
            <input type="text" name="company" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">

            <div class="form__actions">
              <button class="btn btn--primary" type="submit"><?= app_e($content['projects']['submit_label']); ?></button>
            </div>
          </form>
        </div>
      </div>
    </section>

  </main>

  <!-- FOOTER -->
  <footer class="footer">
    <div class="shell footer__row">
      <span><?= app_e($content['footer']['copyright']); ?></span>
      <div class="footer__links">
        <button class="footer__link" type="button" data-lightbox="legal"><?= app_e($content['footer']['legal_link']); ?></button>
        <button class="footer__link" type="button" data-lightbox="contact"><?= app_e($content['footer']['contact_link']); ?></button>
      </div>
    </div>
  </footer>

</div>

<!-- =========================================================
     LIGHTBOXES — legal, contact
     ========================================================= -->
<div class="lightbox" id="lightboxLegal" role="dialog" aria-modal="true" aria-labelledby="lightboxLegalTitle">
  <div class="lightbox__panel">
    <button class="lightbox__close" type="button" data-close-lightbox aria-label="<?= app_e($content['forms']['close']); ?>">×</button>
    <h2 class="lightbox__title" id="lightboxLegalTitle"><?= app_e($content['footer']['legal_link']); ?></h2>
    <?php foreach ($content['footer']['legal_lines'] as $line): ?>
      <p class="lightbox__copy"><?= app_e($line); ?></p>
    <?php endforeach; ?>
  </div>
</div>

<div class="lightbox" id="lightboxContact" role="dialog" aria-modal="true" aria-labelledby="lightboxContactTitle">
  <div class="lightbox__panel">
    <button class="lightbox__close" type="button" data-close-lightbox aria-label="<?= app_e($content['forms']['close']); ?>">×</button>
    <h2 class="lightbox__title" id="lightboxContactTitle"><?= app_e($content['contact']['title']); ?></h2>
    <p class="lightbox__copy"><?= app_e($content['contact']['lead']); ?></p>

    <form class="form" id="contactForm" style="margin-top:1.2rem;background:transparent;border:none;padding:0;" novalidate>
      <div id="contactFormFeedback" class="form__feedback" role="status" aria-live="polite"></div>
      <input type="hidden" name="csrf" value="<?= app_e(app_csrf_token()); ?>">

      <div class="form__row">
        <label class="form__field">
          <span class="form__label"><?= app_e($content['forms']['first_name']); ?></span>
          <input class="form__control" type="text" name="first_name" required maxlength="80">
        </label>
        <label class="form__field">
          <span class="form__label"><?= app_e($content['forms']['last_name']); ?></span>
          <input class="form__control" type="text" name="last_name" required maxlength="80">
        </label>
      </div>

      <label class="form__field">
        <span class="form__label"><?= app_e($content['forms']['email']); ?></span>
        <input class="form__control" type="email" name="email" required maxlength="120">
      </label>

      <label class="form__field">
        <span class="form__label"><?= app_e($content['forms']['contact_subject']); ?></span>
        <input class="form__control" type="text" name="subject" required maxlength="160">
      </label>

      <label class="form__field">
        <span class="form__label"><?= app_e($content['forms']['contact_message']); ?></span>
        <textarea class="form__textarea" name="message" required maxlength="2000"></textarea>
      </label>

      <input type="text" name="company" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0" aria-hidden="true">

      <div class="form__actions">
        <button class="btn btn--primary" type="submit"><?= app_e($content['contact']['submit_label']); ?></button>
      </div>
    </form>
  </div>
</div>

<script src="assets/site.js?v=<?= filemtime(__DIR__ . '/assets/site.js'); ?>" defer></script>

<?php
function render_user_project_card(array $p, string $variant = ''): string
{
    $title = htmlspecialchars((string) ($p['title'] ?? 'Projet'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $desc = htmlspecialchars((string) ($p['description'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $url = (string) ($p['url'] ?? '');
    $img = (string) ($p['image'] ?? '');
    $domain = $url ? preg_replace('#^https?://(www\.)?#', '', $url) : '';
    $domain = $domain ? rtrim((string) strtok($domain, '/'), '/') : '';
    $author = htmlspecialchars(trim((string) ($p['submitter']['first_name'] ?? '') . ' ' . (string) ($p['submitter']['last_name'] ?? '')), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    ob_start(); ?>
    <article class="card">
      <div class="card__thumb">
        <?php if ($img !== ''): ?>
          <img src="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= $title; ?>" loading="lazy" decoding="async">
        <?php else: ?>
          <div style="width:100%;height:100%;background:linear-gradient(135deg,#1a0c2e,#3d1a5b);"></div>
        <?php endif; ?>
        <div class="card__thumb-overlay"></div>
      </div>
      <div class="card__body">
        <h3 class="card__title"><?= $title; ?></h3>
        <p class="card__copy"><?= $desc; ?></p>
        <?php if ($author !== ''): ?>
          <p class="card__copy" style="font-size:0.82rem;color:var(--text-muted);margin:0;">— <?= $author; ?></p>
        <?php endif; ?>
        <div class="card__footer">
          <span class="card__domain"><?= htmlspecialchars((string) $domain, ENT_QUOTES, 'UTF-8'); ?></span>
          <?php if ($url !== ''): ?>
            <a class="btn btn--small btn--ghost" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener">Visiter</a>
          <?php endif; ?>
        </div>
      </div>
    </article>
    <?php
    return (string) ob_get_clean();
}
?>
</body>
</html>
