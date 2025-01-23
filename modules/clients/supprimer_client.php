<?php
session_start();
require_once '../../config/database.php';

if (!isset($_POST['id_client'])) {
    header('Location: liste_clients.php');
    exit();
}

$id_client = $_POST['id_client'];

try {
    // Vérifier si le client a des commandes
    $db = getPDOConnection();
    $stmt = $db->prepare("SELECT COUNT(*) FROM Commandes WHERE client_id = ?");
    $stmt->execute([$id_client]);
    $hasOrders = $stmt->fetchColumn() > 0; // Récupérer le nombre de commandes associées au client

    if ($hasOrders) {
        $_SESSION['error'] = "Impossible de supprimer ce client car il a des commandes associées.";
    } else {
        $stmt = $db->prepare("DELETE FROM Clients WHERE id_client = ?");
        $stmt->execute([$id_client]);
        $_SESSION['success'] = "Client supprimé avec succès.";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur lors de la suppression: " . $e->getMessage();
}

header('Location: liste_clients.php');
exit();
