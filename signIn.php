<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Permite solicitudes desde cualquier origen
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['user']) || !isset($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña faltantes']);
    exit();
}

$user = $data['user'];
$password = $data['password'];
$hashed_password = md5($password); // Considera usar password_hash() para mayor seguridad

$file = 'users.txt';
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    list($id, $existing_user, $existing_password) = explode('|', trim($line));
    if ($existing_user === $user && $existing_password === $hashed_password) {
        $_SESSION['user'] = $id;
        echo json_encode([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso',
            'user_id' => $id
        ]);
        exit();
    }
}

echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos']);
?>
