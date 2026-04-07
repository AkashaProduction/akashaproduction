export type TicketDepartment = "commercial" | "technical";
export type TicketPriority = "normal" | "high" | "urgent";
export type TicketStatus = "open" | "in-progress" | "answered" | "closed";

export const supportTopics: Record<TicketDepartment, string[]> = {
  commercial: [
    "Demande de devis",
    "Question sur une commande",
    "Paiement ou facturation",
    "Pack, combo ou promotion",
    "Nom de domaine",
    "Hébergement",
    "Autre demande commerciale"
  ],
  technical: [
    "Incident site web",
    "Bug d'affichage",
    "Accès FTP / SSH / base de données",
    "Nom de domaine ou DNS",
    "Email ou délivrabilité",
    "Performance ou sécurité",
    "Autre demande technique"
  ]
};

export const ticketStatusLabels: Record<TicketStatus, string> = {
  open: "Ouvert",
  "in-progress": "En cours",
  answered: "Répondu",
  closed: "Clos"
};

export const ticketPriorityLabels: Record<TicketPriority, string> = {
  normal: "Normale",
  high: "Haute",
  urgent: "Urgente"
};

export function isTicketDepartment(value: string): value is TicketDepartment {
  return value === "commercial" || value === "technical";
}

export function isTicketPriority(value: string): value is TicketPriority {
  return value === "normal" || value === "high" || value === "urgent";
}

export function isTicketStatus(value: string): value is TicketStatus {
  return value === "open" || value === "in-progress" || value === "answered" || value === "closed";
}

export function isValidTicketSubject(department: TicketDepartment, subject: string) {
  return supportTopics[department].includes(subject) || subject.trim().length > 5;
}
