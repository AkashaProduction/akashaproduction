import { NextResponse } from "next/server";
import { computeOrderSummary, type OrderDraft } from "@/lib/catalog";
import { saveOrder } from "@/lib/storage";
import { createCheckoutForOrder } from "@/lib/stripe";

export async function POST(request: Request) {
  const order = (await request.json()) as OrderDraft;
  const summary = computeOrderSummary(order);

  const status = summary.subtotal > 0 ? "pending-payment" : "quote-requested";
  const stored = await saveOrder(status, {
    ...order,
    summary
  });

  const checkoutUrl = summary.subtotal > 0 ? await createCheckoutForOrder(order, stored.id) : null;

  return NextResponse.json({
    id: stored.id,
    checkoutUrl,
    message: checkoutUrl
      ? "Redirection vers Stripe."
      : "Votre demande a bien été enregistrée. Nous reviendrons vers vous avec une proposition sur mesure."
  });
}
