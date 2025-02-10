<?php

require_once '../../config/database.php';
require_once '../../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: liste_commandes.php');
    exit();
}

$id = $_GET['id'];

// Récupérer les informations de la commande
$db = getPDOConnection();
$stmt = $db->prepare("
    SELECT c.*, cl.nom as client_nom, cl.email as client_email
    FROM Commandes c
    JOIN Clients cl ON c.client_id = cl.id_client
    WHERE c.id_commande = ?
");
$stmt->execute([$id]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header('Location: liste_commandes.php');
    exit();
}

// Récupérer les produits de la commande
$stmt = $db->prepare("
    SELECT pc.*, p.nom as produit_nom
    FROM Produits_Commandes pc
    JOIN Produits p ON pc.produit_id = p.id_produit
    WHERE pc.commande_id = ?
");
$stmt->execute([$id]);
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la commande</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Détails de la commande #<?php echo $id; ?></h2>

    <div class="commande-info">
        <h3>Informations</h3>
        <p><strong>Client:</strong> <?php echo htmlspecialchars($commande['client_nom']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($commande['client_email']); ?></p>
        <p><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($commande['created_at'])); ?></p> <!-- retourne le TimeStamp au format d/m/Y H:i -->
        <p><strong>Total:</strong> <?php echo number_format($commande['total'], 2); ?> €</p>
    </div>

    <h3>Produits commandés</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Produit</th>
                <th>Prix unitaire</th>
                <th>Quantité</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produit['produit_nom']); ?></td>
                    <td><?php echo number_format($produit['prix_unitaire'], 2); ?> €</td>
                    <td><?php echo $produit['quantite']; ?></td>
                    <td><?php echo number_format($produit['prix_unitaire'] * $produit['quantite'], 2); ?> €</td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-right"><strong>Total</strong></td>
                <td><strong><?php echo number_format($commande['total'], 2); ?> €</strong></td>
            </tr>
        </tbody>
    </table>
    <div class="actions">
        <a href="modifier_commande.php?id=<?php echo $id; ?>" class="btn">Modifier la commande</a>
        <form method="POST" action="supprimer_commande.php">
            <input type="hidden" name="id_commande" value="<?php echo $id; ?>">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette commande ?');">
                Supprimer la commande
            </button>
            <a href="liste_commandes.php" class="btn">Retour à la liste</a>
        </form>

    </div>

</body>

</html>

<?php require_once '../../includes/footer.php'; ?>