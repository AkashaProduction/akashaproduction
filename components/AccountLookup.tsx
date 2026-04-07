"use client";

import { useState } from "react";

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

export function AccountLookup() {
  const [email, setEmail] = useState("");
  const [orders, setOrders] = useState<OrderRecord[]>([]);
  const [message, setMessage] = useState("");

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const response = await fetch("/api/orders/lookup", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email })
    });
    const data = await response.json();
    setOrders(data.orders ?? []);
    setMessage(data.message ?? "");
  }

  return (
    <div className="form-card">
      <form className="form-grid" onSubmit={onSubmit}>
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
    </div>
  );
}
