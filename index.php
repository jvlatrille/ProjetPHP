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
        // On charge le fichier XML contenant les produits
        $xml = simplexml_load_file('xml/produits.xml');

        echo '<div class="row">';
        // On parcourt chaque produit dans le fichier XML et on les affiche
        foreach ($xml->produit as $product) {
            echo '
            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <img src="' . htmlspecialchars($product->image) . '" class="card-img-top" alt="' . htmlspecialchars($product->nomProd) . '">
                    <div class="card-body">
                        <h5 class="card-title">' . htmlspecialchars($product->nomProd) . '</h5>
                        <p class="card-text">' . htmlspecialchars($product->description) . '</p>
                        <p class="card-text"><strong>' . htmlspecialchars($product->prixProd) . ' €</strong></p>';

            // Vérifier si le produit est déjà dans le panier
            $produitAjoute = false;
            $quantiteProduit = 0;

            if ($isLoggedIn && isset($_SESSION['panier'])) {
                foreach ($_SESSION['panier'] as $item) {
                    if ($item['nomProd'] === (string)$product->nomProd) {
                        $produitAjoute = true;
                        $quantiteProduit = $item['quantite'];
                        break;
                    }
                }
            }

            if ($isLoggedIn) {
                if ($produitAjoute) {
                    // Si le produit est déjà dans le panier, afficher le bouton "Ajouter ? (quantité)"
                    echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . htmlspecialchars($product->nomProd) . '\', ' . htmlspecialchars($product->prixProd) . ', ' . $quantiteProduit . ')">';
                    echo '<input type="hidden" name="nomProd" value="' . htmlspecialchars($product->nomProd) . '">';
                    echo '<input type="hidden" name="prixProd" value="' . htmlspecialchars($product->prixProd) . '">';
                    echo '<button type="submit" class="btn btn-primary w-100">Ajouter ? (' . $quantiteProduit . ')</button>';
                    echo '</form>';
                } else {
                    // Sinon, afficher le bouton "Ajouter au panier"
                    echo '<form method="post" action="index.php" onsubmit="return ajouterAuPanier(event, \'' . htmlspecialchars($product->nomProd) . '\', ' . htmlspecialchars($product->prixProd) . ', 0)">';
                    echo '<input type="hidden" name="nomProd" value="' . htmlspecialchars($product->nomProd) . '">';
                    echo '<input type="hidden" name="prixProd" value="' . htmlspecialchars($product->prixProd) . '">';
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
        ?>

    </div>

    <script>
        // Gérer l'ajout au panier et afficher le pop-up
        function ajouterAuPanier(event, nomProd, prixProd, quantiteActuelle) {
            event.preventDefault(); // Empêcher la soumission par défaut du formulaire

            // Ajouter le produit au panier via AJAX ou via PHP
            var formData = new FormData();
            formData.append('nomProd', nomProd);
            formData.append('prixProd', prixProd);

            fetch('index.php', {
                    method: 'POST',
                    body: formData
                }).then(response => response.json()) // Attendre la réponse en JSON avec la quantité mise à jour
                .then(data => {
                    // Une fois le produit ajouté, afficher le pop-up et mettre à jour le bouton

                    // Afficher le pop-up vert en bas à droite
                    var popup = document.createElement('div');
                    popup.className = 'popup-ajout-panier show';
                    popup.innerText = 'Ajouté au panier !';
                    document.body.appendChild(popup);

                    // Masquer le pop-up après 3 secondes
                    setTimeout(() => {
                        popup.classList.add('hide');
                        setTimeout(() => popup.remove(), 500); // Retirer complètement l'élément après l'animation
                    }, 3000);

                    // Mettre à jour le texte du bouton "Ajouter ? (quantité)"
                    var button = event.target.querySelector('button');
                    button.innerText = 'Ajouter ? (' + data.quantite + ')'; // Quantité mise à jour
                });

            return false;
        }
    </script>

    <?php afficherFooter(); ?>

    <!-- Bootstrap JS -->
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>