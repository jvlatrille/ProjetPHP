<?php
session_start(); // On démarre la session

// Vérifier si la requête est un POST (quand on envoie le formulaire)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomUtilisateur = $_POST['username']; // Récupérer le nom d'utilisateur
    $motDePasse = $_POST['password']; // Récupérer le mot de passe

    // Chemin vers le fichier JSON des utilisateurs
    $cheminFichierJson = 'json/utilisateurs.json';
    if (file_exists($cheminFichierJson)) {
        // Lire le fichier JSON
        $donneesJson = file_get_contents($cheminFichierJson);
        $utilisateurs = json_decode($donneesJson, true)['utilisateurs']; // On transforme le JSON en tableau associatif
    } else {
        die("Erreur : fichier utilisateurs.json introuvable !"); // Si le fichier n'existe pas, on arrête tout
    }

    $utilisateurTrouve = false;
    $motDePasseCorrespond = false;

    // On parcourt la liste des utilisateurs pour voir s'il existe
    foreach ($utilisateurs as $utilisateur) {
        if ($utilisateur['username'] === $nomUtilisateur) {
            $utilisateurTrouve = true; // L'utilisateur est trouvé

            // On vérifie si le mot de passe est correct
            if (password_verify($motDePasse, $utilisateur['password'])) {
                $motDePasseCorrespond = true;
                $_SESSION['loggedin'] = true; // On marque l'utilisateur comme connecté
                $_SESSION['username'] = $nomUtilisateur; // On stocke le nom d'utilisateur dans la session pour l'afficher
                header('Location: index.php'); // Redirection après connexion réussie
                exit();
            }
        }
    }

    // Si l'utilisateur n'existe pas ou que le mot de passe est faux
    if (!$utilisateurTrouve || !$motDePasseCorrespond) {
        $_SESSION['loginError'] = true; // On met une erreur de connexion
        header('Location: index.php'); // Redirection en cas d'échec
        exit();
    }
}
?>
