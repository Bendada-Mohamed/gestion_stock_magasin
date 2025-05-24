<?php 
session_start();
require_once '../../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Non autorisé']);
  exit();
}



// Vérification de l'authentification


$method = $_SERVER['REQUEST_METHOD']; 

switch($method){
  case 'GET':
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

    echo json_encode(['status' => 'success', 'data' => $movements]);
    break;
  case 'POST':
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
    break;
  default:
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    break;
}