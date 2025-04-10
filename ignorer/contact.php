
<!-- <?php
// // Traitement du formulaire
// if(isset($_POST['bout'])) {
//     // Récupération des données
//     $name = htmlspecialchars($_POST['name']);
//     $email = htmlspecialchars($_POST['email']);
//     $subject = htmlspecialchars($_POST['subject']);
//     $message = htmlspecialchars($_POST['message']);
    
//     // Validation
//     $errors = [];
//     if(empty($name)) $errors[] = "Le nom est requis";
//     if(empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
//     if(empty($subject)) $errors[] = "Le sujet est requis";
//     if(empty($message)) $errors[] = "Le message est requis";
    
//     if(empty($errors)) {
//         // En-têtes email
//         $to = "kkonanothniel@gmail.com"; // À remplacer par l'email de réception
//         $headers = "From: $email\r\n";
//         $headers .= "Reply-To: $email\r\n";
//         $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
//         // Corps du message
//         $email_content = "Nom: $name\n";
//         $email_content .= "Email: $email\n\n";
//         $email_content .= "Message:\n$message";
        
//         // Envoi email
//         if(mail($to, $subject, $email_content, $headers)) {
//             $success = "Votre message a été envoyé avec succès!";
//         } else {
//             $errors[] = "Une erreur est survenue lors de l'envoi du message";
//         }
//     }
// } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nous Contacter</title>
   
    <style>
          body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 0;
            background-color: #f4f4f4;
        }
        h1 {
            color: #333;
        }
        form {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background: #5cb85c;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #4cae4c;
        }
    </style>
    
</head>
<body>
    <h1>Nous Contacter</h1>
    
    <?php if(isset($success)): ?>
        <div style="color: green; padding: 10px; background: #e6ffe6; border: 1px solid green; margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if(!empty($errors)): ?>
        <div style="color: red; padding: 10px; background: #ffebeb; border: 1px solid red; margin-bottom: 20px;">
            <?php foreach($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <form action="contact.php" method="post">
        <label for="name">Nom:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="subject">Sujet:</label>
        <input type="text" id="subject" name="subject" required><br><br>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea><br><br>

        <input type="submit" name="bout" value="Envoyer">
    </form>
    <?php include 'footer.php'; ?>
</body>
</html>
