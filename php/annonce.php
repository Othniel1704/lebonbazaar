<?php
session_start();
require 'config.php';
require 'navbar.php'; 
// Vérifier si l'utilisateur est connecté

// Récupérer les détails de l'annonce
$annonce = null;
if (isset($_GET['id'])) {
    $annonce_id = intval($_GET['id']);
    
    // Requête avec jointure pour récupérer les images et les infos utilisateur
    $sql = "SELECT annonces.*, users.*, categories.nom as categorie,
                   users.prenom as auteur_pseudo,
                   GROUP_CONCAT(images.image_path) as images

            FROM annonces 
            LEFT JOIN images ON annonces.id = images.annonce_id 
            LEFT JOIN users ON annonces.user_id = users.id 
            left join categories on annonces.categorie_id = categories.id
            WHERE annonces.id = ? 
            GROUP BY annonces.id";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $annonce_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $annonce = $result->fetch_assoc();
    $images = !empty($annonce['images']) ? explode(',', $annonce['images']) : [];

    if (!$annonce) {
        die("Annonce introuvable");
    }
}

// Vérifier si l'annonce est en favoris
$isFavori = false;
if (isset($_SESSION['id'])) {
    $stmt = $conn->prepare("SELECT * FROM favoris WHERE user_id = ? AND annonce_id = ?");
    $stmt->bind_param("ii", $_SESSION['id'], $annonce_id);
    $stmt->execute();
    $isFavori = $stmt->get_result()->num_rows > 0;
}
// Incrémenter le compteur de clics
$check_stmt = $conn->prepare("SELECT COUNT(*) FROM annonce_visite WHERE  annonce_id = ?");
    $check_stmt->bind_param("i", $annonce_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($annonce['titre']) ?> - Le Bon Coin</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
    <main class="annonce-detail-container">
        <!-- Left Column: Gallery & Description -->
        <div class="ad-main-column">
            <!-- Gallery -->
            <div class="ad-gallery">
                <div class="main-image-container">
                    <button class="gallery-nav-btn prev" onclick="changeImage(-1)"><i class="fas fa-chevron-left"></i></button>
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $index => $img): ?>
                            <img class="main-image <?= $index === 0 ? 'active' : '' ?>" 
                                 src="<?= file_exists('../image/' . $img) ? htmlspecialchars('../image/' . $img) : 'default-image.jpg' ?>" 
                                 alt="Image de l'annonce"
                                 style="display: <?= $index === 0 ? 'block' : 'none' ?>">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <img class="main-image" src="../image/default_a.jpg" alt="Image non disponible">
                    <?php endif; ?>
                    <button class="gallery-nav-btn next" onclick="changeImage(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                
                <?php if (count($images) > 1): ?>
                <div class="thumbnails-scroll">
                    <?php foreach ($images as $index => $img): ?>
                        <img class="thumbnail <?= $index === 0 ? 'active' : '' ?>" 
                             src="<?= file_exists('../image/' . $img) ? htmlspecialchars('../image/' . $img) : 'default-image.jpg' ?>" 
                             onclick="changeImageTo(<?= $index ?>)"
                             alt="Miniature <?= $index + 1 ?>">
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Content -->
            <div class="ad-content">
                <h1 class="ad-title"><?= htmlspecialchars_decode(htmlspecialchars($annonce['titre'])) ?></h1>
                
                <div class="ad-meta-grid">
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span><?= htmlspecialchars($annonce['categorie']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-info-circle"></i>
                        <span><?= htmlspecialchars($annonce['etat']) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Publié le <?= date("d/m/Y", strtotime($annonce['date_creation'])) ?></span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-eye"></i>
                        <span><?= $count ?> vues</span>
                    </div>
                </div>

                <div class="ad-description">
                    <h3>Description</h3>
                    <p><?= !empty($annonce['description']) ? nl2br(htmlspecialchars_decode(htmlspecialchars($annonce['description']))) : 'Aucune description fournie.' ?></p>
                </div>
            </div>
        </div>

        <!-- Right Column: Sticky Info Card -->
        <aside class="ad-sidebar">
            <div class="ad-info-card">
                <span class="ad-price"><?= number_format($annonce['prix'], 0, ',', ' ') ?> €</span>
                
                <div class="seller-info">
                    <div class="seller-avatar">
                        <?= strtoupper(substr($annonce['auteur_pseudo'], 0, 1)) ?>
                    </div>
                    <div class="seller-details">
                        <h4><?= htmlspecialchars($annonce['auteur_pseudo']) ?></h4>
                        <span>Membre depuis 2023</span>
                    </div>
                </div>

                <div class="action-buttons">
                    <?php if (isset($_SESSION['id'])): ?>
                        <a href="chat.php?annonce_id=<?= htmlspecialchars($annonce_id) ?>&expediteur_id=<?= htmlspecialchars($annonce["user_id"]) ?>" class="btn btn-primary btn-block">
                            <i class="fas fa-envelope"></i> Contacter
                        </a>
                    <?php else: ?>
                        <a href="connexion.php" class="btn btn-primary btn-block">
                            <i class="fas fa-lock"></i> Se connecter
                        </a>
                    <?php endif; ?>

                    <a href="favoris.php?action=<?= $isFavori ? 'retirer' : 'ajouter' ?>&annonce_id=<?= $annonce_id ?>" 
                       class="btn <?= $isFavori ? 'btn-danger' : 'btn-secondary' ?> btn-block">
                        <i class="<?= $isFavori ? 'fas' : 'far' ?> fa-heart"></i> 
                        <?= $isFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
                    </a>
                </div>
            </div>
        </aside>
    </main>

    <script>
        let currentIndex = 0;
        const images = document.querySelectorAll('.main-image');
        const thumbnails = document.querySelectorAll('.thumbnail');

        function updateDisplay() {
            images.forEach((img, index) => {
                img.style.display = index === currentIndex ? 'block' : 'none';
            });
            thumbnails.forEach((thumb, index) => {
                thumb.classList.toggle('active', index === currentIndex);
            });
        }

        function changeImage(direction) {
            if (images.length === 0) return;
            currentIndex = (currentIndex + direction + images.length) % images.length;
            updateDisplay();
        }

        function changeImageTo(index) {
            currentIndex = index;
            updateDisplay();
        }
    </script>

    <?php require 'footer.php'; ?>
</body>
</html>
