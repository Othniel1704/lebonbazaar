<?php
session_start(); // Démarrer la session pour stocker les informations de l'utilisateur
require_once 'config.php'; // Importer le fichier de configuration
if( isset($_POST['submit'])) {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];
    $mdp2 = $_POST['confmdp'];

    // Vérifier si les mots de passe correspondent
    if ($mdp !== $mdp2) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }
    // Utilisation de requêtes préparées pour éviter les injections SQL
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // L'utilisateur existe, on peut mettre à jour le mot de passe
        $hashed_password = password_hash($mdp, PASSWORD_DEFAULT); // Chiffrer le mot de passe
        $stmt = $conn->prepare("UPDATE users SET mdp = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        if ($stmt->execute()) {
            echo "Mot de passe modifié avec succès.";
        } else {
            echo "Erreur lors de la modification du mot de passe.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/styleconnexion.css">
    <script>
            function valider() {
            let result = true; // On part du principe que le formulaire est valide
            // Supprimer les messages d'erreur existants
            const errorMessages = document.querySelectorAll(".erreur");
            errorMessages.forEach(message => message.remove());

            // Vérification de l'email
            const ctrlemail = document.getElementsByName("email")[0];
            const valmail = ctrlemail.value;
            const regex3 = /^[^\s][^\s@A-Z]+@[^\s@A-Z]+\.[^\s@A-Z]+$/;
            if (!regex3.test(valmail)) {
                const div = document.createElement("div");
                div.className = "erreur";
                div.innerHTML = "Veuillez entrer une adresse email valide";
                ctrlemail.parentElement.appendChild(div);
                ctrlemail.style.borderColor = "red";
                result = false;
            }

            // Vérification du mot de passe
            const ctrlmdp = document.getElementsByName("mdp")[0];
            const lemdp = ctrlmdp.value;
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{10,}$/;
            if (!passwordRegex.test(lemdp)) {
                const div = document.createElement("div");
                div.className = "erreur";
                div.innerHTML = "Le mot de passe doit contenir au moins 10 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial";
                ctrlmdp.parentElement.appendChild(div);
                ctrlmdp.style.borderColor = "red";
                result = false;
            }

            // Vérification de la confirmation du mot de passe
            const ctrlconfmdp = document.getElementsByName("confmdp")[0];
            const leconfmdp = ctrlconfmdp.value;
            if (lemdp !== leconfmdp) {
                const div = document.createElement("div");
                div.className = "erreur";
                div.innerHTML = "Les mots de passe ne sont pas identiques";
                ctrlconfmdp.parentElement.appendChild(div);
                ctrlconfmdp.style.borderColor = "red";
                result = false;
            }

            return result; // On retourne le résultat de la validation
        }
    </script>

</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h1>Modifier le mot de passe</h1>
    <form action="mdpmodif.php" method="post" onsubmit="return valider() ">
    <p><input type="email" name="email" id="email" placeholder="Entez votre adresse mail "></p>
    <p><input type="password" name="mdp" id="mdp" placeholder="Entez le nouveau mot de passe "></p>
    <p><input type="password" name="confmdp" id="mdp2" placeholder="Confirmez votre mot de passe "></p>
    <p><input type="submit" name="submit" value="Modifier le mot de passe"></p>
    <div class="inscrire"><a href="index.php">Retour à la page d'accueil</a></div>
    <div class="inscrire"><a href="connexion.php">Retour à la page de connexion</a></div>
    <div class="inscrire">Vous n'avez pas de compte ? <a href="inscription.php">Inscrivez-vous !</a></div>
    </form>
    </div><br><br><br>
    <?php include 'footer.php'; ?>
</body>
</html>