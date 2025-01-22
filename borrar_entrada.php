<?php
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loginExito']) || !$_SESSION['loginExito']) {
    header("Location: index.php");
    exit();
}

// Verificar si se ha enviado un ID de entrada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrada_id'])) {
    $entrada_id = intval($_POST['entrada_id']);

    // Instanciar la clase Entrada
    $entradaObj = new Entrada($pdo);

    // Intentar eliminar la entrada
    if ($entradaObj->borrarEntrada($entrada_id)) {
        header("Location: todas_entradas.php?mensaje=Entrada eliminada");
        exit();
    } else {
        echo "Error al intentar eliminar la entrada.";
    }
}
