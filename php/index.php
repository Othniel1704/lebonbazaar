<?php
session_start();
require 'config.php';

// Pagination
$par_page = 6; // Nombre d'annonces par page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $par_page;

// Initialisation des filtres
$prix_min = isset($_GET['prix_min']) ? (int)$_GET['prix_min'] : 0;
$prix_max = isset($_GET['prix_max']) ? (int)$_GET['prix_max'] : PHP_INT_MAX;
$categorie = isset($_GET['categorie']) ? (int)$_GET['categorie'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construction de la requête de base avec jointure pour l'image principale
$sql = "SELECT annonces.*, images.image_path as main_image 
        FROM annonces 
        LEFT JOIN images ON annonces.id = images.annonce_id 
        WHERE annonces.prix >= ? AND annonces.prix <= ?";

// Ajouter la condition de catégorie si une catégorie est sélectionnée
$params = [$prix_min, $prix_max];
$types = "ii";

if ($categorie) {
    $sql .= " AND annonces.categorie_id = ?";
    $params[] = $categorie;
    $types .= "i";
}

if (!empty($search)) {
    $sql .= " AND (annonces.titre LIKE ? OR annonces.description LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= "ss";
}

$sql .= " GROUP BY annonces.id 
          ORDER BY annonces.date_creation DESC 
          LIMIT ? OFFSET ?";
$params[] = $par_page;
$params[] = $offset;
$types .= "ii";

// Préparer et exécuter la requête
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Comptage total des annonces pour la pagination
$total_annonces_sql = "SELECT COUNT(*) as total FROM annonces WHERE prix >= ? AND prix <= ?";
$total_params = [$prix_min, $prix_max];
$total_types = "ii";


if ($categorie) {
    $total_annonces_sql .= " AND categorie_id = ?";
    $total_params[] = $categorie;
    $total_types .= "i";
}

$total_stmt = $conn->prepare($total_annonces_sql);
$total_stmt->bind_param($total_types, ...$total_params);
$total_stmt->execute();
$total_annonces = $total_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_annonces / $par_page);

// Vérifier si l'utilisateur est connecté
if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    // Requête pour récupérer les 5 dernières annonces visitées
    $sql_recent = "SELECT annonces.* , images.image_path as main_image
                   FROM annonce_visite 
                   INNER JOIN annonces ON annonce_visite.annonce_id = annonces.id 
                   LEFT JOIN images ON annonces.id = images.annonce_id 
                   WHERE annonce_visite.user_id = ? 
                   GROUP BY annonce_visite.annonce_id
                   ORDER BY annonce_visite.visite_date DESC 
                   LIMIT 5";

    $stmt_recent = $conn->prepare($sql_recent);
    $stmt_recent->bind_param("i", $user_id);
    $stmt_recent->execute();
    $recent_result = $stmt_recent->get_result();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LebonBazaar - Petites annonces</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let searchTimeout;
            
            // Fonction pour récupérer les valeurs des filtres
            function getFilterValues() {
                return {
                    search: $('#search').val(),
                    prix_min: $('#prix_min').val(),
                    prix_max: $('#prix_max').val(),
                    categorie: $('#categorie').val()
                };
            }

            // Gestion des filtres et de la recherche
            $('.filter-input, .filter-select').on('change', function() {
                executeSearch();
            });

            $('#search').on('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(executeSearch, 500);
            });

            function executeSearch() {
                // Ajouter l'indicateur de chargement
                $('.annonces-grid').html('<div class="loading">Recherche en cours...</div>');
                
                // Récupérer toutes les valeurs des filtres
                const filterData = getFilterValues();
                
                $.ajax({
                    url: 'ajax_search.php',
                    method: 'GET',
                    data: filterData,
                    success: function(data) {
                        $('.annonces-grid').html(data);
                    },
                    error: function() {
                        $('.annonces-grid').html('<p class="error">Erreur lors de la recherche</p>');
                    }
                });
            }

            // Supprimer le formulaire classique
            $('.filter-button').on('click', function(e) {
                e.preventDefault();
                executeSearch();
            });
        });
    </script>
