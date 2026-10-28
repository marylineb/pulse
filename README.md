# Pulse 💘

**Pulse** est une application web de rencontre développée en **Symfony 8 / PHP 8.4**.
L'idée : on se découvre d'abord par le pseudo, les centres d'intérêt et la conversation, et on ne révèle son identité que lorsque **les deux personnes l'ont accepté**.

> Projet personnel réalisé dans le cadre de mon Bachelor Développement Web (MyDigitalSchool Lyon).

---

## Fonctionnalités

- **Inscription et connexion** sécurisées (mots de passe hachés, protection CSRF, limitation des tentatives).
- **Profil** : pseudo, âge (18 ans minimum), bio, avatar, « vibe » et centres d'intérêt.
- **Crush anonyme** : on envoie un crush à un pseudo ; la personne reçoit seulement « Quelqu'un t'aime 💌 ».
- **Match mutuel** : quand deux crushs se croisent, un match est créé et une discussion privée se débloque.
- **Score de compatibilité** (service dédié) calculé à partir des centres d'intérêt communs, de la vibe, de l'écart d'âge et de la proximité des bios.
- **Chat privé** entre les deux personnes du match.
- **Révélation à double consentement** : l'un propose de révéler son identité, l'autre accepte ; le prénom n'apparaît qu'à ce moment-là.
- **Notifications** (crush reçu, match mutuel).
- **Mini-jeux** pour briser la glace (Questions Duo, Cartes Mystère, Tu préfères) — en cours de développement.

## Stack technique

| Côté | Technologies |
| --- | --- |
| Back-end | PHP 8.4, Symfony 8 (MVC), Doctrine ORM, Doctrine Migrations |
| Sécurité | Symfony Security (authentificateur personnalisé, remember me, login throttling), CSRF, Validator |
| Front-end | Twig, CSS responsive |
| Base de données | SQLite en développement (PostgreSQL possible via Docker Compose) |

## Modèle de données

`User` ─ 1:1 ─ `Profile`
`Crush` (expéditeur → cible, statut : en attente / mutuel)
`ChatRoom` (deux utilisateurs, état révélé ou non) ─ 1:n ─ `Message`
`RevealRequest` (demande de révélation et accord de chaque côté)
`Notification`, `Report` (signalement — modèle prêt, interface à venir)

## Sécurité

J'ai audité le projet avant de le publier. Les points en place :

- **Contrôle d'accès** : toutes les pages privées exigent une connexion (`#[IsGranted('ROLE_USER')]`), et le chat comme la révélation vérifient que l'utilisateur fait bien partie de la discussion (pas d'accès en changeant l'ID dans l'URL).
- **Double consentement réel** : la personne qui demande la révélation ne peut pas l'accepter à la place de l'autre.
- **CSRF** sur tous les formulaires : connexion, inscription, profil, crush, messages, révélation et déconnexion. Les actions qui modifient des données passent en POST.
- **Anti force brute** : 5 tentatives de connexion ratées maximum par quart d'heure.
- **Mots de passe** hachés automatiquement par Symfony, 12 caractères minimum.
- **Vie privée** : les adresses mail ne sont jamais affichées aux autres utilisateurs, seulement les pseudos.
- **Validation des données** : âge minimum 18 ans, longueurs maximales, avatar limité aux URL `https`, messages limités à 2 000 caractères.
- **Échappement Twig** sur tout le contenu saisi par les utilisateurs, requêtes Doctrine paramétrées.
- **Aucun secret dans le dépôt** : les vraies valeurs vont dans `.env.local` (ignoré par Git).

## Installation

Prérequis : PHP 8.4, Composer, et l'extension `pdo_sqlite`.

```bash
git clone https://github.com/marylineb/pulse.git
cd pulse
composer install
php bin/console doctrine:migrations:migrate
symfony serve   # ou : php -S 127.0.0.1:8000 -t public
```

Puis ouvrir http://127.0.0.1:8000, créer deux comptes et tester le parcours crush → match → chat → révélation.

Pour une mise en production, définir un `APP_SECRET` propre et la vraie `DATABASE_URL` dans `.env.local` (ou dans les variables d'environnement du serveur), avec `APP_ENV=prod`.

## Pistes d'évolution

- Interface de signalement et de blocage d'un profil
- Envoi de photos de profil avec contrôle du fichier
- Chat en temps réel (Mercure)
- Tests automatisés (PHPUnit) sur les règles d'accès

## Autrice

**Maryline Biloa** — développeuse web en formation
[marylinebiloa.com](https://marylinebiloa.com) · [GitHub](https://github.com/marylineb)
