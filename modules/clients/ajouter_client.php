<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';

// ajoute un nouveau client à la base de données
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getPDOConnection();
        $stmt = $db->prepare("INSERT INTO Clients (nom, email) VALUES (:nom, :email)");
        $stmt->execute([
            $_POST['nom'],
            $_POST['email']
        ]);
        header('Location: liste_clients.php');
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de l'ajout du client: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un client</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Ajouter un client</h2>

    <form method="POST" class="form">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter</button>
    </form>
</body>

</html>
<?php require_once '../../includes/footer.php'; ?>