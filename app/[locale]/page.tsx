import { WebsitePreviewCard } from "@/components/WebsitePreviewCard";
import { getDictionary, isLocale, t } from "@/lib/i18n";
import { notFound } from "next/navigation";

const showcaseSites = [
  {
    title: "CMS Source",
    url: "https://www.cms-source.org",
    description: "Plateforme éditoriale et technique orientée publication, structure et déploiement de contenus."
  },
  {
    title: "Mafiaz World",
    url: "https://www.mafiaz.world",
    description: "Univers de marque avec identité forte, narration visuelle et présence web différenciante."
  },
  {
    title: "Permathèque",
    url: "http://www.permatheque.fr",
    description: "Projet de transmission de ressources autour de la permaculture, du vivant et de l’autonomie."
  },
  {
    title: "Vivre en Autonomie",
    url: "https://www.vivre-en-autonomie.fr",
    description: "Site thématique pensé pour des contenus structurés, lisibles et durables."
  },
  {
    title: "Conseil Ayurveda",
    url: "https://www.conseil-ayurveda.fr",
    description: "Présence professionnelle dédiée au conseil, à la prise de contact et à la valorisation d’expertise."
  },
  {
    title: "On Apprend Tous Les Jours",
    url: "https://www.onapprendtouslesjours.fr",
    description: "Projet éditorial conçu pour organiser le savoir, publier et diffuser efficacement."
  },
  {
    title: "Eau Dynamisée",
    url: "https://www.eau-dynamisee.com",
    description: "Site orienté pédagogie produit, crédibilité commerciale et clarté d’offre."
  },
  {
    title: "Atlas Access Immo",
    url: "https://www.atlas-access.immo",
    description: "Vitrine immobilière professionnelle, structurée pour la lisibilité, la confiance et la conversion."
  }
] as const;

export default async function HomePage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <>
      <section className="hero">
        <div className="container hero__grid hero__grid--showcase">
          <div>
            <p className="hero__eyebrow">{messages.home.hero.eyebrow}</p>
            <h1 className="hero__title">{messages.home.hero.title}</h1>
            <p className="hero__lead">{messages.home.hero.lead}</p>
            <div className="cta-row">
              <a href="#qui-nous-sommes" className="btn btn--primary">
                {messages.home.hero.primary}
              </a>
            </div>
          </div>
          <div className="device-scene glass">
            <img
              className="device-shot device-shot--desktop"
              src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=1200&q=80"
              alt="Présentation d'une interface web sur plusieurs écrans"
            />
            <img
              className="device-shot device-shot--tablet"
              src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=900&q=80"
              alt=""
            />
            <img
              className="device-shot device-shot--mobile"
              src="https://images.unsplash.com/photo-1496171367470-9ed9a91ea931?auto=format&fit=crop&w=600&q=80"
              alt=""
            />
          </div>
        </div>
      </section>

      <section className="section" id="qui-nous-sommes">
        <div className="container grid-2">
          <div className="panel">
            <p className="kicker">{messages.home.about.eyebrow}</p>
            <h2 className="section-title">{messages.home.about.title}</h2>
            <p className="section-copy">{messages.home.about.copyOne}</p>
            <p className="section-copy">{messages.home.about.copyTwo}</p>
          </div>
          <div className="grid-2">
            {["ecosystem", "commerce", "hosting", "experience"].map((entry) => (
              <article className="card" key={entry}>
                <p className="kicker">{t(messages, `home.cards.${entry}.kicker`)}</p>
                <h3>{t(messages, `home.cards.${entry}.title`)}</h3>
                <p className="section-copy">{t(messages, `home.cards.${entry}.copy`)}</p>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container">
          <p className="kicker">{messages.home.projects.eyebrow}</p>
          <h2 className="section-title">{messages.home.projects.title}</h2>
          <p className="section-copy">{messages.home.projects.lead}</p>
          <div className="project-grid">
            {showcaseSites.map((site) => (
              <WebsitePreviewCard key={site.url} title={site.title} url={site.url} description={site.description} />
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="container grid-3">
          {["ecosystem", "commerce", "hosting"].map((entry) => (
            <article className="card" key={entry}>
              <p className="kicker">{t(messages, `home.cards.${entry}.kicker`)}</p>
              <h3>{t(messages, `home.cards.${entry}.title`)}</h3>
              <p className="section-copy">{t(messages, `home.cards.${entry}.copy`)}</p>
            </article>
          ))}
        </div>
      </section>
    </>
  );
}
