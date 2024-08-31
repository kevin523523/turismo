<?php
function ciudadesPorActividad($actividad) {
    $archivo = 'informacionCiudades.txt';
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


function getFotoCiudad($ciudad) {
    $archivo = 'informacionCiudades.txt';
    $foto = '';

    if (file_exists($archivo)) {
        $file = fopen($archivo, "r");

        while (($linea = fgets($file)) !== false) {
            $datos = explode(',', trim($linea));
            if (strtolower($datos[0]) === strtolower($ciudad)) {
                $foto = $datos[6]; 
                break;
            }
        }

        fclose($file);
    } else {
        echo "El archivo de información climática no existe.";
    }

    return $foto;
}


?>