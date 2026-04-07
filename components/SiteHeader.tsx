"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { locales } from "@/lib/i18n";
import type { Locale } from "@/lib/catalog";

type HeaderProps = {
  locale: Locale;
  labels: {
    presentation: string;
    solutions: string;
    contact: string;
    order: string;
    account: string;
    subtitle: string;
  };
};

export function SiteHeader({ locale, labels }: HeaderProps) {
  const pathname = usePathname();
  const currentPath = pathname.replace(`/${locale}`, "") || "/";

  return (
    <header className="site-header">
      <div className="site-header__inner">
        <Link href={`/${locale}`} className="brand">
          <strong>Akasha Production</strong>
          <span>{labels.subtitle}</span>
        </Link>
        <nav className="nav">
          <Link href={`/${locale}`} data-active={currentPath === "/"}>
            {labels.presentation}
          </Link>
          <Link href={`/${locale}/solutions`} data-active={currentPath === "/solutions"}>
            {labels.solutions}
          </Link>
          <Link href={`/${locale}/contact`} data-active={currentPath === "/contact"}>
            {labels.contact}
          </Link>
          <Link href={`/${locale}/commander`} data-active={currentPath === "/commander"}>
            {labels.order}
          </Link>
          <Link href={`/${locale}/mon-compte`} data-active={currentPath === "/mon-compte"}>
            {labels.account}
          </Link>
        </nav>
        <div className="locale-switcher">
          {locales.map((entry) => (
            <Link key={entry} href={`/${entry}${currentPath === "/" ? "" : currentPath}`} data-active={entry === locale}>
              {entry.toUpperCase()}
            </Link>
          ))}
        </div>
      </div>
    </header>
  );
}
