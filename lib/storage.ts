import { mkdir, readFile, writeFile } from "node:fs/promises";
import path from "node:path";
import { randomUUID } from "node:crypto";
import type { TicketDepartment, TicketPriority, TicketStatus } from "@/lib/support";

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

export type TicketThreadEntry = {
  id: string;
  createdAt: string;
  authorType: "customer" | "admin";
  authorLabel: string;
  message: string;
};

export type StoredTicket = {
  id: string;
  createdAt: string;
  updatedAt: string;
  status: TicketStatus;
  department: TicketDepartment;
  priority: TicketPriority;
  subject: string;
  orderId?: string;
  customer: {
    email: string;
    name: string;
  };
  thread: TicketThreadEntry[];
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

async function appendRecord<T>(fileName: string, record: T) {
  const fullPath = await ensureFile(fileName);
  const raw = await readFile(fullPath, "utf8");
  const records = JSON.parse(raw) as T[];
  records.unshift(record);
  await writeFile(fullPath, JSON.stringify(records, null, 2), "utf8");
  return record;
}

async function readRecords<T>(fileName: string) {
  const fullPath = await ensureFile(fileName);
  const raw = await readFile(fullPath, "utf8");
  return JSON.parse(raw) as T[];
}

async function writeRecords<T>(fileName: string, records: T[]) {
  const fullPath = await ensureFile(fileName);
  await writeFile(fullPath, JSON.stringify(records, null, 2), "utf8");
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
  const records = await readRecords<StoredOrder>("orders.json");
  return records.filter((record) => {
    const candidate = record.payload.customer as { email?: string } | undefined;
    return candidate?.email?.toLowerCase() === email.toLowerCase();
  });
}

export async function saveTicket(payload: {
  customer: { email: string; name: string };
  department: TicketDepartment;
  priority: TicketPriority;
  subject: string;
  message: string;
  orderId?: string;
}) {
  const now = new Date().toISOString();
  return appendRecord<StoredTicket>("tickets.json", {
    id: randomUUID(),
    createdAt: now,
    updatedAt: now,
    status: "open",
    department: payload.department,
    priority: payload.priority,
    subject: payload.subject,
    orderId: payload.orderId,
    customer: payload.customer,
    thread: [
      {
        id: randomUUID(),
        createdAt: now,
        authorType: "customer",
        authorLabel: payload.customer.name,
        message: payload.message
      }
    ]
  });
}

export async function getTicketsByEmail(email: string) {
  const records = await readRecords<StoredTicket>("tickets.json");
  return records.filter((record) => record.customer.email.toLowerCase() === email.toLowerCase());
}

export async function getAllTickets() {
  return readRecords<StoredTicket>("tickets.json");
}

export async function addTicketReply(input: {
  ticketId: string;
  authorType: "customer" | "admin";
  authorLabel: string;
  message: string;
  customerEmail?: string;
}) {
  const records = await readRecords<StoredTicket>("tickets.json");
  const ticket = records.find((entry) => entry.id === input.ticketId);
  if (!ticket) {
    return null;
  }

  if (input.authorType === "customer" && ticket.customer.email.toLowerCase() !== input.customerEmail?.toLowerCase()) {
    return null;
  }

  ticket.thread.push({
    id: randomUUID(),
    createdAt: new Date().toISOString(),
    authorType: input.authorType,
    authorLabel: input.authorLabel,
    message: input.message
  });
  ticket.updatedAt = new Date().toISOString();
  ticket.status = input.authorType === "admin" ? "answered" : "open";

  await writeRecords("tickets.json", records);
  return ticket;
}

export async function updateTicket(input: {
  ticketId: string;
  status?: TicketStatus;
  priority?: TicketPriority;
}) {
  const records = await readRecords<StoredTicket>("tickets.json");
  const ticket = records.find((entry) => entry.id === input.ticketId);
  if (!ticket) {
    return null;
  }

  if (input.status) {
    ticket.status = input.status;
  }
  if (input.priority) {
    ticket.priority = input.priority;
  }
  ticket.updatedAt = new Date().toISOString();

  await writeRecords("tickets.json", records);
  return ticket;
}
