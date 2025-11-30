<?php
session_start();
require "config.php";

if (!isset($_SESSION['id'])) {
    echo"<script>alert('Vous devez être connecté pour accéder à cette page.');</script>";

    header("Location: connexion.php");
    exit();
}


// Ajout/Retrait favori
if (isset($_GET['action']) && isset($_GET['annonce_id'])) {
    $annonce_id = intval($_GET['annonce_id']);
    
    if ($_GET['action'] === 'ajouter') {
        $stmt = $conn->prepare("INSERT INTO favoris (user_id, annonce_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $_SESSION['id'], $annonce_id);
    } else {
        $stmt = $conn->prepare("DELETE FROM favoris WHERE user_id = ? AND annonce_id = ?");
        $stmt->bind_param("ii", $_SESSION['id'], $annonce_id);
    }
    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
}

// Liste des favoris
$stmt = $conn->prepare("SELECT a.* FROM favoris f 
                       JOIN annonces a ON f.annonce_id = a.id 
                       WHERE f.user_id = ?");
$stmt->bind_param("i", $_SESSION['id']);
$stmt->execute();
$favoris = $stmt->get_result();
?>

