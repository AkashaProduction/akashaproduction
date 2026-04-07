import { mkdir, writeFile } from "node:fs/promises";
import path from "node:path";
import { randomUUID } from "node:crypto";
import { NextResponse } from "next/server";
import { saveContact } from "@/lib/storage";
import { sendContactEmail } from "@/lib/email";

const allowedTypes = new Set([
  "application/pdf",
  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
  "image/jpeg",
  "image/webp"
]);

export async function POST(request: Request) {
  const formData = await request.formData();
  const files = formData.getAll("attachments").filter((value): value is File => value instanceof File && value.size > 0);
  const uploadDir = path.join(process.cwd(), "public", "uploads");
  await mkdir(uploadDir, { recursive: true });

  const attachments: { filename: string; path: string }[] = [];

  for (const file of files) {
    if (!allowedTypes.has(file.type)) {
      return NextResponse.json({ error: "Format de fichier non autorisé." }, { status: 400 });
    }

    const buffer = Buffer.from(await file.arrayBuffer());
    const safeName = `${randomUUID()}-${file.name.replaceAll(/\s+/g, "-")}`;
    const filePath = path.join(uploadDir, safeName);
    await writeFile(filePath, buffer);
    attachments.push({ filename: file.name, path: filePath });
  }

  const payload = Object.fromEntries(
    Array.from(formData.entries()).filter(([key]) => key !== "attachments")
  );

  const record = await saveContact({
    ...payload,
    attachments: attachments.map((attachment) => attachment.filename)
  });

  await sendContactEmail(
    `Nouveau contact Akasha Production - ${payload.email ?? "sans-email"}`,
    `<p>Nouveau message reçu.</p><pre>${JSON.stringify(payload, null, 2)}</pre>`,
    attachments
  );

  return NextResponse.json({
    message: "Votre message a bien été enregistré et transmis. Confirmation affichée sur cette page.",
    id: record.id
  });
}
