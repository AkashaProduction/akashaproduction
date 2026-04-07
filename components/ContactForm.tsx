"use client";

import { useState } from "react";

export function ContactForm({ locale }: { locale: string }) {
  const [isProject, setIsProject] = useState(false);
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">("idle");
  const [message, setMessage] = useState("");

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("loading");
    const formData = new FormData(event.currentTarget);
    formData.set("locale", locale);

    const response = await fetch("/api/contact", {
      method: "POST",
      body: formData
    });

    const data = await response.json();
    if (!response.ok) {
      setStatus("error");
      setMessage(data.error ?? "Erreur");
      return;
    }

    setStatus("success");
    setMessage(data.message);
    event.currentTarget.reset();
    setIsProject(false);
  }

  return (
    <form className="form-card form-grid" onSubmit={onSubmit}>
      {[
        ["firstName", "Prénom"],
        ["lastName", "Nom"],
        ["email", "Email"],
        ["phone", "Téléphone"],
        ["company", "Organisation"],
        ["website", "Site existant"]
      ].map(([name, label]) => (
        <div className="field" key={name}>
          <label htmlFor={name}>{label}</label>
          <input id={name} name={name} type={name === "email" ? "email" : "text"} required={["firstName", "lastName", "email"].includes(name)} />
        </div>
      ))}

      <div className="field field--full">
        <label htmlFor="message">Message</label>
        <textarea id="message" name="message" required />
      </div>

      <div className="field field--full">
        <label className="checkbox-row">
          <input type="checkbox" name="hasProject" onChange={(event) => setIsProject(event.target.checked)} />
          Il s’agit d’un projet à cadrer
        </label>
      </div>

      {isProject ? (
        <div className="field field--full">
          <label htmlFor="projectDetails">Précisions projet</label>
          <textarea id="projectDetails" name="projectDetails" />
        </div>
      ) : null}

      <div className="field field--full">
        <label htmlFor="attachments">Documents joints (docx, pdf, jpg, webp)</label>
        <input id="attachments" name="attachments" type="file" multiple accept=".docx,.pdf,.jpg,.jpeg,.webp" />
      </div>

      <div className="field field--full">
        <button className="btn btn--primary" type="submit" disabled={status === "loading"}>
          Envoyer
        </button>
      </div>

      {message ? <p className={status === "success" ? "success" : "warning"}>{message}</p> : null}
    </form>
  );
}
