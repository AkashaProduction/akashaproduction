document.addEventListener("DOMContentLoaded", () => {
  const quoteTriggers = document.querySelectorAll("[data-toggle-target]");
  quoteTriggers.forEach((element) => {
    element.addEventListener("change", () => {
      const targetSelector = element.getAttribute("data-toggle-target");
      const target = targetSelector ? document.querySelector(targetSelector) : null;
      if (!target) {
        return;
      }
      const shouldShow = element instanceof HTMLInputElement ? element.checked : false;
      target.hidden = !shouldShow;
    });
  });

  const supportDepartment = document.querySelector("#department");
  const supportSubject = document.querySelector("[data-support-subject]");

  if (supportDepartment instanceof HTMLSelectElement && supportSubject instanceof HTMLSelectElement) {
    const rawTopics = supportSubject.dataset.supportTopics || "{}";
    const topics = JSON.parse(rawTopics);

    const syncTopics = () => {
      const department = supportDepartment.value || "commercial";
      const departmentTopics = Array.isArray(topics[department]) ? topics[department] : [];
      supportSubject.innerHTML = "";

      departmentTopics.forEach((topic) => {
        const option = document.createElement("option");
        option.value = topic;
        option.textContent = topic;
        supportSubject.appendChild(option);
      });
    };

    supportDepartment.addEventListener("change", syncTopics);
    syncTopics();
  }

  const orderForm = document.querySelector("[data-order-form]");
  if (!orderForm) {
    return;
  }

  const creation = document.querySelector("#creation");
  const hosting = document.querySelector("#hosting");
  const includeDomain = document.querySelector("#include-domain");
  const split = document.querySelector("#split-payment");
  const totalElement = document.querySelector("[data-order-total]");
  const detailElement = document.querySelector("[data-order-detail]");
  const quoteBlock = document.querySelector("[data-quote-block]");

  const prices = {
    creation: { showcase: 50, complex: 500, custom: 0 },
    hosting: { "shared-monthly": 8, "shared-yearly": 88, vps: 200, cloud: 0 },
    packs: {
      "showcase:shared-yearly": 120,
      "complex:shared-yearly": 550,
      "showcase:vps": 230,
      "complex:vps": 675,
    },
  };

  const refresh = () => {
    const creationValue = creation?.value || "showcase";
    const hostingValue = hosting?.value || "shared-yearly";
    const domainAddon = includeDomain instanceof HTMLInputElement && includeDomain.checked;
    const key = `${creationValue}:${hostingValue}`;

    let total = prices.packs[key] ?? (prices.creation[creationValue] || 0) + (prices.hosting[hostingValue] || 0);
    if (domainAddon) {
      total += 18;
    }

    const isQuote = creationValue === "custom" || hostingValue === "cloud";
    if (quoteBlock) {
      quoteBlock.hidden = !isQuote;
    }
    if (split instanceof HTMLInputElement) {
      split.disabled = isQuote;
      if (isQuote) {
        split.checked = false;
      }
    }
    if (totalElement) {
      totalElement.textContent = isQuote ? "Sur devis" : `${total.toFixed(0)} €`;
    }
    if (detailElement) {
      detailElement.textContent = !isQuote && split instanceof HTMLInputElement && split.checked
        ? `3 x ${(total / 3).toFixed(2)} €`
        : isQuote
          ? "Étude commerciale personnalisée"
          : "Paiement à l’activation";
    }
  };

  [creation, hosting, includeDomain, split].forEach((element) => {
    if (element) {
      element.addEventListener("change", refresh);
    }
  });
  refresh();
});
