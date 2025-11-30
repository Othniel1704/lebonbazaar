<?php
require 'config.php';

// Récupération des paramètres
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$prix_min = isset($_GET['prix_min']) ? (int)$_GET['prix_min'] : 0;
$prix_max = isset($_GET['prix_max']) ? (int)$_GET['prix_max'] : PHP_INT_MAX;
$categorie = isset($_GET['categorie']) ? (int)$_GET['categorie'] : '';

// Construction de la requête
$sql = "SELECT annonces.*, images.image_path as main_image 
        FROM annonces 
        LEFT JOIN images ON annonces.id = images.annonce_id 
        WHERE annonces.prix >= ? AND annonces.prix <= ?";

$params = [$prix_min, $prix_max];
$types = "ii";

// Ajout du filtre par catégorie
if ($categorie) {
    $sql .= " AND annonces.categorie_id = ?";
    $params[] = $categorie;
    $types .= "i";
}

// Ajout de la recherche par mot-clé (priorité plus basse)
if (!empty($search)) {
    $sql .= " AND (annonces.titre LIKE ? OR annonces.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

$sql .= " GROUP BY annonces.id ORDER BY annonces.date_creation DESC LIMIT 12";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($annonce = $result->fetch_assoc()) {
        ?>
        <article class="annonce-card">
            <div class="annonce-image">
                <a href="increment_click.php?annonce_id=<?= $annonce['id'] ?>">
                    <img src="<?= $annonce['main_image'] ?: '../image/default_a.jpg' ?>" 
                         alt="<?= htmlspecialchars($annonce['titre']) ?>">
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
        <?php
    }
} else {
    echo '<p class="no-results">Aucune annonce ne correspond à vos critères.</p>';
}
?>
