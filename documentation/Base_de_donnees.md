# Documentation de la Base de Données - Leboncoin Clone

## Schéma de la base de données

### Tables principales

#### 1. users
- Stocke les informations des utilisateurs
- Champs: id, email, mdp (mot de passe), nom, prenom, tel, adresse, date_inscription, sexe, date_naissance, ville, profession, photo, rôle (1=user, 2=admin)

#### 2. categories
- Catégories d'annonces
- Champs: id, nom

#### 3. annonces
- Annonces publiées
- Champs: id, titre, description, prix, user_id, categorie_id, date_creation, etat (Neuf/Occasion), ville, code_postal, click_count

#### 4. images
- Images associées aux annonces
- Champs: id, annonce_id, image_path

### Tables de relations

#### 1. favoris
- Annonces favorites des utilisateurs
- Champs: id, user_id, annonce_id, date_ajout

#### 2. messages
- Messages entre utilisateurs
- Champs: id, expediteur_id, destinataire_id, annonce_id, contenu, date_envoi

#### 3. annonce_visite
- Historique des vues d'annonces
- Champs: user_id, annonce_id, visite_date

## Relations

1. Une annonce appartient à un utilisateur (user_id → users.id)
2. Une annonce appartient à une catégorie (categorie_id → categories.id)
3. Une annonce peut avoir plusieurs images (annonce_id → annonces.id)
4. Un utilisateur peut avoir plusieurs favoris (user_id → users.id)
5. Un message a un expéditeur et un destinataire (expediteur_id/destinataire_id → users.id)
6. Une visite est liée à un utilisateur et une annonce (user_id → users.id, annonce_id → annonces.id)

## Diagramme Entité-Relation

```
[Users] 1---* [Annonces]
[Users] 1---* [Favoris]
[Users] 1---* [Messages] (as expediteur)
[Users] 1---* [Messages] (as destinataire)
[Users] 1---* [Annonce_visite]
[Categories] 1---* [Annonces]
[Annonces] 1---* [Images]
[Annonces] 1---* [Messages]
[Annonces] 1---* [Favoris]
[Annonces] 1---* [Annonce_visite]
```

## Contraintes d'intégrité

- Toutes les clés étrangères sont configurées avec ON DELETE CASCADE
- Les emails et numéros de téléphone sont uniques dans la table users
- Les noms de catégories sont uniques
