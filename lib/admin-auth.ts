import { createHmac, timingSafeEqual } from "node:crypto";
import { cookies } from "next/headers";

const cookieName = "akasha_admin_session";

function getAdminConfig() {
  return {
    email: process.env.ADMIN_EMAIL ?? "contact@akashaproduction.com",
    password: process.env.ADMIN_PASSWORD ?? "",
    secret: process.env.ADMIN_SESSION_SECRET ?? "akasha-dev-secret"
  };
}

function encode(value: string) {
  return Buffer.from(value, "utf8").toString("base64url");
}

function decode(value: string) {
  return Buffer.from(value, "base64url").toString("utf8");
}

function sign(payload: string, secret: string) {
  return createHmac("sha256", secret).update(payload).digest("hex");
}

export function validateAdminCredentials(email: string, password: string) {
  const config = getAdminConfig();
  return email === config.email && password === config.password && Boolean(config.password);
}

export function createAdminSessionToken(email: string) {
  const { secret } = getAdminConfig();
  const expiresAt = Date.now() + 1000 * 60 * 60 * 12;
  const payload = `${email}:${expiresAt}`;
  const signature = sign(payload, secret);
  return `${encode(payload)}.${signature}`;
}

export function verifyAdminSessionToken(token: string | undefined) {
  if (!token) {
    return null;
  }

  const { secret } = getAdminConfig();
  const [encodedPayload, providedSignature] = token.split(".");
  if (!encodedPayload || !providedSignature) {
    return null;
  }

  const payload = decode(encodedPayload);
  const expectedSignature = sign(payload, secret);
  const isValid = timingSafeEqual(Buffer.from(providedSignature), Buffer.from(expectedSignature));
  if (!isValid) {
    return null;
  }

  const [email, expiresAt] = payload.split(":");
  if (!email || !expiresAt || Number(expiresAt) < Date.now()) {
    return null;
  }

  return email;
}

export async function getAdminSession() {
  const store = await cookies();
  return verifyAdminSessionToken(store.get(cookieName)?.value);
}

export async function setAdminSession(token: string) {
  const store = await cookies();
  store.set(cookieName, token, {
    httpOnly: true,
    sameSite: "lax",
    path: "/",
    secure: true,
    maxAge: 60 * 60 * 12
  });
}

export async function clearAdminSession() {
  const store = await cookies();
  store.delete(cookieName);
}
