<?php
session_start();
require 'config.php';

$annonce_id = (int)$_GET['annonce_id'];
$user_id = isset($_GET["user_id"]) && !empty($_GET["user_id"]) ? (int)$_GET["user_id"] : null;

if (isset($annonce_id) && isset($user_id)) {
    // Vérifier si l'utilisateur a déjà visité cette annonce
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM annonce_visite WHERE user_id = ? AND annonce_id = ?");
    $check_stmt->bind_param("ii", $user_id, $annonce_id);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count == 0) {
        // Insérer une nouvelle visite avec la date actuelle
        $stmt = $conn->prepare("INSERT INTO annonce_visite (user_id, annonce_id, visite_date) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $user_id, $annonce_id);
        $stmt->execute();
        $stmt->close();
    } else {
        // Mettre à jour la date de visite si l'annonce a déjà été visitée
        $update_stmt = $conn->prepare("UPDATE annonce_visite SET visite_date = NOW() WHERE user_id = ? AND annonce_id = ?");
        $update_stmt->bind_param("ii", $user_id, $annonce_id);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

// Redirection vers la page de l'annonce
header("Location: annonce.php?id=" . $annonce_id);
exit();
?>
