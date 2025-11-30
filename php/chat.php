<?php
session_start();
require 'config.php';
require 'navbar.php'; 

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
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>    
    <main class="chat-container">
        <div class="chat-header">
            <div>
                <h2>Discussion</h2>
                <small>Concernant : <?= htmlspecialchars($annonce['titre']) ?></small>
            </div>
            <a href="messagerie.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>

        <div class="chat-messages" id="messagesContainer">
            <?php if (isset($_SESSION['id'])): ?>
                <?php if ($messages->num_rows > 0): ?>
                    <?php while($message = $messages->fetch_assoc()): ?>
                        <div class="message-bubble <?= $message['expediteur_id'] == $_SESSION['id'] ? 'sent' : 'received' ?>">
                            <?= nl2br(htmlspecialchars_decode(htmlspecialchars($message['contenu']))) ?>
                            <span class="message-time">
                                <?= date('H:i', strtotime($message['date_envoi'])) ?>
                            </span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-messages" style="text-align: center; color: var(--gray-500);">Aucun message échangé pour cette annonce</p>
                <?php endif; ?>
            <?php else: ?>
                <p class="no-messages">Connectez-vous pour voir les messages</p>
            <?php endif; ?>
        </div>

        <div class="chat-input-area">
            <form id="messageForm" action="" method="POST" class="chat-form">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <?php if (isset($message_id)): ?>
                    <input type="hidden" name="receiver_id" value="<?= $message_id ?>">
                <?php else: ?>
                <input type="hidden" name="receiver_id" value="<?= $annonce['user_id'] ?>">
                <?php endif; ?>
                
                <textarea id="messageInput" name="message" class="chat-input" placeholder="Écrivez votre message..." rows="1" required></textarea>
                <button type="submit" name="envoyer" class="chat-send-btn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </main>

    <script>
        document.getElementById('messageForm').addEventListener('submit', function(event) {
            <?php if (!isset($_SESSION['id'])): ?>
                event.preventDefault();
                alert('Connectez-vous pour contacter l\'annonceur');
            <?php endif; ?>
        });

        // Défilement automatique vers le dernier message
        window.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('messagesContainer');
            container.scrollTop = container.scrollHeight;
        });

        // Auto-resize textarea
        const textarea = document.getElementById('messageInput');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
            if(this.value === '') this.style.height = 'auto';
        });

        // Empêcher la soumission vide
        document.getElementById('messageForm').addEventListener('submit', (e) => {
            if (textarea.value.trim() === '') {
                e.preventDefault();
            }
        });
        
        // Submit on Enter (without Shift)
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('messageForm').dispatchEvent(new Event('submit'));
                document.getElementById('messageForm').submit();
            }
        });
    </script>

    <?php require 'footer.php'; ?>
</body>
</html>
