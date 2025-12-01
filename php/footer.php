<footer>
    <div class="footer-content">
        <!-- À propos -->
        <div class="footer-section">
            <h3><i class="fas fa-info-circle"></i> À propos</h3>
            <p style="color: var(--gray-400); line-height: 1.6; margin-top: 1rem;">
                LebonBazaar est votre plateforme de confiance pour acheter et vendre des articles d'occasion en toute sécurité.
            </p>
            <div style="margin-top: 1.5rem;">
                <a href="index.php" style="display: inline-block; padding: 0.5rem 1.5rem; background: var(--primary); color: white; border-radius: var(--radius-md); text-decoration: none; transition: background 0.3s;">
                    <i class="fas fa-plus"></i> Déposer une annonce
                </a>
            </div>
        </div>

        <!-- Navigation rapide -->
        <div class="footer-section">
            <h3><i class="fas fa-compass"></i> Navigation</h3>
            <ul>
                <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="index.php#annonces"><i class="fas fa-th"></i> Toutes les annonces</a></li>
                <li><a href="profil.php"><i class="fas fa-user"></i> Mon profil</a></li>
                <li><a href="messagerie.php"><i class="fas fa-envelope"></i> Messagerie</a></li>
                <li><a href="favoris.php"><i class="fas fa-heart"></i> Mes favoris</a></li>
            </ul>
        </div>

        <!-- Aide & Support -->
        <div class="footer-section">
            <h3><i class="fas fa-question-circle"></i> Aide & Support</h3>
            <ul>
                <li><a href="aide.php"><i class="fas fa-life-ring"></i> Centre d'aide</a></li>
                <li><a href="#"><i class="fas fa-shield-alt"></i> Conseils de sécurité</a></li>
                <li><a href="contact.php"><i class="fas fa-phone"></i> Nous contacter</a></li>
                <li><a href="#"><i class="fas fa-file-alt"></i> CGU</a></li>
                <li><a href="#"><i class="fas fa-lock"></i> Confidentialité</a></li>
            </ul>
        </div>

        <!-- Réseaux sociaux -->
        <div class="footer-section">
            <h3><i class="fas fa-share-alt"></i> Suivez-nous</h3>
            <p style="color: var(--gray-400); margin-bottom: 1rem;">Restez connecté avec nous sur les réseaux sociaux</p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #1877f2; color: white; border-radius: 50%; text-decoration: none; transition: transform 0.2s;" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #1da1f2; color: white; border-radius: 50%; text-decoration: none; transition: transform 0.2s;" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; border-radius: 50%; text-decoration: none; transition: transform 0.2s;" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #0077b5; color: white; border-radius: 50%; text-decoration: none; transition: transform 0.2s;" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <p style="margin: 0;">
                &copy; <?= date("Y") ?> <strong>LebonBazaar</strong>. Tous droits réservés.
            </p>
            <p style="margin: 0; color: var(--gray-500);">
                Fait avec <i class="fas fa-heart" style="color: var(--primary);"></i> en France
            </p>
        </div>
    </div>
</footer>

<style>
footer a:hover {
    transform: translateY(-2px);
}
</style>
