import nodemailer from "nodemailer";

async function getTransporter() {
  const host = process.env.SMTP_HOST;
  const user = process.env.SMTP_USER;
  const pass = process.env.SMTP_PASS;

  if (!host || !user || !pass) {
    return null;
  }

  return nodemailer.createTransport({
    host,
    port: Number(process.env.SMTP_PORT ?? 587),
    secure: false,
    auth: { user, pass }
  });
}

export async function sendEmail({
  to,
  subject,
  html,
  attachments = []
}: {
  to: string;
  subject: string;
  html: string;
  attachments?: { filename: string; path: string }[];
}) {
  const transporter = await getTransporter();
  if (!transporter) {
    return { delivered: false };
  }

  await transporter.sendMail({
    from: process.env.SMTP_FROM ?? "Akasha Production <noreply@akashaproduction.com>",
    to,
    subject,
    html,
    attachments
  });

  return { delivered: true };
}

export async function sendContactEmail(subject: string, html: string, attachments: { filename: string; path: string }[]) {
  return sendEmail({
    to: process.env.AKASHA_CONTACT_EMAIL ?? "contact@akashaproduction.com",
    subject,
    html,
    attachments
  });
}
