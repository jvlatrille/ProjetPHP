<?php
session_start();

// Simulons un utilisateur pour l'exemple
$users = [
    'root' => 'root'
];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isset($users[$username]) && $users[$username] == $password) {
        $_SESSION['user'] = $username;
        header("Location: index.php");
    } else {
        $_SESSION['error'] = true;
        header("Location: index.php");
    }
}
?>
