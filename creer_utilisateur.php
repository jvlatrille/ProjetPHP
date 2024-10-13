<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['new_username'];
    $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash sécurisé du mot de passe

    // Charger le fichier XML
    $xmlFilePath = 'xml/utilisateurs.xml';
    if (!file_exists($xmlFilePath)) {
        // Si le fichier n'existe pas encore, créer un fichier vide de base
        $xml = new SimpleXMLElement('<utilisateurs></utilisateurs>');
    } else {
        $xml = simplexml_load_file($xmlFilePath);
    }

    // Vérifier si l'utilisateur existe déjà
    $userExists = false;
    foreach ($xml->utilisateur as $utilisateur) {
        if ($utilisateur->username == $username) {
            $userExists = true;
            break;
        }
    }

    if ($userExists) {
        // Si l'utilisateur existe déjà, rediriger avec une erreur
        header('Location: index.php?error=userexists');
        exit();
    } else {
        // Ajouter un nouvel utilisateur dans le fichier XML
        $newUser = $xml->addChild('utilisateur');
        $newUser->addChild('username', $username);
        $newUser->addChild('password', $password);

        // Sauvegarder les modifications dans le fichier XML
        $xml->asXML($xmlFilePath);

        // Créer la session pour l'utilisateur nouvellement créé
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;

        // Rediriger vers la page d'accueil
        header('Location: index.php');
        exit();
    }
}
