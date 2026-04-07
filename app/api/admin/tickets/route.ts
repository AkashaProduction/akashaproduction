import { NextResponse } from "next/server";
import { getAdminSession } from "@/lib/admin-auth";
import { getAllTickets } from "@/lib/storage";

export async function GET() {
  const session = await getAdminSession();
  if (!session) {
    return NextResponse.json({ message: "Authentification requise." }, { status: 401 });
  }

  const tickets = await getAllTickets();
  return NextResponse.json({ tickets });
}
