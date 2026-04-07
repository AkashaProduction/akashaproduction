import { Footer } from "@/components/Footer";
import { SiteHeader } from "@/components/SiteHeader";
import { getDictionary, isLocale } from "@/lib/i18n";
import { notFound } from "next/navigation";

export default async function LocaleLayout({
  children,
  params
}: Readonly<{
  children: React.ReactNode;
  params: Promise<{ locale: string }>;
}>) {
  const { locale } = await params;
  if (!isLocale(locale)) {
    notFound();
  }

  const messages = getDictionary(locale);

  return (
    <html lang={locale}>
      <body>
        <div className="page-shell">
          <SiteHeader
            locale={locale}
            labels={{
              presentation: messages.nav.presentation,
              solutions: messages.nav.solutions,
              contact: messages.nav.contact,
              order: messages.nav.order,
              account: messages.nav.account,
              subtitle: messages.nav.subtitle
            }}
          />
          <main>{children}</main>
          <Footer copy={messages.footer.copy} />
        </div>
      </body>
    </html>
  );
}
