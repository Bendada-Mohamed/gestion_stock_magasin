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
    // Vérifier si le produit existe
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: index.php');
        exit();
    }

    // Supprimer le produit
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);

    header('Location: index.php');
    exit();
} catch (PDOException $e) {
    die("Erreur lors de la suppression du produit : " . $e->getMessage());
}
?> 