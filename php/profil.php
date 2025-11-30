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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php require 'navbar.php'; ?>

    <main class="dashboard-container">
        <!-- Sidebar -->
        <aside class="profile-sidebar">
            <div class="profile-card">
                <form method="POST" enctype="multipart/form-data" id="photoForm">
                    <div class="profile-avatar-container">
                        <img src="../image/<?= $user['photo'] ?? 'default.png' ?>" class="profile-avatar" alt="Photo de profil">
                        <label for="photoInput" class="profile-avatar-edit">
                            <i class="fas fa-camera"></i>
                        </label>
                        <input type="file" name="photo" id="photoInput" accept="image/*" style="display: none;" onchange="document.getElementById('photoForm').submit()">
                        <input type="hidden" name="update_profile" value="1">
                        <!-- Hidden fields to preserve other data when updating photo -->
                        <input type="hidden" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>">
                        <input type="hidden" name="nom" value="<?= htmlspecialchars($user['nom']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($user['email']) ?>">
                        <input type="hidden" name="tel" value="<?= htmlspecialchars($user['tel']) ?>">
                        <input type="hidden" name="sexe" value="<?= htmlspecialchars($user['sexe']) ?>">
                        <input type="hidden" name="date_naissance" value="<?= htmlspecialchars($user['date_naissance']) ?>">
                        <input type="hidden" name="adresse" value="<?= htmlspecialchars($user['adresse']) ?>">
                        <input type="hidden" name="ville" value="<?= htmlspecialchars($user['ville']) ?>">
                        <input type="hidden" name="profession" value="<?= htmlspecialchars($user['profession']) ?>">
                    </div>
                </form>
                
                <h2 class="profile-name"><?= htmlspecialchars($user['prenom']) ?> <?= htmlspecialchars($user['nom']) ?></h2>
                <span class="profile-role"><?= htmlspecialchars($user['profession'] ?: 'Membre') ?></span>
                
                <div class="profile-stats">
                    <div class="stat-item">
                        <strong><?= $annonces->num_rows ?></strong>
                        <span>Annonces</span>
                    </div>
                    <div class="stat-item">
                        <strong><?= $favoris->num_rows ?></strong>
                        <span>Favoris</span>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="dashboard-content">
            
            <!-- Personal Info -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-user-edit"></i> Mes Informations</h2>
                </div>
                <form method="POST" class="profile-form-grid">
                    <div class="form-group">
                        <label>Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled style="background: var(--gray-100);">
                    </div>
                    <div class="form-group">
                        <label>Téléphone</label>
                        <input type="tel" name="tel" value="<?= htmlspecialchars($user['tel']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Ville</label>
                        <input type="text" name="ville" value="<?= htmlspecialchars($user['ville']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Profession</label>
                        <input type="text" name="profession" value="<?= htmlspecialchars($user['profession']) ?>">
                    </div>
                    <div class="form-group" style="grid-column: 1 / -1;">
                        <button type="submit" name="update_profile" class="btn btn-primary">
                            Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </section>

            <!-- My Ads -->
            <section class="dashboard-section">
                <div class="section-header">
                    <h2><i class="fas fa-bullhorn"></i> Mes Annonces</h2>
                    <a href="ajout_annonce.php" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> Déposer
                    </a>
                </div>
                
                <?php if ($annonces->num_rows > 0): ?>
                    <div class="annonces-grid">
                        <?php 
                        // Reset pointer if needed or re-fetch. Assuming $annonces is accessible.
                        $annonces->data_seek(0); 
                        while($annonce = $annonces->fetch_assoc()): 
                        ?>
                        <div class="annonce-card">
                            <!-- Image placeholder or actual image logic if available -->
                            <div class="card-content" style="padding: 1rem;">
                                <h3><?= htmlspecialchars($annonce['titre']) ?></h3>
                                <p class="price"><?= number_format($annonce['prix'], 0, ',', ' ') ?> €</p>
                                <div class="card-actions" style="margin-top: 1rem; display: flex; gap: 0.5rem;">
                                    <a href="annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-secondary btn-sm">Voir</a>
                                    <a href="ajout_annonce.php?id=<?= $annonce['id'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="annonce_id" value="<?= $annonce['id'] ?>">
                                        <button type="submit" name="delete_annonce" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Vous n'avez aucune annonce en ligne.</p>
                <?php endif; ?>
            </section>

            <!-- Favorites & Messages Grid -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                
                <!-- Favorites -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-heart"></i> Mes Favoris</h2>
                    </div>
                    <div class="dashboard-list">
                        <?php 
                        $favoris->data_seek(0);
                        while($favori = $favoris->fetch_assoc()): 
                        ?>
                        <div class="dashboard-list-item">
                            <div class="item-content">
                                <a href="annonce.php?id=<?= $favori['id'] ?>" class="item-title">
                                    <?= htmlspecialchars($favori['titre']) ?>
                                </a>
                                <span class="item-meta">Ajouté le <?= date('d/m/Y', strtotime($favori['date_ajout'])) ?></span>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="favori_id" value="<?= $favori['id'] ?>">
                                <button type="submit" name="delete_favoris" class="btn btn-text text-danger">
                                    <i class="fas fa-times"></i>
                                </button>
                            </form>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>

                <!-- Recent Messages -->
                <section class="dashboard-section">
                    <div class="section-header">
                        <h2><i class="fas fa-envelope"></i> Messages Récents</h2>
                        <a href="messagerie.php" class="btn btn-text">Tout voir</a>
                    </div>
                    <div class="dashboard-list">
                        <?php 
                        $messages->data_seek(0);
                        while($message = $messages->fetch_assoc()): 
                        ?>
                        <div class="dashboard-list-item">
                            <div class="item-content">
                                <a href="chat.php?expediteur_id=<?= $message['expediteur_id'] ?>&annonce_id=<?= $message['annonce_id'] ?>" class="item-title">
                                    <?= htmlspecialchars($message['expediteur']) ?>
                                </a>
                                <span class="item-meta"><?= date('d/m H:i', strtotime($message['date_envoi'])) ?></span>
                            </div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </section>
            </div>

        </div>
    </main>

    <?php require 'footer.php'; ?>
</body>
</html>
