<?php
include 'commun.php';
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
    <title>Site Peluches</title>

    <!-- Bootstrap CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php afficherHeader(); ?>

    <div class="container mt-4">
        <h1 class="text-center">Vente de peluches</h1>
        <p class="text-center">Plein plein de pitites pilouches à vendre</p>

        <?php
        // On charge le fichier JSON contenant les produits
        $jsonFile = file_get_contents('json/produits.json');
        $data = json_decode($jsonFile, true); // Décoder le JSON en tableau associatif

        // S'assurer que la clé "produits" existe et est un tableau
        if (isset($data['produits']) && is_array($data['produits'])) {
            echo '<div class="row">';

            // On parcourt chaque produit dans le tableau JSON et on les affiche
            foreach ($data['produits'] as $product) {
                $nomProd = isset($product['nomProd']) ? htmlspecialchars($product['nomProd']) : 'Produit sans nom';
                $prixProd = isset($product['prixProd']) ? htmlspecialchars($product['prixProd']) : '0.00';
                $image = isset($product['image']) ? htmlspecialchars($product['image']) : 'img/default.png';
                $description = isset($product['description']) ? htmlspecialchars($product['description']) : 'Pas de description';

                echo '
                <div class="col-md-4">
                    <div class="card mb-4 shadow-sm">
                        <img src="' . $image . '" class="card-img-top" alt="' . $nomProd . '">
                        <div class="card-body">
                            <h5 class="card-title">' . $nomProd . '</h5>
                            <p class="card-text">' . $description . '</p>
                            <p class="card-text"><strong>' . $prixProd . ' €</strong></p>';

                // Vérifier si le produit est déjà dans le panier
                $produitAjoute = false;
                $quantiteProduit = 0;

                if ($isLoggedIn && isset($_SESSION['panier'])) {
                    foreach ($_SESSION['panier'] as $item) {
                        if ($item['nomProd'] === $product['nomProd']) {
                            $produitAjoute = true;
                            $quantiteProduit = $item['quantite'];
                            break;
                        }
                    }
                }

                if ($isLoggedIn) {
                    if ($produitAjoute) {
                        // Si le produit est déjà dans le panier, afficher le bouton "Ajouter ? (quantité)"
                        echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . $nomProd . '\', ' . $prixProd . ', ' . $quantiteProduit . ')">';
                        echo '<input type="hidden" name="nomProd" value="' . $nomProd . '">';
                        echo '<input type="hidden" name="prixProd" value="' . $prixProd . '">';
                        echo '<button type="submit" class="btn btn-primary w-100">Ajouter ? (' . $quantiteProduit . ')</button>';
                        echo '</form>';
                    } else {
                        // Sinon, afficher le bouton "Ajouter au panier"
                        echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . $nomProd . '\', ' . $prixProd . ', 0)">';
                        echo '<input type="hidden" name="nomProd" value="' . $nomProd . '">';
                        echo '<input type="hidden" name="prixProd" value="' . $prixProd . '">';
                        echo '<button type="submit" class="btn btn-success w-100">Ajouter au panier</button>';
                        echo '</form>';
                    }
                } else {
                    echo '<p class="text-danger">Connectez-vous pour ajouter au panier</p>';
                }

                echo '
                        </div>
                    </div>
                </div>';
            }
            echo '</div>';
        } else {
            echo '<p>Aucun produit disponible.</p>';
        }
        ?>

    </div>

    <?php afficherFooter(); ?>

    <!-- Bootstrap JS -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
