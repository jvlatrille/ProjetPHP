<?php
// fichier commun.php

function afficherHeader() {
    $isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : '';

    echo '
    <header>
        <nav class="navbar navbar-expand-lg bg-primary">
            <div class="container-fluid">
                <a href="index.php">Page d\'accueil</a>
            </div>
            <div class="menu-center">
                <form action="search.php" method="get">
                    <input class="form-control me-sm-2" type="text" name="query" placeholder="Rechercher...">
                    <button class="btn btn-secondary my-2 my-sm-0" type="submit">Rechercher</button>
                </form>
            </div>
            <div class="menu-right">';

    if ($isLoggedIn) {
        echo "<p style='color: white;'>Utilisateur :<br> $username</p>";
        echo '
            <a href="panier.php" class="btn-panier">
                <img src="img/panier.png" alt="Panier" style="width: 50px; height: 50px;">
            </a>
            <button class="btn btn-secondary my-2 my-sm-0" onclick="location.href=\'logout.php\'">Se déconnecter</button>';
    } else {
        echo '<button class="btn btn-secondary my-2 my-sm-0" onclick="openLoginPopup()">Se connecter</button>';
    }

    echo '</div>
        </nav>
    </header>';
}


function afficherFooter() {
    echo '
    <!-- Footer -->
    <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-left">
                <p>&copy; 2024 Peluches R Us. Tous droits réservés.</p>
            </div>
            <div class="footer-right">
                <a href="#" onclick="openContactPopup(event)">Contact</a> |
                <a href="#" onclick="openPrivacyPopup(event)">Politique de confidentialité</a>
            </div>
        </div>
    </footer>

    <!-- Popup Contact -->
    <div id="contactPopup" class="contact-popup" style="display: none;">
        <div class="contact-popup-content">
            <span class="close" onclick="closeContactPopup()">&times;</span>
            <h2>Contacts</h2>
            <p><strong>Tatiana NOVION :</strong> <a href="mailto:tnovion@iutbayonne.univ-pau.fr">tnovion@iutbayonne.univ-pau.fr</a></p>
            <p><strong>Jules VINET LATRILLE :</strong> <a href="mailto:jvlatrille@iutbayonne.univ-pau.fr">jvlatrille@iutbayonne.univ-pau.fr</a></p>
            <p><em>TD2 TP4</em></p>
        </div>
    </div>

    <!-- Popup Politique de confidentialité (recette de gâteau au chocolat) -->
    <div id="privacyPopup" class="privacy-popup" style="display: none;">
        <div class="privacy-popup-content">
            <span class="close" onclick="closePrivacyPopup()">&times;</span>
            <h2>Politique de confidentialité</h2>
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

    <script>
        // Ouvrir le popup de contact
        function openContactPopup(event) {
            event.preventDefault();
            document.getElementById("contactPopup").style.display = "block";
        }

        // Fermer le popup de contact
        function closeContactPopup() {
            document.getElementById("contactPopup").style.display = "none";
        }

        // Ouvrir le popup de politique de confidentialité
        function openPrivacyPopup(event) {
            event.preventDefault();
            document.getElementById("privacyPopup").style.display = "block";
        }

        // Fermer le popup de politique de confidentialité
        function closePrivacyPopup() {
            document.getElementById("privacyPopup").style.display = "none";
        }
    </script>';
}
?>
