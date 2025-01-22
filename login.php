<?php
session_start();

$_SESSION['errorInicioSesion'] = $_SESSION['errorInicioSesion'] ?? 0;
$_SESSION['ultimoIntento'] = $_SESSION['ultimoIntento'] ?? time();

require_once 'requires/conexion.php';

// Manejo del formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login']) && $_SESSION['errorInicioSesion'] < 3) {
    $email = filter_var(trim($_POST['emailLogin']), FILTER_VALIDATE_EMAIL);
    $password = trim($_POST['passwordLogin']);

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                $_SESSION['errorInicioSesion'] = 0;
                $_SESSION['rol'] = $user['rol'];
                $_SESSION['nombre'] = $user['email'];
                header("Location: index.php");
                exit();
            } else {
                $_SESSION['errorInicioSesion']++;
                $_SESSION['ultimoIntento'] = time();
                echo "Contraseña incorrecta.";
            }
        } else {
            echo "Email no registrado.";
        }
    } else {
        echo "Por favor completa todos los campos de inicio de sesión.";
    }
}

// Si se superan los 3 intentos, bloquear temporalmente
if ($_SESSION['errorInicioSesion'] >= 3) {
    $tiempoRestante = time() - $_SESSION['ultimoIntento']; // Tiempo transcurrido desde el último intento fallido
    if ($tiempoRestante < 5) {
        // Si no han pasado los 5 segundos, bloquear al usuario y mostrar un mensaje
        echo "<h2>Has superado el número de intentos permitidos. Por favor, espera 5 segundos.</h2>";
        echo "<script>
            setTimeout(function() {
                window.location.reload(); // Recargar la página después de 5 segundos
            }, 5000);
        </script>";
        exit(); // Detener la ejecución para que no continúe con el resto del código
    } else {
        // Si ya han pasado los 5 segundos, reseteamos los errores
        $_SESSION['errorInicioSesion'] = 0;
    }
}
?>
