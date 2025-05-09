<?php
session_start();
require_once '../config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Filtres
$type = $_GET['type'] ?? '';
$product_id = $_GET['product_id'] ?? '';
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

// Construction de la requête
$query = "SELECT sm.*, p.name as product_name, u.username 
          FROM stock_movements sm 
          JOIN products p ON sm.product_id = p.id 
          JOIN users u ON sm.user_id = u.id 
          WHERE 1=1";
$params = [];

if (!empty($type)) {
    $query .= " AND sm.type = ?";
    $params[] = $type;
}

if (!empty($product_id)) {
    $query .= " AND sm.product_id = ?";
    $params[] = $product_id;
}

if (!empty($date_start)) {
    $query .= " AND DATE(sm.date_mouvement) >= ?";
    $params[] = $date_start;
}

if (!empty($date_end)) {
    $query .= " AND DATE(sm.date_mouvement) <= ?";
    $params[] = $date_end;
}

$query .= " ORDER BY sm.date_mouvement DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$movements = $stmt->fetchAll();

// Récupération des produits pour le filtre
$products = $conn->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mouvements de Stock - Gestion de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">Gestion de Stock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../products/index.php">Produits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categories/index.php">Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Mouvements</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Mouvements de Stock</h2>
            <a href="create.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nouveau Mouvement
            </a>
        </div>

        <!-- Filtres -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" name="type">
                            <option value="">Tous les types</option>
                            <option value="entree" <?php echo $type === 'entree' ? 'selected' : ''; ?>>Entrée</option>
                            <option value="sortie" <?php echo $type === 'sortie' ? 'selected' : ''; ?>>Sortie</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="product_id">
                            <option value="">Tous les produits</option>
                            <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo $product_id == $product['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_start" placeholder="Date début" value="<?php echo $date_start; ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_end" placeholder="Date fin" value="<?php echo $date_end; ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Liste des mouvements -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Produit</th>
                                <th>Type</th>
                                <th>Quantité</th>
                                <th>Utilisateur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($movements as $movement): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($movement['date_mouvement'])); ?></td>
                                <td><?php echo htmlspecialchars($movement['product_name']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $movement['type'] === 'entree' ? 'success' : 'danger'; ?>">
                                        <?php echo $movement['type'] === 'entree' ? 'Entrée' : 'Sortie'; ?>
                                    </span>
                                </td>
                                <td><?php echo $movement['quantity']; ?></td>
                                <td><?php echo htmlspecialchars($movement['username']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 