# Documentation Développeur

## Architecture

Le projet repose sur `Next.js App Router` avec un segment dynamique `[locale]` pour servir trois langues via des fichiers JSON.

## Localisation

- Les traductions sont stockées dans `messages/fr.json`, `messages/en.json`, `messages/es.json`.
- `lib/i18n.ts` centralise l’accès aux dictionnaires.
- Les nouvelles pages doivent consommer les messages du dictionnaire actif.

## Métier

- `lib/catalog.ts` contient:
  - la définition des produits
  - la liste des domaines parents
  - les questions du devis
  - la logique de calcul des packs et promotions
- `computeOrderSummary` applique automatiquement les promotions connues.

## Persistance locale

- `data/contacts.json` est créé à la première soumission du formulaire de contact.
- `data/orders.json` est créé à la première commande ou demande de devis.
- `data/tickets.json` est créé à la première création de ticket support.
- `lib/storage.ts` gère la création des fichiers et les lectures/écritures.

## Ticketing support

- `lib/support.ts` définit les catégories, priorités, statuts et sujets cohérents.
- Le panel client utilise `app/api/tickets` et `app/api/tickets/reply`.
- Le panel admin utilise `app/api/admin/login`, `app/api/admin/tickets` et `app/api/admin/tickets/[id]`.
- Le système actuel reste cohérent avec le socle existant:
  - identification client légère par email dans `Mon compte`
  - authentification admin via cookie HTTP-only signé
  - persistance JSON locale

### Variables d’environnement admin

- `ADMIN_EMAIL`
- `ADMIN_PASSWORD`
- `ADMIN_SESSION_SECRET`

## Contact et uploads

- `app/api/contact/route.ts` reçoit un `FormData`.
- Les fichiers sont copiés dans `public/uploads`.
- Si `SMTP_*` est configuré, un email est envoyé à `contact@akashaproduction.com`.

## Stripe

- Le projet suit une approche Stripe compatible avec la recommandation Checkout:
  - paiement unique: `Checkout Sessions` en mode `payment`
  - paiement en 3 fois: `Checkout Sessions` en mode `subscription`
- `app/api/stripe/webhook/route.ts` coupe la souscription après la troisième facture payée.

### Variables d’environnement Stripe

- `STRIPE_SECRET_KEY`
- `STRIPE_WEBHOOK_SECRET`
- `STRIPE_PRICE_SHOWCASE`
- `STRIPE_PRICE_COMPLEX`
- `STRIPE_PRICE_DOMAIN`
- `STRIPE_PRICE_HOSTING_SHARED_MONTHLY`
- `STRIPE_PRICE_HOSTING_SHARED_YEARLY`
- `STRIPE_PRICE_HOSTING_VPS_MONTHLY`
- `STRIPE_PRICE_INSTALLMENT_SHOWCASE`
- `STRIPE_PRICE_INSTALLMENT_COMPLEX`

### Mise en œuvre recommandée pour le 3x

- Créer des prix Stripe récurrents avec un intervalle de `4 mois`.
- Faire pointer le checkout fractionné vers ces prix récurrents.
- Laisser le webhook annuler la souscription après `3` factures payées.

## Limites actuelles

- L’espace compte repose sur une recherche par email et non sur une authentification complète.
- Les prix Stripe sont encore partiellement construits en `price_data` dynamique pour les combinaisons.
- Une vraie base SQL pourra remplacer la persistance JSON plus tard.
