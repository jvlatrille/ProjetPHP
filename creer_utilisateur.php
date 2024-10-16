<?php
session_start(); // On démarre la session

// Vérifier si la requête est un POST (envoi de formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveauNomUtilisateur = $_POST['new_username']; // On récupère le nom d'utilisateur
    $nouveauMotDePasse = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hacher le mot de passe pour le stockage parce que la sécurité c'est important

    // Chemin vers le fichier JSON des utilisateurs
    $cheminFichierJson = 'json/utilisateurs.json';
    if (file_exists($cheminFichierJson)) {
        // On lit le contenu du fichier JSON
        $donneesJson = file_get_contents($cheminFichierJson);
        $utilisateurs = json_decode($donneesJson, true); // On le décode en tableau associatif
    } else {
        die("Erreur : fichier utilisateurs.json introuvable !"); // Si le fichier n'existe pas, on arrête tout
    }

    // Ajouter le nouvel utilisateur au tableau
    $utilisateurs['utilisateurs'][] = [
        'username' => $nouveauNomUtilisateur,
        'password' => $nouveauMotDePasse
    ];

    // Sauvegarder les modifications dans le fichier JSON (plutôt que de faire un simple file_put_contents, on encode en JSON pour la lisibilité)
    file_put_contents($cheminFichierJson, json_encode($utilisateurs, JSON_PRETTY_PRINT));

    // Connecter automatiquement l'utilisateur après création
    $_SESSION['loggedin'] = true; // L'utilisateur est connecté
    $_SESSION['username'] = $nouveauNomUtilisateur; // On stocke le nom d'utilisateur dans la session pour l'afficher (encore)

    // Rediriger vers la page d'accueil après l'inscription
    header('Location: index.php');
    exit();
}
?>
