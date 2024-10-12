<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Remplacer ces valeurs par les bons identifiants et mots de passe
    $validUsername = "root";
    $validPassword = "root";

    // Vérifier les informations de connexion
    if ($username === $validUsername && $password === $validPassword) {
        // Si les informations sont correctes, on initialise la session
        $_SESSION['loggedin'] = true;
        header("Location: index.php"); // Rediriger vers la page d'accueil
    } else {
        // Sinon, on retourne sur la page de connexion avec une erreur
        header("Location: index.php?loginError=true");
    }
}
?>