<?php
function obtener_datos_climaticos($ciudad) {
    $archivo = 'condiciones_climaticas.txt';
    $datos_climaticos = [];

    if (file_exists($archivo)) {
        $file = fopen($archivo, "r");

        while (($linea = fgets($file)) !== false) {
            $datos = explode(',', trim($linea));
            if (strtolower($datos[0]) === strtolower($ciudad)) {
                $datos_climaticos= [
                    'fecha' => $datos[1],
                    'temperatura' => $datos[2],
                    'humedad' => $datos[3],
                    'viento' => $datos[4],
                ];
                break;
            }
        }

        fclose($file);
    } else {
        echo "El archivo de datos climáticos no existe.";
    }

    return $datos_climaticos;
}

function obtener_ciudades_disponibles() {
    $archivo = 'condiciones_climaticas.txt';
    $ciudades = [];

    if (file_exists($archivo)) {
        $file = fopen($archivo, "r");

        while (($linea = fgets($file)) !== false) {
            $datos = explode(',', trim($linea));
            if (!in_array($datos[0], $ciudades)) {
                $ciudades[] = $datos[0];
            }
        }

        fclose($file);
    }

    return $ciudades;
}

function ciudadesPorActividad($actividad) {
    $archivo = 'condiciones_climaticas.txt';
    $ciudades = [];

    if (file_exists($archivo)) {
        $file = fopen($archivo, "r");

        while (($linea = fgets($file)) !== false) {
            $datos = explode(',', trim($linea));
            if (strtolower($datos[5]) === strtolower($actividad)) {
                $ciudades[] = $datos[0];
            }
        }
        fclose($file);
    }

    return $ciudades;
}


?>
