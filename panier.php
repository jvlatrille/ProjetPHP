<?php
session_start();

// Si le panier n'existe pas encore, on le crée
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Si on reçoit des données de produit via POST, on les ajoute au panier
if (isset($_POST['nomProd'], $_POST['prixProd'])) {
    $product = [
        'nomProd' => $_POST['nomProd'],
        'prixProd' => $_POST['prixProd'],
        'quantite' => 1 // On commence avec une quantité de 1
    ];

    $_SESSION['panier'][] = $product; // On ajoute le produit au panier
}

// Si on reçoit une demande de retrait de produit
if (isset($_POST['retirer']) && isset($_POST['index'])) {
    $index = (int)$_POST['index'];
    array_splice($_SESSION['panier'], $index, 1); // On enlève le produit du panier
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
                <button class="btn-connect">Se connecter</button>
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
            <?php foreach ($_SESSION['panier'] as $index => $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['nomProd']); ?></td>
                    <td><?php echo number_format($product['prixProd'], 2); ?> €</td>
                    <td><?php echo $product['quantite']; ?></td>
                    <td>
                        <form action="panier.php" method="post">
                            <input type="hidden" name="index" value="<?php echo $index; ?>">
                            <button type="submit" name="retirer">Retirer</button>
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
