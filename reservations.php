<?php
session_start();
include 'reservations_functions.php';

//if (!isset($_SESSION['userId'])) {
//    header('Location: login_register.php');
//    exit();
//}

//$userId = $_SESSION['userId'];
$userId =4;
$_SESSION['username'] = 'kevin';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['guideId'])) {
    $guideId = $_GET['guideId'];
    $reservations = getReservations($userId);

    $response = null;

    foreach ($reservations as $reservation) {
        $details = explode(' reservado por Usuario ', $reservation);
        $guideName = $details[0];
        $reservedGuideId = $details[1];
        
        if ($reservedGuideId == $guideId) {
            $response = [
                'id' => $reservedGuideId,
                'user' => $_SESSION['username'], 
                'reservado' => true,
                'guia' => $guideName
            ];
            break;
        }
    }

    if ($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        header('Content-Type: application/json', true, 404);
        echo json_encode(['error' => 'Reserva no encontrada']);
    }
    exit();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel'])) {
        $guideId = $_POST['guideId'];
        cancelReservation($guideId, $userId);
        header('Location: reservations.php');
        exit();
    }
}

$reservations = getReservations($userId);
?>

<html>
<head>
    <title>Mis Reservas</title>
</head>
<body>
<h2>Mis Reservas:</h2>
<?php if (empty($reservations)): ?>
    <p>No tienes reservas.</p>
<?php else: ?>
    <ul>
        <?php foreach ($reservations as $reservation): ?>
            <li><?php echo htmlspecialchars($reservation); ?></li>
        <?php endforeach; ?>
    </ul>
    <h2>Cancelar reserva:</h2>
    <form method="POST" action="">
        <label for="guideId">Selecciona un guía para cancelar:</label><br>
        <select id="guideId" name="guideId" required>
            <?php
            foreach ($reservations as $reservation) {
                $details = explode(' reservado por Usuario ', $reservation);
                $guideName = $details[0];
                $guideId = $details[1];
                echo "<option value=\"$guideId\">$guideName</option>";
            }
            ?>
        </select><br><br>
        <input type="submit" name="cancel" value="Cancelar reserva">
    </form>
<?php endif; ?>
<a href="index.php">Volver a la página principal</a>
</body>
</html>
