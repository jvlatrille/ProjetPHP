<?php
include 'commun.php';
session_start();

// Récupérer le nom du produit à partir de l'URL
if (isset($_GET['nomProd'])) {
    $nomProduit = urldecode($_GET['nomProd']);

    // Charger le fichier JSON contenant les produits
    $fichierJson = file_get_contents('json/produits.json');
    $donnees = json_decode($fichierJson, true); // On convertit le JSON en tableau associatif

    // Vérifier si le produit existe
    $produitTrouve = null;
    foreach ($donnees['produits'] as $produit) {
        if (htmlspecialchars($produit['nomProd']) === $nomProduit) {
            $produitTrouve = $produit;
            break;
        }
    }

    if ($produitTrouve) {
        // Détails du produit
        $nomProduit = htmlspecialchars($produitTrouve['nomProd']);
        $prixProduit = htmlspecialchars($produitTrouve['prixProd']);
        $image = htmlspecialchars($produitTrouve['image']);
        $description = htmlspecialchars($produitTrouve['description']);
        $douceur = htmlspecialchars($produitTrouve['douceur']);
    } else {
        // Si le produit n'existe pas, rediriger vers la page principale
        header('Location: index.php');
        exit();
    }
} else {
    // Si aucun produit n'est spécifié dans l'URL, rediriger vers la page principale
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit : <?php echo $nomProduit; ?></title>

    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php afficherHeader(); ?>

    <div class="container mt-4">
        <h1 class="text-center"><?php echo $nomProduit; ?></h1>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <img src="<?php echo $image; ?>" class="card-img-top" alt="<?php echo $nomProduit; ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $nomProduit; ?></h5>
                        <p class="card-text"><?php echo $description; ?></p>
                        <p class="card-text"><strong><?php echo $prixProduit; ?> €</strong></p>
                        <p class="card-title">Niveau de douceur : </p>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" style="width: <?php echo $douceur; ?>%" aria-valuenow="<?php echo $douceur; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <br>

                        <!-- Bouton pour revenir à l'accueil -->
                        <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php afficherFooter(); ?>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
