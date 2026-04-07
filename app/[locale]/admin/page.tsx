import { AdminDesk } from "@/components/AdminDesk";
import { getDictionary, isLocale } from "@/lib/i18n";
import { notFound } from "next/navigation";

export default async function AdminPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <section className="section">
      <div className="container grid-2">
        <div>
          <p className="kicker">Administration</p>
          <h1 className="page-title">Modération des tickets et suivi client</h1>
          <p className="section-copy">
            Ce panel permet de modérer les tickets, de répondre aux clients et de piloter les demandes du service
            commercial et du service technique.
          </p>
          <div className="panel">
            <ul>
              <li>{messages.account.future.one}</li>
              <li>Catégories cohérentes de tickets: commercial et technique</li>
              <li>Réponses de modération visibles côté client dans le panel utilisateur</li>
            </ul>
          </div>
        </div>
        <AdminDesk />
      </div>
    </section>
  );
}
