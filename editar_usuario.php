<?php
session_start();
require_once 'requires/conexion.php';
require_once 'usuario.php'; // Incluir la clase Usuario

// Verificar si el usuario está logueado
if (!isset($_SESSION['loginExito']) || !$_SESSION['loginExito']) {
    header('Location: login.php');
    exit;
}

// Crear una instancia de la clase Usuario
$usuarioObj = new Usuario($pdo);

// Obtener los datos del usuario actual de la sesión
$usuarioId = $_SESSION['usuario_id'] ?? null;
if (!$usuarioId) {
    // Si no hay un ID de usuario en la sesión, redirigir
    header('Location: login.php');
    exit;
}

// Obtener los datos del usuario desde la base de datos
$usuarioData = $usuarioObj->obtenerUsuarioPorId($usuarioId);

// Si el formulario es enviado, procesar la edición
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editarUsuario'])) {
    // Obtener los datos del formulario
    $nombre = trim($_POST['nombre']);
    $apellidos = trim($_POST['apellidos']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validar que los campos obligatorios estén llenos
    if (empty($nombre) || empty($apellidos) || empty($email)) {
        $_SESSION['error_message'] = "Todos los campos son obligatorios.";
    } else {
        try {
            // Editar el usuario
            $usuarioObj->editarUsuario($usuarioId, $nombre, $apellidos, $email, $password);
            
            // Guardar los nuevos valores en la sesión
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_apellidos'] = $apellidos;
            $_SESSION['usuario_email'] = $email;

            $_SESSION['success_message'] = "Datos actualizados correctamente.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
    }
}
header('Location: index.php'); 

?>

<!-- Mostrar mensaje de error o éxito -->
<?php if (isset($_SESSION['error_message'])): ?>
    <p style="color: red;"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
<?php elseif (isset($_SESSION['success_message'])): ?>
    <p style="color: green;"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
<?php endif; ?>

