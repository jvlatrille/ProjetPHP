<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Accès refusé. Vous devez être connecté.");
}

// Fonction pour créer une vignette
function creerVignette($fichierSource, $fichierDestination, $largeurVoulue, $hauteurVoulue) {
    list($largeurSource, $hauteurSource, $typeImage) = getimagesize($fichierSource);

    // Créer une nouvelle image vide aux dimensions voulues
    $vignette = imagecreatetruecolor($largeurVoulue, $hauteurVoulue);

    // Créer l'image à partir du fichier source selon le type
    switch ($typeImage) {
        case IMAGETYPE_JPEG:
            $imageSource = imagecreatefromjpeg($fichierSource);
            break;
        case IMAGETYPE_PNG:
            $imageSource = imagecreatefrompng($fichierSource);
            break;
        case IMAGETYPE_GIF:
            $imageSource = imagecreatefromgif($fichierSource);
            break;
        default:
            return false;
    }

    // Redimensionner l'image source vers la vignette
    imagecopyresampled($vignette, $imageSource, 0, 0, 0, 0, $largeurVoulue, $hauteurVoulue, $largeurSource, $hauteurSource);

    // Sauvegarder la vignette dans le bon format
    switch ($typeImage) {
        case IMAGETYPE_JPEG:
            imagejpeg($vignette, $fichierDestination);
            break;
        case IMAGETYPE_PNG:
            imagepng($vignette, $fichierDestination);
            break;
        case IMAGETYPE_GIF:
            imagegif($vignette, $fichierDestination);
            break;
    }

    // Libérer la mémoire
    imagedestroy($vignette);
    imagedestroy($imageSource);

    return true;
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

        // Vérification du téléchargement de l'image
        if ($_FILES["image"]["tmp_name"] != '') {
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
                            // Si tout est bon, déplacer l'image principale
                            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                                // Créer une vignette
                                $fichierVignette = $targetDir . 'vignette_' . basename($_FILES["image"]["name"]);
                                creerVignette($targetFile, $fichierVignette, 100, 100); // Redimensionner à 100x100 pixels

                                // Ajouter le produit dans le fichier JSON
                                $produitsJson = file_get_contents('json/produits.json');
                                $produitsArray = json_decode($produitsJson, true);

                                $nouveauProduit = [
                                    'nomProd' => $nomProd,
                                    'prixProd' => $prixProd,
                                    'image' => $fichierVignette, // Utiliser la vignette à la place de l'image principale
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
            echo "Aucune image n'a été téléchargée.";
        }
    } else {
        echo "Tous les champs sont requis.";
    }
}


?>