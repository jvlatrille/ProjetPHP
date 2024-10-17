<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Accès refusé. Vous devez être connecté.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifier que tous les champs sont présents
    if (isset($_POST['nomProd'], $_POST['prixProd'], $_POST['description'], $_POST['douceur']) && isset($_FILES['image'])) {
        $nomProd = htmlspecialchars($_POST['nomProd']);
        $prixProd = (float)$_POST['prixProd'];
        $description = htmlspecialchars($_POST['description']);
        $douceur = (int)$_POST['douceur'];

        // Gestion de l'image
        $targetDir = "img/";
        $targetFile = $targetDir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Vérification du type de fichier
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            // Vérifier si le fichier existe déjà
            if (file_exists($targetFile)) {
                echo "Désolé, l'image existe déjà.";
            } else {
                // Limitation de la taille de l'image (par exemple 5Mo max)
                if ($_FILES["image"]["size"] > 5000000) {
                    echo "Désolé, l'image est trop volumineuse.";
                } else {
                    // Autoriser certains formats d'images
                    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
                        echo "Désolé, seuls les fichiers JPG, JPEG et PNG sont autorisés.";
                    } else {
                        // Si tout est bon, déplacer l'image
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                            // Ajouter le produit dans le fichier JSON
                            $produitsJson = file_get_contents('json/produits.json');
                            $produitsArray = json_decode($produitsJson, true);

                            $nouveauProduit = [
                                'nomProd' => $nomProd,
                                'prixProd' => $prixProd,
                                'image' => $targetFile,
                                'description' => $description,
                                'douceur' => $douceur
                            ];

                            // Ajouter le nouveau produit à la liste
                            $produitsArray['produits'][] = $nouveauProduit;

                            // Sauvegarder le fichier JSON
                            if (file_put_contents('json/produits.json', json_encode($produitsArray, JSON_PRETTY_PRINT))) {
                                echo "Produit ajouté avec succès.";
                                header('Location: index.php');
                                exit();
                            } else {
                                echo "Désolé, une erreur s'est produite lors de l'enregistrement du produit.";
                            }
                        } else {
                            echo "Désolé, une erreur s'est produite lors du téléchargement de l'image.";
                        }
                    }
                }
            }
        } else {
            echo "Le fichier sélectionné n'est pas une image.";
        }
    } else {
        echo "Tous les champs sont requis.";
    }
}
