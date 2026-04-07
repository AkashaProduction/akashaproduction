import { mkdir, readFile, writeFile } from "node:fs/promises";
import path from "node:path";
import { randomUUID } from "node:crypto";

const dataDir = path.join(process.cwd(), "data");

type StoredContact = {
  id: string;
  createdAt: string;
  payload: Record<string, unknown>;
};

type StoredOrder = {
  id: string;
  createdAt: string;
  status: "draft" | "pending-payment" | "paid" | "quote-requested";
  payload: Record<string, unknown>;
};

async function ensureFile(fileName: string) {
  await mkdir(dataDir, { recursive: true });
  const fullPath = path.join(dataDir, fileName);
  try {
    await readFile(fullPath, "utf8");
  } catch {
    await writeFile(fullPath, "[]", "utf8");
  }
  return fullPath;
}

async function appendRecord<T extends StoredContact | StoredOrder>(fileName: string, record: T) {
  const fullPath = await ensureFile(fileName);
  const raw = await readFile(fullPath, "utf8");
  const records = JSON.parse(raw) as T[];
  records.unshift(record);
  await writeFile(fullPath, JSON.stringify(records, null, 2), "utf8");
  return record;
}

export async function saveContact(payload: Record<string, unknown>) {
  return appendRecord("contacts.json", {
    id: randomUUID(),
    createdAt: new Date().toISOString(),
    payload
  });
}

export async function saveOrder(status: StoredOrder["status"], payload: Record<string, unknown>) {
  return appendRecord("orders.json", {
    id: randomUUID(),
    createdAt: new Date().toISOString(),
    status,
    payload
  });
}

export async function getOrdersByEmail(email: string) {
  const fullPath = await ensureFile("orders.json");
  const raw = await readFile(fullPath, "utf8");
  const records = JSON.parse(raw) as StoredOrder[];
  return records.filter((record) => {
    const candidate = record.payload.customer as { email?: string } | undefined;
    return candidate?.email?.toLowerCase() === email.toLowerCase();
  });
}
