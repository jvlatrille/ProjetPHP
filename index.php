<?php
session_start(); ?>

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
                <a href="panier.php" class="btn-panier">
                    <img src="img/panier.png" alt="Panier" style="width: 50px; height: 50px;">
                </a>
                <button class="btn-connect" onclick="openLoginPopup()">Se connecter</button>
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

        // Formulaire pour ajouter le produit au panier
        echo '<form method="post" action="index.php">';
        echo '<input type="hidden" name="nomProd" value="' . htmlspecialchars($product->nomProd) . '">';
        echo '<input type="hidden" name="prixProd" value="' . htmlspecialchars($product->prixProd) . '">';
        echo '<button type="submit">Ajouter au panier</button>';
        echo '</form>';

        echo '</div>';
    }
    echo '</div>';
    ?>

    <?php
    // Si la méthode de requête est POST et que les champs nomProd et prixProd sont définis
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nomProd'], $_POST['prixProd'])) {
        // Si le panier n'existe pas encore dans la session, on le crée
        if (!isset($_SESSION['panier'])) {
            $_SESSION['panier'] = [];
        }

        // On crée un tableau pour le produit avec son nom, prix et quantité
        $product = [
            'nomProd' => $_POST['nomProd'],
            'prixProd' => $_POST['prixProd'],
            'quantite' => 1
        ];
        // On ajoute le produit au panier
        $_SESSION['panier'][] = $product;

        // On affiche un message de confirmation
        echo '<div id="popup" class="popup-message">Produit ajouté au panier !</div>';
    }
    ?>
    <div id="loginPopup" class="login-popup">
        <div class="login-popup-content">
            <span class="close" onclick="closeLoginPopup()">&times;</span>
            <h2>Connexion</h2>
            <form action="connexion.php" method="post">
                <label for="identifiant">Identifiant :</label>
                <input type="text" id="identifiant" name="identifiant" required>
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
                <button type="submit">Se connecter</button>
            </form>
        </div>
    </div>

    <script src="js/scriptPanier.js"></script>
    <script src="js/scriptConnexion.js"></script>
</body>

</html>