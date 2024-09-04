<?php
header('Content-Type: application/json');

function readGuides() {
    $file = 'guias.txt';
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $guides = [];

    foreach ($lines as $index => $line) {
        list($name, $image_url, $availability, $max_reservations, $rating, $comment) = explode('|', trim($line));
        $guides[$index] = [
            'name' => $name,
            'image_url' => $image_url,
            'availability' => $availability,
            'max_reservations' => intval($max_reservations),
            'rating' => intval($rating),
            'comment' => $comment,
        ];
    }

    return $guides;
}

function saveGuides($guides) {
    $file = 'guias.txt';
    $lines = [];

    foreach ($guides as $guide) {
        $lines[] = "{$guide['name']}|{$guide['image_url']}|{$guide['availability']}|{$guide['max_reservations']}|{$guide['rating']}|{$guide['comment']}";
    }

    file_put_contents($file, implode(PHP_EOL, $lines));
}

function showAvailableGuides($user_id) {
    $guides = readGuides();  // Asumiendo que 'readGuides' es una función que lee el archivo de guías
    $available_guides = [];

    foreach ($guides as $id => $guide) {
        if ($guide['status'] === 'Disponible') {
            $available_guides[$id] = $guide;
        }
    }

    echo json_encode([
        'status' => 'success',
        'guides' => $available_guides
    ]);
}

function reserveGuide($guide_index) {
    $guides = readGuides();

    if (!isset($guides[$guide_index])) {
        echo json_encode(['status' => 'error', 'message' => 'Guía no encontrado']);
        return;
    }

    $guide = &$guides[$guide_index];

    if ($guide['availability'] !== 'Disponible' || $guide['max_reservations'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Guía no disponible o reservas completas']);
        return;
    }

    $guide['max_reservations'] -= 1;

    if ($guide['max_reservations'] == 0) {
        $guide['availability'] = 'No Disponible';
    }

    saveGuides($guides);
    echo json_encode(['status' => 'success', 'message' => 'Guía reservado correctamente']);
}

function cancelReservation($guide_index) {
    $guides = readGuides();

    if (!isset($guides[$guide_index])) {
        echo json_encode(['status' => 'error', 'message' => 'Guía no encontrado']);
        return;
    }

    $guide = &$guides[$guide_index];

    if ($guide['availability'] === 'No Disponible') {
        $guide['availability'] = 'Disponible';
    }

    $guide['max_reservations'] += 1;
    
    saveGuides($guides);
    echo json_encode(['status' => 'success', 'message' => 'Reserva cancelada correctamente']);
}
?>

