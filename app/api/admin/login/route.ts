import { NextResponse } from "next/server";
import { createAdminSessionToken, setAdminSession, validateAdminCredentials } from "@/lib/admin-auth";

export async function POST(request: Request) {
  const { email, password } = (await request.json()) as { email?: string; password?: string };

  if (!email || !password || !validateAdminCredentials(email, password)) {
    return NextResponse.json({ message: "Identifiants administrateur invalides." }, { status: 401 });
  }

  await setAdminSession(createAdminSessionToken(email));
  return NextResponse.json({ message: "Connexion administrateur ouverte." });
}
