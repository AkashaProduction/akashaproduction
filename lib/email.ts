import nodemailer from "nodemailer";

export async function sendContactEmail(subject: string, html: string, attachments: { filename: string; path: string }[]) {
  const host = process.env.SMTP_HOST;
  const user = process.env.SMTP_USER;
  const pass = process.env.SMTP_PASS;
  const to = process.env.AKASHA_CONTACT_EMAIL ?? "contact@akashaproduction.com";

  if (!host || !user || !pass) {
    return { delivered: false };
  }

  const transporter = nodemailer.createTransport({
    host,
    port: Number(process.env.SMTP_PORT ?? 587),
    secure: false,
    auth: { user, pass }
  });

  await transporter.sendMail({
    from: process.env.SMTP_FROM ?? "Akasha Production <noreply@akashaproduction.com>",
    to,
    subject,
    html,
    attachments
  });

  return { delivered: true };
}
