# Réservation de Salles de Réunion

Application web permettant aux utilisateurs de réserver des salles de réunion, avec gestion automatique des conflits d'horaires.

## Fonctionnalités
- Inscription et connexion sécurisée des utilisateurs (mots de passe hashés)
- Réservation d'une salle parmi 3 disponibles (Salle A, B, C)
- Détection automatique des conflits horaires (impossible de réserver une salle déjà occupée sur un créneau)
- Affichage de toutes les réservations programmées (salle, date, heure, personne)
- Annulation d'une réservation par son créateur

## Technologies utilisées
- PHP (PDO pour la connexion à la base de données)
- MySQL
- HTML / CSS / JavaScript

## Structure de la base de données
- `users` : comptes utilisateurs
- `salles` : liste des salles disponibles
- `reunions` : réservations (liées à une salle et à un utilisateur)

## Installation
1. Cloner le dépôt dans le dossier `htdocs` de XAMPP
2. Démarrer Apache et MySQL depuis XAMPP
3. Créer la base de données `reservation_salles` dans phpMyAdmin et importer le script SQL
4. Configurer `config.php` avec vos identifiants MySQL
5. Accéder au projet via `localhost/task-manager-php-/register.php`

## Auteur
Khadija Abdallaoui