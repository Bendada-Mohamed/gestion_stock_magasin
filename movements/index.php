<?php
session_start();
require_once '../config/database.php';
require_once "../assets/header.php";
// Récupération des produits pour le filtre
    $products = $conn->query("SELECT * FROM products ORDER BY name")->fetchAll();
?>
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
                <form id="filterForm" class="row g-3">
                    <div class="col-md-3">
                        <select class="form-select" name="type">
                            <option value="">Tous les types</option>
                            <option value="entree">Entrée</option>
                            <option value="sortie">Sortie</option>
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
                    <tbody id="MovementsTableBody"></tbody>
                </div>
            </div>
        </div>
    </div>


    <script src="../assets/js/api.js"></script>
    <script src="../assets/deconexion.js"></script>
    <script src="../assets/movements.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    
</body>
</html> 