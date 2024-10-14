<?php
include 'commun.php';
session_start();

// Si le panier n'existe pas encore, on le crée
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Si on reçoit une demande de retrait d'un produit
if (isset($_POST['retirerProd'])) {
    $nomProd = $_POST['retirerProd'];

    // Parcourir le panier pour trouver le produit et réduire sa quantité
    foreach ($_SESSION['panier'] as $key => &$item) {
        if ($item['nomProd'] === $nomProd) {
            if ($item['quantite'] > 1) {
                // Réduire la quantité de 1
                $item['quantite'] -= 1;
            } else {
                // Si la quantité est 1, retirer complètement le produit
                unset($_SESSION['panier'][$key]);
            }
            break;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre panier</title>
    <link rel="stylesheet" href="css/styleCommun.css">
    <link rel="stylesheet" href="css/stylePanier.css">
</head>

<body>

    <?php afficherHeader(); ?>

    <div class="container">
        <h1>Votre Panier</h1>

        <?php if (!empty($_SESSION['panier'])): ?>
            <table class="table-panier">
                <tr>
                    <th>Produit</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($_SESSION['panier'] as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['nomProd']); ?></td>
                        <td><?php echo number_format($item['prixProd'], 2); ?> €</td>
                        <td><?php echo $item['quantite']; ?></td>
                        <td>
                            <form action="panier.php" method="post">
                                <input type="hidden" name="retirerProd" value="<?php echo htmlspecialchars($item['nomProd']); ?>">
                                <button type="submit" class="btn-retirer">Retirer une unité</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <h3>Total :
                <?php
                $total = 0;
                // On calcule le total du panier
                foreach ($_SESSION['panier'] as $product) {
                    $total += $product['prixProd'] * $product['quantite'];
                }
                echo number_format($total, 2);
                ?> €
            </h3>
        <?php else: ?>
            <p>Votre panier est vide.</p>
        <?php endif; ?>

        <a href="index.php" class="btn-ajouter">Continuer vos achats</a>
    </div>

    <?php afficherFooter(); ?>

</body>

</html>
