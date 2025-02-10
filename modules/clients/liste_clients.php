<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';
// connexion à la base de données
$db = getPDOConnection();

// Récupérer la liste des clients
$stmt = $db->query("SELECT * FROM Clients ORDER BY nom");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// gestion des messages de session
if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Clients</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Liste des clients</h2>
    <a href="ajouter_client.php" class="btn btn-primary">Ajouter un client</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['id_client']); ?></td>
                    <td><?php echo htmlspecialchars($client['nom']); ?></td>
                    <td><?php echo htmlspecialchars($client['email']); ?></td>
                    <td>
                        <a href="modifier_client.php?id=<?php echo $client['id_client']; ?>" class="btn">Modifier</a>
                        <a href="../commandes/liste_commandes.php?client=<?php echo $client['id_client']; ?>" class="btn">Commandes</a>
                        <form method="POST" action="supprimer_client.php" style="display: inline;">
                            <input type="hidden" name="id_client" value="<?php echo $client['id_client']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                Supprimer
                            </button>
                        </form>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>

<?php require_once '../../includes/footer.php'; ?>