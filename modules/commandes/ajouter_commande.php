<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';

$db = getPDOConnection();
// Récupérer la liste des clients
$stmt = $db->query("SELECT * FROM Clients ORDER BY nom");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des produits
$stmt = $db->query("SELECT * FROM Produits WHERE stock > 0 ORDER BY nom");
$produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction(); // Démarrer la transaction

        // Créer la commande
        $stmt = $db->prepare("INSERT INTO Commandes (client_id, total) VALUES (:client_id, 0)");
        $stmt->execute([$_POST['client_id']]);
        $commande_id = $db->lastInsertId(); // Obtenir l'ID de la commande nouvellement crée de la dernière ligne inserée

        $total = 0;

        // Ajouter les produits à la commande
        foreach ($_POST['produits'] as $produit_id => $quantite) {
            if ($quantite > 0) {
                // Récupérer les informations du produit
                $stmt = $db->prepare("SELECT prix, stock FROM Produits WHERE id_produit = ?");
                $stmt->execute([$produit_id]);
                $produit = $stmt->fetch();

                // Vérifier le stock
                if ($produit['stock'] < $quantite) {
                    throw new Exception("Stock insuffisant pour le produit ID: " . $produit_id); // lance le traitement de la nouvelle exception
                }

                // Ajouter le produit à la commande
                $stmt = $db->prepare("INSERT INTO Produits_Commandes (commande_id, produit_id, quantite, prix_unitaire) VALUES (?, ?, ?, ?)");
                $stmt->execute([$commande_id, $produit_id, $quantite, $produit['prix']]);

                // Mettre à jour le stock
                $stmt = $db->prepare("UPDATE Produits SET stock = stock - ? WHERE id_produit = ?");
                $stmt->execute([$quantite, $produit_id]);

                $total += $produit['prix'] * $quantite;
            }
        }

        // Mettre à jour le total de la commande
        $stmt = $db->prepare("UPDATE Commandes SET total = ? WHERE id_commande = ?");
        $stmt->execute([$total, $commande_id]);

        $db->commit(); // Valider la transaction
        header('Location: details_commande.php?id=' . $commande_id);
        exit();
    } catch (Exception $e) {
        $db->rollBack(); // Annuler la transaction
        $error = "Erreur lors de la création de la commande: " . $e->getMessage();
    }
}

/*
L'instruction try indique que le code à l'intérieur de cette instruction sera exécuté.
Si une exception se produit à l'intérieur de cette instruction, le code sautera à la section catch suivante.
À l'intérieur de l'instruction try, une transaction est démarrée avec $db->beginTransaction(). 
Cela permet de regrouper plusieurs opérations de base de données en une seule transaction, afin de garantir la cohérence des données.
Une nouvelle commande est créée avec l'instruction INSERT INTO qui insère les informations du client dans la table Commandes.
Une variable $total est initialisée à 0 pour stocker le total de la commande.
Une foreach est utilisé pour parcourir les produits sélectionnés dans le formulaire. Pour chaque produit, il vérifie si la quantité est supérieure à 0.
Si la quantité est supérieure à 0, il récupère les informations du produit à partir de la table Produits.
Il vérifie si le stock du produit est suffisant pour la quantité sélectionnée. Si ce n'est pas le cas, une exception est levée.
Si le stock est suffisant, le produit est ajouté à la commande avec une instruction INSERT INTO dans la table Produits_Commandes.
Le stock du produit dans la table Produits est mis à jour avec l'instruction UPDATE.
Le total de la commande est mis à jour avec l'instruction UPDATE dans la table Commandes.
La transaction est confirmée avec $db->commit().
Enfin, l'utilisateur est redirigé vers la page de détails de la commande avec l'ID de la commande dans l'URL.
Si une exception est levée à l'intérieur de l'instruction try, le code sautera à la section catch suivante. 
Dans ce cas, la transaction est annulée avec $db->rollBack() et une erreur est stockée dans la variable $error pour afficher plus tard.
*/

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une commande</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Nouvelle commande</h2>

    <form method="POST" class="form">
        <div class="form-group">
            <label for="client_id">Client</label>
            <select id="client_id" name="client_id" class="form-control" required>
                <option value="">Sélectionner un client</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?php echo $client['id_client']; ?>">
                        <?php echo htmlspecialchars($client['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <h3>Produits</h3>
        <?php foreach ($produits as $produit): ?>
            <div class="form-group">
                <label>
                    <?php echo htmlspecialchars($produit['nom']); ?>
                    (<?php echo number_format($produit['prix'], 2); ?> € - Stock: <?php echo $produit['stock']; ?>)
                </label>
                <input type="number" name="produits[<?php echo $produit['id_produit']; ?>]"
                    class="form-control" min="0" max="<?php echo $produit['stock']; ?>" value="0">
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary">Créer la commande</button>
    </form>
</body>

</html>

<?php require_once '../../includes/footer.php'; ?>