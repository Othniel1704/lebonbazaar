<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit();
}

if (isset($_POST['confirmer'])) {
    $conn = mysqli_connect("localhost", "root", "root", "leboncoindb");
    $annonce_id = intval($_POST['annonce_id']);
    
    // Vérification propriétaire
    $stmt = $conn->prepare("DELETE FROM annonces WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $annonce_id, $_SESSION['id']);
    $stmt->execute();
    
    header("Location: profil.php");
}

// Récupérer l'annonce à supprimer pour affichage
$annonce_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM annonces WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $annonce_id, $_SESSION['id']);
$stmt->execute();
$annonce = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<body>
    <h2>Confirmer la suppression</h2>
    <p>Êtes-vous sûr de vouloir supprimer "<?= htmlspecialchars($annonce['titre']) ?>" ?</p>
    <form method="POST">
        <input type="hidden" name="annonce_id" value="<?= $annonce_id ?>">
        <button type="submit" name="confirmer">Confirmer</button>
        <a href="profil.php">Annuler</a>
    </form>
</body>
</html>