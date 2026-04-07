import { OrderForm } from "@/components/OrderForm";
import { getDictionary, isLocale } from "@/lib/i18n";
import { notFound } from "next/navigation";

export default async function OrderPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <section className="section">
      <div className="container">
        <p className="kicker">{messages.order.eyebrow}</p>
        <h1 className="page-title">{messages.order.title}</h1>
        <p className="section-copy">{messages.order.lead}</p>

        <div className="grid-3">
          <article className="card">
            <h3>{messages.order.lines.creation}</h3>
            <ul>
              <li>Site vitrine 3 pages: 50 €</li>
              <li>Site complexe 9 pages: 500 €</li>
              <li>Création personnalisée: offert en mode devis</li>
            </ul>
          </article>
          <article className="card">
            <h3>{messages.order.lines.hosting}</h3>
            <ul>
              <li>Mutualisé mensuel: 8 €</li>
              <li>Mutualisé annuel: 88 €</li>
              <li>VPS dédié: 200 €</li>
            </ul>
          </article>
          <article className="card">
            <h3>{messages.order.lines.packs}</h3>
            <ul>
              <li>Pack vitrine + mutualisé annuel: 120 €</li>
              <li>Pack complexe + mutualisé annuel: 550 €</li>
              <li>Pack personnalisé: sur devis</li>
            </ul>
          </article>
        </div>

        <OrderForm
          locale={locale}
          copy={{
            title: messages.order.form.title,
            description: messages.order.form.description,
            submit: messages.order.form.submit,
            success: messages.order.form.success,
            stripeMissing: messages.order.form.stripeMissing
          }}
        />
      </div>
    </section>
  );
}
