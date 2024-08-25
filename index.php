<?php
session_start();

// Configuración de cabeceras para CORS
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

header('Content-Type: application/json');

include 'reservations_functions.php';

// Verifica que se haya solicitado una acción
if (!isset($_GET['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acción no especificada']);
    exit();
}

$action = $_GET['action'];

// Función para verificar el estado de la sesión
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
            'status' => 'error',
            'authenticated' => false,
            'user_id' => null
        ];
    }
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null; // Obtener el ID del usuario de la consulta

// Manejo de las solicitudes POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $user_id = isset($data['user_id']) ? intval($data['user_id']) : null; // Obtener el ID del usuario del cuerpo de la solicitud
}

// Llama a la función correspondiente según la acción solicitada
switch ($action) {
    case 'show_available':

        showAvailableRoutes($user_id);
        break;


    case 'reserve':
        $session_status = checkSession($user_id);
        if (!$session_status['authenticated']) {
            echo json_encode(['status' => 'error', 'message' => 'Debes iniciar sesión para realizar esta acción']);
            exit();
        }
        if (isset($_POST['route_id'])) {
            reserveRoute($_POST['route_id'], $user_id);
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
        if (isset($_POST['route_id'])) {
            cancelReservation($_POST['route_id'], $user_id);
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
