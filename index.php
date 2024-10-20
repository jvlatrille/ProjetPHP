<?php
include 'commun.php';
include 'redimensionner_image.php';
session_start();

// On vérifie si l'utilisateur est connecté (genre selon l'affichage des infos s'il est connecté ou pas)
$estConnecte = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
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
                        <img src="' . $imageVignette . '" class="card-img-top" alt="' . $nomProduit . '">
                        <div class="card-body">
                            <h5 class="card-title">' . $nomProduit . '</h5>
                            <p class="card-text">' . $description . '</p>
                            <p class="card-text"><strong>' . $prixProduit . ' €</strong></p>
                            <p class="card-title">Niveau de douceur : </p>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: ' . $douceur . '%" aria-valuenow="' . $douceur . '" aria-valuemin="0" aria-valuemax="100"></div>
                            </div><br>';

                // On vérifie si le produit est déjà dans le panier
                $produitAjoute = false;
                $quantiteProduit = 0;

                if ($estConnecte && isset($_SESSION['panier'])) {
                    foreach ($_SESSION['panier'] as $item) {
                        if ($item['nomProd'] === $produit['nomProd']) {
                            $produitAjoute = true;
                            $quantiteProduit = $item['quantite'];
                            break;
                        }
                    }
                }

                if ($estConnecte) {
                    if ($produitAjoute) {
                        // Si le produit est déjà dans le panier, on propose d'en ajouter plus
                        echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . $nomProduit . '\', ' . $prixProduit . ', ' . $quantiteProduit . ')">';
                        echo '<input type="hidden" name="nomProd" value="' . $nomProduit . '">';
                        echo '<input type="hidden" name="prixProd" value="' . $prixProduit . '">';
                        echo '<button type="submit" class="btn btn-primary w-100">Ajouter ? (' . $quantiteProduit . ')</button>';
                        echo '</form>';
                    } else {
                        // Si le produit n'est pas encore dans le panier, on affiche le bouton "Ajouter"
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

        <!-- Formulaire pour ajouter un produit si l'utilisateur est connecté -->
        <?php if ($estConnecte): ?>
            <div class="col mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Créer un nouveau produit</h5>
                        <form method="post" action="creer_produit.php" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="nomProd" class="form-label">Nom du produit</label>
                                <input type="text" class="form-control" id="nomProd" name="nomProd" required>
                            </div>
                            <div class="mb-3">
                                <label for="prixProd" class="form-label">Prix du produit (€)</label>
                                <input type="number" step="0.01" class="form-control" id="prixProd" name="prixProd" required>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image du produit</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required onchange="previewImage(event)">
                                <img id="imagePreview" src="#" alt="Aperçu de l'image" style="display:none; margin-top:10px; max-width:100px; max-height:100px;">
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="douceur" class="form-label">Niveau de douceur</label>
                                <input type="range" class="form-range" id="douceur" name="douceur" min="0" max="100" value="50">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Créer le produit</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <?php afficherFooter(); ?>

    <!-- Un peu de JS pour la magie Bootstrap -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
