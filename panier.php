<?php
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

                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
                    <!-- Bouton Se déconnecter en rouge -->
                    <button class="btn-disconnect" onclick="location.href='logout.php'">Se déconnecter</button>
                <?php else: ?>
                    <!-- Bouton Se connecter en vert -->
                    <button class="btn-connect" onclick="openLoginPopup()">Se connecter</button>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <h1>Votre Panier</h1>

    <?php if (!empty($_SESSION['panier'])): ?>
        <table border="1">
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
                            <button type="submit">Retirer une unité</button>
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

    <a href="index.php">Continuer vos achats</a>

</body>

</html>