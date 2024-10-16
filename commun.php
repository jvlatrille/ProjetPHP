<?php
function afficherHeader() {
    $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';

    echo '
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-primary">
            <div class="container-fluid">
                <a class="navbar-brand text-white" href="index.php">Page d\'accueil</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="#">Produits</a>
                        </li>
                    </ul>
                    <form class="d-flex" action="search.php" method="get">
                        <input class="form-control me-2" type="search" name="query" placeholder="Rechercher..." aria-label="Search">
                        <button class="btn btn-secondary" type="submit">Rechercher</button>
                    </form>
                </div>';

    if ($isLoggedIn) {
        echo '
        <div class="d-flex align-items-center ms-4">
            <span class="text-white me-3">Utilisateur : ' . $username . '</span>
            <a href="panier.php" class="me-3">
                <img src="img/panier.png" alt="Panier" style="width: 40px; height: 40px;">
            </a>
            <button class="btn btn-outline-light" onclick="location.href=\'logout.php\'">Se déconnecter</button>
        </div>';
    } else {
        echo '
        <button class="btn btn-outline-light ms-4" data-bs-toggle="modal" data-bs-target="#loginModal">Se connecter</button>';
    }

    echo '
            </div>
        </nav>
    </header>';

    // Afficher le pop-up d'erreur si une erreur de connexion est présente dans la session
    if (isset($_SESSION['loginError'])) {
        echo '
        <div class="alert alert-danger position-fixed bottom-0 end-0 m-3" role="alert">
            Informations incorrectes !
        </div>
        <script>
            setTimeout(function() {
                document.querySelector(".alert").remove();
            }, 3000); // Retirer le pop-up après 3 secondes
        </script>';
        unset($_SESSION['loginError']); // Effacer l'erreur après l'affichage
    }
}

function afficherFooter() {
    echo '
    <!-- Footer -->
    <footer class="bg-primary text-white py-3 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2024 Peluches R Us. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-white me-3" data-bs-toggle="modal" data-bs-target="#contactModal">Contact</a>
                    <a href="#" class="text-white" data-bs-toggle="modal" data-bs-target="#privacyModal">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Modal Se connecter -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Connexion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="connexion.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nom d\'utilisateur</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>';

    // Vérifier si une erreur de connexion est définie
    if (isset($_SESSION['loginError'])) {
        echo '<div class="alert alert-danger">Informations erronées. <a href="#" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-dismiss="modal">Créer un compte ?</a></div>';
        unset($_SESSION['loginError']); // Effacer l'erreur après l'affichage
    }

    echo '
                        <button type="submit" class="btn btn-primary">Se connecter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Créer un compte -->
    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="signupModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="signupModalLabel">Créer un compte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="creer_utilisateur.php" method="POST">
                        <div class="mb-3">
                            <label for="new_username" class="form-label">Nom d\'utilisateur</label>
                            <input type="text" class="form-control" id="new_username" name="new_username" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mot de passe</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Créer un compte</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Contact -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contacts</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Tatiana NOVION :</strong> <a href="mailto:tnovion@iutbayonne.univ-pau.fr">tnovion@iutbayonne.univ-pau.fr</a></p>
                    <p><strong>Jules VINET LATRILLE :</strong> <a href="mailto:jvlatrille@iutbayonne.univ-pau.fr">jvlatrille@iutbayonne.univ-pau.fr</a></p>
                    <p><em>TD2 TP4</em></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Politique de confidentialité -->
    <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="privacyModalLabel">Politique de confidentialité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Recette du gâteau au chocolat :</strong></p>
                    <ul>
                        <li>200g de chocolat noir</li>
                        <li>150g de beurre</li>
                        <li>150g de sucre</li>
                        <li>50g de farine</li>
                        <li>4 œufs</li>
                        <li>1 pincée de sel</li>
                    </ul>
                    <p>Faire fondre le chocolat avec le beurre. Mélanger les œufs et le sucre, ajouter la farine et la pincée de sel. Incorporer le chocolat fondu. Verser dans un moule et enfourner à 180°C pendant 20 minutes. Bon appétit !</p>
                </div>
            </div>
        </div>
    </div>';
}
?>
