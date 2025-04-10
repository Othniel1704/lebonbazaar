<?php
session_start();
require "config.php";

if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

$annonce = null;
$success = $error = '';

// Gestion des messages de succès/erreur
if (isset($_GET['success'])) $success = "Annonce ".($_GET['id'] ? 'modifiée' : 'créée')." avec succès !";
if (isset($_GET['error'])) $error = "Erreur lors de l'enregistrement";

// Récupération annonce existante
if (isset($_GET['id'])) {
    $annonce_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT a.*, GROUP_CONCAT(i.image_path) AS images 
                          FROM annonces a
                          LEFT JOIN images i ON a.id = i.annonce_id
                          WHERE a.id = ? AND a.user_id = ?
                          GROUP BY a.id");
    $stmt->bind_param("ii", $annonce_id, $_SESSION['id']);
    $stmt->execute();
    $annonce = $stmt->get_result()->fetch_assoc();
    
    if (!$annonce) die("Accès non autorisé");
}

// Traitement du formulaire
if (isset($_POST['submit'])) {
    try {
        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $prix = floatval($_POST['prix']);
        $categorie = intval($_POST['categorie']);
        $ville = htmlspecialchars($_POST['ville']);
        $etat = htmlspecialchars($_POST['etat']);
        $annonce_id = $_POST['annonce_id'] ?? null;
        

        // Démarrer une transaction
        $conn->begin_transaction();

        if ($annonce_id) {
            // Mise à jour de l'annonce existante
            $stmt = $conn->prepare("UPDATE annonces SET titre=?, description=?, prix=?, categorie_id=?, etat=?, ville=? WHERE id=?");
            $stmt->bind_param("ssdisss", $titre, $description, $prix, $categorie, $etat, $ville, $annonce_id);
        } else {
            // Création d'une nouvelle annonce
            $stmt = $conn->prepare("INSERT INTO annonces (titre, description, prix, categorie_id, user_id, etat, ville) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiiss", $titre, $description, $prix, $categorie, $_SESSION['id'], $etat, $ville);
        }

        // Exécuter la requête (une seule fois)
        $stmt->execute();

        // Si une nouvelle annonce est créée, récupérer son ID
        if (!$annonce_id) {
            $annonce_id = $conn->insert_id;
        }

        // Gestion des images
        if (!empty($_FILES['photo']['name'][0])) {
            $stmt_img = $conn->prepare("INSERT INTO images (annonce_id, image_path) VALUES (?, ?)");
            $maxFileSize = 2 * 1024 * 1024; // 2Mo en octets
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];

            foreach ($_FILES['photo']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['photo']['error'][$key] === UPLOAD_ERR_OK) {
                    // Vérification de la taille
                    if ($_FILES['photo']['size'][$key] > $maxFileSize) {
                        throw new Exception("L'image {$_FILES['photo']['name'][$key]} dépasse la taille maximale de 2Mo");
                    }

                    // Vérification du type de fichier
                    $fileType = mime_content_type($tmp_name);
                    if (!in_array($fileType, $allowedTypes)) {
                        throw new Exception("Le type de fichier n'est pas autorisé. Utilisez uniquement JPEG ou PNG");
                    }

                    $ext = pathinfo($_FILES['photo']['name'][$key], PATHINFO_EXTENSION);
                    $filename = uniqid() . '_' . date("Ymd_His") . '.' . $ext;
                    $target = "../image/" . $filename;

                    // Redimensionnement de l'image si nécessaire
                    list($width, $height) = getimagesize($tmp_name);
                    $maxWidth = 1200;
                    $maxHeight = 1200;

                    if ($width > $maxWidth || $height > $maxHeight) {
                        $ratio = min($maxWidth / $width, $maxHeight / $height);
                        $newWidth = round($width * $ratio);
                        $newHeight = round($height * $ratio);

                        $newImage = imagecreatetruecolor($newWidth, $newHeight);

                        if ($fileType == 'image/jpeg') {
                            $source = imagecreatefromjpeg($tmp_name);
                            imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                            imagejpeg($newImage, $target, 80);
                        } elseif ($fileType == 'image/png') {
                            $source = imagecreatefrompng($tmp_name);
                            imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                            imagepng($newImage, $target, 8);
                        }elseif ($fileType == 'image/jpg') {
                            $source = imagecreatefromjpeg($tmp_name);
                            imagecopyresampled($newImage, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                            imagejpeg($newImage, $target);
                        } else {
                            throw new Exception("Type de fichier non pris en charge");
                        }

                        imagedestroy($newImage);
                        imagedestroy($source);
                    } else {
                        move_uploaded_file($tmp_name, $target);
                    }

                    $stmt_img->bind_param("is", $annonce_id, $target);
                    $stmt_img->execute();
                }
            }
        }

        // Valider la transaction
        $conn->commit();

        // Redirection avec succès
        header("Location: profil.php?success=1&id=" . $annonce_id);
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollback();
        header("Location: ajout_annonce.php?error=1");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $annonce ? 'Modifier' : 'Déposer' ?> une annonce - Le Bon Coin</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require 'navbar.php'; ?>

    <main class="container form-container">
        <h1 class="form-title"><?= $annonce ? 'Modifier votre annonce' : 'Déposer une nouvelle annonce' ?></h1>
        
        <?php if($success): ?>
        <div class="alert success"><?= $success ?></div>
        <?php elseif($error): ?>
        <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <form class="annonce-form" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?? '' ?>">

            <div class="form-grid">
                <!-- Colonne gauche -->
                <div class="form-column">
                    <!-- Section Images -->
                    <div class="form-section">
                        <h2>Photos de l'annonce</h2>
                        <div class="image-upload-container">
                            <!-- Aperçu des images existantes -->
                            <?php if(!empty($annonce['images'])): ?>
                            <div class="existing-images">
                                <?php 
                                $images = explode(',', $annonce['images']);
                                foreach($images as $img): 
                                ?>
                                <div class="image-preview">
                                    <img src="<?= $img ?>" alt="Photo annonce">
                                    <button type="button" class="delete-image" data-image="<?= $img ?>">×</button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Zone de dépôt -->
                            <div class="upload-zone">
                                <label class="upload-label">
                                    <input type="file" name="photo[]" multiple accept="image/*">
                                    <span class="upload-text">+ Ajouter des photos</span>
                                    <span class="upload-hint">(JPEG, PNG, max 2Mo par image)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite -->
                <div class="form-column">
                    <!-- Section Informations -->
                    <div class="form-section">
                        <h2>Informations principales</h2>
                        
                        <div class="form-group">
                            <label>Titre de l'annonce *</label>
                            <input type="text" name="titre" value="<?= $annonce['titre'] ?? '' ?>" 
                                   placeholder="Ex : Vélo électrique neuf" required>
                        </div>

                        <div class="form-group">
                            <label>Description détaillée *</label>
                            <textarea name="description" rows="5" 
                                      placeholder="Décrivez votre article en détail..." required><?= $annonce['description'] ?? '' ?></textarea>
                        </div>
                        <div class="form-group">
                            <label>État du produit *</label>
                            <select name="etat" class="nice-select" required>
                                <option value="neuf" <?= ($annonce['etat'] ?? '') == 'neuf' ? 'selected' : '' ?>>Neuf</option>
                                <option value="occasion" <?= ($annonce['etat'] ?? '') == 'occasion' ? 'selected' : '' ?>>Occasion</option>
                            </select>
                        </div>

                        <div class="form-group price-group">
                            <label>Prix *</label>
                            <div class="price-input">
                                <input type="number" name="prix" step="0.01" 
                                       value="<?= $annonce['prix'] ?? '' ?>" required>
                                <span>€</span>
                            </div>
                        </div>


                        <div class="form-group">
                            <label>Catégorie *</label>
                            <select name="categorie" class="nice-select" required>
                                <?php
                                $categories = $conn->query("SELECT * FROM categories ORDER BY nom");
                                while ($cat = $categories->fetch_assoc()):
                                ?>
                                <option value="<?= $cat['id'] ?>" 
                                    <?= ($annonce['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                                          
                        <div class="form-group">
                            <label>Ville *</label>
                            <input type="text" name="ville" value="<?= $annonce['ville'] ?? '' ?>" 
                                   placeholder="Ex : Paris" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" name="submit" class="btn primary-btn">
                            <?= $annonce ? 'Mettre à jour' : 'Publier l\'annonce' ?>
                        </button>
                        <a href="profil.php" class="btn secondary-btn">Annuler</a>
                    </div>
                </div>
            </div>
        </form>
    </main>
</body>
</html>

