<?php
session_start();
// Configuración de cabeceras para CORS
header('Access-Control-Allow-Origin: *'); // Permite solicitudes desde cualquier origen. En producción, reemplaza '*' con tu dominio.
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Configuración de cabeceras
header('Content-Type: application/json');

// Obtener datos JSON del cuerpo de la solicitud
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Verificar si los datos están presentes
if (!isset($data['user']) || !isset($data['password']) || !isset($data['verify_password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos']);
    exit();
}

$user = $data['user'];
$password = $data['password'];
$verify_password = $data['verify_password'];

// Validar la contraseña
if ($password !== $verify_password) {
    echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden']);
    exit();
}

// Validar el nombre de usuario (puedes agregar más validaciones según tus necesidades)
if (empty($user) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Nombre de usuario o contraseña vacíos']);
    exit();
}

// Encriptar la contraseña
$hashed_password = md5($password); // Usa una función de hash más segura en producción, como `password_hash()`

// Leer el archivo de usuarios
$file = 'users.txt';
$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Verificar si el usuario ya existe
foreach ($lines as $line) {
    list($id, $existing_user) = explode('|', trim($line));
    if ($existing_user === $user) {
        echo json_encode(['status' => 'error', 'message' => 'El usuario ya existe']);
        exit();
    }
}

// Generar un nuevo ID (puedes mejorarlo con una lógica más robusta)
$new_id = count($lines) + 1;

// Añadir el nuevo usuario al archivo
$new_user_data = "$new_id|$user|$hashed_password";
file_put_contents($file, $new_user_data . PHP_EOL, FILE_APPEND);

// Responder al cliente
echo json_encode(['status' => 'success', 'message' => 'Usuario registrado correctamente']);
?>
