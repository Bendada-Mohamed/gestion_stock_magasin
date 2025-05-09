<?php
session_start();
require_once '../config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Récupération des produits
$products = $conn->query("SELECT * FROM products ORDER BY name")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $type = $_POST['type'] ?? '';
    $quantity = $_POST['quantity'] ?? 0;

    $errors = [];

    // Validation
    if (empty($product_id)) {
        $errors[] = "Le produit est requis";
    }
    if (empty($type)) {
        $errors[] = "Le type de mouvement est requis";
    }
    if ($quantity <= 0) {
        $errors[] = "La quantité doit être supérieure à 0";
    }

    if (empty($errors)) {
        try {
            // Vérifier le stock disponible pour les sorties
            if ($type === 'sortie') {
                $stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $current_stock = $stmt->fetchColumn();

                if ($current_stock < $quantity) {
                    $errors[] = "Stock insuffisant. Stock disponible : " . $current_stock;
                }
            }

            if (empty($errors)) {
                // Enregistrer le mouvement
                $stmt = $conn->prepare("INSERT INTO stock_movements (product_id, type, quantity, user_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$product_id, $type, $quantity, $_SESSION['user_id']]);

                // Mettre à jour le stock
                $stmt = $conn->prepare("UPDATE products SET quantity = quantity + ? WHERE id = ?");
                $quantity_update = $type === 'entree' ? $quantity : -$quantity;
                $stmt->execute([$quantity_update, $product_id]);

                header('Location: index.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de l'enregistrement du mouvement : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Mouvement - Gestion de Stock</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Nouveau Mouvement</h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="product_id" class="form-label">Produit</label>
                                <select class="form-select" id="product_id" name="product_id" required>
                                    <option value="">Sélectionner un produit</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?php echo $product['id']; ?>" 
                                                <?php echo (isset($_POST['product_id']) && $_POST['product_id'] == $product['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product['name']); ?> 
                                            (Stock: <?php echo $product['quantity']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="type" class="form-label">Type de mouvement</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="entree" <?php echo (isset($_POST['type']) && $_POST['type'] === 'entree') ? 'selected' : ''; ?>>Entrée</option>
                                    <option value="sortie" <?php echo (isset($_POST['type']) && $_POST['type'] === 'sortie') ? 'selected' : ''; ?>>Sortie</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required
                                       value="<?php echo htmlspecialchars($_POST['quantity'] ?? ''); ?>">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Enregistrer le mouvement</button>
                                <a href="index.php" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 