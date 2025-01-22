<?php
// Conectar a la base de datos usando PDO
require_once 'requires/conexion.php';

// Función para obtener las entradas de una categoría
function conseguirEntradasPorCategoria($pdo, $categoria_id) {
    // Consulta para obtener todas las entradas asociadas a una categoría
    $sql = "SELECT e.id, e.titulo, e.descripcion, e.fecha, u.nombre AS usuario_nombre 
            FROM entradas e
            JOIN usuarios u ON e.usuario_id = u.id
            WHERE e.categoria_id = :categoria_id
            ORDER BY e.fecha DESC";  // Ordenamos por fecha de manera descendente
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':categoria_id' => $categoria_id]);

    // Devolver el resultado de las entradas
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener el id de la categoría desde la URL
$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

// Si no se pasa un id de categoría válido, redirigir o mostrar un error
if ($categoria_id <= 0) {
    die("Categoría no válida.");
}

// Obtener las entradas asociadas a la categoría
try {
    $entradas = conseguirEntradasPorCategoria($pdo, $categoria_id);
} catch (Exception $e) {
    die("Error al obtener las entradas: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas de la Categoría</title>
</head>
<body>
    <h1>Entradas de la Categoría</h1>

    <!-- Listado de entradas -->
    <?php if (count($entradas) > 0): ?>
        <ul>
            <?php foreach ($entradas as $entrada): ?>
                <li>
                    <h3><?= htmlspecialchars($entrada['titulo']) ?></h3>
                    <p><strong>Autor:</strong> <?= htmlspecialchars($entrada['usuario_nombre']) ?></p>
                    <p><strong>Fecha:</strong> <?= htmlspecialchars($entrada['fecha']) ?></p>
                    <p><?= nl2br(htmlspecialchars($entrada['descripcion'])) ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No hay entradas disponibles en esta categoría.</p>
    <?php endif; ?>

    <!-- Enlace para volver a la lista de categorías -->
    <p><a href="categorias.php">Volver a las categorías</a></p>
</body>
</html>