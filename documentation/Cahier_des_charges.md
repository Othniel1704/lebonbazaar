# Cahier des Charges - Leboncoin Clone

## 1. Introduction
Plateforme de petites annonces en ligne inspirée de Leboncoin, permettant aux utilisateurs de publier, rechercher et échanger autour d'annonces classées.

## 2. Objectifs
- Créer une plateforme simple d'utilisation pour les petites annonces
- Permettre aux utilisateurs de publier des annonces avec photos
- Faciliter la recherche et le contact entre utilisateurs
- Offrir des fonctionnalités de base gratuitement

## 3. Fonctionnalités principales

### 3.1 Gestion des utilisateurs
- Inscription/connexion sécurisée
- Profil utilisateur avec informations personnelles
- Système de rôles (utilisateur standard/administrateur)

### 3.2 Gestion des annonces
- Publication d'annonces avec:
  - Titre, description, prix
  - Catégorie, état (Neuf/Occasion)
  - Localisation
  - Photos (jusqu'à 5)
- Modification/suppression des annonces
- Statistiques de vues

### 3.3 Recherche
- Barre de recherche principale
- Filtres par:
  - Catégorie
  - Fourchette de prix
  - Localisation
- Affichage des résultats par pertinence/date

### 3.4 Interactions
- Système de favoris
- Messagerie interne entre utilisateurs
- Page de contact pour le support

## 4. Contraintes techniques

### 4.1 Environnement
- Serveur web Apache
- PHP 8.2+
- Base de données MariaDB/MySQL
- Compatibilité mobile (responsive design)

### 4.2 Performances
- Temps de réponse < 2s pour 95% des requêtes
- Support jusqu'à 1000 utilisateurs simultanés
- Optimisation des images uploadées

### 4.3 Sécurité
- Protection des données personnelles (RGPD)
- Hashage des mots de passe
- Protection contre les injections SQL/XSS
- Gestion des permissions

## 5. Livrables
- Application web fonctionnelle
- Documentation technique
- Documentation utilisateur
- Scripts d'installation/de déploiement

## 6. Planning (V1.0)
- Conception: 2 semaines
- Développement: 6 semaines
- Tests: 2 semaines
- Déploiement: 1 semaine
