<?php
session_start();
header('Content-Type: application/json');

// Destroy the session
session_destroy();

// Return success response
echo json_encode(['status' => true, 'message' => 'Déconnexion réussie']);
?>
