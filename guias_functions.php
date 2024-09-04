<?php
header('Content-Type: application/json');

function readGuides() {
    $file = 'guias.txt';
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $guides = [];

    foreach ($lines as $index => $line) {
        list($id, $name, $image_url, $availability, $max_reservations, $rating, $comment, $reserved_users) = explode('|', trim($line));
        $guides[$id] = [
            'id' => $id,
            'name' => $name,
            'image_url' => $image_url,
            'availability' => $availability,
            'max_reservations' => intval($max_reservations),
            'rating' => intval($rating),
            'comment' => $comment,
            'reserved_users' => $reserved_users ? explode(',', $reserved_users) : []
        ];
    }

    return $guides;
}

function saveGuides($guides) {
    $file = 'guias.txt';
    $lines = [];

    foreach ($guides as $guide) {
        $lines[] = "{$guide['id']}|{$guide['name']}|{$guide['image_url']}|{$guide['availability']}|{$guide['max_reservations']}|{$guide['rating']}|{$guide['comment']}|{$reserved_users}";
    }

    file_put_contents($file, implode(PHP_EOL, $lines));
}

function showAvailableGuides($user_id) {
    $guides = readGuides();
    $available_guides = [];

    foreach ($guides as $id => $guide) {
        if ($guide['availability'] === 'Disponible' || !in_array($user_id, $guide['reserved_users'])) {
            $available_guides[$id] = $guide;
        }
    }

    echo json_encode([
        'status' => 'success',
        'guides' => $available_guides
    ]);
}

function showAvailableReservationsGuides($user_id) {
    $guides = readGuides();
    $available_guides = [];

    foreach ($guides as $id => $guide) {
        if (in_array($user_id, $guide['reserved_users'])) {
            $available_guides[$id] = $guide;
        }
    }

    echo json_encode([
        'status' => 'success',
        'guides' => $available_guides
    ]);
}

function reserveGuide($guide_id, $user_id) {
    $guides = readGuides();

    if (!isset($guides[$guide_id])) {
        echo json_encode(['status' => 'error', 'message' => 'Guia no encontrado']);
        return;
    }

    $guide = &$guides[$guide_id];

    if ($guide['availability'] !== 'Disponible' || $guide['max_reservations'] <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Guia no disponible o reservas completas']);
        return;
    }
    
    if (in_array($user_id, $guide['reserved_users'])) {
        echo json_encode(['status' => 'error', 'message' => 'Ya has reservado este guia']);
        return;
    }

    if (count($guide['reserved_users']) >= $guide['max_reservations']) {
        echo json_encode(['status' => 'error', 'message' => 'Límite de reservas alcanzado']);
        return;
    }

    $guide['reserved_users'][] = $user_id;
    $guide['max_reservations'] = $guide['max_reservations'] - 1;

    if ($guide['max_reservations'] == 0) {
        $guide['availability'] = 'No Disponible';
    }

    saveGuides($guides);
    echo json_encode(['status' => 'success', 'message' => 'Guía reservado correctamente']);
}

function cancelReservationGuides($guide_id, $user_id) {
    $guides = readGuides();

    if (!isset($guides[$guide_id])) {
        echo json_encode(['status' => 'error', 'message' => 'Guia no encontrado']);
        return;
    }

    $guide = &$guides[$guide_id];

    if (!in_array($user_id, $guide['reserved_users'])) {
        echo json_encode(['status' => 'error', 'message' => 'No tienes una reserva para este guia']);
        return;
    }

    $guide['reserved_users'] = array_diff($guide['reserved_users'], [$user_id]);
    $guide['max_reservations'] = $guide['max_reservations'] + 1;

    if (!empty($guide['reserved_users'])) {
        $guide['availability'] = 'Disponible';
    }
    
    saveGuides($guides);
    echo json_encode(['status' => 'success', 'message' => 'Reserva cancelada correctamente']);
}
?>

