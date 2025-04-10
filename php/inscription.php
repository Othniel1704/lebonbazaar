<script type="module" src="https://cdn.jsdelivr.net/npm/ldrs/dist/auto/bouncyArc.js"></script>

<?php
    if (isset($_POST["bout"])) {
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['mdp'];
        $tel = htmlspecialchars($_POST['tel']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $id = mysqli_connect("localhost", "root", "", "leboncoindb");
            if (!$id) {
                throw new Exception("Erreur de connexion à la base de données");
            }

            // Préparer la requête de vérification
            $stmt = $id->prepare("SELECT * FROM users WHERE email=? OR tel=?");
            $stmt->bind_param("ss", $email, $tel);
            $stmt->execute();
            $result = $stmt->get_result();
            $use = $result->fetch_assoc();

            if ($use && $use["email"] == $email) {
                echo '<div class="alert error">Le mail ' . htmlspecialchars($email) . ' est déjà utilisé</div>';
            } elseif ($use && $use["tel"] == $tel) {
                echo '<div class="alert error">Le numéro ' . htmlspecialchars($tel) . ' est déjà utilisé</div>';
            } else {
                $stmt = $id->prepare("INSERT INTO users (prenom, email, mdp, tel) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $prenom, $email, $hashed_password, $tel);
                
                if ($stmt->execute()) {
                    echo '<div class="alert success">
                        Inscription réussie, vous allez être redirigé pour vous connecter<br>
                        <l-bouncy-arc size="70" speed="1.65" color="red"></l-bouncy-arc>
                    </div>';
                    header("refresh:3;url=connexion.php");
                } else {
                    throw new Exception("Erreur lors de l'inscription");
                }
            }
            mysqli_close($id);
        } catch (Exception $e) {
            echo '<div class="alert error">' . $e->getMessage() . '</div>';
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../css/styleconnexion.css">

    <script src="inscription.js"></script>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container">
    <h1>Inscription</h1>
    <form action="" method="POST" onsubmit="return valider()">
        <p><input type="text" name="prenom" placeholder="Prénom" required></p>
        <p><input type="email" name="email" placeholder="Email" required></p>
        <p><input type="text" name="tel" placeholder="Numéro de téléphone" required></p>
        <p><input type="password" name="mdp" placeholder="Mot de passe" required></p>
        <p><input type="password" name="confmdp" placeholder="Confirmation de mot de passe" required></p>
        <p><input type="submit" name="bout" value="S'inscrire"></p>
        <div class="inscrire">Vous avez déjà un compte ? <a href="connexion.php">Connectez-vous !</a></div>
    </form>
    </div><br><br><br>
    <?php include 'footer.php'; ?>
</body>
</html>