</head>
<body>
    <?php require 'navbar.php'; ?> 

    <section class="hero-section">
        <div class="container">
            <h1 class="search-title">Trouvez la bonne affaire</h1>
        </div>
    </section>

    <main class="container">
        <!-- Filtres -->
        <div class="filters-container">
            <form action="" method="GET">
                <div class="filter-group" style="margin-bottom: 1.5rem;">
                    <label for="search">Recherche</label>
                    <input type="text" class="filter-input" id="search" placeholder="Que cherchez-vous ?" 
                            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                </div>

                <div class="filter-grid">
                    <div class="filter-group">
                        <label for="prix_min">Prix minimum</label>
                        <div class="price-inputs">
                            <input type="number" class="filter-input" id="prix_min" name="prix_min" placeholder="Min" value="<?= $prix_min > 0 ? $prix_min : '' ?>">
                            <span class="currency-symbol">€</span>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="prix_max">Prix maximum</label>
                        <div class="price-inputs">
                            <input type="number" class="filter-input" id="prix_max" name="prix_max" placeholder="Max" value="<?= $prix_max < PHP_INT_MAX ? $prix_max : '' ?>">
                            <span class="currency-symbol">€</span>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label for="categorie">Catégorie</label>
                        <select class="filter-select" id="categorie" name="categorie">
                            <option value="">Toutes les catégories</option>
                            <?php
                            $categories = $conn->query("SELECT * FROM categories");
                            while ($cat = $categories->fetch_assoc()) {
                                $selected = ($cat['id'] == $categorie) ? 'selected' : '';
                                echo "<option value='{$cat['id']}' $selected>{$cat['nom']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="filter-button">Rechercher</button>
                </div>
            </form>
        </div>

        <!-- Liste des annonces -->
        <div class="annonces-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($annonce = $result->fetch_assoc()): ?>
                    <article class="annonce-card">
                        <div class="annonce-image">
                            <a href="increment_click.php?annonce_id=<?= $annonce['id'] ?>&user_id=<?= isset($_SESSION['id']) ? $_SESSION['id'] : '' ?>">
                                <img src="<?= $annonce['main_image'] ?: '../image/default_a.jpg' ?>" 
                                     alt="<?= htmlspecialchars($annonce['titre']) ?>">
                            </a>
                        </div>
                        <div class="annonce-content">
                            <div style="padding: 0 1rem;">
                                <h2>
                                    <a href="increment_click.php?annonce_id=<?= $annonce['id'] ?>&user_id=<?= isset($_SESSION['id']) ? $_SESSION['id'] : '' ?>">
                                        <?= htmlspecialchars($annonce['titre']) ?>
                                    </a>
                                </h2>
                                <p class="annonce-price"><?= number_format($annonce['prix'], 0, ',', ' ') ?> €</p>
                                <p class="annonce-date">
                                    <?= date('d/m/Y', strtotime($annonce['date_creation'])) ?>
                                </p>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results" style="grid-column: 1/-1;">
                    <p>Aucune annonce ne correspond à votre recherche.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="page-link">←</a>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="page-link <?= $i === $page ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="page-link">→</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>


        <!-- dernières annonces visitées -->
        <?php if (isset($recent_result) && $recent_result->num_rows > 0): ?>
            <section class="recent-ads-section">
                <h2><i class="fas fa-history"></i> Vos dernières visites</h2>
                <div class="recent-ads-grid">
                    <?php while ($annonce = $recent_result->fetch_assoc()): ?>
                        <a href="increment_click.php?annonce_id=<?= $annonce['id'] ?>&user_id=<?= $user_id ?>" class="recent-ad-card">
                            <img src="<?= $annonce['main_image'] ?: '../image/default_a.jpg' ?>" 
                                 class="recent-ad-image"
                                 alt="<?= htmlspecialchars($annonce['titre']) ?>">
                            <div class="recent-ad-content">
                                <h3 class="recent-ad-title"><?= htmlspecialchars($annonce['titre']) ?></h3>
                                <p class="recent-ad-price"><?= number_format($annonce['prix'], 0, ',', ' ') ?> €</p>
                            </div>
                        </a>
                    <?php endwhile; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php require 'footer.php'; ?>
</body>
</html>
