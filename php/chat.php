<?php
session_start();
require 'config.php';


// Afficher un message de succès si un message a été envoyé
if (isset($_SESSION['message_sent'])) {
    echo '<div class="alert alert-success">Message envoyé avec succès!</div>';
    unset($_SESSION['message_sent']);
}

// Vérifier si l'utilisateur est connecté
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['id'])) {
    die("Vous devez être connecté pour utiliser le chat.");
}

// Récupérer l'ID de l'annonce
$annonce_id = intval($_GET['annonce_id']);
$receiver_id = intval($_GET['expediteur_id']);

// Gestion de l'envoi de message
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF invalide.");
    }
    $message = htmlspecialchars($_POST['message']);
    $sender_id = $_SESSION['id'];
    if (!empty($message)) {
        $sql = "INSERT INTO messages (expediteur_id, destinataire_id, contenu, annonce_id) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisi", $sender_id, $receiver_id, $message, $annonce_id);
        $stmt->execute();
        
        // Après l'insertion du message, avant la redirection
        $_SESSION['message_sent'] = true;
        header("Location: chat.php?annonce_id=" . $annonce_id . "&expediteur_id=" . $receiver_id);
        exit();
    }
}

// Vérifier que l'annonce existe
$sql = "SELECT * FROM annonces WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $annonce_id);
$stmt->execute();
$result = $stmt->get_result();
$annonce = $result->fetch_assoc();

if (!$annonce) {
    die("Annonce non trouvée.");
}

// Vérifier que l'expéditeur existe
$receiverCheck = $conn->prepare("SELECT * FROM users WHERE id = ?");
$receiverCheck->bind_param("i", $receiver_id);
$receiverCheck->execute();
$res=$receiverCheck->get_result();
$receiver = $res->fetch_assoc();
if ($res->num_rows === 0) {
    die("Expéditeur introuvable.");
}

// Récupérer l'historique des messages entre l'utilisateur connecté et l'expéditeur
$sql = "SELECT m.*, 
            u1.prenom as expediteur_pseudo,
            u2.prenom as destinataire_pseudo 
        FROM messages m
        LEFT JOIN users u1 ON m.expediteur_id = u1.id
        LEFT JOIN users u2 ON m.destinataire_id = u2.id
        WHERE m.annonce_id = ? 
        AND ((m.expediteur_id = ? AND m.destinataire_id = ?) 
        OR (m.expediteur_id = ? AND m.destinataire_id = ?))
        ORDER BY m.date_envoi ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $annonce_id, $_SESSION['id'], $receiver_id, $receiver_id, $_SESSION['id']);
$stmt->execute();
$messages = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Chat - Le Bon Coin</title>
    <link rel="stylesheet" href="../css/message.css">
</head>
<body>
    <?php require 'navbar.php'; ?>
    <main class="container">
         <!-- Section messagerie -->
         <section class="messagerie">
        <p><a href="messagerie.php">Accéder à la messagerie</a></p>
            <h2 >Chat</h2>
            <div class="messages-container">
                <?php if (isset($_SESSION['id'])): ?>
                    <?php if ($messages->num_rows > 0): ?>
                        <?php while($message = $messages->fetch_assoc()): ?>
                            <div class="message <?= $message['expediteur_id'] == $_SESSION['id'] ? 'sent' : 'received' ?>">
                                <div class="message-header">
                                    <span class="author"><?= htmlspecialchars($message['expediteur_pseudo']) ?></span>
                                    <span class="date"><?= date('H:i - d/m/y', strtotime($message['date_envoi'])) ?></span>
                                </div>
                                <p class="content"><?= nl2br(htmlspecialchars_decode(htmlspecialchars($message['contenu']))) ?></p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-messages">Aucun message échangé pour cette annonce</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="no-messages">Aucun message échangé pour cette annonce</p>
                <?php endif; ?>
            </div>
            <script>
                
            </script>

            <form id="messageForm" action="" method="POST" class="message-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <?php if (isset($message_id)): ?>
                    <input type="hidden" name="receiver_id" value="<?= $message_id ?>">
                <?php else: ?>
                <input type="hidden" name="receiver_id" value="<?= $annonce['user_id'] ?>">
                <?php endif; ?>
                <label for="messageInput" class="sr-only">Votre message</label>
                <textarea id="messageInput" name="message" placeholder="Votre message..." required></textarea>
                <button type="submit" name="envoyer" class="btn">Envoyer</button>
            </form>
            <script>
                document.getElementById('messageForm').addEventListener('submit', function(event) {
                    <?php if (!isset($_SESSION['id'])): ?>
                        event.preventDefault();
                        alert('Connectez-vous pour contacter l\'annonceur');
                    <?php endif; ?>
                });
                // Défilement automatique vers le dernier message
                window.addEventListener('DOMContentLoaded', () => {
                    const container = document.querySelector('.messages-container');
                    container.scrollTop = container.scrollHeight;
                });

                // Empêcher la soumission vide
                document.getElementById('messageForm').addEventListener('submit', (e) => {
                    const textarea = e.target.querySelector('textarea');
                    if (textarea.value.trim() === '') {
                        e.preventDefault();
                        alert('Veuillez écrire un message avant d\'envoyer');
                    }
                });
            </script>
        </section>
   
    </main>
    <?php require 'footer.php'; ?>
</body>
</html>
