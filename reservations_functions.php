<?php
header('Content-Type: application/json');

// Función para leer el archivo de rutas
function readRoutes() {
    $file = 'routes.txt';
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $routes = [];

    foreach ($lines as $line) {
        list($id, $name, $status, $reservations) = explode('|', trim($line));
        $routes[] = [
            'id' => $id,
            'name' => $name,
            'status' => $status,
            'reservations' => $reservations ? explode(',', $reservations) : []
        ];
    }

    return $routes;
}

// Función para guardar los cambios en el archivo de rutas
function saveRoutes($routes) {
    $file = 'routes.txt';
    $lines = [];

    foreach ($routes as $route) {
        $reservations = implode(',', $route['reservations']);
        $lines[] = "{$route['id']}|{$route['name']}|{$route['status']}|{$reservations}";
    }

    file_put_contents($file, implode(PHP_EOL, $lines));
}

// Función para mostrar las rutas disponibles
function showAvailableRoutes() {
    $routes = readRoutes();
    $available_routes = array_filter($routes, function($route) {
        return $route['status'] === 'disponible';
    });

    echo json_encode([
        'status' => 'success',
        'routes' => $available_routes
    ]);
}

// Función para reservar una ruta
function reserveRoute($route_id, $user_id) {
    $routes = readRoutes();
    $route_found = false;

    foreach ($routes as &$route) {
        if ($route['id'] == $route_id) {
            if ($route['status'] === 'reservado') {
                if (in_array($user_id, $route['reservations'])) {
                    echo json_encode(['status' => 'error', 'message' => 'Ya has reservado esta ruta']);
                    return;
                }

                if (count($route['reservations']) >= 3) { // Ejemplo de límite de reservas
                    echo json_encode(['status' => 'error', 'message' => 'Límite de reservas alcanzado']);
                    return;
                }
            }

            $route['status'] = 'reservado';
            $route['reservations'][] = $user_id;
            $route_found = true;
            break;
        }
    }

    if ($route_found) {
        saveRoutes($routes);
        echo json_encode(['status' => 'success', 'message' => 'Ruta reservada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ruta no encontrada']);
    }
}

// Función para cancelar una reserva
function cancelReservation($route_id, $user_id) {
    $routes = readRoutes();
    $route_found = false;

    foreach ($routes as &$route) {
        if ($route['id'] == $route_id) {
            if ($route['status'] === 'reservado') {
                if (!in_array($user_id, $route['reservations'])) {
                    echo json_encode(['status' => 'error', 'message' => 'No tienes una reserva para esta ruta']);
                    return;
                }

                $route['reservations'] = array_diff($route['reservations'], [$user_id]);
                if (empty($route['reservations'])) {
                    $route['status'] = 'disponible';
                }
            }

            $route_found = true;
            break;
        }
    }

    if ($route_found) {
        saveRoutes($routes);
        echo json_encode(['status' => 'success', 'message' => 'Reserva cancelada correctamente']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Ruta no encontrada']);
    }
}
?>
