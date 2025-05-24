<?php
session_start();
require_once '../../config/database.php';

// Pour l'endpoint d'authentification, nous ne vérifions pas si l'utilisateur est déjà connecté
// car c'est précisément ce que nous essayons de faire ici

header('Content-Type: application/json');

$input = json_decode(file_get_contents("php://input"), true);

$username = $input['username'] ?? '';
$password = $input['password'] ?? '';

if (empty($username) || empty($password)) {
    // http_response_code(401);  // Unauthorized
    echo json_encode(['status' => false, 'message' => 'Champs manquants']);
    exit;
}

// Requête vers la base
$stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    echo json_encode(['status' => true]);
} else {
    // http_response_code(400); //
    echo json_encode(['status' => false, 'message' => 'Identifiants incorrects']);
}
?>
