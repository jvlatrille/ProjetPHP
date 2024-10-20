<?php
session_start();

// Vérifier que l'utilisateur est connecté et est root
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['username'] !== 'root') {
    die("Accès refusé. Vous devez être root pour supprimer un produit.");
}

// Vérifier que le produit à supprimer est bien fourni
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nomProd'])) {
    $nomProd = htmlspecialchars($_POST['nomProd']);

    // Charger le fichier JSON contenant les produits
    $fichierJson = 'json/produits.json';
    $produitsJson = file_get_contents($fichierJson);
    $produitsArray = json_decode($produitsJson, true);

    // Chercher et supprimer le produit
    if (isset($produitsArray['produits']) && is_array($produitsArray['produits'])) {
        foreach ($produitsArray['produits'] as $key => $produit) {
            if ($produit['nomProd'] === $nomProd) {
                // Supprimer le produit du tableau
                unset($produitsArray['produits'][$key]);
                break;
            }
        }

        // Réindexer le tableau et enregistrer les modifications dans le fichier JSON
        $produitsArray['produits'] = array_values($produitsArray['produits']);
        file_put_contents($fichierJson, json_encode($produitsArray, JSON_PRETTY_PRINT));

        // Redirection après la suppression
        header('Location: index.php');
        exit();
    }
} else {
    echo "Aucun produit sélectionné pour suppression.";
}
?>
