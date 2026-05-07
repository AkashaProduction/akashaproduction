(() => {
  'use strict';

  const $  = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ============================================================
     LOADER + WELCOME orchestration
     ============================================================ */
  const loader = $('#loader');
  const welcome = $('#welcome');
  const enterBtn = $('#welcomeEnter');
  const cosmicBg = $('#cosmicBg');
  const countEl = $('#loaderCount');
  const body = document.body;

  const sequence = ['3', '2', '1'];
  const stepDuration = reduceMotion ? 350 : 900;

  function runCountdown() {
    return new Promise((resolve) => {
      let i = 0;
      if (countEl) countEl.textContent = sequence[i];
      const interval = setInterval(() => {
        i += 1;
        if (i < sequence.length) {
          if (countEl) countEl.textContent = sequence[i];
        } else {
          clearInterval(interval);
          resolve();
        }
      }, stepDuration);
    });
  }

  function preload(src) {
    return new Promise((resolve) => {
      if (!src) { resolve(); return; }
      const im = new Image();
      im.onload = () => resolve();
      im.onerror = () => resolve();
      im.src = src;
    });
  }

  async function bootIntro() {
    const minDelay = reduceMotion ? 200 : 600;
    const tasks = [
      runCountdown(),
      preload('assets/img/akasha-logo.webp').catch(() => preload('assets/img/akasha-logo.jpg')),
      new Promise((r) => setTimeout(r, minDelay)),
    ];
    await Promise.all(tasks);

    if (loader) loader.classList.add('is-hidden');
    if (welcome) {
      welcome.classList.add('is-visible');
      welcome.setAttribute('aria-hidden', 'false');
    }
    setTimeout(() => loader && loader.remove(), 1100);
  }

  function enterSite() {
    if (welcome) {
      welcome.classList.remove('is-visible');
      welcome.classList.add('is-hidden');
      welcome.setAttribute('aria-hidden', 'true');
    }
    body.classList.remove('is-locked');
    if (cosmicBg) {
      const reveal = () => cosmicBg.classList.add('is-visible');
      if (cosmicBg.complete) reveal();
      else cosmicBg.addEventListener('load', reveal, { once: true });
    }
    setTimeout(() => welcome && welcome.remove(), 1100);
  }

  if (enterBtn) {
    enterBtn.addEventListener('click', enterSite);
  }

  document.addEventListener('keydown', (e) => {
    if (welcome && welcome.classList.contains('is-visible') && (e.key === 'Enter' || e.key === ' ')) {
      e.preventDefault();
      enterSite();
    }
  });

  bootIntro();

  /* ============================================================
     NAVBAR — sticky scroll state, mobile toggle, smooth scroll
     ============================================================ */
  const nav = $('#mainNav');
  const navToggle = $('#navToggle');

  function updateNavState() {
    if (!nav) return;
    if (window.scrollY > 30) nav.classList.add('is-scrolled');
    else nav.classList.remove('is-scrolled');
  }

  window.addEventListener('scroll', updateNavState, { passive: true });
  updateNavState();

  if (navToggle && nav) {
    navToggle.addEventListener('click', () => {
      const open = nav.classList.toggle('is-open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  $$('.nav__link').forEach((link) => {
    link.addEventListener('click', () => {
      if (nav && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  });

  /* ============================================================
     LIGHTBOX — open/close + esc + click outside
     ============================================================ */
  const lightboxes = $$('.lightbox');

  function openLightbox(name) {
    const target = document.getElementById('lightbox' + capitalize(name));
    if (!target) return;
    target.classList.add('is-open');
    body.classList.add('is-locked');
    const focusable = target.querySelector('input, textarea, button, [href]');
    if (focusable) setTimeout(() => focusable.focus(), 80);
  }

  function closeLightboxes() {
    let any = false;
    lightboxes.forEach((lb) => {
      if (lb.classList.contains('is-open')) {
        lb.classList.remove('is-open');
        any = true;
      }
    });
    if (any && !(welcome && welcome.classList.contains('is-visible'))) {
      body.classList.remove('is-locked');
    }
  }

  function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
  }

  $$('[data-lightbox]').forEach((trigger) => {
    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      openLightbox(trigger.getAttribute('data-lightbox'));
    });
  });

  $$('[data-close-lightbox]').forEach((closer) => {
    closer.addEventListener('click', closeLightboxes);
  });

  lightboxes.forEach((lb) => {
    lb.addEventListener('click', (e) => {
      if (e.target === lb) closeLightboxes();
    });
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeLightboxes();
  });

  /* ============================================================
     FORMS — async submit with feedback
     ============================================================ */
  function attachFormSubmit(formId, endpoint, feedbackId, options = {}) {
    const form = document.getElementById(formId);
    const feedback = document.getElementById(feedbackId);
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (feedback) {
        feedback.className = 'form__feedback';
        feedback.textContent = '';
      }

      const submitBtn = form.querySelector('button[type="submit"]');
      const originalText = submitBtn ? submitBtn.textContent : '';
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi…';
      }

      try {
        const data = new FormData(form);
        const res = await fetch(endpoint, {
          method: 'POST',
          body: data,
          headers: { 'Accept': 'application/json' },
        });
        let payload = {};
        try { payload = await res.json(); } catch (err) { /* ignore */ }

        if (res.ok && payload.ok) {
          if (feedback) {
            feedback.className = 'form__feedback is-success';
            feedback.textContent = payload.message || 'Merci, votre message a bien été transmis.';
          }
          form.reset();
          if (options.onSuccess) options.onSuccess(payload);
        } else {
          if (feedback) {
            feedback.className = 'form__feedback is-error';
            feedback.textContent = (payload && payload.message) || 'Un incident est survenu. Réessayez dans quelques instants.';
          }
        }
      } catch (err) {
        if (feedback) {
          feedback.className = 'form__feedback is-error';
          feedback.textContent = 'Connexion interrompue. Veuillez réessayer.';
        }
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.textContent = originalText;
        }
      }
    });
  }

  attachFormSubmit('projectForm', 'api/submit-project.php', 'projectFormFeedback', {
    onSuccess() {
      const target = document.getElementById('projects');
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    },
  });
  attachFormSubmit('contactForm', 'api/submit-contact.php', 'contactFormFeedback', {
    onSuccess() {
      setTimeout(closeLightboxes, 1800);
    },
  });

  /* ============================================================
     LOAD MORE — user projects (DOM-built, no innerHTML)
     ============================================================ */
  const loadMoreBtn = document.getElementById('userProjectsLoadMore');
  const moreContainer = document.getElementById('userProjectsMore');
  if (loadMoreBtn && moreContainer) {
    let offset = 9;
    const batch = parseInt(loadMoreBtn.dataset.batch || '8', 10);

    loadMoreBtn.addEventListener('click', async () => {
      loadMoreBtn.disabled = true;
      try {
        const res = await fetch(`api/user-projects.php?offset=${offset}&limit=${batch}`, {
          headers: { 'Accept': 'application/json' },
        });
        const data = await res.json();
        if (data && Array.isArray(data.items) && data.items.length) {
          const wrap = document.createElement('div');
          wrap.className = 'user-projects__row user-projects__row--3';
          data.items.forEach((p) => wrap.appendChild(buildUserProjectCard(p)));
          moreContainer.appendChild(wrap);
          offset += data.items.length;
          if (!data.has_more) loadMoreBtn.remove();
          else loadMoreBtn.disabled = false;
        } else {
          loadMoreBtn.remove();
        }
      } catch (err) {
        loadMoreBtn.disabled = false;
      }
    });
  }

  function el(tag, attrs = {}, text = null) {
    const node = document.createElement(tag);
    Object.entries(attrs).forEach(([k, v]) => {
      if (v == null) return;
      if (k === 'class') node.className = v;
      else if (k === 'style') node.setAttribute('style', v);
      else node.setAttribute(k, v);
    });
    if (text != null) node.textContent = text;
    return node;
  }

  function buildUserProjectCard(p) {
    const url = (p && p.url) || '';
    const img = (p && p.image) || '';
    const title = (p && p.title) || 'Projet';
    const desc = (p && p.description) || '';
    const submitter = (p && p.submitter) || {};
    const author = `${submitter.first_name || ''} ${submitter.last_name || ''}`.trim();
    const domain = url ? url.replace(/^https?:\/\/(www\.)?/, '').replace(/\/.*$/, '') : '';

    const article = el('article', { class: 'card' });
    const thumb = el('div', { class: 'card__thumb' });
    if (img) {
      const im = el('img', { src: img, alt: title, loading: 'lazy', decoding: 'async' });
      thumb.appendChild(im);
    } else {
      const ph = el('div', { style: 'width:100%;height:100%;background:linear-gradient(135deg,#1a0c2e,#3d1a5b);' });
      thumb.appendChild(ph);
    }
    thumb.appendChild(el('div', { class: 'card__thumb-overlay' }));
    article.appendChild(thumb);

    const bodyEl = el('div', { class: 'card__body' });
    bodyEl.appendChild(el('h3', { class: 'card__title' }, title));
    bodyEl.appendChild(el('p', { class: 'card__copy' }, desc));
    if (author) {
      bodyEl.appendChild(el('p', { class: 'card__copy', style: 'font-size:0.82rem;color:var(--text-muted);margin:0;' }, '— ' + author));
    }

    const footer = el('div', { class: 'card__footer' });
    footer.appendChild(el('span', { class: 'card__domain' }, domain));
    if (url) {
      footer.appendChild(el('a', { class: 'btn btn--small btn--ghost', href: url, target: '_blank', rel: 'noopener' }, 'Visiter'));
    }
    bodyEl.appendChild(footer);

    article.appendChild(bodyEl);
    return article;
  }
})();
