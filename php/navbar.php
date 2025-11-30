<header class="header-main">
    <nav role="navigation" class="nav-modern">
        <a href="index.php" class="logo-container">
            <img src="../image/logo.jpeg" alt="LebonBazaar" class="logo-img">
            <span class="logo-text">LebonBazaar</span>
        </a>
        
        <div class="nav-links">
            <?php if(isset($_SESSION['id'])): ?>
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="profil.php" class="nav-link">Mon Compte</a>
                <a href="messagerie.php" class="nav-link">Messagerie</a>
                <a href="ajout_annonce.php" class="btn-primary">Déposer une annonce</a>
                <a href="deconnexion.php" class="nav-link" style="color: var(--primary);">Déconnexion</a>
            <?php else: ?>
                <a href="index.php" class="nav-link">Accueil</a>
                <a href="aide.php" class="nav-link">Aide</a>
                <a href="connexion.php" class="nav-link">Connexion</a>
                <a href="inscription.php" class="btn-primary">Inscription</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

