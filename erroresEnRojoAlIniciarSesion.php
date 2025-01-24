<?php

session_start();

require_once 'requires/conexion.php';

// Inicializamos variables de sesión si no están definidas
$_SESSION['errorInicioSesion'] = $_SESSION['errorInicioSesion'] ?? 0;
$_SESSION['ultimoIntento'] = $_SESSION['ultimoIntento'] ?? time();
$_SESSION['loginExito'] = $_SESSION['loginExito'] ?? false;

// Inicializamos valores de email y password desde cookies si existen
$email = isset($_COOKIE['emailLogin']) ? $_COOKIE['emailLogin'] : '';
$passwordCookie = isset($_COOKIE['passwordLogin']) ? $_COOKIE['passwordLogin'] : '';

$errorEmail = false;
$errorPassword = false;

// Formulario de Inicio de Sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['botonLogin']) && $_SESSION['errorInicioSesion'] < 3) {
    $email = filter_var(trim($_POST['emailLogin']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['passwordLogin']);
    $rememberMe = isset($_POST['rememberMe']); // Verificamos si se marcó "Recuérdame"

    if (!$email) {
        $errorEmail = true;
    }
    if (empty($password)) {
        $errorPassword = true;
    }

    if (!$errorEmail && !$errorPassword) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                $_SESSION['errorInicioSesion'] = 0;
                $_SESSION['loginExito'] = true;

                // Guardamos cookies si "Recuérdame" está seleccionado
                if ($rememberMe) {
                    setcookie('emailLogin', $email, time() + (86400 * 30), "/"); // 30 días
                    setcookie('passwordLogin', $password, time() + (86400 * 30), "/");
                } else {
                    // Eliminamos las cookies si no está seleccionado
                    setcookie('emailLogin', '', time() - 3600, "/");
                    setcookie('passwordLogin', '', time() - 3600, "/");
                }

                header("Location: dashboard.php");
                exit();
            } else {
                $errorPassword = true;
                $_SESSION['errorInicioSesion']++;
                $_SESSION['ultimoIntento'] = time();
            }
        } else {
            $errorEmail = true;
        }
    }
}

// Controlamos los intentos fallidos de inicio de sesión
if ($_SESSION['errorInicioSesion'] >= 3) {
    $tiempoRestante = time() - $_SESSION['ultimoIntento'];
    if ($tiempoRestante < 5) {
        echo "<script> 
        setTimeout(function() {
            window.location.reload();
        }, 5000);
        </script>";
    } else {
        $_SESSION['errorInicioSesion'] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <style>
        .error {
            border: 2px solid red;
        }
        .error-text {
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <h1>Inicio de Sesión</h1>
    <form method="POST" action="">
        <label for="emailLogin">Email:</label><br>
        <input type="email" id="emailLogin" name="emailLogin" 
               value="<?= htmlspecialchars($email); ?>" 
               class="<?= $errorEmail ? 'error' : ''; ?>" required><br>
        <?php if ($errorEmail): ?>
            <span class="error-text">El email es incorrecto o no existe.</span><br>
        <?php endif; ?><br>

        <label for="passwordLogin">Contraseña:</label><br>
        <input type="password" id="passwordLogin" name="passwordLogin" 
               value="<?= htmlspecialchars($passwordCookie); ?>" 
               class="<?= $errorPassword ? 'error' : ''; ?>" required><br>
        <?php if ($errorPassword): ?>
            <span class="error-text">La contraseña es incorrecta.</span><br>
        <?php endif; ?><br>

        <label>
            <input type="checkbox" name="rememberMe" <?= isset($_COOKIE['emailLogin']) ? 'checked' : ''; ?>> Recuérdame
        </label><br><br>

        <button type="submit" name="botonLogin">Iniciar Sesión</button>
    </form>
</body>
</html>
