<?php
session_start();
require_once '../config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit();
}

// Récupération du produit
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: index.php');
    exit();
}

// Récupération des catégories
$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetchAll();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? 0;
    $quantity = $_POST['quantity'] ?? 0;
    $category_id = $_POST['category_id'] ?: null;

    $errors = [];

    // Validation
    if (empty($name)) {
        $errors[] = "Le nom du produit est requis";
    }
    if ($price <= 0) {
        $errors[] = "Le prix doit être supérieur à 0";
    }
    if ($quantity < 0) {
        $errors[] = "La quantité ne peut pas être négative";
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, category_id = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $quantity, $category_id, $id]);

            // Si la quantité a changé, enregistrer le mouvement
            if ($quantity != $product['quantity']) {
                $diff = $quantity - $product['quantity'];
                $type = $diff > 0 ? 'entree' : 'sortie';
                $stmt = $conn->prepare("INSERT INTO stock_movements (product_id, type, quantity, user_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$id, $type, abs($diff), $_SESSION['user_id']]);
            }

            header('Location: index.php');
            exit();
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la modification du produit : " . $e->getMessage();
        }
    }
}
require_once "../assets/header.php";
?>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Modifier Produit</h3>
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
                                <label for="name" class="form-label">Nom du produit</label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       value="<?php echo htmlspecialchars($_POST['name'] ?? $product['name']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($_POST['description'] ?? $product['description']); ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label">Catégorie</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" 
                                                <?php echo (isset($_POST['category_id']) ? $_POST['category_id'] : $product['category_id']) == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Prix</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required
                                           value="<?php echo htmlspecialchars($_POST['price'] ?? $product['price']); ?>">
                                    <span class="input-group-text">€</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantité</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="0" required
                                       value="<?php echo htmlspecialchars($_POST['quantity'] ?? $product['quantity']); ?>">
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                <a href="index.php" class="btn btn-secondary">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/api.js"></script>
    <script src="../assets/deconexion.js"></script>
</body>
</html> 