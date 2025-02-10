<?php
session_start();
require_once '../../config/database.php';

if (!isset($_POST['id_commande'])) {
    header('Location: liste_commandes.php');
    exit();
}

$id_commande = $_POST['id_commande'];

try {
    $db = getPDOConnection();
    $db->beginTransaction();

    // Récupérer les produits de la commande pour restaurer les stocks
    $stmt = $db->prepare("SELECT produit_id, quantite FROM Produits_Commandes WHERE commande_id = ?");
    $stmt->execute([$id_commande]);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Restaurer les stocks des produits
    foreach ($produits as $produit) {
        $stmt = $db->prepare("UPDATE Produits SET stock = stock + ? WHERE id_produit = ?");
        $stmt->execute([$produit['quantite'], $produit['produit_id']]);
    }

    // Supprimer les produits de la commande
    $stmt = $db->prepare("DELETE FROM Produits_Commandes WHERE commande_id = ?");
    $stmt->execute([$id_commande]);

    // Supprimer la commande
    $stmt = $db->prepare("DELETE FROM Commandes WHERE id_commande = ?");
    $stmt->execute([$id_commande]);

    $db->commit();
    $_SESSION['success'] = "Commande supprimée avec succès.";
} catch (PDOException $e) {
    $db->rollBack();
    $_SESSION['error'] = "Erreur lors de la suppression: " . $e->getMessage();
}
header('Location: liste_commandes.php');
exit();
