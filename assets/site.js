document.addEventListener("DOMContentLoaded", () => {
  const solutionBuilder = document.querySelector("[data-solution-builder]");
  if (solutionBuilder instanceof HTMLFormElement) {
    const creationInputs = Array.from(solutionBuilder.querySelectorAll('input[name="creation"]'));
    const hostingInputs = Array.from(solutionBuilder.querySelectorAll('input[name="hosting"]'));
    const packInputs = Array.from(solutionBuilder.querySelectorAll('input[name="pack"]'));
    const includeDomainInSolutions = solutionBuilder.querySelector("#solutions-domain");
    const summaryText = solutionBuilder.querySelector("[data-solution-summary-text]");
    const summaryTotal = solutionBuilder.querySelector("[data-solution-summary-total]");

    const prices = {
      creation: { showcase: 50, complex: 500, custom: 0 },
      hosting: { "shared-yearly": 88, vps: 200, cloud: 0 },
      packs: {
        "showcase-shared-yearly": { label: "Pack vitrine", total: 120, creation: "showcase", hosting: "shared-yearly" },
        "complex-shared-yearly": { label: "Pack complexe", total: 550, creation: "complex", hosting: "shared-yearly" },
        custom: { label: "Pack personnalisé", total: null, creation: "custom", hosting: "cloud" },
      },
    };

    const clearPacks = () => {
      packInputs.forEach((input) => {
        input.checked = false;
      });
    };

    const pick = (inputs) => inputs.find((input) => input.checked)?.value || "";

    const refreshSolutions = () => {
      const activePack = pick(packInputs);
      let creation = pick(creationInputs) || "showcase";
      let hosting = pick(hostingInputs) || "shared-yearly";
      let label = "";
      let total = null;

      if (activePack && prices.packs[activePack]) {
        const preset = prices.packs[activePack];
        creation = preset.creation;
        hosting = preset.hosting;
        label = preset.label;
        total = preset.total;
      } else {
        label = `${creation === "showcase" ? "Création vitrine" : creation === "complex" ? "Création complexe" : "Création personnalisée"} + ${hosting === "shared-yearly" ? "mutualisé annuel" : hosting === "vps" ? "VPS dédié" : "cloud"}`;
        total = creation === "custom" || hosting === "cloud"
          ? null
          : (prices.creation[creation] || 0) + (prices.hosting[hosting] || 0);
      }

      if (includeDomainInSolutions instanceof HTMLInputElement && includeDomainInSolutions.checked && total !== null) {
        total += 18;
      }

      if (summaryText) {
        summaryText.textContent = label;
      }

      if (summaryTotal) {
        summaryTotal.textContent = total === null ? "Sur devis" : `${total} €`;
      }
    };

    creationInputs.forEach((input) => input.addEventListener("change", () => {
      clearPacks();
      refreshSolutions();
    }));

    hostingInputs.forEach((input) => input.addEventListener("change", () => {
      clearPacks();
      refreshSolutions();
    }));

    packInputs.forEach((input) => input.addEventListener("change", refreshSolutions));

    if (includeDomainInSolutions instanceof HTMLInputElement) {
      includeDomainInSolutions.addEventListener("change", refreshSolutions);
    }

    refreshSolutions();
  }

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
