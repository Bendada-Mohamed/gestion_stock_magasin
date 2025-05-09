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

try {
    // Vérifier si la catégorie existe et n'a pas de produits associés
    $stmt = $conn->prepare("SELECT c.*, COUNT(p.id) as product_count 
                           FROM categories c 
                           LEFT JOIN products p ON c.id = p.category_id 
                           WHERE c.id = ? 
                           GROUP BY c.id");
    $stmt->execute([$id]);
    $category = $stmt->fetch();

    if (!$category) {
        header('Location: index.php');
        exit();
    }

    if ($category['product_count'] > 0) {
        die("Impossible de supprimer cette catégorie car elle contient des produits.");
    }

    // Supprimer la catégorie
    $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php');
    exit();
} catch (PDOException $e) {
    die("Erreur lors de la suppression de la catégorie : " . $e->getMessage());
}
?> 