<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Charger le fichier JSON des utilisateurs
    $jsonFilePath = 'json/utilisateurs.json';
    if (file_exists($jsonFilePath)) {
        $jsonData = file_get_contents($jsonFilePath);
        $utilisateurs = json_decode($jsonData, true)['utilisateurs'];
    } else {
        die("Erreur : fichier utilisateurs.json introuvable !");
    }

    $userFound = false;
    $passwordMatch = false;

    // Parcourir les utilisateurs dans le fichier JSON
    foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur['username'] === $username) {
            $userFound = true;
            // Vérifier le mot de passe avec password_verify
            if (password_verify($password, $utilisateur['password'])) {
                $passwordMatch = true;
                $_SESSION['loggedin'] = true;
                $_SESSION['username'] = $username; // Stocker le nom d'utilisateur dans la session
                header('Location: index.php'); // Rediriger après connexion réussie
                exit();
            }
        }
    }

    // Si l'utilisateur n'existe pas ou que le mot de passe est incorrect
    if (!$userFound || !$passwordMatch) {
        $_SESSION['loginError'] = true; // Indiquer qu'il y a une erreur de connexion
        header('Location: index.php');
        exit();
    }
}
?>
