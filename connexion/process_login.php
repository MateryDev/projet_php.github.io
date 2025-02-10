<?php

require_once __DIR__ . "../../config/database.php";
session_start();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // récupère les données de l'utilisateur
    $username = $_POST["username"];
    $password = $_POST["password"];

    // vérifie le login 
    if ($username === USERNAME && $password === PASSWORD) {
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $username;
        header("Location: ../index.php");
        exit;
    } else {
        // sinon échec de la connexion
        $_SESSION['error'] = "Nom d'utilisateur ou mot de passe incorrect";
        header("Location: login.php");
        exit;
    }
} else {
    // si accès direct à process_login.php sans formulaire 
    header("Location: login.php");
    exit;
}
