<?php
session_start();
require_once 'requires/conexion.php';
require_once 'categorias.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCategoria = $_POST['nombreCategoria'] ?? '';

    if (!empty($nombreCategoria)) {
        try {
            $entradaObj = new Entrada($pdo);
            $categoriaObj = new Categoria($pdo); 
            $resultado = $categoriaObj->insertarCategoria($nombreCategoria);

            if ($resultado === true) {
                $_SESSION['success_message'] = "Categoría creada con éxito.";
            } else {
                $_SESSION['error_message'] = $resultado;  // Por ejemplo, "La categoría ya existe."
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error al crear la categoría: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "El nombre de la categoría no puede estar vacío.";
    }
}

// Redirigir a la página principal o donde desees
header('Location: index.php');
exit();
?>