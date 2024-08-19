<?php
session_start();

function registerUser($username, $password) {
    $passwordHash = md5($password);
    $id = generateUserId();
    $userData = "$id|$username|$passwordHash\n";
    file_put_contents('users.txt', $userData, FILE_APPEND);
} 

function generateUserId() {
    $lines = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return count($lines) + 1; // Genera un ID único
}

function authenticateUser($username, $password) {
    $passwordHash = md5($password);
    $lines = file('users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        list($id, $name, $hash) = explode('|', trim($line));
        if ($name === $username && $hash === $passwordHash) {
            $_SESSION['userId'] = $id;
            $_SESSION['username'] = $username;
            return true;
        }
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        registerUser($username, $password);
        echo "<p>Registro exitoso. <a href='login_register.php'>Iniciar sesión</a></p>";
    } elseif (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (authenticateUser($username, $password)) {
            header('Location: index.php');
            exit();
        } else {
            $error = 'Nombre de usuario o contraseña incorrectos.';
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Iniciar sesión / Registrarse</title>
</head>
<body>
<h2>Iniciar sesión</h2>
<form method="POST" action="">
    <label for="username">Nombre de usuario:</label>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required><br>
    <input type="submit" name="login" value="Iniciar sesión">
    <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
</form>
<h2>Registrar usuario</h2>
<form method="POST" action="">
    <label for="username">Nombre de usuario:</label>
    <input type="text" id="username" name="username" required><br>
    <label for="password">Contraseña:</label>
    <input type="password" id="password" name="password" required><br>
    <input type="submit" name="register" value="Registrar">
</form>
</body>
</html>
