<?php
session_start();

$utilisateur = [
    'root' => 'root' // Utilisateur 'root' avec mot de passe 'root'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Vérifie si la requête est de type POST
    $nomUtilisateur = $_POST['nomUtilisateur']; // Récupère le nom d'utilisateur du formulaire
    $mdp = $_POST['mdp']; // Récupère le mot de passe du formulaire

    // Vérifie si l'utilisateur existe et si le mot de passe est correct
    if (isset($utilisateur[$nomUtilisateur]) && $utilisateur[$nomUtilisateur] == $mdp) {
        $_SESSION['user'] = $nomUtilisateur; // Stocke le nom d'utilisateur dans la session
        header("Location: index.php"); // Redirige vers la page d'accueil
    } else {
        $_SESSION['error'] = true; // Indique une erreur de connexion
        header("Location: index.php"); // Redirige vers la page d'accueil
    }
}
?>
