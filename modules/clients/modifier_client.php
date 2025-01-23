<?php
require_once '../../config/database.php';
require_once '../../includes/header.php';

if (!isset($_GET['id'])) {
    header('Location: liste_clients.php');
    exit();
}

$id = $_GET['id'];

// Modification du client
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getPDOConnection();
        $stmt = $db->prepare("UPDATE Clients SET nom = ?, email = ? WHERE id_client = ?");
        $stmt->execute([
            $_POST['nom'],
            $_POST['email'],
            $id
        ]);
        header('Location: liste_clients.php');
        exit();
    } catch (PDOException $e) {
        $error = "Erreur lors de la modification du client: " . $e->getMessage();
    }
}
// Récupérer les informations du client
$db = getPDOConnection();
$stmt = $db->prepare("SELECT * FROM Clients WHERE id_client = ?");
$stmt->execute([$id]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    header('Location: liste_clients.php');
    exit();
}

// les marqueurs de position (?) sont utilisés pour indiquer que la valeur du paramètre sera fournie plus tard,
// tandis que les noms de paramètres (:id_client) sont utilisés pour indiquer que la valeur du paramètre sera fournie sous forme de tableau associatif.
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un client</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <h2>Modifier un client</h2>

    <form method="POST" class="form">
        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($client['nom']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($client['email']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Modifier</button>
    </form>
</body>

</html>

<?php require_once '../../includes/footer.php'; ?>