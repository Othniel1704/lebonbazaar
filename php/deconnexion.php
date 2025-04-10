<?php
session_start(); // Démarrer la session pour stocker les informations de l'utilisateur
session_destroy(); // Détruire la session pour déconnecter l'utilisateur
header("Location: index.php"); // Rediriger l'utilisateur vers la page d'accueil
?>
