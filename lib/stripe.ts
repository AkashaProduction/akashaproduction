import Stripe from "stripe";
import { catalog, computeOrderSummary, getInstallmentAmount, type OrderDraft } from "@/lib/catalog";

export function getStripeClient() {
  const secretKey = process.env.STRIPE_SECRET_KEY;
  if (!secretKey) {
    return null;
  }

  return new Stripe(secretKey, {
    apiVersion: "2026-02-25.clover"
  });
}

function getBaseUrl() {
  return process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000";
}

export async function createCheckoutForOrder(order: OrderDraft, orderId: string) {
  const stripe = getStripeClient();
  if (!stripe) {
    return null;
  }

  const summary = computeOrderSummary(order);
  const subtotal = summary.subtotal;
  const lineItems: Stripe.Checkout.SessionCreateParams.LineItem[] = [];

  if (order.selections.splitPayment && subtotal > 0) {
    const creationEnv =
      order.selections.creation === "showcase"
        ? process.env.STRIPE_PRICE_INSTALLMENT_SHOWCASE
        : order.selections.creation === "complex"
          ? process.env.STRIPE_PRICE_INSTALLMENT_COMPLEX
          : "";

    if (!creationEnv) {
      throw new Error("Missing installment Stripe price for selected creation tier.");
    }

    lineItems.push({
      price: creationEnv,
      quantity: 1
    });

    if (order.selections.includeDomainAddon) {
      const domainPrice = process.env.STRIPE_PRICE_DOMAIN;
      if (domainPrice) {
        lineItems.push({ price: domainPrice, quantity: 1 });
      }
    }

    const session = await stripe.checkout.sessions.create({
      mode: "subscription",
      customer_email: order.customer.email,
      success_url: `${getBaseUrl()}/${order.locale}/mon-compte?checkout=success`,
      cancel_url: `${getBaseUrl()}/${order.locale}/commander?checkout=canceled`,
      line_items: lineItems,
      metadata: {
        orderId,
        splitPayment: "true",
        creation: order.selections.creation,
        hosting: order.selections.hosting
      }
    });

    return session.url;
  }

  for (const item of summary.items) {
    if (item.kind === "pack" || item.kind === "creation" || item.kind === "hosting" || item.kind === "domain") {
      lineItems.push({
        price_data: {
          currency: "eur",
          product_data: {
            name: item.label
          },
          unit_amount: Math.round(item.amount * 100)
        },
        quantity: 1
      });
    }
  }

  if (!lineItems.length) {
    return null;
  }

  const session = await stripe.checkout.sessions.create({
    mode: "payment",
    customer_email: order.customer.email,
    success_url: `${getBaseUrl()}/${order.locale}/mon-compte?checkout=success`,
    cancel_url: `${getBaseUrl()}/${order.locale}/commander?checkout=canceled`,
    line_items: lineItems,
    metadata: {
      orderId,
      splitPayment: "false",
      total: String(subtotal),
      suggestedInstallment: String(getInstallmentAmount(subtotal))
    }
  });

  return session.url;
}

export async function handleWebhook(body: string, signature: string | null) {
  const stripe = getStripeClient();
  const secret = process.env.STRIPE_WEBHOOK_SECRET;
  if (!stripe || !secret || !signature) {
    return null;
  }

  return stripe.webhooks.constructEvent(body, signature, secret);
}
