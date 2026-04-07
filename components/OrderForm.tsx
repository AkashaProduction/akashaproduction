"use client";

import { useMemo, useState } from "react";
import { parentDomains, quoteQuestions } from "@/lib/catalog";

type Copy = {
  title: string;
  description: string;
  submit: string;
  success: string;
  stripeMissing: string;
};

const initialForm = {
  firstName: "",
  lastName: "",
  email: "",
  phone: "",
  company: "",
  country: "",
  creation: "showcase",
  hosting: "shared-yearly",
  domain: "subdomain",
  requestedParentDomain: parentDomains[0],
  includeDomainAddon: false,
  splitPayment: false,
  projectDescription: ""
};

export function OrderForm({ locale, copy }: { locale: string; copy: Copy }) {
  const [form, setForm] = useState(initialForm);
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">("idle");
  const [message, setMessage] = useState("");

  const showQuote = form.creation === "custom" || form.hosting === "cloud";

  const summary = useMemo(() => {
    let total = 0;
    const c = form.creation;
    const h = form.hosting;
    if (c === "showcase") total += 50;
    if (c === "complex") total += 500;
    if (h === "shared-monthly") total += 8;
    if (h === "shared-yearly") total += 88;
    if (h === "vps") total += 200;
    if (c === "showcase" && h === "shared-yearly") total = 120;
    if (c === "complex" && h === "shared-yearly") total = 550;
    if (c === "showcase" && h === "vps") total = 230;
    if (c === "complex" && h === "vps") total = 675;
    if (form.includeDomainAddon) total += 18;
    return total;
  }, [form]);

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("loading");
    setMessage("");

    const answers = Object.fromEntries(
      quoteQuestions.map((question) => [
        question.id,
        (event.currentTarget.elements.namedItem(`quote-${question.id}`) as HTMLSelectElement | null)?.value ?? ""
      ])
    );

    const response = await fetch("/api/checkout", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        locale,
        customer: {
          firstName: form.firstName,
          lastName: form.lastName,
          email: form.email,
          phone: form.phone,
          company: form.company,
          country: form.country
        },
        selections: {
          creation: form.creation,
          hosting: form.hosting,
          domain: form.domain,
          requestedParentDomain: form.requestedParentDomain,
          splitPayment: form.splitPayment,
          includeDomainAddon: form.includeDomainAddon
        },
        quoteAnswers: answers,
        projectDescription: form.projectDescription
      })
    });

    const data = await response.json();

    if (!response.ok) {
      setStatus("error");
      setMessage(data.error ?? "Erreur");
      return;
    }

    if (data.checkoutUrl) {
      window.location.href = data.checkoutUrl;
      return;
    }

    setStatus("success");
    setMessage(data.message ?? copy.success);
  }

  return (
    <div className="form-card">
      <div className="split">
        <div>
          <p className="kicker">{copy.title}</p>
          <p className="section-copy">{copy.description}</p>
        </div>
        <div className="glass">
          <strong>{summary} €</strong>
          <div className="muted">{form.splitPayment && summary > 0 ? `3 x ${(summary / 3).toFixed(2)} €` : "Paiement unique"}</div>
        </div>
      </div>
      <form className="form-grid" onSubmit={onSubmit}>
        {[
          ["firstName", "Prénom"],
          ["lastName", "Nom"],
          ["email", "Email"],
          ["phone", "Téléphone"],
          ["company", "Organisation"],
          ["country", "Pays"]
        ].map(([key, label]) => (
          <div className="field" key={key}>
            <label htmlFor={key}>{label}</label>
            <input
              id={key}
              value={form[key as keyof typeof initialForm] as string}
              onChange={(event) => setForm((current) => ({ ...current, [key]: event.target.value }))}
              required={["firstName", "lastName", "email"].includes(key)}
            />
          </div>
        ))}

        <div className="field">
          <label htmlFor="creation">Création</label>
          <select id="creation" value={form.creation} onChange={(event) => setForm((current) => ({ ...current, creation: event.target.value }))}>
            <option value="showcase">Site vitrine multilingue 3 pages - 50 €</option>
            <option value="complex">Site complexe 9 pages + 3 modules + base de données - 500 €</option>
            <option value="custom">Création personnalisée - sur devis</option>
          </select>
        </div>

        <div className="field">
          <label htmlFor="hosting">Hébergement</label>
          <select id="hosting" value={form.hosting} onChange={(event) => setForm((current) => ({ ...current, hosting: event.target.value }))}>
            <option value="shared-monthly">Serveur mutualisé - 8 €/mois</option>
            <option value="shared-yearly">Serveur mutualisé - 88 €/an</option>
            <option value="vps">VPS dédié - 200 €/mois</option>
            <option value="cloud">Cloud - sur devis</option>
          </select>
        </div>

        <div className="field">
          <label htmlFor="domain">Mode de domaine</label>
          <select id="domain" value={form.domain} onChange={(event) => setForm((current) => ({ ...current, domain: event.target.value }))}>
            <option value="subdomain">Sous-domaine offert</option>
            <option value="custom-domain">Nom de domaine personnalisé</option>
          </select>
        </div>

        <div className="field">
          <label htmlFor="requestedParentDomain">Domaine parent souhaité</label>
          <select
            id="requestedParentDomain"
            value={form.requestedParentDomain}
            onChange={(event) => setForm((current) => ({ ...current, requestedParentDomain: event.target.value }))}
          >
            {parentDomains.map((domain) => (
              <option key={domain} value={domain}>
                {domain}
              </option>
            ))}
          </select>
        </div>

        <div className="field field--full">
          <label htmlFor="projectDescription">Description du projet</label>
          <textarea
            id="projectDescription"
            value={form.projectDescription}
            onChange={(event) => setForm((current) => ({ ...current, projectDescription: event.target.value }))}
            required
          />
        </div>

        {showQuote &&
          quoteQuestions.map((question) => (
            <div className="field" key={question.id}>
              <label htmlFor={`quote-${question.id}`}>{question.id}</label>
              <select id={`quote-${question.id}`} name={`quote-${question.id}`}>
                {question.options.map((option) => (
                  <option key={option} value={option}>
                    {option}
                  </option>
                ))}
              </select>
            </div>
          ))}

        <div className="field field--full">
          <label className="checkbox-row">
            <input
              type="checkbox"
              checked={form.includeDomainAddon}
              onChange={(event) => setForm((current) => ({ ...current, includeDomainAddon: event.target.checked }))}
            />
            Ajouter un nom de domaine personnalisé à 18 €/an
          </label>
        </div>

        <div className="field field--full">
          <label className="checkbox-row">
            <input
              type="checkbox"
              checked={form.splitPayment}
              onChange={(event) => setForm((current) => ({ ...current, splitPayment: event.target.checked }))}
              disabled={showQuote}
            />
            Payer en 3x sans frais sur 12 mois avec prélèvement immédiat puis à 4 mois et 8 mois
          </label>
        </div>

        <div className="field field--full">
          <button className="btn btn--primary" type="submit" disabled={status === "loading"}>
            {copy.submit}
          </button>
        </div>
      </form>
      {message ? <p className={status === "success" ? "success" : "warning"}>{message}</p> : null}
      <p className="notice">{copy.stripeMissing}</p>
    </div>
  );
}
