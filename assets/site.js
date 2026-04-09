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

  const prices = {
    creation: { showcase: 50, complex: 500, custom: 0 },
    hosting: { "shared-monthly": 8, "shared-yearly": 88, vps: 200, cloud: 0 },
    packs: { "showcase:shared-yearly": 120, "complex:shared-yearly": 550, "showcase:vps": 230, "complex:vps": 675 },
  };

  const labels = {
    creation: { showcase: "Site vitrine multilingue 3 pages", complex: "Site complexe 9 pages + 3 modules + BDD", custom: "Creation personnalisee sur devis" },
    hosting: { "shared-monthly": "Mutualise mensuel", "shared-yearly": "Mutualise annuel", vps: "VPS dedie", cloud: "Cloud personnalise sur devis" },
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

    if (summaryCreation) summaryCreation.textContent = labels.creation[c] || c;
    if (summaryCreationPrice) summaryCreationPrice.textContent = prices.creation[c] > 0 ? prices.creation[c] + " \u20AC" : "Sur devis";
    if (summaryHosting) summaryHosting.textContent = labels.hosting[h] || h;
    if (summaryHostingPrice) summaryHostingPrice.textContent = prices.hosting[h] > 0 ? prices.hosting[h] + " \u20AC" : "Sur devis";

    if (summaryDomainLine) {
      summaryDomainLine.hidden = domainPrice === 0;
      if (summaryDomainName) {
        var nameInput = $("#domain-name-input");
        var ext = domainExtSelect ? domainExtSelect.value : "";
        summaryDomainName.textContent = nameInput && nameInput.value ? nameInput.value + "." + ext : "Domaine personnalise";
      }
      if (summaryDomainPrice) summaryDomainPrice.textContent = domainPrice + " \u20AC/an";
    }

    if (summaryPromo) {
      if (hasPack && !isQuote) {
        summaryPromo.hidden = false;
        var saving = rawSum - base;
        if (summaryPromoLabel) summaryPromoLabel.textContent = "-" + saving + " \u20AC (tarif pack)";
      } else {
        summaryPromo.hidden = true;
      }
    }

    if (quoteBlock) quoteBlock.hidden = !isQuote;
    if (splitInput instanceof HTMLInputElement) {
      splitInput.disabled = isQuote;
      if (isQuote) splitInput.checked = false;
    }

    if (totalEl) totalEl.textContent = isQuote ? "Sur devis" : total + " \u20AC";
    if (detailEl) {
      if (isQuote) {
        detailEl.textContent = "Etude commerciale personnalisee";
      } else if (splitInput instanceof HTMLInputElement && splitInput.checked) {
        detailEl.textContent = "3 x " + (total / 3).toFixed(2) + " \u20AC";
      } else {
        detailEl.textContent = "Paiement a l'activation";
      }
    }

    orderForm.querySelectorAll(".product-tab input").forEach(function(inp) {
      inp.closest(".product-tab").classList.toggle("product-tab--active", inp.checked);
    });

    var submitBtn = orderForm.querySelector("[data-submit-btn]");
    if (submitBtn) submitBtn.textContent = isQuote ? "Envoyer la demande de devis" : "Payer avec Stripe";
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
        showResult("warning", "Saisissez au moins 2 caracteres pour le nom de domaine.");
        return;
      }
      searchBtn.disabled = true;
      searchBtn.textContent = "Recherche...";

      fetch("/domain-check?name=" + encodeURIComponent(name) + "&tld=" + encodeURIComponent(tld))
        .then(function(resp) { return resp.json(); })
        .then(function(data) {
          if (data.error) {
            showResult("warning", data.error);
          } else if (data.available === true) {
            showResult("success", data.domain + " est disponible — " + data.price + " \u20AC/an");
          } else if (data.available === false) {
            showResult("taken", data.domain + " est deja enregistre. Essayez un autre nom ou une autre extension.");
          } else {
            showResult("warning", "Impossible de confirmer la disponibilite de " + data.domain + ". Contactez-nous.");
          }
        })
        .catch(function() {
          showResult("warning", "Erreur de connexion. Reessayez dans quelques instants.");
        })
        .finally(function() {
          searchBtn.disabled = false;
          searchBtn.textContent = "Verifier";
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
