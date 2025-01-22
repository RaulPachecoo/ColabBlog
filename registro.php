<?php
// Iniciar sesión
session_start();
require_once 'requires/conexion.php';

// Asegurarse de que $pdo esté definido
if (!isset($pdo)) {
    $_SESSION['registro_mensaje'] = "Error en la conexión a la base de datos.";
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    // Validar y limpiar entradas
    $nombre = isset($_POST['nombreRegistro']) ? trim($_POST['nombreRegistro']) : null;
    $apellidos = isset($_POST['apellidosRegistro']) ? trim($_POST['apellidosRegistro']) : null;
    $email = isset($_POST['emailRegistro']) ? filter_var(trim($_POST['emailRegistro']), FILTER_VALIDATE_EMAIL) : null;
    $password = isset($_POST['passwordRegistro']) ? trim($_POST['passwordRegistro']) : null;

    if ($nombre && $apellidos && $email && $password) {
        try {
            // Comprobar si el email ya existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() == 0) {
                // Insertar el nuevo usuario
                $password_hash = password_hash($password, PASSWORD_BCRYPT);
                $fecha = date('Y-m-d H:i:s'); // Fecha actual en formato SQL

                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellidos, email, password, fecha) VALUES (:nombre, :apellidos, :email, :password, :fecha)");
                $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
                $stmt->bindParam(':apellidos', $apellidos, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password_hash, PDO::PARAM_STR);
                $stmt->bindParam(':fecha', $fecha, PDO::PARAM_STR);
                $stmt->execute();
                $_SESSION['registro_mensaje'] = "Usuario registrado correctamente.";
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['registro_mensaje'] = "El email ya está registrado.";
            }
        } catch (PDOException $e) {
            $_SESSION['registro_mensaje'] = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $_SESSION['registro_mensaje'] = "Por favor completa todos los campos del formulario.";
    }
}

// Redirigir de vuelta a index.php
header("Location: index.php");
exit;
