<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['new_username'];
    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hacher le mot de passe

    // Charger le fichier JSON des utilisateurs
    $jsonFilePath = 'json/utilisateurs.json';
    if (file_exists($jsonFilePath)) {
        $jsonData = file_get_contents($jsonFilePath);
        $utilisateurs = json_decode($jsonData, true);
    } else {
        die("Erreur : fichier utilisateurs.json introuvable !");
    }

    // Ajouter le nouvel utilisateur
    $utilisateurs['utilisateurs'][] = [
        'username' => $newUsername,
        'password' => $newPassword
    ];

    // Sauvegarder les changements dans le fichier JSON
    file_put_contents($jsonFilePath, json_encode($utilisateurs, JSON_PRETTY_PRINT));

    // Connecter automatiquement l'utilisateur
    $_SESSION['loggedin'] = true;
    $_SESSION['username'] = $newUsername;

    // Rediriger vers la page d'accueil
    header('Location: index.php');
    exit();
}
?>
