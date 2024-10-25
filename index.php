<?php
// Inclusion des fichiers nécessaires
include 'commun.php';
include 'redimensionner_image.php';
session_start();

// On vérifie si l'utilisateur est connecté
$estConnecte = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// On vérifie si l'utilisateur est root
$estRoot = isset($_SESSION['username']) && $_SESSION['username'] === 'root';

// Ajouter un produit au panier
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nomProd']) && isset($_POST['prixProd'])) {
    $nomProduit = $_POST['nomProd'];
    $prixProduit = (float)$_POST['prixProd'];

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    $produitAjoute = false;
    foreach ($_SESSION['panier'] as &$article) {
        if ($article['nomProd'] === $nomProduit) {
            $article['quantite'] += 1;
            $produitAjoute = true;
            break;
        }
    }

    if (!$produitAjoute) {
        $_SESSION['panier'][] = [
            'nomProd' => $nomProduit,
            'prixProd' => $prixProduit,
            'quantite' => 1
        ];
    }
}
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

        <?php
        // Récupérer le terme de recherche s'il existe
        $termeRecherche = isset($_GET['query']) ? strtolower(trim($_GET['query'])) : '';

        // Charger le fichier JSON contenant les produits
        $fichierJson = file_get_contents('json/produits.json');
        $donnees = json_decode($fichierJson, true);

        // On vérifie que la clé "produits" existe et que c'est bien un tableau
        if (isset($donnees['produits']) && is_array($donnees['produits'])) {
            echo '<div class="row row-cols-1 row-cols-md-5 g-4">';

            // Parcourir les produits et afficher seulement ceux qui correspondent à la recherche
            foreach ($donnees['produits'] as $produit) {
                $nomProduit = isset($produit['nomProd']) ? htmlspecialchars($produit['nomProd']) : 'Produit sans nom';

                // Vérifier si le produit correspond au terme de recherche
                if ($termeRecherche === '' || strpos(strtolower($nomProduit), $termeRecherche) !== false) {
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

                    if ($estConnecte) {
                        echo '<form method="post" action="index.php">';
                        echo '<input type="hidden" name="nomProd" value="' . $nomProduit . '">';
                        echo '<input type="hidden" name="prixProd" value="' . $prixProduit . '">';
                        echo '<button type="submit" class="btn btn-success w-100">Ajouter au panier</button>';
                        echo '</form><br>';
                    } else {
                        echo '<p class="text-danger">Connectez-vous pour ajouter au panier</p>';
                    }

                    if ($estRoot) {
                        echo '
                        <form method="post" action="suppression_produit.php">
                            <input type="hidden" name="nomProd" value="' . htmlspecialchars($nomProduit) . '">
                            <button type="submit" class="btn btn-danger w-100">
                                Supprimer le produit
                            </button>
                        </form>';
                    }

                    echo '
                            </div>
                        </div>
                    </div>';
                }
            }
            echo '</div>';
        } else {
            echo '<p>Aucun produit disponible pour le moment, dommage...</p>';
        }
        ?>
        <?php if ($estRoot): ?>
            <div class="col mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Créer un nouveau produit</h5>
                        <form method="post" action="creer_produit.php" enctype="multipart/form-data">
                            <input type="hidden" name="creerProduit" value="1">
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
                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
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

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>