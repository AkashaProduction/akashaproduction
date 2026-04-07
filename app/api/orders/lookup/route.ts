import { NextResponse } from "next/server";
import { getOrdersByEmail } from "@/lib/storage";

export async function POST(request: Request) {
  const { email } = (await request.json()) as { email?: string };
  if (!email) {
    return NextResponse.json({ message: "Veuillez renseigner un email.", orders: [] }, { status: 400 });
  }

  const orders = await getOrdersByEmail(email);
  return NextResponse.json({
    message: orders.length ? `${orders.length} commande(s) retrouvée(s).` : "Aucune commande pour cet email pour le moment.",
    orders
  });
}
