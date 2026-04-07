import { AccountLookup } from "@/components/AccountLookup";
import { getDictionary, isLocale } from "@/lib/i18n";
import { notFound } from "next/navigation";

export default async function AccountPage({ params }: { params: Promise<{ locale: string }> }) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <section className="section">
      <div className="container grid-2">
        <div>
          <p className="kicker">{messages.account.eyebrow}</p>
          <h1 className="page-title">{messages.account.title}</h1>
          <p className="section-copy">{messages.account.lead}</p>
          <div className="panel">
            <ul>
              <li>{messages.account.future.one}</li>
              <li>{messages.account.future.two}</li>
              <li>{messages.account.future.three}</li>
              <li>Support intégré: tickets Service commercial et Service technique</li>
            </ul>
          </div>
        </div>
        <AccountLookup />
      </div>
    </section>
  );
}
