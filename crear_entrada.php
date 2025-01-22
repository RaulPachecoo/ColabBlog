<?php
// Iniciar sesión y cargar dependencias
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loginExito']) || !$_SESSION['loginExito']) {
    header('Location: index.php'); // Redirigir si no está autenticado
    exit();
}

// Instanciar la clase Entrada
$entradaObj = new Entrada($pdo);

// Procesar el formulario al enviarlo
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger datos del formulario
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria_id = (int)$_POST['categoria_id'];
    $usuario_id = $_SESSION['usuario_id']; // Asumimos que el ID del usuario está almacenado en sesión

    // Validar los datos
    if (!empty($titulo) && !empty($descripcion) && $categoria_id > 0) {
        // Intentar crear la entrada
        $resultado = $entradaObj->crearEntrada($usuario_id, $categoria_id, $titulo, $descripcion);

        if ($resultado) {
            $_SESSION['mensaje'] = 'Entrada creada con éxito.';
            header('Location: index.php'); // Redirigir al inicio
            exit();
        } else {
            $error = 'Error al guardar la entrada. Inténtalo nuevamente.';
        }
    } else {
        $error = 'Todos los campos son obligatorios.';
    }
}

// Obtener categorías para el formulario
$categorias = $entradaObj->conseguirCategorias();
?>

