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
include 'funciones_clima.php';
include 'actividades_functions.php';
include 'guias_functions.php';

// Verifica que se haya solicitado una acción
if (!isset($_GET['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'Acción no especificada']);
    exit();
}

$action = $_GET['action'];

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

switch ($action) {
    // Funciones relacionadas con rutas 
    case 'show_available':
        showAvailableRoutes($user_id);
        break;

    case 'show_available_reservations':
        showAvailableReservations($user_id);
        break;

    case 'reserve':
        if (isset($_GET['route_id'])) {
            reserveRoute($_GET['route_id'], $_GET['user_id']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de ruta no especificado']);
        }
        break;

    case 'cancel':
        if (isset($_GET['route_id'])) {
            cancelReservation($_GET['route_id'], $_GET['user_id']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID de ruta no especificado']);
        }
        break;

    // Funciones relacionadas con guías
    case 'show_available_guides':
        showAvailableGuides($user_id);
        break;

    case 'reserve_guide':
        if (isset($_GET['guide_index'])) {
            reserveGuide($_GET['guide_index']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Índice de guía no especificado']);
        }
        break;

    case 'cancel_guide_reservation':
        if (isset($_GET['guide_index'])) {
            cancelReservation($_GET['guide_index']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Índice de guía no especificado']);
        }
        break;

    case 'check_session':
        $session_status = checkSession($user_id);
        echo json_encode($session_status);
        break;
    
    // Funciones relacionadas con ciudades
    case 'obtenerCiudad':
        $ciudades = obtener_ciudades_disponibles();
        echo json_encode($ciudades);
        break;

    case 'climaCiudad':
        if (isset($_GET['ciudad'])) {
            $ciudad = $_GET['ciudad'];
            $datos_climaticos = obtener_datos_climaticos($ciudad);
            echo json_encode($datos_climaticos);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ciudad no especificada']);
        }
        break;

    case 'ciudadesPorActividad':
        if (isset($_GET['actividad'])) {
            $actividad = $_GET['actividad'];
            $ciudades = ciudadesPorActividad($actividad);
            echo json_encode($ciudades);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No se encontraron ciudades para esta actividad']);
        }
        break;

    case 'fotoCiudad':
        if (isset($_GET['ciudad'])) {
            $ciudad = $_GET['ciudad'];
            $foto = getFotoCiudad($ciudad);
            echo json_encode($foto);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ciudad no especificada']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}
?>
