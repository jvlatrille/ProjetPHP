<?php
include 'commun.php';
include 'redimensionner_image.php';
session_start();

// On vérifie si l'utilisateur est connecté
$estConnecte = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// On vérifie si l'utilisateur est root
$estRoot = isset($_SESSION['username']) && $_SESSION['username'] === 'root';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Peluches</title>

    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php afficherHeader(); ?>

    <div class="container mt-4">
        <h1 class="text-center">Vente de peluches</h1>
        <p class="text-center">Plein plein de pitites pilouches à vendre</p>

        <!-- Affichage des produits existants -->
        <?php
        // On charge le fichier JSON contenant les produits
        $fichierJson = file_get_contents('json/produits.json');
        $donnees = json_decode($fichierJson, true); // On convertit le JSON en tableau associatif

        // On vérifie que la clé "produits" existe et que c'est bien un tableau
        if (isset($donnees['produits']) && is_array($donnees['produits'])) {
            echo '<div class="row row-cols-1 row-cols-md-5 g-4">';

            // On parcourt chaque produit et on les affiche
            foreach ($donnees['produits'] as $produit) {
                $nomProduit = isset($produit['nomProd']) ? htmlspecialchars($produit['nomProd']) : 'Produit sans nom';
                $prixProduit = isset($produit['prixProd']) ? htmlspecialchars($produit['prixProd']) : '0.00';
                $image = isset($produit['image']) ? htmlspecialchars($produit['image']) : 'img/default.png';
                $description = isset($produit['description']) ? htmlspecialchars($produit['description']) : 'Pas de description';
                $douceur = isset($produit['douceur']) ? htmlspecialchars($produit['douceur']) : 0;

                // Générer ou récupérer la vignette de l'image
                $imageVignette = creerVignetteSiNecessaire($image);

                echo '
                <div class="col">
                    <div class="card mb-4 shadow-sm">
                        <a href="details_produit.php?nomProd=' . urlencode($nomProduit) . '">
                            <img src="' . $imageVignette . '" class="card-img-top" alt="' . $nomProduit . '">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">' . $nomProduit . '</h5>
                            <p class="card-text">' . $description . '</p>
                            <p class="card-text"><strong>' . $prixProduit . ' €</strong></p>
                            <p class="card-title">Niveau de douceur : </p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: ' . $douceur . '%" aria-valuenow="' . $douceur . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div><br>';

                // Ajouter le bouton de suppression pour root
                if ($estRoot) {
                    echo '
                    <form method="post" action="suppression_produit.php">
                        <input type="hidden" name="nomProd" value="' . htmlspecialchars($nomProduit) . '">
                        <button type="submit" class="btn btn-danger w-100">
                            Supprimer le produit
                        </button>
                    </form> <br>';
                }

                // On ajoute le bouton "Ajouter au panier" pour tout utilisateur connecté
                if ($estConnecte) {
                    // On vérifie si le produit est déjà dans le panier
                    $produitAjoute = false;
                    $quantiteProduit = 0;

                    if (isset($_SESSION['panier'])) {
                        foreach ($_SESSION['panier'] as $item) {
                            if ($item['nomProd'] === $produit['nomProd']) {
                                $produitAjoute = true;
                                $quantiteProduit = $item['quantite'];
                                break;
                            }
                        }
                    }

                    // Affichage du bouton "Ajouter au panier"
                    if ($produitAjoute) {
                        echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . $nomProduit . '\', ' . $prixProduit . ', ' . $quantiteProduit . ')">';
                        echo '<input type="hidden" name="nomProd" value="' . $nomProduit . '">';
                        echo '<input type="hidden" name="prixProd" value="' . $prixProduit . '">';
                        echo '<button type="submit" class="btn btn-primary w-100">Ajouter ? (' . $quantiteProduit . ')</button>';
                        echo '</form>';
                    } else {
                        echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . $nomProduit . '\', ' . $prixProduit . ', 0)">';
                        echo '<input type="hidden" name="nomProd" value="' . $nomProduit . '">';
                        echo '<input type="hidden" name="prixProd" value="' . $prixProduit . '">';
                        echo '<button type="submit" class="btn btn-success w-100">Ajouter au panier</button>';
                        echo '</form>';
                    }
                } else {
                    // Si l'utilisateur n'est pas connecté
                    echo '<p class="text-danger">Connectez-vous pour ajouter au panier</p>';
                }

                echo '
                        </div>
                    </div>
                </div>';
            }
            echo '</div>';
        } else {
            echo '<p>Aucun produit disponible pour le moment, dommage...</p>';
        }
        ?>

    </div>

    <?php afficherFooter(); ?>

    <!-- Un peu de JS pour la magie Bootstrap -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
