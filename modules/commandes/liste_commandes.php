<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/header.php';

$whereClause = "";
$params = [];
//récupère la liste des commandes d'un client
if (isset($_GET['client'])) {
    $whereClause = "WHERE c.client_id = :client_id";
    $params = [$_GET['client']];
}
// récupère la liste de toutes les commandes
$query = "SELECT c.*, cl.nom as client_nom 
          FROM Commandes c 
          JOIN Clients cl ON c.client_id = cl.id_client 
          $whereClause 
          ORDER BY c.created_at DESC";
$db = getPDOConnection();
$stmt = $db->prepare($query);
$stmt->execute($params);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
    <title>Liste des commandes</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Liste des commandes</h2>
    <a href="ajouter_commande.php" class="btn btn-primary">Nouvelle commande</a>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Client</th>
                <th>Total</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $commande): ?>
                <tr>
                    <td><?php echo htmlspecialchars($commande['id_commande']); ?></td>
                    <td><?php echo htmlspecialchars($commande['client_nom']); ?></td>
                    <td><?php echo number_format($commande['total'], 2); ?> €</td>
                    <td><?php echo date('d/m/Y H:i', strtotime($commande['created_at'])); ?></td>
                    <td>
                        <a href="details_commande.php?id=<?php echo $commande['id_commande']; ?>" class="btn">Détails</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>

</html>
<?php require_once '../../includes/footer.php'; ?>