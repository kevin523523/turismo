<?php
function getAvailableGuides() {
    $guides = [];
    $lines = file('guides.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($id, $name, $status, $userId) = explode('|', trim($line));
        if ($status === 'disponible') {
            $guides[] = [$id, $name];
        }
    }
    return $guides;
}

function reserveService($guideId, $userId) {
    $lines = file('guides.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newLines = [];
    foreach ($lines as $line) {
        list($id, $name, $status, $currentUserId) = explode('|', trim($line));
        if ($id == $guideId) {
            $newLines[] = "$id|$name|reservado|$userId";
        } else {
            $newLines[] = $line;
        }
    }
    file_put_contents('guides.txt', implode("\n", $newLines));
}

function getReservations($userId) {
    $reservations = [];
    $lines = file('guides.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($id, $name, $status, $currentUserId) = explode('|', trim($line));
        if ($status === 'reservado' && $currentUserId == $userId) {
            $reservations[] = "$name reservado por Usuario $userId";
        }
    }
    return $reservations;
}

function cancelReservation($guideId, $userId) {
    $lines = file('guides.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $newLines = [];
    foreach ($lines as $line) {
        list($id, $name, $status, $currentUserId) = explode('|', trim($line));
        if ($id == $guideId && $status === 'reservado' && $currentUserId == $userId) {
            $newLines[] = "$id|$name|disponible|";
        } else {
            $newLines[] = $line;
        }
    }
    file_put_contents('guides.txt', implode("\n", $newLines));
}
?>
