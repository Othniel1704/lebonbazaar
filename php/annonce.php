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
    <title><?= htmlspecialchars($annonce['titre']) ?> - Le Bon Coin</title>
    <link rel="stylesheet" href="../css/styleannonce.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="favori-bouton">
        <a href="favoris.php?action=<?= $isFavori ? 'retirer' : 'ajouter' ?>&annonce_id=<?= $annonce_id ?>">
            <svg aria-hidden="true" style="width:35px;height:35px" viewBox="0 0 24 24">
                <path fill="red" d="<?= $isFavori ? 'M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z' : 'M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3zm-4.4 15.55l-.1.1-.1-.1C7.14 14.24 4 11.39 4 8.5 4 6.5 5.5 5 7.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5c2 0 3.5 1.5 3.5 3.5 0 2.89-3.14 5.74-7.9 10.05z' ?>"/>
            </svg>
            <?= $isFavori ? 'Retirer des favoris' : 'Ajouter aux favoris' ?>
        </a>
    </div>
    <main class="container annonce-detail">
    <!-- Section détail annonce -->
    <section class="annonce-info">
        <h1><?= htmlspecialchars_decode(htmlspecialchars($annonce['titre'])) ?></h1>

        <!-- Galerie d'images -->
        <div class="gallery-container">
            <div class="gallery">
                <button class="nav-button left" onclick="changeImage(-1)">&#10094;</button>
                <?php foreach ($images as $img): ?>
                    <?php if (file_exists('../image/' . $img)): ?>
                        <img class="gallery-image" src="<?= htmlspecialchars('../image/' . $img) ?>" alt="Image de l'annonce">
                    <?php else: ?>
                        <img class="gallery-image" src="default-image.jpg" alt="Image non disponible">
                    <?php endif; ?>
                <?php endforeach; ?>
                <button class="nav-button right" onclick="changeImage(1)">&#10095;</button><br>

                <div class="image-indicators">
                        <?php foreach ($images as $index => $img): ?>
                            <button class="indicator <?= $index === 0 ? 'active' : '' ?>" 
                                    onclick="changeImageTo(<?= $index ?>)"
                                    aria-label="Image <?= $index + 1 ?>"></button>
                        <?php endforeach; ?>
                    </div>
            </div>
       

        <script>
            let currentIndex = 0;
            const images = document.querySelectorAll('.gallery-image');
            const indicators = document.querySelectorAll('.indicator');

            function updateIndicators() {
                indicators.forEach((indicator, index) => {
                    indicator.classList.toggle('active', index === currentIndex);
                });
            }

            function changeImage(direction) {
                images[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + direction + images.length) % images.length;
                images[currentIndex].classList.add('active');
                updateIndicators();
            }

            function changeImageTo(index) {
                images[currentIndex].classList.remove('active');
                currentIndex = index;
                images[currentIndex].classList.add('active');
                updateIndicators();
            }

            // Initialisation
            if (images.length > 0) {
                images[0].classList.add('active');
            }
        </script>

        <!-- Détails de l'annonce -->
        <div class="annonce-meta">
            <!-- Prix -->
            <div class="meta-item price">
                <i class="fas fa-tag"></i>
                <div>
                    <span class="label">Prix</span>
                    <span class="value"><?= is_numeric($annonce['prix']) ? number_format($annonce['prix'], 0, ',', ' ') . ' €' : 'Non spécifié' ?></span>
                </div>
            </div>
            <!-- État -->
            <p class="meta-item etat">
            <i class="fas fa-info-circle"></i>
            <?= htmlspecialchars($annonce['etat']) ?>
            </p>
            <!-- Catégorie -->
            <p class="meta-item etat">
            <i class="fas fa-list"></i>
            <?= htmlspecialchars($annonce['categorie']) ?>
            </p>
            <!-- Date de publication -->
            <p class="meta-item date">
            <i class="fas fa-calendar-alt"></i>
            <?php 
                if (!empty($annonce['date_creation']) && strtotime($annonce['date_creation']) !== false) {
                echo date("d/m/Y", strtotime($annonce['date_creation']));
                } else {
                echo "Date non disponible";
                }
            ?>
            </p>
            <?= isset($_SESSION['id']) ? '<p class="meta-item etat"><i class="fas fa-user"></i> Vous êtes connecté</p>' : '<p class="meta-item etat"><i class="fas fa-user"></i> Vous n\'êtes pas connecté</p>' ?>
            <!-- Nombre de consultations -->
            <div class="meta-item stats">
            <i class="fas fa-eye"></i>
            <span><?=  $count  ?> consultations</span>
            </div>
             <!-- Vendeur -->
             <p class="meta-item author">
            <i class="fas fa-user"></i>
            Vendeur : <?= htmlspecialchars($annonce['auteur_pseudo']) ?>
            </p>
            <!-- Bouton de contact -->
            <p class="contact-wrapper">
    <?php if (isset($_SESSION['id'])): ?>
        <a href="chat.php?annonce_id=<?= htmlspecialchars($annonce_id) ?>&expediteur_id=<?= htmlspecialchars($_SESSION['id']) ?>" 
           class="contact-button" 
           title="Contacter le vendeur"
           aria-label="Contacter le vendeur">
            <i class="fas fa-envelope icon"></i> 
            <span>Contacter</span>
        </a>
    <?php else: ?>
        <a href="connexion.php" 
           class="contact-button login-required" 
           title="Se connecter pour contacter le vendeur"
           aria-label="Connexion requise">
            <i class="fas fa-envelope icon"></i>
            <span>Se connecter pour contacter</span>
        </a>
    <?php endif; ?>
    </p>
        </div>
        </div>
        <!-- Description -->
        <div class="description">
            <h2>Description</h2>
            <p><?= !empty($annonce['description']) ? nl2br(htmlspecialchars_decode(htmlspecialchars($annonce['description']))) : 'Description non disponible' ?></p>
        </div>
    </section>

    <!-- Section contact -->
    <section class="contact">
        <h2>Contact</h2>
        <p>Pour toute question concernant cette annonce, n'hésitez pas à contacter le vendeur. <p class="contact-wrapper">
    <?php if (isset($_SESSION['id'])): ?>
        <a href="chat.php?annonce_id=<?= htmlspecialchars($annonce_id) ?>&expediteur_id=<?= htmlspecialchars($annonce["user_id"]) ?>" 
           class="contact-button" 
           title="Contacter le vendeur"
           aria-label="Contacter le vendeur">
            <i class="fas fa-envelope icon"></i> 
            <span>Contacter</span>
        </a>
    <?php else: ?>
        <a href="connexion.php" 
           class="contact-button login-required" 
           title="Se connecter pour contacter le vendeur"
           aria-label="Connexion requise">
            <i class="fas fa-envelope icon"></i>
            <span>Se connecter pour contacter</span>
        </a>
        <?php endif; ?>
        </p>
    </section>
</main>
<?php require 'footer.php'; ?>
</body>
</html>
