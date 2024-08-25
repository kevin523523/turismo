<?php
session_start();

// Configuración de cabeceras para CORS
header('Access-Control-Allow-Origin: *'); // Permite solicitudes desde cualquier origen. En producción, reemplaza '*' con tu dominio.
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejo de solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

header('Content-Type: application/json');

// Manejo de solicitudes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

include 'reservations_functions.php';


// Verifica que se haya solicitado una acción
if (!isset($_GET['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acción no especificada']);
    exit();
}

$action = $_GET['action'];
$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null; // Obtener el ID del usuario de la consulta

// Función para verificar el estado de la sesión
function checkSession($user_id) {
    if ($user_id && isset($_SESSION['user']) && $_SESSION['user'] == $user_id) {
        return [
            'status' => 'success',
            'authenticated' => true,
            'user_id' => $user_id
        ];
    } else {
        return [
            'status' => 'success',
            'authenticated' => false,
            'user_id' => null
        ];
    }
}

// Llama a la función correspondiente según la acción solicitada
switch ($action) {
    case 'show_available':
        $session_status = checkSession($user_id);
        if (!$session_status['authenticated']) {
            echo json_encode(['status' => 'error', 'message' => 'Debes iniciar sesión para realizar esta acción']);
            exit();
        }
        showAvailableRoutes($user_id);
        break;

    case 'reserve':
        $session_status = checkSession($user_id);
        if (!$session_status['authenticated']) {
            echo json_encode(['status' => 'error', 'message' => 'Debes iniciar sesión para realizar esta acción']);
            exit();
        }
        if (isset($_GET['route_id'])) {
            reserveRoute($_GET['route_id'], $user_id);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de ruta no especificado']);
        }
        break;

    case 'cancel':
        $session_status = checkSession($user_id);
        if (!$session_status['authenticated']) {
            echo json_encode(['status' => 'error', 'message' => 'Debes iniciar sesión para realizar esta acción']);
            exit();
        }
        if (isset($_GET['route_id'])) {
            cancelReservation($_GET['route_id'], $user_id);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de ruta no especificado']);
        }
        break;

    case 'check_session':
        $session_status = checkSession($user_id);
        echo json_encode($session_status);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
?>
