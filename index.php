<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site peluches</title>
    <link rel="stylesheet" href="style.css">
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
                <button class="btn-connect">Se connecter</button>
            </div>
        </nav>
    </header>


    <h1>Vente de peluches</h1>
    <p>Plein plein de pitites pilouches à vendre</p>

    <?php
    // Lire le fichier XML
    $xml = simplexml_load_file('produits.xml');

    echo '<div class="listeProduits">';
    foreach ($xml->produit as $product) {
        echo '<div class="produit">';
        echo '<img src="' . htmlspecialchars($product->image) . '" alt="' . htmlspecialchars($product->nomProd) . '">';
        echo '<h2>' . htmlspecialchars($product->nomProd) . '</h2>';
        echo '<p class="prixProd">' . htmlspecialchars($product->prixProd) . ' €</p>';
        echo '<p>' . htmlspecialchars($product->description) . '</p>';
        echo '<button>Ajouter au panier</button>';
        echo '</div>';
    }
    echo '</div>';
    ?>

</body>

</html>