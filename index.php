<?php
session_start();
require_once 'config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

// Récupération des statistiques
$stats = [
    'total_products' => $conn->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'total_categories' => $conn->query("SELECT COUNT(*) FROM categories")->fetchColumn(),
    'low_stock' => $conn->query("SELECT COUNT(*) FROM products WHERE quantity < 10")->fetchColumn(),
    'total_movements' => $conn->query("SELECT COUNT(*) FROM stock_movements")->fetchColumn()
];

// Récupération des derniers mouvements
$stmt = $conn->query("
    SELECT sm.*, p.name as product_name, u.username 
    FROM stock_movements sm 
    JOIN products p ON sm.product_id = p.id 
    JOIN users u ON sm.user_id = u.id 
    ORDER BY sm.date_mouvement DESC 
    LIMIT 5
");
$recent_movements = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - Gestion de Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Gestion de Stock</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Tableau de bord</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products/index.php">Produits</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories/index.php">Catégories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="movements/index.php">Mouvements</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Tableau de bord</h2>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Produits</h5>
                        <p class="card-text display-4"><?php echo $stats['total_products']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Catégories</h5>
                        <p class="card-text display-4"><?php echo $stats['total_categories']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Stock Faible</h5>
                        <p class="card-text display-4"><?php echo $stats['low_stock']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Mouvements</h5>
                        <p class="card-text display-4"><?php echo $stats['total_movements']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Derniers mouvements</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
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
                                <?php foreach ($recent_movements as $movement): ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 