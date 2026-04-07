"use client";

import { useState } from "react";
import { supportTopics } from "@/lib/support";

type OrderRecord = {
  id: string;
  createdAt: string;
  status: string;
  payload: {
    customer?: {
      firstName?: string;
      lastName?: string;
    };
    selections?: {
      creation?: string;
      hosting?: string;
      splitPayment?: boolean;
      includeDomainAddon?: boolean;
    };
    summary?: {
      subtotal?: number;
    };
    checkoutUrl?: string | null;
  };
};

type TicketRecord = {
  id: string;
  createdAt: string;
  updatedAt: string;
  status: string;
  department: string;
  priority: string;
  subject: string;
  orderId?: string;
  thread: {
    id: string;
    createdAt: string;
    authorType: string;
    authorLabel: string;
    message: string;
  }[];
};

export function AccountLookup() {
  const [email, setEmail] = useState("");
  const [name, setName] = useState("");
  const [orders, setOrders] = useState<OrderRecord[]>([]);
  const [tickets, setTickets] = useState<TicketRecord[]>([]);
  const [message, setMessage] = useState("");
  const [department, setDepartment] = useState<"commercial" | "technical">("commercial");
  const [subject, setSubject] = useState(supportTopics.commercial[0]);

  async function loadDashboard(emailAddress: string) {
    const response = await fetch("/api/orders/lookup", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: emailAddress })
    });
    const data = await response.json();
    setOrders(data.orders ?? []);
    setTickets(data.tickets ?? []);
    setMessage(data.message ?? "");
    return response;
  }

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    await loadDashboard(email);
  }

  async function createTicket(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const form = event.currentTarget;
    const formData = new FormData(form);
    const response = await fetch("/api/tickets", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        email,
        name,
        department,
        subject,
        priority: formData.get("priority"),
        orderId: formData.get("orderId"),
        message: formData.get("ticketMessage")
      })
    });
    const data = await response.json();
    setMessage(data.message ?? "");
    if (response.ok) {
      form.reset();
      setSubject(supportTopics[department][0]);
      await loadDashboard(email);
    }
  }

  async function replyToTicket(ticketId: string, text: string) {
    const response = await fetch("/api/tickets/reply", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        ticketId,
        email,
        name,
        message: text
      })
    });
    const data = await response.json();
    setMessage(data.message ?? "");
    if (response.ok) {
      await loadDashboard(email);
    }
  }

  return (
    <div className="form-card">
      <form className="form-grid" onSubmit={onSubmit}>
        <div className="field field--full">
          <label htmlFor="lookup-name">Nom du client</label>
          <input id="lookup-name" value={name} onChange={(event) => setName(event.target.value)} required />
        </div>
        <div className="field field--full">
          <label htmlFor="lookup-email">Email client</label>
          <input id="lookup-email" type="email" value={email} onChange={(event) => setEmail(event.target.value)} required />
        </div>
        <div className="field field--full">
          <button className="btn btn--primary" type="submit">
            Ouvrir mon compte
          </button>
        </div>
      </form>

      {message ? <p className="notice">{message}</p> : null}

      <div className="grid-2">
        {orders.map((order) => (
          <article className="panel" key={order.id}>
            <p className="kicker">{order.status}</p>
            <h3>{order.payload.selections?.creation ?? "Création"} / {order.payload.selections?.hosting ?? "Hébergement"}</h3>
            <p className="muted">
              Référence {order.id.slice(0, 8)} · {new Date(order.createdAt).toLocaleDateString("fr-FR")}
            </p>
            <ul>
              <li>Total: {order.payload.summary?.subtotal ?? 0} €</li>
              <li>3x sans frais: {order.payload.selections?.splitPayment ? "Oui" : "Non"}</li>
              <li>Domaine personnalisé: {order.payload.selections?.includeDomainAddon ? "Oui" : "Non"}</li>
            </ul>
            <p className="warning">
              Panel avancé prévu: gestion FTP, SSH, phpMyAdmin, PostgreSQL et supervision d’hébergement.
            </p>
          </article>
        ))}
      </div>

      <div className="panel">
        <p className="kicker">Support client</p>
        <h3>Créer un ticket commercial ou technique</h3>
        <form className="form-grid" onSubmit={createTicket}>
          <div className="field">
            <label htmlFor="department">Service</label>
            <select
              id="department"
              value={department}
              onChange={(event) => {
                const nextDepartment = event.target.value as "commercial" | "technical";
                setDepartment(nextDepartment);
                setSubject(supportTopics[nextDepartment][0]);
              }}
            >
              <option value="commercial">Service commercial</option>
              <option value="technical">Service technique</option>
            </select>
          </div>
          <div className="field">
            <label htmlFor="priority">Priorité</label>
            <select id="priority" name="priority" defaultValue="normal">
              <option value="normal">Normale</option>
              <option value="high">Haute</option>
              <option value="urgent">Urgente</option>
            </select>
          </div>
          <div className="field field--full">
            <label htmlFor="subject">Sujet</label>
            <select id="subject" name="subject" value={subject} onChange={(event) => setSubject(event.target.value)}>
              {supportTopics[department].map((topic) => (
                <option key={topic} value={topic}>
                  {topic}
                </option>
              ))}
            </select>
          </div>
          <div className="field field--full">
            <label htmlFor="orderId">Référence de commande liée</label>
            <select id="orderId" name="orderId" defaultValue="">
              <option value="">Aucune</option>
              {orders.map((order) => (
                <option key={order.id} value={order.id}>
                  {order.id.slice(0, 8)} - {order.payload.selections?.creation ?? "création"}
                </option>
              ))}
            </select>
          </div>
          <div className="field field--full">
            <label htmlFor="ticketMessage">Message</label>
            <textarea id="ticketMessage" name="ticketMessage" required />
          </div>
          <div className="field field--full">
            <button className="btn btn--primary" type="submit">Créer le ticket</button>
          </div>
        </form>
      </div>

      <div className="grid-2">
        {tickets.map((ticket) => (
          <article className="panel" key={ticket.id}>
            <p className="kicker">{ticket.department} · {ticket.status}</p>
            <h3>{ticket.subject}</h3>
            <p className="muted">
              Ticket {ticket.id.slice(0, 8)} · {new Date(ticket.createdAt).toLocaleDateString("fr-FR")}
            </p>
            <ul>
              <li>Priorité: {ticket.priority}</li>
              <li>Commande liée: {ticket.orderId ? ticket.orderId.slice(0, 8) : "Aucune"}</li>
            </ul>
            <div className="ticket-thread">
              {ticket.thread.map((entry) => (
                <div className="ticket-entry" key={entry.id}>
                  <strong>{entry.authorLabel}</strong>
                  <p className="muted">{entry.message}</p>
                </div>
              ))}
            </div>
            <form
              className="form-grid"
              onSubmit={async (event) => {
                event.preventDefault();
                const textarea = event.currentTarget.elements.namedItem("reply") as HTMLTextAreaElement;
                await replyToTicket(ticket.id, textarea.value);
                event.currentTarget.reset();
              }}
            >
              <div className="field field--full">
                <label htmlFor={`reply-${ticket.id}`}>Ajouter un message</label>
                <textarea id={`reply-${ticket.id}`} name="reply" required />
              </div>
              <div className="field field--full">
                <button className="btn btn--secondary" type="submit">Envoyer un complément</button>
              </div>
            </form>
          </article>
        ))}
      </div>
    </div>
  );
}
