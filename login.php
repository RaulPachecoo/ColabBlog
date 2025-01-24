<?php

session_start();

require_once 'requires/conexion.php';

// 7. Definimos una variable de sesión para controlar los 3 intentos fallidos de inicio de sesión
$_SESSION['errorInicioSesion'] = $_SESSION['errorInicioSesion'] ?? 0;
$_SESSION['ultimoIntento'] = $_SESSION['ultimoIntento'] ?? time();
$_SESSION['loginExito'] = $_SESSION['loginExito'] ?? false;

// 6. Formulario de Inicio de Sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['botonLogin']) && $_SESSION['errorInicioSesion'] < 3) {
    // Comprobamos que el email es válido
    $email = filter_var(trim($_POST['emailLogin']), FILTER_VALIDATE_EMAIL);
    // Comprobamos que la contraseña es válida
    $password = trim($_POST['passwordLogin']);
    $recordarme = isset($_POST['recordarme']); // Verificar si se seleccionó el checkbox "Recuérdame"

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch();

            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Inicio de sesión exitoso
                $_SESSION['errorInicioSesion'] = 0; // Restablecer intentos fallidos
                $_SESSION['loginExito'] = true;
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];

                if ($recordarme) {
                    // Establecer cookies válidas por 1 día
                    setcookie('usuario_email', $email, time() + 86400, '/');
                    setcookie('usuario_id', $user['id'], time() + 86400, '/');
                }

                // Redirigir al índice tras un login exitoso
                header("Location: index.php");
                exit();
            } else {
                // Contraseña incorrecta
                $_SESSION['errorPassLogin'] = "La contraseña no es correcta.";
                $_SESSION['errorInicioSesion']++;
                $_SESSION['ultimoIntento'] = time(); // Guardar la hora del último intento fallido
            }
        } else {
            // El email no existe
            $_SESSION['errorPassLogin'] = "El email no existe en nuestra Base de Datos.";
        }
    } else {
        // Email o contraseña no válidos
        $_SESSION['errorPassLogin'] = "Email o contraseña erróneos.";
    }

    // Redirigir al índice tras procesar el formulario
    header("Location: index.php");
    exit();
}


// 7. Controlamos los 3 intentos fallidos de inicio de sesión
if ($_SESSION['errorInicioSesion'] >= 3) {
    $tiempoRestante = time() - $_SESSION['ultimoIntento'];
    if ($tiempoRestante < 5) {
        // Bloqueo al usuario durante 5 segundos
        echo "<script> 
        setTimeout(function() {
            window.location.reload();
        }, 5000);
        </script>";
    } else {
        // Hacemos un reset de los errores si han pasado más de 5 segundos
        $_SESSION['errorInicioSesion'] = 0;
    }
}
?>
