import { NextResponse } from "next/server";
import { addTicketReply } from "@/lib/storage";

export async function POST(request: Request) {
  const payload = (await request.json()) as {
    ticketId?: string;
    email?: string;
    name?: string;
    message?: string;
  };

  if (!payload.ticketId || !payload.email || !payload.name || !payload.message?.trim()) {
    return NextResponse.json({ message: "Réponse client incomplète." }, { status: 400 });
  }

  const ticket = await addTicketReply({
    ticketId: payload.ticketId,
    authorType: "customer",
    authorLabel: payload.name,
    customerEmail: payload.email,
    message: payload.message.trim()
  });

  if (!ticket) {
    return NextResponse.json({ message: "Impossible d'ajouter cette réponse." }, { status: 404 });
  }

  return NextResponse.json({ message: "Votre complément a bien été ajouté au ticket." });
}
