<?php
session_start();

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Eliminar cookies de autenticación si existen
if (isset($_COOKIE['usuario_email'])) {
    setcookie('usuario_email', '', time() - 3600, '/'); // Caduca la cookie
}
if (isset($_COOKIE['usuario_id'])) {
    setcookie('usuario_id', '', time() - 3600, '/'); // Caduca la cookie
}

// Redirigir al índice después de cerrar sesión
header("Location: index.php");
exit();
?>