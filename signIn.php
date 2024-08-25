<?php
session_start();

// Configuración de cabeceras
header('Content-Type: application/json');

// Obtener datos JSON del cuerpo de la solicitud
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar si los datos están presentes
if (!isset($data['user']) || !isset($data['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña faltantes']);
    exit();
}

$user = $data['user'];
$password = $data['password'];

// Encriptar la contraseña
$hashed_password = md5($password); // Usa una función de hash más segura en producción, como `password_hash()`

// Leer el archivo de usuarios
$file = 'users.txt';
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Verificar si el usuario y la contraseña son correctos
foreach ($lines as $line) {
    list($id, $existing_user, $existing_password) = explode('|', trim($line));
    if ($existing_user === $user && $existing_password === $hashed_password) {
        // Autenticación exitosa
        $_SESSION['user'] = $id;
        echo json_encode(['status' => 'success', 'message' => 'Inicio de sesión exitoso']);
        exit();
    }
}

// Autenticación fallida
echo json_encode(['status' => 'error', 'message' => 'Usuario o contraseña incorrectos']);
?>
