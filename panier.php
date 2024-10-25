<?php
include 'commun.php';
session_start();
$total = 0;

// Si le panier n'existe pas encore, on le crée (vide)
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Si une demande d'ajout de produit au panier a été faite
if (isset($_POST['nomProd'])) {
    $nomProduit = $_POST['nomProd'];
    $prixProduit = (float)$_POST['prixProd'];

    // On cherche dans le panier si ce produit existe déjà
    $produitExisteDeja = false;
    foreach ($_SESSION['panier'] as &$produit) {
        if ($produit['nomProd'] === $nomProduit) {
            $produit['quantite'] += 1; // Si le produit existe déjà, on augmente la quantité
            $produitExisteDeja = true;
            break;
        }
    }
    unset($produit); // Libérer la référence

    // Si le produit n'existe pas encore dans le panier, on l'ajoute
    if (!$produitExisteDeja) {
        $_SESSION['panier'][] = [
            'nomProd' => $nomProduit,
            'prixProd' => $prixProduit,
            'quantite' => 1
        ];
    }
}

// Si ya une demande de retrait d'un produit
if (isset($_POST['retirerProd'])) {
    $nomProduit = $_POST['retirerProd']; // Récupérer le nom du produit à retirer

    // Parcourir le panier pour trouver le produit et réduire sa quantité de 1
    foreach ($_SESSION['panier'] as $cle => &$article) {
        if ($article['nomProd'] === $nomProduit) {
            if ($article['quantite'] > 1) {
                // Si la quantité est supérieure à 1, on réduit de 1
                $article['quantite'] -= 1;
            } else {
                // Si la quantité est de 1, on retire complètement le produit
                unset($_SESSION['panier'][$cle]);
            }
            break;
        }
    }
    unset($article); // Libérer la référence

    // Réindexer le tableau après suppression d'un produit
    $_SESSION['panier'] = array_values($_SESSION['panier']);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre panier</title>
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php afficherHeader(); // Affichage du header 
    ?>

    <div class="container mt-4">
        <h1 class="text-center">Votre Panier</h1>

        <?php if (!empty($_SESSION['panier'])): // Si le panier n'est pas vide 
        ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Produit</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_SESSION['panier'] as $article): // Parcourir chaque produit du panier 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($article['nomProd']); ?></td>
                                <td><?php echo number_format($article['prixProd'], 2); ?> €</td>
                                <td><?php echo $article['quantite']; ?></td>
                                <td>
                                    <form action="panier.php" method="post">
                                        <input type="hidden" name="retirerProd" value="<?php echo htmlspecialchars($article['nomProd']); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Retirer une unité</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <h3>Total :
                    <span class="text-success">
                        <?php
                        // On calcule le total du panier
                        foreach ($_SESSION['panier'] as $produit) {
                            $total += $produit['prixProd'] * $produit['quantite'];
                        }
                        echo number_format($total, 2);

                        $_SESSION['total_panier'] = $total;
                        ?> €
                    </span>
                </h3>
            </div>

        <?php else: ?>
            <p class="text-center text-muted">Votre panier est vide.</p>
        <?php endif; ?>

        <div class="text-center mt-4">
            
            <a href="index.php" class="btn btn-success">Continuer vos achats</a>
            <?php
            if($total != 0){
                echo '<a href="paiement.php" class="btn btn-success">Procéder au paiement</a>';}
            ?>
        </div>
    </div>

    <?php afficherFooter(); ?>

    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>