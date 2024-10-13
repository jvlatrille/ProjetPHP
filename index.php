<?php
session_start(); // Démarrer la session

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site peluches</title>
    <link rel="stylesheet" href="css/styleCommun.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/stylePanier.css">
    <link rel="stylesheet" href="css/styleConnexion.css">
</head>

<body>
    <header>
        <nav class="menu">
            <div class="menu-left">
                <a href="index.php">Page d'accueil</a>
            </div>
            <div class="menu-center">
                <form action="search.php" method="get">
                    <input type="text" name="query" placeholder="Rechercher...">
                    <button type="submit">Rechercher</button>
                </form>
            </div>
            <div class="menu-right">
                <?php if ($isLoggedIn): ?>
                    <!-- Afficher le nom d'utilisateur au-dessus du bouton Se déconnecter -->
                    <p style="color: white;">Connecté en tant que <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <!-- Afficher le bouton Panier uniquement si connecté -->
                    <a href="panier.php" class="btn-panier">
                        <img src="img/panier.png" alt="Panier" style="width: 50px; height: 50px;">
                    </a>
                    <!-- Bouton Se déconnecter en rouge -->
                    <button class="btn-disconnect" onclick="location.href='logout.php'">Se déconnecter</button>
                <?php else: ?>
                    <!-- Afficher le bouton Se connecter si non connecté -->
                    <button class="btn-connect" onclick="openLoginPopup()">Se connecter</button>
                <?php endif; ?>

            </div>
        </nav>
    </header>

    <h1>Vente de peluches</h1>
    <p>Plein plein de pitites pilouches à vendre</p>

    <?php
    // On charge le fichier XML contenant les produits
    $xml = simplexml_load_file('xml/produits.xml');

    echo '<div class="listeProduits">';
    // On parcourt chaque produit dans le fichier XML et on les affiche
    foreach ($xml->produit as $product) {
        echo '<div class="produit">';
        echo '<img src="' . htmlspecialchars($product->image) . '" alt="' . htmlspecialchars($product->nomProd) . '">';
        echo '<h2>' . htmlspecialchars($product->nomProd) . '</h2>';
        echo '<p class="prixProd">' . htmlspecialchars($product->prixProd) . ' €</p>';
        echo '<p>' . htmlspecialchars($product->description) . '</p>';

        // Vérifier si le produit est déjà dans le panier
        $produitAjoute = false;
        $quantiteProduit = 0;

        if ($isLoggedIn && isset($_SESSION['panier'])) {
            foreach ($_SESSION['panier'] as $item) {
                if ($item['nomProd'] === (string)$product->nomProd) {
                    $produitAjoute = true;
                    $quantiteProduit = $item['quantite'];
                    break;
                }
            }
        }

        if ($isLoggedIn) {
            if ($produitAjoute) {
                // Si le produit est déjà dans le panier, afficher le bouton "Ajouter ? (quantité)"
                echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . htmlspecialchars($product->nomProd) . '\', ' . htmlspecialchars($product->prixProd) . ', ' . $quantiteProduit . ')">';
                echo '<input type="hidden" name="nomProd" value="' . htmlspecialchars($product->nomProd) . '">';
                echo '<input type="hidden" name="prixProd" value="' . htmlspecialchars($product->prixProd) . '">';
                echo '<button type="submit">Ajouter ? (' . $quantiteProduit . ')</button>';
                echo '</form>';
            } else {
                // Sinon, afficher le bouton "Ajouter au panier"
                echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . htmlspecialchars($product->nomProd) . '\', ' . htmlspecialchars($product->prixProd) . ', 0)">';
                echo '<input type="hidden" name="nomProd" value="' . htmlspecialchars($product->nomProd) . '">';
                echo '<input type="hidden" name="prixProd" value="' . htmlspecialchars($product->prixProd) . '">';
                echo '<button type="submit">Ajouter au panier</button>';
                echo '</form>';
            }
        } else {
            echo '<p class="message-ajout-panier">Connectez-vous pour ajouter au panier</p>';
        }

        echo '</div>';
    }
    echo '</div>';
    ?>

    <script>
        // Gérer l'ajout au panier et afficher le pop-up
        function ajouterAuPanier(event, nomProd, prixProd, quantiteActuelle) {
            event.preventDefault(); // Empêcher la soumission par défaut du formulaire

            // Ajouter le produit au panier via AJAX ou via PHP
            var formData = new FormData();
            formData.append('nomProd', nomProd);
            formData.append('prixProd', prixProd);

            fetch('index.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json()) // Attendre la réponse en JSON avec la quantité mise à jour
                .then(data => {
                    // Une fois le produit ajouté, afficher le pop-up et mettre à jour le bouton

                    // Afficher le pop-up vert en bas à droite
                    var popup = document.createElement('div');
                    popup.className = 'popup-ajout-panier show';
                    popup.innerText = 'Ajouté au panier !';
                    document.body.appendChild(popup);

                    // Masquer le pop-up après 3 secondes
                    setTimeout(() => {
                        popup.classList.add('hide');
                        setTimeout(() => popup.remove(), 500); // Retirer complètement l'élément après l'animation
                    }, 3000);

                    // Mettre à jour le texte du bouton "Ajouter ? (quantité)"
                    var button = event.target.querySelector('button');
                    button.innerText = 'Ajouter ? (' + data.quantite + ')'; // Quantité mise à jour
                });

            return false;
        }
    </script>


    <?php
    // Si la méthode de requête est POST et que les champs nomProd et prixProd sont définis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomProd'], $_POST['prixProd'])) {
        // Si le panier n'existe pas encore dans la session, on le crée
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        // Vérifier si le produit est déjà dans le panier
        $produitExistant = false;
        foreach ($_SESSION['panier'] as &$item) {
            if ($item['nomProd'] === $_POST['nomProd']) {
                $item['quantite'] += 1;
                $produitExistant = true;
                break;
            }
        }

        // Si le produit n'est pas encore dans le panier, l'ajouter
        if (!$produitExistant) {
            $_SESSION['panier'][] = [
                'nomProd' => $_POST['nomProd'],
                'prixProd' => $_POST['prixProd'],
                'quantite' => 1
            ];
        }

        // Afficher un message de confirmation
        echo '<div id="popup" class="popup-message">Produit ajouté au panier !</div>';
    }
    ?>

    <!-- Popup de connexion -->
    <div id="loginPopup" class="login-popup" style="display: <?php echo isset($_GET['loginError']) ? 'block' : 'none'; ?>;">
        <div class="login-popup-content">
            <span class="close" onclick="closeLoginPopup()">&times;</span>
            <h2>Connexion</h2>
            <form action="connexion.php" method="POST">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>

            <!-- Si erreur de connexion, afficher le message d'erreur -->
            <?php if (isset($_GET['loginError'])): ?>
                <p style="color:red;">Informations incorrectes</p>
                <a href="#" onclick="openSignupPopup()">Pas de compte ? Créez-en un !</a> <!-- Lien pour ouvrir le popup de création de compte -->
            <?php endif; ?>
        </div>
    </div>


    <!-- Popup de création de compte -->
    <div id="signupPopup" class="signup-popup" style="display: none;">
        <div class="signup-popup-content">
            <span class="close" onclick="closeSignupPopup()">&times;</span>
            <h2>Créer un compte</h2>
            <form action="creer_utilisateur.php" method="POST">
                <input type="text" name="new_username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="new_password" placeholder="Mot de passe" required>
                <button type="submit">Créer un compte</button>
            </form>
        </div>
    </div>



    <script src="js/scriptConnexion.js"></script>
</body>

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
        event.preventDefault(); // Empêcher la redirection par défaut
        document.getElementById("contactPopup").style.display = "block";
    }

    // Fermer le popup de contact
    function closeContactPopup() {
        document.getElementById("contactPopup").style.display = "none";
    }

    // Ouvrir le popup de politique de confidentialité
    function openPrivacyPopup(event) {
        event.preventDefault(); // Empêcher la redirection par défaut
        document.getElementById("privacyPopup").style.display = "block";
    }

    // Fermer le popup de politique de confidentialité
    function closePrivacyPopup() {
        document.getElementById("privacyPopup").style.display = "none";
    }
</script>

</html>