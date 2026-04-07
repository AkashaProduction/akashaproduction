# Akasha Production

Site vitrine multilingue pour présenter l’écosystème Akasha Production, commercialiser les offres de création et d’hébergement, recueillir les demandes de contact, et préparer un espace client.

## Stack

- Next.js App Router
- TypeScript
- Traductions JSON (`fr`, `en`, `es`)
- Persistance locale JSON pour contacts et commandes
- Intégration Stripe préparée
- Envoi email SMTP optionnel

## Démarrage

1. Copier `.env.example` vers `.env.local`
2. Installer les dépendances avec `npm install`
3. Lancer `npm run dev`

## Structure

- `app/[locale]` : pages publiques localisées
- `app/api` : endpoints contact, commandes, checkout, webhook Stripe
- `components` : composants UI
- `lib` : catalogue métier, i18n, stockage, Stripe, email
- `messages` : traductions JSON
- `docs` : documentation utilisateur et développeur
