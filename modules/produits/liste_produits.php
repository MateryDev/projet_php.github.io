<?php
require_once '../../config/database.php';

// Récupérer la liste des produits
$db = getPDOConnection();
$stmt = $db->query("SELECT * FROM Produits ORDER BY nom");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des produits</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php require_once '../../includes/header.php'; ?>
    <h1>Bienvenue dans l'épicerie en ligne</h1>
    <h2>Liste des produits:</h2>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Description</th>
                <th>Prix</th>
                <th>Stock</th>
            </tr>
        </thead>
        <tbody> <!-- affiche la liste des produits -->
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit['id_produit']); ?></td>
                    <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                    <td><?php echo htmlspecialchars($produit['description']); ?></td>
                    <td><?php echo number_format($produit['prix'], 2); ?> €</td>
                    <td><?php echo htmlspecialchars($produit['stock']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php require_once '../../includes/footer.php'; ?>
</body>

</html>