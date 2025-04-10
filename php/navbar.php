<header class="header-main">
    <nav role="navigation" class="nav-modern">
        <img src="../image/logo.jpeg" alt="logo" class="logo-img">
        <a href="index.php" class="logo">Accueil</a>
        <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false">☰</button>
        <div class="nav-links">
            <?php if(isset($_SESSION['id'])): ?>
                <div class="dropdown">
                    <button class="dropdown-toggle" aria-expanded="false">
                        <i class="fas fa-user"></i> Mon Compte ▼
                    </button>
                    <div class="dropdown-menu">
                        <a href="profil.php"><i class="fas fa-user-circle"></i> Mon Profil</a>
                        <a href="ajout_annonce.php"><i class="fas fa-plus-circle"></i> Déposer une annonce</a>
                        <a href="profil.php?#favoris"><i class="fas fa-heart"></i> Mes Favoris</a>
                        <a href="messagerie.php"><i class="fas fa-envelope"></i> Messagerie</a>
                        <a href="deconnexion.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="connexion.php"><i class="fas fa-sign-in-alt"></i> Connexion</a>
                <a href="inscription.php"><i class="fas fa-user-plus"></i> Inscription</a>
                <a href="aide.php"><i class="fas fa-question-circle"></i> Aide</a>
            <?php endif; ?>
            
        </div>
    </nav>
</header>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.querySelector(".nav-toggle").addEventListener("click", function() {
        const navLinks = document.querySelector(".nav-links");
        navLinks.classList.toggle("active");
        this.setAttribute("aria-expanded", navLinks.classList.contains("active"));
    });

    const dropdownToggle = document.querySelector(".dropdown-toggle");
    if (dropdownToggle) {
        dropdownToggle.addEventListener("click", function() {
            const dropdownMenu = document.querySelector(".dropdown-menu");
            dropdownMenu.classList.toggle("open");
            this.setAttribute("aria-expanded", dropdownMenu.classList.contains("open"));
        });
    }
});
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    /* Header */
.header-main {
    background: linear-gradient(90deg, #ff4e50, #f9d423); 
    opacity: 0.9;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 1rem;
    position: sticky;
    top: 0;
    z-index: 1000;
}

.header-main nav {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    margin-right: auto;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    text-decoration: none;
}
.logo:hover {
    color: black;
    text-decoration: none;
}
.logo-img{
    width: 100px;
    height: 100px;
    object-fit: cover;
    border-radius: 100%;
    margin-right: 10px;
}

.nav-links {
    display: flex;
    gap: 1.5rem;
}

.nav-links a {
    color: var(--dark-color);
    text-decoration: none;
    font-weight: 500;
}

.nav-links a:hover {
    color: var(--primary-color);
}
.nav-home {
    font-size: 1.2rem;
    padding: 0.5rem 1rem;
    margin-right: auto;
    text-decoration: none;
    color: #333;
}
.nav-home:hover {
    color: var(--primary-color);
    text-decoration: none;
}

/* Navigation Toggle Button */
.nav-toggle {
    display: none;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--dark-color);
}

/* Dropdown Menu */
.dropdown {
    position: relative;
}

.dropdown-toggle {
    background: none;
    border: none;
    font-size: 1rem;
    font-weight: 500;
    color: var(--dark-color);
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
    z-index: 1000;
}

.dropdown-menu a {
    display: block;
    padding: 0.8rem 1.2rem;
    color: var(--dark-color);
    text-decoration: none;
    font-size: 0.9rem;
}

.dropdown-menu a:hover {
    background: var(--primary-color);
    color: white;
}

.dropdown:hover .dropdown-menu {
    display: block;
}@media (max-width: 768px) {
    
    .nav-links {
        display: none;
        flex-direction: column;
        gap: 1rem;
        background: white;
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 1rem;
    }

    .nav-links a {
        font-size: 1rem;
    }

    .nav-links.active {
        display: flex;
    }

    .nav-toggle {
        display: block;
    }
}

</style>

