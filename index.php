<?php
require_once __DIR__ . "/config/database.php";
session_start();

// Fonction de vérification de connexion
function isLoggedIn()
{
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <?php if (isLoggedIn()) { ?>
            <div class="alert alert-success">
                Bienvenue <?php echo htmlspecialchars($_SESSION['username']); ?>
            </div>
            <div class="mb-3">
                <a href="modules/produits/liste_produits.php" class="btn btn-primary">Accéder à l'Épicerie en ligne
                </a>
            </div>
            <a href="connexion/logout.php" class="btn btn-danger">Déconnexion</a> <?php } else { ?>
            <div class="alert alert-info"> Vous n'êtes pas connecté.
            </div>
            <a href="connexion/login.php" class="btn btn-primary">Se connecter</a> <?php } ?>
    </div>
</body>

</html>