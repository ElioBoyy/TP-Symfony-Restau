# TP-Restaurant

## Description

Ce projet est un site web de création et de réservation de restaurants. Il permet aux utilisateurs de créer des profils utilisateurs et de restaurants et de réserver des tables en ligne.

Ce projet a été fait en 48h.

Il manque quelques fonctionnalités.

## Fonctionnalités

- Création de profils utilisateur et de restaurants
- Réservation de tables en ligne
- Gestion des utilisateurs
- Consultation des tables

## Prérequis

- PHP >= 7.4
- Composer
- Symfony CLI
- MySQL

## Installation

1. Clonez le dépôt :
   ```bash
   git clone https://github.com/ElioBoyy/TP-Symfony-Restau.git
   ```
2. Installez les dépendances :
   ```bash
   cd TP-Restaurant
   composer install
   ```
3. Configurez la base de données dans le fichier `.env` :
   ```env
   inclus
   ```
4. Créez la base de données et exécutez les migrations :
   ```bash
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```
   Créez les fixtures
   ```bash
   php bin/console h:f:l
   ```
5. Démarrez le serveur Symfony :
   ```bash
   symfony server:start
   ```

## Utilisation

- Accédez à l'application via `http://localhost:8000`
