<?php
header('Content-Type: application/json');

function readRoutes() {
    $file = 'routes.txt';
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $routes = [];

    foreach ($lines as $line) {
        list($id, $name, $image_url, $status, $max_reservations, $reserved_users) = explode('|', trim($line));
        $routes[$id] = [
            'id' => $id,
            'name' => $name,
            'image_url' => $image_url,
            'status' => $status,
            'max_reservations' => intval($max_reservations),
            'reserved_users' => $reserved_users ? explode(',', $reserved_users) : []
        ];
    }

    return $routes;
}

function saveRoutes($routes) {
    $file = 'routes.txt';
    $lines = [];

    foreach ($routes as $route) {
        $reserved_users = implode(',', $route['reserved_users']);
        $lines[] = "{$route['id']}|{$route['name']}|{$route['image_url']}|{$route['status']}|{$route['max_reservations']}|{$reserved_users}";
    }

    file_put_contents($file, implode(PHP_EOL, $lines));
}

function showAvailableRoutes($user_id) {
    $routes = readRoutes();
    $available_routes = [];

    foreach ($routes as $id => $route) {
        if ($route['status'] === 'disponible' || !in_array($user_id, $route['reserved_users'])) {
            $available_routes[$id] = $route;
        }
    }

    echo json_encode([
        'status' => 'success',
        'routes' => $available_routes
    ]);
}

function reserveRoute($route_id, $user_id) {
    $routes = readRoutes();

    if (!isset($routes[$route_id])) {
        echo json_encode(['status' => 'error', 'message' => 'Ruta no encontrada']);
        return;
    }

    $route = &$routes[$route_id];

    if (in_array($user_id, $route['reserved_users'])) {
        echo json_encode(['status' => 'error', 'message' => 'Ya has reservado esta ruta']);
        return;
    }

    if (count($route['reserved_users']) >= $route['max_reservations']) {
        echo json_encode(['status' => 'error', 'message' => 'Límite de reservas alcanzado']);
        return;
    }

    $route['reserved_users'][] = $user_id;
    $route['max_reservations'] = $route['max_reservations'] - 1;
    
    if ($route['max_reservations'] == 0) {
        $route['status'] = 'reservado';
    }

    saveRoutes($routes);
    echo json_encode(['status' => 'success', 'message' => 'Ruta reservada correctamente']);
}

function cancelReservation($route_id, $user_id) {
    $routes = readRoutes();

    if (!isset($routes[$route_id])) {
        echo json_encode(['status' => 'error', 'message' => 'Ruta no encontrada']);
        return;
    }

    $route = &$routes[$route_id];

    if (!in_array($user_id, $route['reserved_users'])) {
        echo json_encode(['status' => 'error', 'message' => 'No tienes una reserva para esta ruta']);
        return;
    }

    $route['reserved_users'] = array_diff($route['reserved_users'], [$user_id]);
    $route['max_reservations'] = $route['max_reservations'] + 1;
    
    if (!empty($route['reserved_users'])) {
        $route['status'] = 'disponible';
    }

    saveRoutes($routes);
    echo json_encode(['status' => 'success', 'message' => 'Reserva cancelada correctamente']);
}

?>