<?php
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'No hay sesión activa']);
    exit();
}

session_destroy();

echo json_encode(['status' => 'success', 'message' => 'Sesión cerrada correctamente']);
?>
