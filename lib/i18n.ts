import fr from "@/messages/fr.json";
import en from "@/messages/en.json";
import es from "@/messages/es.json";
import type { Locale } from "@/lib/catalog";

const dictionaries = { fr, en, es };

export const locales: Locale[] = ["fr", "en", "es"];

export function getDictionary(locale: Locale) {
  return dictionaries[locale] ?? dictionaries.fr;
}

export function isLocale(value: string): value is Locale {
  return locales.includes(value as Locale);
}

export function t(messages: Record<string, unknown>, path: string): string {
  return path.split(".").reduce<unknown>((acc, key) => {
    if (acc && typeof acc === "object" && key in acc) {
      return (acc as Record<string, unknown>)[key];
    }
    return path;
  }, messages) as string;
}
