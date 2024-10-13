<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Charger le fichier XML des utilisateurs
    $xmlFilePath = 'xml/utilisateurs.xml';
    if (file_exists($xmlFilePath)) {
        $xml = simplexml_load_file($xmlFilePath);
    } else {
        die("Erreur : fichier utilisateurs.xml introuvable !");
    }

    $userFound = false;

    // Parcourir les utilisateurs dans le fichier XML
    foreach ($xml->utilisateur as $utilisateur) {
        if ($utilisateur->username == $username) {
            // Vérifier le mot de passe avec password_verify
            if (password_verify($password, $utilisateur->password)) {
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username; // Stocker le nom d'utilisateur dans la session
                $userFound = true;
                header('Location: index.php'); // Rediriger après connexion réussie
                exit();
            }
        }
    }

    if (!$userFound) {
        // En cas d'échec de connexion, rediriger avec une erreur
        header('Location: index.php?loginError=1');
        exit();
    }
}
?>
