import { NextResponse } from "next/server";
import { getAdminSession } from "@/lib/admin-auth";
import { sendEmail } from "@/lib/email";
import { addTicketReply, updateTicket } from "@/lib/storage";
import { isTicketPriority, isTicketStatus } from "@/lib/support";

export async function PATCH(request: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await getAdminSession();
  if (!session) {
    return NextResponse.json({ message: "Authentification requise." }, { status: 401 });
  }

  const { id } = await params;
  const payload = (await request.json()) as {
    status?: string;
    priority?: string;
    replyMessage?: string;
  };

  const ticket = await updateTicket({
    ticketId: id,
    status: payload.status && isTicketStatus(payload.status) ? payload.status : undefined,
    priority: payload.priority && isTicketPriority(payload.priority) ? payload.priority : undefined
  });

  if (!ticket) {
    return NextResponse.json({ message: "Ticket introuvable." }, { status: 404 });
  }

  if (payload.replyMessage?.trim()) {
    const updated = await addTicketReply({
      ticketId: id,
      authorType: "admin",
      authorLabel: "Akasha Production",
      message: payload.replyMessage.trim()
    });

    if (updated) {
      await sendEmail({
        to: updated.customer.email,
        subject: `Réponse à votre ticket ${updated.id.slice(0, 8)}`,
        html: `<p>Bonjour ${updated.customer.name},</p><p>${payload.replyMessage.trim()}</p>`
      });
    }
  }

  return NextResponse.json({ message: "Ticket mis à jour." });
}
