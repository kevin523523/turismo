<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: signIn.php');
    exit();
}
$userId = $_SESSION['user'];


include 'reservations_functions.php';
include 'funciones_clima.php';
include 'calificacion_testimonios.php';


$ciudades = obtener_ciudades_disponibles();
$datos_climaticos = [];
$calificacion = null;
$testimonios = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reserve'])) {
        $guideId = $_POST['guideId'];
        if (!hasReservation($userId)) {
            reserveService($guideId, $userId);
            header('Location: reservations.php');
            exit();
        } else {
            $error = 'Ya tienes una reserva activa.';
        }
    }elseif (isset($_POST['logout'])) {
        header('Location: logout.php');
        exit();
    }elseif (isset($_POST['ciudad'])) {
        $ciudad_seleccionada = $_POST['ciudad'];
        $datos_climaticos = obtener_datos_climaticos($ciudad_seleccionada);
    } elseif (isset($_POST['show_ratings']) && isset($_POST['guideId'])) {
        $guideId = $_POST['guideId'];
        $calificacion = obtener_calificacion_general($guideId);
        $testimonios = obtener_testimonios($guideId);
    }
}



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['ciudad'])) {
    $ciudad_seleccionada = $_GET['ciudad'];
    $datos_climaticos = obtener_datos_climaticos($ciudad_seleccionada);
    header('Content-Type: application/json');
    echo json_encode($datos_climaticos);
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'getTestimonios' && isset($_GET['guideId'])) {
    $guideId = $_GET['guideId'];
    $testimonios = obtener_testimonios($guideId);
    header('Content-Type: application/json');
    echo json_encode(['testimonios' => $testimonios]);
    exit();
}

function hasReservation($userId) {
    $lines = file('guides.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($id, $name, $status, $currentUserId) = explode('|', trim($line));
        if ($status === 'reservado' && $currentUserId == $userId) {
            return true;
        }
    }
    return false;
}
?>

