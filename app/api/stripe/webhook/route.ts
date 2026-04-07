import { NextResponse } from "next/server";
import { getStripeClient, handleWebhook } from "@/lib/stripe";

export async function POST(request: Request) {
  const body = await request.text();
  const event = await handleWebhook(body, request.headers.get("stripe-signature"));
  const stripe = getStripeClient();

  if (!event || !stripe) {
    return NextResponse.json({ received: false }, { status: 400 });
  }

  if (event.type === "invoice.paid") {
    const invoice = event.data.object;
    if (invoice.subscription) {
      const invoices = await stripe.invoices.list({
        subscription: String(invoice.subscription),
        status: "paid",
        limit: 10
      });

      if (invoices.data.length >= 3) {
        await stripe.subscriptions.cancel(String(invoice.subscription));
      }
    }
  }

  return NextResponse.json({ received: true });
}
