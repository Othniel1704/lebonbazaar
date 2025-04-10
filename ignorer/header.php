<?php
// header.php : En-tête global du site

// Inclure le fichier de configuration
include_once($_SERVER['DOCUMENT_ROOT'].'config.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Bon Coin mini</title>

    <!-- Lien vers les styles CSS -->
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/responsive.css">

    <!-- Lien vers les scripts JavaScript
    <script src="/js/main.js" defer></script>
    <script src="/js/messagerie.js" defer></script> -->
</head>
<body>

    <!-- Inclus le menu de navigation |  Cette ligne inclut un fichier PHP externe,
      navbar.php, qui contient le menu de navigation du site. Cela permet de réutiliser 
      le menu sur plusieurs pages sans avoir à le recréer chaque fois. -->
    <?php include($_SERVER['DOCUMENT_ROOT'].'navbar.php'); ?>

    <!-- Contenu principal | qui est destiné à contenir le contenu principal de la page
      (le contenu spécifique à chaque page, comme les annonces ou le profil utilisateur.)-->
    <main>