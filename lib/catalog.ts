export type Locale = "fr" | "en" | "es";

export type CreationTier = "showcase" | "complex" | "custom";
export type HostingTier = "shared-monthly" | "shared-yearly" | "vps" | "cloud";
export type DomainTier = "subdomain" | "custom-domain";

export type OrderDraft = {
  locale: Locale;
  customer: {
    firstName: string;
    lastName: string;
    email: string;
    phone: string;
    company: string;
    country: string;
  };
  selections: {
    creation: CreationTier | "";
    hosting: HostingTier | "";
    domain: DomainTier | "";
    requestedParentDomain: string;
    splitPayment: boolean;
    includeDomainAddon: boolean;
  };
  quoteAnswers?: Record<string, string>;
  projectDescription: string;
};

export const parentDomains = [
  "akashaproduction.com",
  "permatheque.fr",
  "vivre-en-autonomie.fr",
  "conseil-ayurveda.fr",
  "onapprendtouslesjours.fr",
  "harmonie-holistique.fr",
  "mafiaz.world",
  "alasourcedeleau.org"
] as const;

export const quoteQuestions = [
  {
    id: "goal",
    options: ["Site vitrine", "E-commerce", "Blog", "Espace membre", "À étudier"]
  },
  {
    id: "style",
    options: ["Sobre et premium", "Nature et bien-être", "Institutionnel", "Éditorial", "À étudier"]
  },
  {
    id: "content",
    options: ["J'ai déjà les contenus", "J'ai besoin d'aide", "Traductions à prévoir", "Photos à produire", "À étudier"]
  },
  {
    id: "timeline",
    options: ["Urgent", "1 mois", "2 à 3 mois", "Souple", "À étudier"]
  },
  {
    id: "features",
    options: ["Newsletter", "Blog", "Paiement en ligne", "Base de données", "À étudier"]
  }
] as const;

export const catalog = {
  creation: {
    showcase: {
      id: "showcase",
      labelKey: "products.creation.showcase.name",
      descriptionKey: "products.creation.showcase.description",
      amount: 50,
      stripeEnv: "STRIPE_PRICE_SHOWCASE"
    },
    complex: {
      id: "complex",
      labelKey: "products.creation.complex.name",
      descriptionKey: "products.creation.complex.description",
      amount: 500,
      stripeEnv: "STRIPE_PRICE_COMPLEX"
    },
    custom: {
      id: "custom",
      labelKey: "products.creation.custom.name",
      descriptionKey: "products.creation.custom.description",
      amount: 0,
      stripeEnv: ""
    }
  },
  hosting: {
    "shared-monthly": {
      id: "shared-monthly",
      labelKey: "products.hosting.sharedMonthly.name",
      descriptionKey: "products.hosting.sharedMonthly.description",
      amount: 8,
      recurring: "month",
      stripeEnv: "STRIPE_PRICE_HOSTING_SHARED_MONTHLY"
    },
    "shared-yearly": {
      id: "shared-yearly",
      labelKey: "products.hosting.sharedYearly.name",
      descriptionKey: "products.hosting.sharedYearly.description",
      amount: 88,
      recurring: "year",
      stripeEnv: "STRIPE_PRICE_HOSTING_SHARED_YEARLY"
    },
    vps: {
      id: "vps",
      labelKey: "products.hosting.vps.name",
      descriptionKey: "products.hosting.vps.description",
      amount: 200,
      recurring: "month",
      stripeEnv: "STRIPE_PRICE_HOSTING_VPS_MONTHLY"
    },
    cloud: {
      id: "cloud",
      labelKey: "products.hosting.cloud.name",
      descriptionKey: "products.hosting.cloud.description",
      amount: 0,
      recurring: "",
      stripeEnv: ""
    }
  },
  domain: {
    subdomain: {
      id: "subdomain",
      labelKey: "products.domain.subdomain.name",
      descriptionKey: "products.domain.subdomain.description",
      amount: 0,
      stripeEnv: ""
    },
    "custom-domain": {
      id: "custom-domain",
      labelKey: "products.domain.custom.name",
      descriptionKey: "products.domain.custom.description",
      amount: 18,
      stripeEnv: "STRIPE_PRICE_DOMAIN"
    }
  }
} as const;

const packDiscountMatrix: Record<string, number> = {
  "showcase:shared-yearly": 120,
  "complex:shared-yearly": 550,
  "showcase:vps": 230,
  "complex:vps": 675
};

export type PricedSelection = {
  label: string;
  amount: number;
  kind: "creation" | "hosting" | "domain" | "pack" | "quote";
};

export function computeOrderSummary(order: OrderDraft) {
  const items: PricedSelection[] = [];
  const { creation, hosting, domain, includeDomainAddon } = order.selections;
  let total = 0;

  if (creation && creation !== "custom") {
    items.push({
      label: creation,
      amount: catalog.creation[creation].amount,
      kind: "creation"
    });
    total += catalog.creation[creation].amount;
  }

  if (hosting && hosting !== "cloud") {
    items.push({
      label: hosting,
      amount: catalog.hosting[hosting].amount,
      kind: "hosting"
    });
    total += catalog.hosting[hosting].amount;
  }

  if (creation && hosting) {
    const key = `${creation}:${hosting}`;
    const packAmount = packDiscountMatrix[key];
    if (packAmount) {
      const baseTotal =
        (creation === "custom" ? 0 : catalog.creation[creation].amount) +
        (hosting === "cloud" ? 0 : catalog.hosting[hosting].amount);
      if (baseTotal > packAmount) {
        return {
          items: [
            {
              label: key,
              amount: packAmount,
              kind: "pack"
            },
            ...(includeDomainAddon
              ? [
                  {
                    label: "custom-domain",
                    amount: catalog.domain["custom-domain"].amount,
                    kind: "domain" as const
                  }
                ]
              : [])
          ],
          subtotal: packAmount + (includeDomainAddon ? catalog.domain["custom-domain"].amount : 0),
          packDiscount: baseTotal - packAmount
        };
      }
    }
  }

  if (includeDomainAddon || domain === "custom-domain") {
    items.push({
      label: "custom-domain",
      amount: catalog.domain["custom-domain"].amount,
      kind: "domain"
    });
    total += catalog.domain["custom-domain"].amount;
  }

  return {
    items,
    subtotal: total,
    packDiscount: 0
  };
}

export function getInstallmentAmount(total: number) {
  return Math.ceil((total / 3) * 100) / 100;
}
