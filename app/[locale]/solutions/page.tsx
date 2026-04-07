import { getDictionary, isLocale } from "@/lib/i18n";
import { notFound } from "next/navigation";

export default async function SolutionsPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <section className="section">
      <div className="container">
        <p className="kicker">{messages.solutions.eyebrow}</p>
        <h1 className="page-title">{messages.solutions.title}</h1>
        <p className="section-copy">{messages.solutions.lead}</p>

        <div className="grid-3">
          <article className="pricing-card">
            <p className="kicker">{messages.products.creation.showcase.name}</p>
            <h3>{messages.products.creation.showcase.headline}</h3>
            <div className="price">
              <strong>50 €</strong>
            </div>
            <ul>
              <li>{messages.products.creation.showcase.points.one}</li>
              <li>{messages.products.creation.showcase.points.two}</li>
              <li>{messages.products.creation.showcase.points.three}</li>
            </ul>
          </article>
          <article className="pricing-card">
            <p className="kicker">{messages.products.creation.complex.name}</p>
            <h3>{messages.products.creation.complex.headline}</h3>
            <div className="price">
              <strong>500 €</strong>
            </div>
            <ul>
              <li>{messages.products.creation.complex.points.one}</li>
              <li>{messages.products.creation.complex.points.two}</li>
              <li>{messages.products.creation.complex.points.three}</li>
            </ul>
          </article>
          <article className="pricing-card">
            <p className="kicker">{messages.products.creation.custom.name}</p>
            <h3>{messages.products.creation.custom.headline}</h3>
            <div className="price">
              <strong>{messages.common.onRequest}</strong>
            </div>
            <ul>
              <li>{messages.products.creation.custom.points.one}</li>
              <li>{messages.products.creation.custom.points.two}</li>
              <li>{messages.products.creation.custom.points.three}</li>
            </ul>
          </article>
        </div>

        <div className="grid-3 section">
          <article className="pricing-card">
            <p className="kicker">{messages.products.hosting.sharedMonthly.name}</p>
            <h3>{messages.products.hosting.sharedMonthly.headline}</h3>
            <div className="price">
              <strong>8 €</strong>
              <span>/ mois</span>
            </div>
          </article>
          <article className="pricing-card">
            <p className="kicker">{messages.products.hosting.vps.name}</p>
            <h3>{messages.products.hosting.vps.headline}</h3>
            <div className="price">
              <strong>200 €</strong>
              <span>/ mois</span>
            </div>
          </article>
          <article className="pricing-card">
            <p className="kicker">{messages.products.hosting.cloud.name}</p>
            <h3>{messages.products.hosting.cloud.headline}</h3>
            <div className="price">
              <strong>{messages.common.onRequest}</strong>
            </div>
          </article>
        </div>

        <div className="grid-3">
          <article className="pricing-card">
            <p className="kicker">{messages.packs.first.title}</p>
            <div className="price">
              <strong>120 €</strong>
              <s>138 €</s>
            </div>
            <p className="section-copy">{messages.packs.first.copy}</p>
          </article>
          <article className="pricing-card">
            <p className="kicker">{messages.packs.second.title}</p>
            <div className="price">
              <strong>550 €</strong>
              <s>588 €</s>
            </div>
            <p className="section-copy">{messages.packs.second.copy}</p>
          </article>
          <article className="pricing-card">
            <p className="kicker">{messages.packs.third.title}</p>
            <div className="price">
              <strong>{messages.common.onRequest}</strong>
            </div>
            <p className="section-copy">{messages.packs.third.copy}</p>
          </article>
        </div>
      </div>
    </section>
  );
}
