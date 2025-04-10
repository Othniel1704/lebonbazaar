<footer>
    <div class="footer-container">
        <div class="footer-section">
            <h4>Informations légales</h4>
            <ul>
                <li><a href="#">Conditions d'utilisation</a></li>
                <li><a href="#">Politique de cookies</a></li>
                <li><a href="#">Mentions légales</a></li>
                <li><a href="#">Politique de confidentialité</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Contact</h4>
            <ul>
                <li><a href="contact.php">Nous contacter</a></li>
                <li><a href="#">FAQ</a></li>
                <li><a href="#">Aide</a></li>
            </ul>
        </div>
        <div class="footer-section">
            <h4>Suivez-nous</h4>
            <ul>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">Twitter</a></li>
                <li><a href="#">Instagram</a></li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date("Y") ?> Le Bon bazaar. Tous droits réservés.</p>
    </div>
</footer>

<style>
.footer-container {
    background: #f8f8f8;
    padding: 20px;
    display: flex;
    justify-content: space-between;
}

.footer-section {
    flex: 1;
    margin: 0 10px;
}

.footer-section h4 {
    margin-bottom: 10px;
}

.footer-section ul {
    list-style-type: none;
    padding: 0;
}

.footer-section ul li {
    margin: 5px 0;
}

.footer-section a {
    text-decoration: none;
    color: #333;
}

.footer-bottom {
    text-align: center;
    padding: 10px 0;
    background: #e0e0e0;
}
</style>
