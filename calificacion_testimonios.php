<?php

function obtener_calificacion_general($guideId) {
    $calificaciones = [];
    $lineas = file('calificaciones.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        list($id, $calificacion, $comentario) = explode('|', trim($linea));
        if ($id == $guideId) {
            $calificaciones[] = $calificacion;
        }
    }
    if (count($calificaciones) > 0) {
        return round(array_sum($calificaciones) / count($calificaciones), 1);
    } else {
        return "Sin calificación";
    }
}

function obtener_testimonios($guideId) {
    $testimonios = [];
    $lineas = file('calificaciones.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lineas as $linea) {
        list($id, $calificacion, $comentario) = explode('|', trim($linea));
        if ($id == $guideId && !empty($comentario)) {
            $testimonios[] = $comentario;
        }
    }
    return $testimonios;
}

function agregar_calificacion_testimonio($guideId, $calificacion, $comentario) {
    $linea = $guideId . '|' . $calificacion . '|' . trim($comentario) . "\n";
    file_put_contents('calificaciones.txt', $linea, FILE_APPEND);
}

function eliminar_testimonio($guideId, $comentario) {
    $lineas = file('calificaciones.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $nuevasLineas = [];
    foreach ($lineas as $linea) {
        list($id, $calificacion, $lineaComentario) = explode('|', trim($linea));
        if (!($id == $guideId && trim($lineaComentario) == trim($comentario))) {
            $nuevasLineas[] = $linea;
        }
    }
    file_put_contents('calificaciones.txt', implode("\n", $nuevasLineas));
}

?>
