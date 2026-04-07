import { NextResponse } from "next/server";
import { sendContactEmail } from "@/lib/email";
import { saveTicket } from "@/lib/storage";
import { isTicketDepartment, isTicketPriority, isValidTicketSubject } from "@/lib/support";

export async function POST(request: Request) {
  const payload = (await request.json()) as {
    email?: string;
    name?: string;
    department?: string;
    subject?: string;
    priority?: string;
    orderId?: string;
    message?: string;
  };

  if (
    !payload.email ||
    !payload.name ||
    !payload.department ||
    !payload.subject ||
    !payload.priority ||
    !payload.message ||
    !isTicketDepartment(payload.department) ||
    !isTicketPriority(payload.priority) ||
    !isValidTicketSubject(payload.department, payload.subject)
  ) {
    return NextResponse.json({ message: "Les informations du ticket sont incomplètes ou invalides." }, { status: 400 });
  }

  const ticket = await saveTicket({
    customer: {
      email: payload.email,
      name: payload.name
    },
    department: payload.department,
    priority: payload.priority,
    subject: payload.subject,
    orderId: payload.orderId || undefined,
    message: payload.message
  });

  await sendContactEmail(
    `Nouveau ticket ${ticket.department} - ${ticket.subject}`,
    `<p>Ticket ${ticket.id}</p><p>${payload.name} - ${payload.email}</p><p>${payload.message}</p>`,
    []
  );

  return NextResponse.json({ message: "Votre ticket a bien été créé." });
}
