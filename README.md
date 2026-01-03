# Helpdesk voor Ouderen

## Projectomschrijving
In dit project is een webapplicatie gebouwd in PHP waarmee ouderen eenvoudig hulp kunnen aanvragen bij computer- en internetproblemen.  
Gebruikers kunnen een account aanmaken, inloggen en hun problemen melden via een formulier.  
Helpers/beheerders kunnen deze meldingen bekijken, beheren, status aanpassen en via een chat-achtig systeem reageren met een oplossing.

Dit project is bedoeld om ouderen te ondersteunen bij digitale problemen en is een realistische toepassing van de technieken die ik de afgelopen weken heb geleerd.

---

## Doelgroep
- Ouderen met computer- en internetproblemen
- Helpers / beheerders die ondersteuning bieden

---

## Functionaliteiten

### Voor ouderen (gebruikers)
- Registreren van een nieuw account
- Inloggen en uitloggen
- Nieuwe melding/ticket aanmaken (titel + beschrijving)
- Overzicht van eigen meldingen bekijken
- Status volgen (open / in_behandeling / opgelost)
- Ticket detail bekijken
- Reageren via een chat-achtig berichtensysteem binnen een ticket

### Voor beheerders/helpers (admin)
- Overzicht van alle meldingen (van alle gebruikers)
- Ticket details bekijken
- Status van tickets aanpassen (open / in_behandeling / opgelost)
- Reageren in de chat (advies/oplossing geven)

---

## Vereiste technieken (bewijs)
- **Minimaal 2 database tabellen:** `users`, `tickets` (extra: `ticket_comments`)
- **JOIN-query’s:** gebruikt in o.a. `dashboard.php`, `admin_tickets.php`, `ticket_detail.php`
- **Authenticatie:** registreren/inloggen/uitloggen met sessies (`register.php`, `login.php`, `logout.php`, `includes/auth.php`)

---

## Gebruikte technieken
### Backend
- PHP
- Sessions (login + rollen)
- Formulierverwerking (POST/GET)
- Password hashing (`password_hash()` / `password_verify()`)
- PDO connectie via `includes/connection.php`

### Database
- MySQL (phpMyAdmin)
- Tabellen:
  - `users` (accounts + role)
  - `tickets` (meldingen)
  - `ticket_comments` (chat/reacties)

### Frontend
- HTML & CSS
- Bootstrap (CDN)
- Eigen `style.css`
- (optioneel) `script.js`

---

## Database structuur

### Tabel: users
- id
- name
- email
- password
- role (user/admin)
- created_at

### Tabel: tickets
- id
- user_id (FK naar users.id)
- title
- description
- status
- created_at

### Tabel: ticket_comments
- id
- ticket_id (FK naar tickets.id)
- user_id (FK naar users.id)
- message
- created_at

---

## Voorbeeld JOIN-query’s

Tickets met gebruiker:
```sql
SELECT tickets.*, users.name
FROM tickets
JOIN users ON tickets.user_id = users.id;
