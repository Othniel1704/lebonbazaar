<?php
session_start(); // Démarrer la session pour stocker les informations de l'utilisateur
require 'config.php'; // Inclure la configuration de la base de données
require 'navbar.php'; // Inclure la barre de navigation
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php"); // Rediriger vers la page de connexion si non connecté
    exit();
}

// Récupérer les derniers messages reçus de chaque utilisateur concernant vos annonces
$userId = $_SESSION['id'];
$messageQuery = $conn->prepare("
    SELECT m.*, u.email AS sender_email, a.titre AS annonce_titre
    FROM messages m
    JOIN users u ON m.expediteur_id = u.id
    JOIN annonces a ON m.annonce_id = a.id
    WHERE m.destinataire_id = ?
    AND m.date_envoi = (
        SELECT MAX(date_envoi)
        FROM messages
        WHERE destinataire_id = ?
        AND expediteur_id = m.expediteur_id
        AND annonce_id = m.annonce_id
    )
    ORDER BY m.date_envoi DESC;
");

$messageQuery->bind_param("ii", $userId, $userId);
$messageQuery->execute();
$messageResult = $messageQuery->get_result();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>    
    <main class="messagerie-container">
        <div class="messagerie-header">
            <h1>Boîte de Messagerie</h1>
        </div>

        <div class="message-list">
            <?php if ($messageResult->num_rows > 0): ?>
                <?php while ($message = $messageResult->fetch_assoc()): ?>
                    <a href="chat.php?expediteur_id=<?php echo $message['expediteur_id']; ?>&annonce_id=<?php echo $message['annonce_id']; ?>" class="message-thread-card">
                        <div class="thread-avatar">
                            <?php echo strtoupper(substr($message['sender_email'], 0, 1)); ?>
                        </div>
                        <div class="thread-content">
                            <h3><?php echo htmlspecialchars($message['sender_email']); ?></h3>
                            <p><?php echo htmlspecialchars_decode(htmlspecialchars($message['contenu'])); ?></p>
                            <span class="thread-product">
                                <i class="fas fa-tag"></i> <?php echo htmlspecialchars($message['annonce_titre']); ?>
                            </span>
                        </div>
                        <div class="thread-meta">
                            <span><?php echo date('d/m/Y', strtotime($message['date_envoi'])); ?></span><br>
                            <span><?php echo date('H:i', strtotime($message['date_envoi'])); ?></span>
                        </div>
                    </a>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert info">Vous n'avez aucun message pour le moment.</div>
            <?php endif; ?>
        </div>
    </main>

    <?php require 'footer.php'; ?>
</body>
</html>
