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
  default:
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
    break;
}