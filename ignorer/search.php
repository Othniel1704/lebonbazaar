<?php
session_start();
require 'config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// First try full-text search if available
try {
    $sql = "SELECT annonces.*, images.image_path as main_image,
            MATCH(annonces.titre, annonces.description) AGAINST(? IN BOOLEAN MODE) as relevance
            FROM annonces 
            LEFT JOIN images ON annonces.id = images.annonce_id 
            WHERE MATCH(annonces.titre, annonces.description) AGAINST(? IN BOOLEAN MODE) > 0
            GROUP BY annonces.id
            ORDER BY relevance DESC, annonces.date_creation DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    
} catch (mysqli_sql_exception $e) {
    // If full-text fails, fall back to LIKE search
    $sql = "SELECT annonces.*, images.image_path as main_image 
            FROM annonces 
            LEFT JOIN images ON annonces.id = images.annonce_id 
            WHERE (annonces.titre LIKE ? OR annonces.description LIKE ?)
            GROUP BY annonces.id
            ORDER BY annonces.date_creation DESC";
    
    $search_term = "%$search%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Display results
?>

<h1>resulats de recherche</h1>

<div class="annonces-grid" style="display: flex; flex-wrap: wrap; gap: 20px;">
            <?php if ($result->num_rows > 0): ?>
                <?php while($annonce = $result->fetch_assoc()): ?>
                    <article class="annonce-card">
                        <!-- Gestion de l'image (première image ou défaut) -->
                        <div class="annonce-image">
                            <a href="increment_click.php?annonce_id=<?= $annonce['id'] ?>&user_id=<?= isset($_SESSION['id']) ? $_SESSION['id'] : '' ?>">
                                <img src="<?= $annonce['main_image'] ?: '../image/default_a.jpg' ?>" 
                                     alt="<?= htmlspecialchars_decode(htmlspecialchars($annonce['titre'])) ?>">
                                     </a>
                        </div>
                        <div class="annonce-content">
                            <h2>
                            <a href="increment_click.php?annonce_id=<?= $annonce['id'] ?>&user_id=<?= isset($_SESSION['id']) ? $_SESSION['id'] : '' ?>">
                                    <?= htmlspecialchars_decode(htmlspecialchars($annonce['titre'])) ?>
                                </a>
                            </h2>
                            <p class="annonce-price"><?= number_format($annonce['prix'], 0, ',', ' ') ?> €</p>
                            <p class="annonce-date">
                                <?= date('d/m/Y H:i', strtotime($annonce['date_creation'])) ?>
                            </p>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-results">Aucune annonce ne correspond à votre recherche.</p>
            <?php endif; ?>
        </div>
