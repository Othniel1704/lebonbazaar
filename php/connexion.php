<?php
session_start(); // Démarrer la session pour stocker les informations de l'utilisateur
if (isset($_POST['bout'])) {
    $mdp = $_POST['mdp'];
    $mail = $_POST['email'];
    $id = mysqli_connect("localhost", "root", "", "leboncoindb");

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $stmt = $id->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Vérification du mot de passe chiffré
        if (password_verify($mdp, $user['mdp'])) {
            // Stocker les informations de l'utilisateur dans la session
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $idu = $_SESSION['id'];
            header("Location: index.php");
        } else {
            $erreur = "Erreur de login ou de mot de passe incorrect";
        }
    } else {
        $erreur = "Erreur de login ou de mot de passe incorrect";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/styleconnexion.css">
</head>
<body class="connexion">
    <?php include 'navbar.php'; ?>
    <div class="container">
    <h1>Connexion</h1>
    <form action="connexion.php" method="post">
    
        <input type="email" name="email" placeholder="Entrez email"required><br><br>
        
    
        <input type="password" id="mdp" name="mdp" placeholder="Entrez votre mot de passe" required><br><br>
        <?php if (isset($erreur)) { echo "<p style='font-family: italic; color:red;'>".$erreur."</p>"; } ?><!-- Afficher l'erreur si le login ou le mot de passe est incorrect -->
        <input type="submit" name="bout" value="Se connecter"><br>
        <div class="inscrire"><a href="mdpmodif.php">Mot de passe oublié ?</a> </div><br>
        <div class="inscrire">Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous !</a></div>
    </form>
     </div><br><br><br>
    <?php include 'footer.php'; ?>  
</body>
</html>
