<?php
// Configuración de la base de datos
$dsn = 'mysql:host=localhost;dbname=blog;charset=utf8mb4';
$usuario = 'root';
$password = '';

try {
    // Crear la conexión con PDO
    $pdo = new PDO($dsn, $usuario, $password);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo("Conexión realizada con éxito");
} catch (PDOException $e) {
    // Manejo de errores en caso de que falle la conexión
    die("Error en la conexión: " . $e->getMessage());
}
?>