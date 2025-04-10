<?php
session_start();
require 'config.php';

// Vérifier la connexion
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

// Récupérer les infos utilisateur
$user_id = $_SESSION['id'];
$user = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user->bind_param("i", $user_id);
$user->execute();
$user = $user->get_result()->fetch_assoc();

// Mise à jour du profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Nettoyer les données
        $data = [
            'prenom' => htmlspecialchars($_POST['prenom']),
            'nom' => htmlspecialchars($_POST['nom']),
            'sexe' => in_array($_POST['sexe'], ['Masculin', 'Féminin', 'Autre']) ? $_POST['sexe'] : 'Autre',
            'date_naissance' => $_POST['date_naissance'],
            'adresse' => htmlspecialchars($_POST['adresse']),
            'ville' => htmlspecialchars($_POST['ville']),
            'tel' => preg_replace('/[^0-9]/', '', $_POST['tel']),
            'profession' => htmlspecialchars($_POST['profession'])
        ];

        // Gestion de la photo
        if (!empty($_FILES['photo']['name'])) {
            $photo = $_FILES['photo'];
            $ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
            $filename = 'user_'.$user_id.'_'.time().'.'.$ext;
            $target = "../image/".$filename;
            
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']) && $photo['size'] < 2000000) {
                move_uploaded_file($photo['tmp_name'], $target);
                $data['photo'] = $filename;
            }
        }

        // Mettre à jour la BDD
        $sql = "UPDATE users SET 
                prenom = ?, 
                nom = ?, 
                sexe = ?, 
                date_naissance = ?, 
                adresse = ?, 
                ville = ?, 
                tel = ?, 
                profession = ?, 
                photo = COALESCE(?, photo)
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $photoParam = $data['photo'] ?? null; // Ensure photo is set to null if not uploaded
        $stmt->bind_param("sssssssssi", 
            $data['prenom'],
            $data['nom'],
            $data['sexe'],
            $data['date_naissance'],
            $data['adresse'],
            $data['ville'],
            $data['tel'],
            $data['profession'],
            $photoParam,
            $user_id
        );

        // After successful update
        if ($stmt->execute()) {
            $_SESSION['message'] = "Profil mis à jour avec succès!";
            header("Location: profil.php");
            exit();
        }
    }

    // Handle delete_annonce
    if (isset($_POST["delete_annonce"])) {
        // Supprimer les images associées à l'annonce
        $annonce_id = $_POST["annonce_id"];
        
        // Récupérer les chemins des images
        $stmt = $conn->prepare("SELECT image_path FROM images WHERE annonce_id = ?");
        $stmt->bind_param("i", $annonce_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Supprimer les fichiers d'image du dossier
        while ($row = $result->fetch_assoc()) {
            $image_path = '../image/' . $row['image_path'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        // Supprimer les images de la base de données
        $stmt = $conn->prepare("DELETE FROM images WHERE annonce_id = ?");
        $stmt->bind_param("i", $annonce_id);
        $stmt->execute();

        // Supprimer l'annonce
        $stmt = $conn->prepare("DELETE FROM annonces WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $annonce_id, $user_id);
        $stmt->execute();

        header("Location: profil.php");
        exit();
    }

    // Handle delete_favoris
    if (isset($_POST["delete_favoris"])) {
        // Supprimer les favoris
        $favori_id = $_POST["favori_id"];
        $stmt = $conn->prepare("DELETE FROM favoris WHERE annonce_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $favori_id, $user_id);
        $stmt->execute();

        header("Location: profil.php");
        exit();
    }
}

// Display success message if exists
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
    unset($_SESSION['message']);
}

// Récupérer les annonces de l'utilisateur
$annonces = $conn->prepare("
    SELECT a.*, c.nom as categorie 
    FROM annonces a
    LEFT JOIN categories c ON a.categorie_id = c.id
    WHERE a.user_id = ?
");
$annonces->bind_param("i", $user_id);
$annonces->execute();
$annonces = $annonces->get_result();

// Récupérer les favoris
$favoris = $conn->prepare("
    SELECT a.*, f.date_ajout 
    FROM favoris f
    JOIN annonces a ON f.annonce_id = a.id
    WHERE f.user_id = ?
");
$favoris->bind_param("i", $user_id);
$favoris->execute();
$favoris = $favoris->get_result();

// Récupérer les messages
$messages = $conn->prepare("
    SELECT m.*, u.prenom as expediteur 
    FROM messages m
    JOIN users u ON m.expediteur_id = u.id
    JOIN annonces a ON m.annonce_id = a.id
    WHERE m.destinataire_id = ?
    AND m.date_envoi = (
        SELECT MAX(date_envoi)
        FROM messages
        WHERE destinataire_id = ?
        AND expediteur_id = m.expediteur_id
        AND annonce_id = m.annonce_id
    )
    ORDER BY m.date_envoi DESC
    LIMIT 5
");
$messages->bind_param("ii", $user_id, $user_id);
$messages->execute();
$messages = $messages->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Le Bon Coin</title>
    <link rel="stylesheet" href="../css/styleprofil.css">
  
</head>
<body>
    <?php require 'navbar.php'; ?>

    <div class="profile-container">
        <!-- Section Profil -->
        <section class="section">
            <h2>Mes Informations</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="flex">
                    <div class="photo-section">
                        <img src="../image/<?= $user['photo'] ?? 'default.png' ?>" class="photo-profil" alt="Photo de profil"><br>
                        <input type="file" name="photo" accept="image/*">
                    </div>
                    
                    <div class="form-group">
                        Prénom:
                        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required><br>
                        
                        Nom:
                        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required><br>
                        Email :
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled><br>
                        Numéro de téléphone:
                        <input type="text" id="tel" name="tel" value="<?= htmlspecialchars($user['tel']); ?>" ><br>
               
                        
                        Sexe:
                        <select name="sexe">
                            <option value="Masculin" <?= $user['sexe'] == 'Masculin' ? 'selected' : '' ?>>Masculin</option>
                            <option value="Féminin" <?= $user['sexe'] == 'Féminin' ? 'selected' : '' ?>>Féminin</option>
                            <option value="Autre" <?= $user['sexe'] == 'Autre' ? 'selected' : '' ?>>Autre</option>
                        </select> <br>
                        Date de naissance:
                      <input type="date" id="date_naissance" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']); ?>" ><br>
                        
                        Adresse:
                        <input type="text" id="adresse" name="adresse" value="<?= htmlspecialchars($user['adresse']); ?>" ><br>
                        Ville:
                        <input type="text" id="ville" name="ville" value="<?= htmlspecialchars( $user['ville']); ?>" >

                        Numéro de téléphone:
                        <input type="text" id="tel" name="tel" value="<?= htmlspecialchars($user['tel']); ?>" ><br>
                        
                        Profession:
                        <input type="text" id="profession" name="profession" value="<?= htmlspecialchars($user['profession']); ?>" ><br>

                    
                    </div>
                </div>

                <!-- Autres champs -->
                <button type="submit" name="update_profile" class="btn btn-primary">Mettre à jour</button>
            </form>
        </section>

        <!-- Mes Annonces -->
        <section class="section" >
            <h2>Mes Annonces (<?= $annonces->num_rows ?>)</h2>
            <a href="ajout_annonce.php" class="btn btn-success">+ Nouvelle annonce</a>
            
            <div class="grid">
                <?php while($annonce = $annonces->fetch_assoc()): ?>
          
                <div class="annonce-card">
                <a href="annonce.php?id=<?= $annonce['id'] ?>" style="text-decoration: none; color: black;">
                    <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                    <p>Catégorie: <?= $annonce['categorie'] ?></p>
                    <p>Prix: <?= number_format($annonce['prix'], 0, ',', ' ') ?> €</p>
                    <div class="actions">
                        <a href="ajout_annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-primary">Modifier</a>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">
                            <button type="submit" name="delete_annonce" class="btn btn-danger" 
                                    onclick="return confirm('Supprimer cette annonce ?')">Supprimer</button>
                        </form>
                    </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Favoris -->
        <section class="section" id="favoris"><br><br><br>
            <h2>Mes Favoris</h2>
            <div class="grid">
                <?php while($favori = $favoris->fetch_assoc()): ?>
                <div class="annonce-card">
                    <h3><?= htmlspecialchars($favori['titre']) ?></h3>
                    <p>Ajouté le <?= date('d/m/Y', strtotime($favori['date_ajout'])) ?></p>
                    <a href="annonce.php?id=<?= $favori['id'] ?>" class="btn btn-primary">Voir</a>
                    <form method="POST" style="display:inline;">
                            <input type="hidden" name="favori_id" value="<?= $favori['id'] ?>">
                            <input type="submit" name="delete_favoris" class="btn btn-danger" value="Retirer">
                    </form>
                </div>
                <?php endwhile; ?>
            </div>
        </section>

        <!-- Messagerie -->
        <section class="section">
            <h2>Messages récents</h2>
            <div class="message-list">
                <?php while($message = $messages->fetch_assoc()): ?>
                <div class="message-card">
                    <p>De: <?= htmlspecialchars($message['expediteur']) ?></p>
                    <p><?= nl2br(htmlspecialchars_decode( htmlspecialchars($message['contenu']))) ?></p>
                    <small><?= date('d/m/Y H:i', strtotime($message['date_envoi'])) ?></small>
                </div>
                <?php endwhile; ?>
            </div>
            <a href="messagerie.php" class="btn btn-primary">Voir tous les messages</a>
        </section>
    </div>
 
    <?php require 'footer.php'; ?>
</body>
</html>
