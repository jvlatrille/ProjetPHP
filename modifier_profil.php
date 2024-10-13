<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $username = $_SESSION['username'];

    // Charger le fichier XML
    $xml = simplexml_load_file('xml/utilisateurs.xml');

    // Rechercher l'utilisateur et modifier son mot de passe
    foreach ($xml->utilisateur as $utilisateur) {
        if ($utilisateur->username == $username) {
            $utilisateur->password = $newPassword;
            break;
        }
    }

    // Sauvegarder le fichier XML
    $xml->asXML('xml/utilisateurs.xml');

    // Rediriger vers la page de profil avec un message de succès
    header('Location: profil.php?success=passwordchanged');
    exit();
}
?>
