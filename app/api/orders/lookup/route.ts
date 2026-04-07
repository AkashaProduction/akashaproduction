import { NextResponse } from "next/server";
import { getOrdersByEmail, getTicketsByEmail } from "@/lib/storage";

export async function POST(request: Request) {
  const { email } = (await request.json()) as { email?: string };
  if (!email) {
    return NextResponse.json({ message: "Veuillez renseigner un email.", orders: [] }, { status: 400 });
  }

  const orders = await getOrdersByEmail(email);
  const tickets = await getTicketsByEmail(email);
  return NextResponse.json({
    message:
      orders.length || tickets.length
        ? `${orders.length} commande(s) et ${tickets.length} ticket(s) retrouvés.`
        : "Aucune commande ni ticket pour cet email pour le moment.",
    orders,
    tickets
  });
}
