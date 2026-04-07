import Link from "next/link";
import type { Locale } from "@/lib/catalog";

export function Hero({
  locale,
  title,
  lead,
  primary,
  secondary,
  stats
}: {
  locale: Locale;
  title: string;
  lead: string;
  primary: string;
  secondary: string;
  stats: { value: string; label: string }[];
}) {
  return (
    <section className="hero">
      <div className="container hero__grid">
        <div>
          <div className="hero__eyebrow">Akasha Production</div>
          <h1 className="hero__title">{title}</h1>
          <p className="hero__lead">{lead}</p>
          <div className="cta-row">
            <Link href={`/${locale}/commander`} className="btn btn--primary">
              {primary}
            </Link>
            <Link href={`/${locale}/solutions`} className="btn btn--secondary">
              {secondary}
            </Link>
          </div>
          <div className="hero__stats">
            {stats.map((stat) => (
              <div className="glass" key={stat.value + stat.label}>
                <strong>{stat.value}</strong>
                <span>{stat.label}</span>
              </div>
            ))}
          </div>
        </div>
        <div className="glass">
          <p className="kicker">Sous-projets fondateurs</p>
          <div className="tag-list">
            {[
              "CMS Source",
              "Ombres & Lumières",
              "Permatheque",
              "Vivre en Autonomie",
              "Conseil Ayurveda",
              "On Apprend Tous Les Jours",
              "Location Vente",
              "Université Védique",
              "À La Source De L'Eau",
              "Épanouissement Amoureux",
              "Eau Dynamisée",
              "Harmonie Holistique",
              "Atlas Access Immo"
            ].map((tag) => (
              <span key={tag} className="tag">
                {tag}
              </span>
            ))}
          </div>
        </div>
      </div>
    </section>
  );
}
