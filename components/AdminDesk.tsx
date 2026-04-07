"use client";

import { useEffect, useState } from "react";

type Ticket = {
  id: string;
  createdAt: string;
  updatedAt: string;
  status: string;
  department: string;
  priority: string;
  subject: string;
  orderId?: string;
  customer: {
    email: string;
    name: string;
  };
  thread: {
    id: string;
    createdAt: string;
    authorType: string;
    authorLabel: string;
    message: string;
  }[];
};

export function AdminDesk() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [message, setMessage] = useState("");
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  async function loadTickets() {
    const response = await fetch("/api/admin/tickets");
    if (response.status === 401) {
      setIsLoggedIn(false);
      return;
    }
    const data = await response.json();
    setTickets(data.tickets ?? []);
    setIsLoggedIn(true);
  }

  useEffect(() => {
    loadTickets();
  }, []);

  async function onLogin(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const response = await fetch("/api/admin/login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email, password })
    });
    const data = await response.json();
    setMessage(data.message ?? "");
    if (response.ok) {
      setPassword("");
      await loadTickets();
    }
  }

  async function updateTicket(ticketId: string, formData: FormData) {
    const response = await fetch(`/api/admin/tickets/${ticketId}`, {
      method: "PATCH",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        status: formData.get("status"),
        priority: formData.get("priority"),
        replyMessage: formData.get("replyMessage")
      })
    });
    const data = await response.json();
    setMessage(data.message ?? "");
    await loadTickets();
  }

  if (!isLoggedIn) {
    return (
      <div className="form-card">
        <form className="form-grid" onSubmit={onLogin}>
          <div className="field field--full">
            <label htmlFor="admin-email">Email administrateur</label>
            <input id="admin-email" type="email" value={email} onChange={(event) => setEmail(event.target.value)} required />
          </div>
          <div className="field field--full">
            <label htmlFor="admin-password">Mot de passe</label>
            <input id="admin-password" type="password" value={password} onChange={(event) => setPassword(event.target.value)} required />
          </div>
          <div className="field field--full">
            <button className="btn btn--primary" type="submit">Ouvrir le panel admin</button>
          </div>
        </form>
        {message ? <p className="warning">{message}</p> : null}
      </div>
    );
  }

  return (
    <div className="form-card">
      {message ? <p className="notice">{message}</p> : null}
      <div className="grid-2">
        {tickets.map((ticket) => (
          <article className="panel" key={ticket.id}>
            <p className="kicker">{ticket.department} · {ticket.status}</p>
            <h3>{ticket.subject}</h3>
            <p className="muted">
              {ticket.customer.name} · {ticket.customer.email} · {new Date(ticket.createdAt).toLocaleString("fr-FR")}
            </p>
            <ul>
              <li>Priorité: {ticket.priority}</li>
              <li>Commande liée: {ticket.orderId ?? "Aucune"}</li>
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
                await updateTicket(ticket.id, new FormData(event.currentTarget));
                event.currentTarget.reset();
              }}
            >
              <div className="field">
                <label htmlFor={`status-${ticket.id}`}>Statut</label>
                <select id={`status-${ticket.id}`} name="status" defaultValue={ticket.status}>
                  <option value="open">Ouvert</option>
                  <option value="in-progress">En cours</option>
                  <option value="answered">Répondu</option>
                  <option value="closed">Clos</option>
                </select>
              </div>
              <div className="field">
                <label htmlFor={`priority-${ticket.id}`}>Priorité</label>
                <select id={`priority-${ticket.id}`} name="priority" defaultValue={ticket.priority}>
                  <option value="normal">Normale</option>
                  <option value="high">Haute</option>
                  <option value="urgent">Urgente</option>
                </select>
              </div>
              <div className="field field--full">
                <label htmlFor={`reply-${ticket.id}`}>Réponse admin</label>
                <textarea id={`reply-${ticket.id}`} name="replyMessage" />
              </div>
              <div className="field field--full">
                <button className="btn btn--secondary" type="submit">Mettre à jour le ticket</button>
              </div>
            </form>
          </article>
        ))}
      </div>
    </div>
  );
}
