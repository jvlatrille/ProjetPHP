<?php
session_start(); // On démarre la session
session_destroy(); // On détruit toutes les données de la session
header("Location: index.php"); // On redirige vers la page d'accueil (parce que quand on est pas connecté on est sur la page d'accueil)
?>
