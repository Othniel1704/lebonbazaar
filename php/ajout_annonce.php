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
    <title><?= $annonce ? 'Modifier' : 'Déposer' ?> une annonce - Le Bon Bazaar</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require 'navbar.php'; ?>

    <main class="add-listing-container">
        <div class="add-listing-header">
            <h1>
                <i class="fas fa-<?= $annonce ? 'edit' : 'plus-circle' ?>"></i>
                <?= $annonce ? 'Modifier votre annonce' : 'Déposer une nouvelle annonce' ?>
            </h1>
            <p>Les champs marqués d'un astérisque (*) sont obligatoires</p>
        </div>

        <?php if($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> <?= $success ?>
        </div>
        <?php elseif($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="listing-form">
            <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?? '' ?>">

            <!-- Section Photos -->
            <div class="form-card">
                <div class="form-card-header">
                    <h2><i class="fas fa-images"></i> Photos de l'annonce</h2>
                    <span class="form-hint">Ajoutez jusqu'à 5 photos (JPEG, PNG, max 2Mo chacune)</span>
                </div>
                <div class="form-card-body">
                    <?php if(!empty($annonce['images'])): ?>
                    <div class="existing-images-grid">
                        <?php 
                        $images = explode(',', $annonce['images']);
                        foreach($images as $img): 
                        ?>
                        <div class="image-preview-item">
                            <img src="<?= $img ?>" alt="Photo annonce">
                            <button type="button" class="delete-image-btn" data-image="<?= $img ?>">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="upload-zone">
                        <input type="file" name="photo[]" id="photoInput" multiple accept="image/*" style="display: none;">
                        <label for="photoInput" class="upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Cliquez ou glissez vos photos ici</span>
                            <small>JPEG, PNG - Max 2Mo par image</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Section Informations -->
            <div class="form-card">
                <div class="form-card-header">
                    <h2><i class="fas fa-info-circle"></i> Informations principales</h2>
                </div>
                <div class="form-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="titre">Titre de l'annonce *</label>
                            <input type="text" id="titre" name="titre" 
                                   value="<?= htmlspecialchars($annonce['titre'] ?? '') ?>" 
                                   placeholder="Ex : Vélo électrique neuf" 
                                   required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="description">Description détaillée *</label>
                            <textarea id="description" name="description" rows="6" 
                                      placeholder="Décrivez votre article en détail..." 
                                      required><?= htmlspecialchars($annonce['description'] ?? '') ?></textarea>
                            <small class="form-hint">Soyez précis pour attirer plus d'acheteurs</small>
                        </div>
                    </div>

                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label for="categorie">Catégorie *</label>
                            <select id="categorie" name="categorie" required>
                                <option value="">Choisir une catégorie</option>
                                <?php
                                $categories = $conn->query("SELECT * FROM categories ORDER BY nom");
                                while($cat = $categories->fetch_assoc()):
                                ?>
                                <option value="<?= $cat['id'] ?>" 
                                        <?= ($annonce['categorie_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nom']) ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="etat">État du produit *</label>
                            <select id="etat" name="etat" required>
                                <option value="neuf" <?= ($annonce['etat'] ?? '') == 'neuf' ? 'selected' : '' ?>>Neuf</option>
                                <option value="occasion" <?= ($annonce['etat'] ?? '') == 'occasion' ? 'selected' : '' ?>>Occasion</option>
                                <option value="reconditionné" <?= ($annonce['etat'] ?? '') == 'reconditionné' ? 'selected' : '' ?>>Reconditionné</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row form-row-2">
                        <div class="form-group">
                            <label for="prix">Prix (€) *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-euro-sign"></i>
                                <input type="number" id="prix" name="prix" 
                                       value="<?= $annonce['prix'] ?? '' ?>" 
                                       placeholder="0.00" 
                                       step="0.01" 
                                       min="0" 
                                       required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="ville">Ville *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-map-marker-alt"></i>
                                <input type="text" id="ville" name="ville" 
                                       value="<?= htmlspecialchars($annonce['ville'] ?? '') ?>" 
                                       placeholder="Ex : Paris" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="form-actions">
                <a href="profil.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Annuler
                </a>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fas fa-check"></i> 
                    <?= $annonce ? 'Enregistrer les modifications' : 'Publier l\'annonce' ?>
                </button>
            </div>
        </form>
    </main>

    <?php require 'footer.php'; ?>

    <script>
        // Preview des images avant upload
        document.getElementById('photoInput').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 5) {
                alert('Vous ne pouvez ajouter que 5 photos maximum');
                this.value = '';
                return;
            }
            
            // Vérifier la taille de chaque fichier
            for (let file of files) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('Chaque photo ne doit pas dépasser 2Mo');
                    this.value = '';
                    return;
                }
            }
        });

        // Suppression d'image
        document.querySelectorAll('.delete-image-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                if (confirm('Supprimer cette image ?')) {
                    this.closest('.image-preview-item').remove();
                }
            });
        });
    </script>
</body>
</html>


    