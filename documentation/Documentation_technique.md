# Documentation Technique - Leboncoin Clone

## Architecture du système

### Stack technique
- **Frontend**: HTML, CSS, JavaScript, PHP
- **Backend**: PHP
- **Base de données**: MySQL/MariaDB
- **Serveur**: Apache (via XAMPP)

### Structure des fichiers
```
/leboncoin1
├── php/               # Scripts PHP principaux
│   ├── annonce.php    # Gestion des annonces
│   ├── contact.php    # Page de contact
│   ├── favoris.php    # Gestion des favoris
│   └── chat.php       # Système de messagerie
├── images/            # Stockage des images uploadées
└── documentation/     # Documentation du projet
```

## Configuration requise

### Serveur
- XAMPP (Apache, MySQL, PHP)
- PHP 8.2+
- MariaDB 10.4+

### Configuration PHP
Extensions requises:
- PDO_MYSQL
- GD (pour le traitement d'images)
- mbstring

## Fonctionnalités implémentées

### 1. Gestion des utilisateurs
- Inscription/connexion
- Profil utilisateur
- Rôles (utilisateur/admin)

### 2. Gestion des annonces
- Création/modification/suppression
- Recherche/filtrage
- Upload d'images
- Statistiques de vues

### 3. Système de messagerie
- Envoi de messages entre utilisateurs
- Liés aux annonces

### 4. Fonctionnalités supplémentaires
- Favoris
- Historique de vues
- Contact

## API (Endpoints PHP)

### Annonces
- `annonce.php?action=create`
- `annonce.php?action=view&id=[ID]`
- `annonce.php?action=delete&id=[ID]`

### Utilisateurs
- `user.php?action=login`
- `user.php?action=register`
- `user.php?action=profile&id=[ID]`

## Sécurité
- Protection contre les injections SQL (PDO)
- Hashage des mots de passe (password_hash)
- Validation des inputs
- Protection contre XSS (htmlspecialchars)

## Dépendances
- Aucune librairie externe requise
- Fonctionne avec les extensions PHP standard
