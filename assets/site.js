document.addEventListener("DOMContentLoaded", () => {
  /* ---------- toggle helpers ---------- */
  document.querySelectorAll("[data-toggle-target]").forEach((el) => {
    el.addEventListener("change", () => {
      const target = document.querySelector(el.getAttribute("data-toggle-target"));
      if (target) target.hidden = !(el instanceof HTMLInputElement ? el.checked : false);
    });
  });

  /* ---------- support ticket topics ---------- */
  const dept = document.querySelector("#department");
  const subj = document.querySelector("[data-support-subject]");
  if (dept instanceof HTMLSelectElement && subj instanceof HTMLSelectElement) {
    const topics = JSON.parse(subj.dataset.supportTopics || "{}");
    const sync = () => {
      const list = Array.isArray(topics[dept.value]) ? topics[dept.value] : [];
      subj.textContent = "";
      list.forEach((t) => { const o = document.createElement("option"); o.value = t; o.textContent = t; subj.appendChild(o); });
    };
    dept.addEventListener("change", sync);
    sync();
  }

  /* ---------- order form ---------- */
  const orderForm = document.querySelector("[data-order-form]");
  if (!orderForm) return;

  var i18n = window.i18n || {};
  var i18nLabels = i18n.labels || {};

  const prices = {
    creation: { showcase: 50, complex: 500, custom: 0 },
    hosting: { "shared-monthly": 8, "shared-yearly": 88, vps: 200, cloud: 0 },
    packs: { "showcase:shared-yearly": 120, "complex:shared-yearly": 550, "showcase:vps": 230, "complex:vps": 675 },
  };

  const $ = (sel) => document.querySelector(sel);
  const pick = (name) => { const el = orderForm.querySelector("[name=\"" + name + "\"]:checked"); return el ? el.value : ""; };

  const summaryCreation = $("[data-summary-creation]");
  const summaryCreationPrice = $("[data-summary-creation-price]");
  const summaryHosting = $("[data-summary-hosting]");
  const summaryHostingPrice = $("[data-summary-hosting-price]");
  const summaryDomainLine = $("[data-summary-domain-line]");
  const summaryDomainName = $("[data-summary-domain-name]");
  const summaryDomainPrice = $("[data-summary-domain-price]");
  const summaryPromo = $("[data-summary-promo]");
  const summaryPromoLabel = $("[data-summary-promo-label]");
  const totalEl = $("[data-order-total]");
  const detailEl = $("[data-order-detail]");
  const quoteBlock = $("[data-quote-block]");
  const splitInput = $("#split-payment");
  const includeDomainInput = $("#include-domain");
  const domainExtSelect = $("#domain-extension");

  function getDomainPrice() {
    if (!(includeDomainInput instanceof HTMLInputElement) || !includeDomainInput.checked) return 0;
    if (!domainExtSelect) return 0;
    var opt = domainExtSelect.selectedOptions[0];
    if (!opt) return 0;
    var m = opt.textContent.match(/(\d+)\s/);
    return m ? parseInt(m[1], 10) : 0;
  }

  function refresh() {
    var c = pick("creation") || "showcase";
    var h = pick("hosting") || "shared-yearly";
    var isQuote = c === "custom" || h === "cloud";
    var packKey = c + ":" + h;
    var domainPrice = getDomainPrice();
    var base = prices.packs[packKey] != null ? prices.packs[packKey] : ((prices.creation[c] || 0) + (prices.hosting[h] || 0));
    var hasPack = prices.packs[packKey] != null;
    var rawSum = (prices.creation[c] || 0) + (prices.hosting[h] || 0);
    var total = base + domainPrice;

    var onQuote = i18n.on_quote || "Sur devis";
    var currency = i18n.currency || "\u20AC";

    if (summaryCreation) summaryCreation.textContent = (i18nLabels.creation && i18nLabels.creation[c]) || c;
    if (summaryCreationPrice) summaryCreationPrice.textContent = prices.creation[c] > 0 ? prices.creation[c] + " " + currency : onQuote;
    if (summaryHosting) summaryHosting.textContent = (i18nLabels.hosting && i18nLabels.hosting[h]) || h;
    if (summaryHostingPrice) summaryHostingPrice.textContent = prices.hosting[h] > 0 ? prices.hosting[h] + " " + currency : onQuote;

    if (summaryDomainLine) {
      summaryDomainLine.hidden = domainPrice === 0;
      if (summaryDomainName) {
        var nameInput = $("#domain-name-input");
        var ext = domainExtSelect ? domainExtSelect.value : "";
        summaryDomainName.textContent = nameInput && nameInput.value ? nameInput.value + "." + ext : (i18n.domain_custom || "Domaine personnalis\u00E9");
      }
      if (summaryDomainPrice) summaryDomainPrice.textContent = domainPrice + " " + (i18n.per_year || "\u20AC/an");
    }

    if (summaryPromo) {
      if (hasPack && !isQuote) {
        summaryPromo.hidden = false;
        var saving = rawSum - base;
        if (summaryPromoLabel) summaryPromoLabel.textContent = "-" + saving + " " + currency + " (" + (i18n.pack_discount || "tarif pack") + ")";
      } else {
        summaryPromo.hidden = true;
      }
    }

    if (quoteBlock) quoteBlock.hidden = !isQuote;
    if (splitInput instanceof HTMLInputElement) {
      splitInput.disabled = isQuote;
      if (isQuote) splitInput.checked = false;
    }

    if (totalEl) totalEl.textContent = isQuote ? onQuote : total + " " + currency;
    if (detailEl) {
      if (isQuote) {
        detailEl.textContent = i18n.quote_detail || "\u00C9tude commerciale personnalis\u00E9e";
      } else if (splitInput instanceof HTMLInputElement && splitInput.checked) {
        var splitText = i18n.split_format || "3 x :amount \u20AC";
        detailEl.textContent = splitText.replace(":amount", (total / 3).toFixed(2));
      } else {
        detailEl.textContent = i18n.pay_detail || "Paiement \u00E0 l'activation";
      }
    }

    orderForm.querySelectorAll(".product-tab input").forEach(function(inp) {
      inp.closest(".product-tab").classList.toggle("product-tab--active", inp.checked);
    });

    var submitBtn = orderForm.querySelector("[data-submit-btn]");
    if (submitBtn) submitBtn.textContent = isQuote ? (i18n.submit_quote || "Envoyer la demande de devis") : (i18n.submit_pay || "Payer avec Stripe");
  }

  orderForm.querySelectorAll("[name=\"creation\"], [name=\"hosting\"]").forEach(function(el) { el.addEventListener("change", refresh); });
  if (includeDomainInput) includeDomainInput.addEventListener("change", refresh);
  if (domainExtSelect) domainExtSelect.addEventListener("change", refresh);
  if (splitInput) splitInput.addEventListener("change", refresh);
  var domainNameInput = $("#domain-name-input");
  if (domainNameInput) domainNameInput.addEventListener("input", refresh);
  refresh();

  /* ---------- domain availability search ---------- */
  var searchBtn = $("[data-domain-search]");
  var resultBox = $("[data-domain-result]");

  if (searchBtn && domainNameInput && domainExtSelect) {
    searchBtn.addEventListener("click", function() {
      var name = domainNameInput.value.trim().toLowerCase().replace(/[^a-z0-9\-]/g, "");
      var tld = domainExtSelect.value;
      if (!name || name.length < 2) {
        showResult("warning", i18n.search_min || "Saisissez au moins 2 caract\u00E8res pour le nom de domaine.");
        return;
      }
      searchBtn.disabled = true;
      searchBtn.textContent = i18n.searching || "Recherche\u2026";

      fetch("/domain-check?name=" + encodeURIComponent(name) + "&tld=" + encodeURIComponent(tld))
        .then(function(resp) { return resp.json(); })
        .then(function(data) {
          if (data.error) {
            showResult("warning", data.error);
          } else if (data.available === true) {
            var msg = (i18n.domain_available || ":domain est disponible \u2014 :price \u20AC/an").replace(":domain", data.domain).replace(":price", data.price);
            showResult("success", msg);
          } else if (data.available === false) {
            var msg = (i18n.domain_taken || ":domain est d\u00E9j\u00E0 enregistr\u00E9. Essayez un autre nom ou une autre extension.").replace(":domain", data.domain);
            showResult("taken", msg);
          } else {
            var msg = (i18n.domain_unknown || "Impossible de confirmer la disponibilit\u00E9 de :domain. Contactez-nous.").replace(":domain", data.domain);
            showResult("warning", msg);
          }
        })
        .catch(function() {
          showResult("warning", i18n.domain_error || "Erreur de connexion. R\u00E9essayez dans quelques instants.");
        })
        .finally(function() {
          searchBtn.disabled = false;
          searchBtn.textContent = i18n.verify || "V\u00E9rifier";
        });
    });

    domainNameInput.addEventListener("keydown", function(e) {
      if (e.key === "Enter") { e.preventDefault(); searchBtn.click(); }
    });
  }

  function showResult(type, text) {
    if (!resultBox) return;
    resultBox.hidden = false;
    resultBox.className = "domain-result domain-result--" + type;
    resultBox.textContent = text;
  }
});
