<?php
session_start();
require_once '../../config/database.php';

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

header('Content-Type: application/json');

// Gestion des requêtes CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Paramètres de recherche et filtrage
        $search = $_GET['search'] ?? '';
        $category = $_GET['category'] ?? '';
        $sort = $_GET['sort'] ?? 'name';
        $order = $_GET['order'] ?? 'ASC';

        // Construction de la requête
        $query = "SELECT p.*, c.name as category_name 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        if (!empty($category)) {
            $query .= " AND p.category_id = ?";
            $params[] = $category;
        }

        $query .= " ORDER BY $sort $order";

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['status' => 'success', 'data' => $products]); // Réponse JSON
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $conn->prepare("INSERT INTO products (name, description, quantity, price, category_id) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $data['name'],
            $data['description'],
            $data['quantity'],
            $data['price'],
            $data['category_id'] ?? null
        ]);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Produit créé avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du produit']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);
        
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, quantity = ?, price = ?, category_id = ? WHERE id = ?");
        $result = $stmt->execute([
            $data['name'],
            $data['description'],
            $data['quantity'],
            $data['price'],
            $data['category_id'] ?? null,
            $data['id']
        ]);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Produit mis à jour avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du produit']);
        }
        break;

    case 'DELETE':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'ID manquant']);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $result = $stmt->execute([$id]);

        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Produit supprimé avec succès']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du produit']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
        break;
}
?> 