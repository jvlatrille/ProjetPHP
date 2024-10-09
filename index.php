<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste de Produits</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Gros titre pour le projet</h1>
    <p>Texte</p>

    <?php
    // Lie le fichier .json
    $json_data = file_get_contents('produits.json');

    // Convertir le JSON en tableau PHP
    $products = json_decode($json_data, true);

    // Afficher les produits
    echo '<div class="listeProduits">';
    foreach ($products as $product) {
        echo '<div class="produit">';
        echo '<img src="' . htmlspecialchars($product['image']) . '" alt="' . htmlspecialchars($product['nomProd']) . '">';
        echo '<h2>' . htmlspecialchars($product['nomProd']) . '</h2>';
        echo '<p class="prixProd">' . $product['prixProd']. ' €</p>';
        echo '<p>' . htmlspecialchars($product['description']) . '</p>';
        echo '<button>Ajouter au panier</button>';
        echo '</div>';
    }
    echo '</div>';
    ?>

</body>

</html>