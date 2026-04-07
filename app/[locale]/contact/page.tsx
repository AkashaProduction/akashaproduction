import { ContactForm } from "@/components/ContactForm";
import { getDictionary, isLocale } from "@/lib/i18n";
import { notFound } from "next/navigation";

export default async function ContactPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <section className="section">
      <div className="container grid-2">
        <div>
          <p className="kicker">{messages.contact.eyebrow}</p>
          <h1 className="page-title">{messages.contact.title}</h1>
          <p className="section-copy">{messages.contact.lead}</p>
          <div className="panel">
            <h3>contact@akashaproduction.com</h3>
            <p className="muted">
              Le formulaire enregistre toutes les informations et peut transmettre vos pièces jointes si un SMTP est
              configuré.
            </p>
          </div>
        </div>
        <ContactForm locale={locale} />
      </div>
    </section>
  );
}
