<?php
session_start();
include 'reservations_functions.php';
include 'funciones_clima.php';
include 'calificacion_testimonios.php';

if (!isset($_SESSION['userId'])) {
    header('Location: login_register.php');
    exit();
}

$userId = $_SESSION['userId'];

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
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        header('Location: login_register.php');
        exit();
    } elseif (isset($_POST['ciudad'])) {
        $ciudad_seleccionada = $_POST['ciudad'];
        $datos_climaticos = obtener_datos_climaticos($ciudad_seleccionada);
    } elseif (isset($_POST['show_ratings']) && isset($_POST['guideId'])) {
        $guideId = $_POST['guideId'];
        $calificacion = obtener_calificacion_general($guideId);
        $testimonios = obtener_testimonios($guideId);
    }
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

<!DOCTYPE html>
<html>
<head>
    <title>Reserva de Guías</title>
</head>
<body>
<h2>Seleccione un guía para reservar:</h2>
<?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
<form method="POST" action="">
    <label for="guideId">Guías disponibles:</label><br>
    <select id="guideId" name="guideId" required>
        <?php
        $guides = getAvailableGuides();
        foreach ($guides as $guide) {
            echo "<option value=\"{$guide[0]}\">{$guide[1]}</option>";
        }
        ?>
    </select><br><br>
    <input type="submit" name="reserve" value="Reservar">
    <input type="submit" name="logout" value="Cerrar sesión">
    <input type="submit" name="show_ratings" value="Ver calificaciones y testimonios">
</form>
<a href="reservations.php">Ver reservas</a>

<?php if (isset($calificacion) && isset($testimonios)): ?>
    <h3>Calificación del Guía</h3>
    <p>Calificación general: <?php echo $calificacion; ?>/5</p>

    <h3>Testimonios de Usuarios</h3>
    <?php if (!empty($testimonios)): ?>
        <ul>
            <?php foreach ($testimonios as $testimonio): ?>
                <li><?php echo htmlspecialchars($testimonio); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay testimonios para este guía.</p>
    <?php endif; ?>
<?php endif; ?>

<h2>Consultar datos climáticos</h2>
<form method="POST" action="">
    <label for="ciudad">Seleccione una ciudad:</label>
    <select name="ciudad" id="ciudad">
        <option value="" disabled selected>Ciudades</option>
        <?php foreach ($ciudades as $ciudad): ?>
            <option value="<?php echo $ciudad; ?>"><?php echo $ciudad; ?></option>
        <?php endforeach; ?>
    </select>
    <input type="submit" value="Ver Datos">
</form>

<?php if (!empty($datos_climaticos)): ?>
    <h3>Datos climáticos para <?php echo htmlspecialchars($ciudad_seleccionada); ?></h3>
    <table border="1">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Temperatura (°C)</th>
                <th>Humedad (%)</th>
                <th>Viento (km/h)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos_climaticos as $dato): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dato['fecha']); ?></td>
                    <td><?php echo htmlspecialchars($dato['temperatura']); ?></td>
                    <td><?php echo htmlspecialchars($dato['humedad']); ?></td>
                    <td><?php echo htmlspecialchars($dato['viento']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($ciudad_seleccionada)): ?>
    <p>No se encontraron datos para la ciudad seleccionada.</p>
<?php endif; ?>

</body>
</html>