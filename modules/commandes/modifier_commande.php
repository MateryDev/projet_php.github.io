<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: liste_commandes.php');
    exit();
}

$id_commande = $_GET['id'];
$db = getPDOConnection();

// Récupérer la commande
$stmt = $db->prepare("
    SELECT c.*, cl.nom as client_nom 
    FROM Commandes c
    JOIN Clients cl ON c.client_id = cl.id_client
    WHERE c.id_commande = ?
");
$stmt->execute([$id_commande]);
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$commande) {
    header('Location: liste_commandes.php');
    exit();
}

// Récupérer les produits de la commande
$stmt = $db->prepare("
    SELECT pc.*, p.nom as produit_nom, p.stock as stock_actuel, p.prix as prix_actuel
    FROM Produits_Commandes pc
    JOIN Produits p ON pc.produit_id = p.id_produit
    WHERE pc.commande_id = ?
");
$stmt->execute([$id_commande]);
$produits_commande = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer tous les produits disponibles
$stmt = $db->query("SELECT * FROM Produits ORDER BY nom");
$tous_produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();

        // Restaurer les stocks des produits actuels de la commande
        foreach ($produits_commande as $produit) {
            $stmt = $db->prepare("UPDATE Produits SET stock = stock + ? WHERE id_produit = ?");
            $stmt->execute([$produit['quantite'], $produit['produit_id']]);
        }

        // Supprimer les anciens produits de la commande
        $stmt = $db->prepare("DELETE FROM Produits_Commandes WHERE commande_id = ?");
        $stmt->execute([$id_commande]);

        $total = 0;

        // Ajouter les nouveaux produits
        foreach ($_POST['produits'] as $produit_id => $quantite) {
            if ($quantite > 0) {
                $stmt = $db->prepare("SELECT prix, stock FROM Produits WHERE id_produit = ?");
                $stmt->execute([$produit_id]);
                $produit = $stmt->fetch();

                if ($produit['stock'] < $quantite) {
                    throw new Exception("Stock insuffisant pour le produit ID: " . $produit_id); // lance le traitement de la nouvelle exception
                }

                // Ajouter le produit à la commande
                $stmt = $db->prepare("INSERT INTO Produits_Commandes (commande_id, produit_id, quantite, prix_unitaire) VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)");
                $stmt->execute([$id_commande, $produit_id, $quantite, $produit['prix']]);

                // Mettre à jour le stock
                $stmt = $db->prepare("UPDATE Produits SET stock = stock - ? WHERE id_produit = ?");
                $stmt->execute([$quantite, $produit_id]);

                $total += $produit['prix'] * $quantite;
            }
        }

        // Mettre à jour le total de la commande
        $stmt = $db->prepare("UPDATE Commandes SET total = ? WHERE id_commande = ?");
        $stmt->execute([$total, $id_commande]);

        $db->commit();
        header('Location: details_commande.php?id=' . $id_commande);
        exit();
    } catch (Exception $e) {
        $db->rollBack();
        $error = "Erreur lors de la modification de la commande: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la commande</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Modifier la commande #<?php echo $id_commande; ?></h2>

    <div class="commande-info">
        <p><strong>Client:</strong> <?php echo htmlspecialchars($commande['client_nom']); ?></p>
        <p><strong>Date de création:</strong> <?php echo date('d/m/Y H:i', strtotime($commande['created_at'])); ?></p>
    </div>

    <form method="POST" class="form">
        <h3>Produits</h3>
        <?php foreach ($tous_produits as $produit): ?>
            <?php
            // Trouver si le produit est dans la commande actuelle
            $produit_commande = array_filter($produits_commande, function ($p) use ($produit) { // retourne le tableau filtrer des produits commandés qui correspondent au produit actuel
                return $p['produit_id'] == $produit['id_produit'];
            });
            $quantite_actuelle = !empty($produit_commande) ? current($produit_commande)['quantite'] : 0; // si le produit est dans la commande actuelle, retourne la quantité actuelle, sinon retourne 0
            $stock_disponible = $produit['stock'] + $quantite_actuelle; // calculer le stock disponible
            ?>
            <div class="form-group">
                <label>
                    <?php echo htmlspecialchars($produit['nom']); ?>
                    (<?php echo number_format($produit['prix'], 2); ?> € - Stock disponible: <?php echo $stock_disponible; ?>)
                </label>
                <input type="number"
                    name="produits[<?php echo $produit['id_produit']; ?>]"
                    class="form-control"
                    min="0"
                    max="<?php echo $stock_disponible; ?>"
                    value="<?php echo $quantite_actuelle; ?>">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Mettre à jour la commande</button>
        <a href="details_commande.php?id=<?php echo $id_commande; ?>" class="btn">Annuler</a>
    </form>
</body>

</html>
<?php require_once '../../includes/footer.php'; ?>