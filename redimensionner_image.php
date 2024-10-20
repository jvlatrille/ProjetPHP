<?php
function creerVignetteSiNecessaire($imagePath, $largeurVignette = 100, $hauteurVignette = 100) {
    $infoImage = pathinfo($imagePath);
    $vignettePath = $infoImage['dirname'] . '/vignette_' . $infoImage['basename'];

    // Vérifier si la vignette existe déjà
    if (!file_exists($vignettePath)) {
        // Créer la vignette si elle n'existe pas
        list($largeurOriginale, $hauteurOriginale) = getimagesize($imagePath);

        // Charger l'image source selon son type
        $imageSource = null;
        switch (strtolower($infoImage['extension'])) {
            case 'jpg':
            case 'jpeg':
                $imageSource = imagecreatefromjpeg($imagePath);
                break;
            case 'png':
                $imageSource = imagecreatefrompng($imagePath);
                break;
            default:
                return $imagePath; // Si l'image n'est ni JPG, ni PNG, retourne l'image originale
        }

        // Créer une nouvelle image pour la vignette
        $vignette = imagecreatetruecolor($largeurVignette, $hauteurVignette);

        // Redimensionner l'image source dans la vignette
        imagecopyresampled(
            $vignette, $imageSource,
            0, 0, 0, 0,
            $largeurVignette, $hauteurVignette,
            $largeurOriginale, $hauteurOriginale
        );

        // Sauvegarder la vignette dans le même format que l'original
        switch (strtolower($infoImage['extension'])) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($vignette, $vignettePath);
                break;
            case 'png':
                imagepng($vignette, $vignettePath);
                break;
        }

        // Libérer la mémoire
        imagedestroy($imageSource);
        imagedestroy($vignette);
    }

    return $vignettePath; // Retourner le chemin de la vignette
}
?>
