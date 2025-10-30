# LebonBazaar

Description
-----------
LebonBazaar est une application de petites annonces (type "leboncoin") fournie avec une base de données MySQL pré-remplie. Le dépôt contient le schéma et des données d'exemple dans le fichier SQL `leboncoindb .sql` ainsi que des styles CSS (`css/style.css`, `css/message.css`).

Technologies
-----------
- Base de données : MariaDB / MySQL
- Front / styles : CSS
- Backend : (non fourni dans le dump — attendez-vous à du PHP, Node.js ou autre selon le reste du dépôt)

Prérequis
--------
- MySQL ou MariaDB (ex. via __XAMPP__, __WAMP__, __MAMP__ ou installation native)
- PHP 8+ si le backend est en PHP
- Accès à phpMyAdmin ou client MySQL en ligne de commande

Installation rapide
------------------
1. Cloner le dépôt :
   - Exemple : `git clone https://github.com/Othniel1704/lebonbazaar.git`

2. Placer le fichier SQL dans un environnement MySQL accessible (ou renommer le fichier pour enlever l'espace) :
   - Fichier présent : `leboncoindb .sql`
   - Conseil : renommer en `leboncoindb.sql` pour simplicité.

3. Importer la base de données :
   - Avec phpMyAdmin : importer le fichier `leboncoindb .sql`.
   - En ligne de commande :
     - Si vous laissez le nom avec espace :  
       `mysql -u root -p leboncoindb < "leboncoindb .sql"`
     - Si vous avez renommé :  
       `mysql -u root -p leboncoindb < leboncoindb.sql`
   - Ou créer la base puis exécuter le script :
     ```bash
     mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS leboncoindb CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
     mysql -u root -p leboncoindb < leboncoindb.sql
     ```

Configuration
-------------
- Créer un fichier de configuration ou un __.env__ (selon le backend) avec les paramètres DB :
  - `DB_HOST=127.0.0.1`
  - `DB_PORT=3306`
  - `DB_DATABASE=leboncoindb`
  - `DB_USERNAME=root`
  - `DB_PASSWORD=mot_de_passe`
- Vérifier le chemin des images : le SQL référence des images dans `../image/...`. Assurez-vous d'avoir ce dossier ou d'ajuster les chemins.

Résumé du schéma de la base de données
-------------------------------------
Tables principales (extrait du fichier SQL) :
- `users` : gestion des utilisateurs (`id`, `email`, `mdp`, `nom`, `prenom`, `tel`, `photo`, `rôle`, ...)
- `annonces` : annonces publiées (`id`, `titre`, `description`, `prix`, `user_id`, `categorie_id`, `etat`, `ville`, `click_count`, ...)
- `categories` : catégories d'annonces (`id`, `nom`)
- `images` : images des annonces (`id`, `annonce_id`, `image_path`) — valeur par défaut `default_a.jpg`
- `favoris` : favoris des utilisateurs (`id`, `user_id`, `annonce_id`, `date_ajout`)
- `messages` : messagerie interne (`id`, `expediteur_id`, `destinataire_id`, `annonce_id`, `contenu`, `date_envoi`)
- `annonce_visite` : historique des visites (`user_id`, `annonce_id`, `visite_date`)

Notes utiles extraites :
- Les contraintes FK relient `annonces` ↔ `users`, `annonces` ↔ `categories`, etc.
- Certains comptes d'exemple existent (IDs 11, 22, 23, 24 — 24 est `admin`).

Exemples utiles
---------------
- Créer un utilisateur (MySQL) :
