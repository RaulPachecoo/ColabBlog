<?php
// Conectar a la base de datos usando PDO
require_once 'requires/conexion.php';

// Función para obtener las categorías desde la base de datos usando PDO
function conseguirCategorias($pdo) {
    $sql = "SELECT * FROM categorias ORDER BY nombre ASC";  // Consulta SQL
    $stmt = $pdo->query($sql);  // Ejecutar la consulta con PDO

    if (!$stmt) {
        throw new Exception("Error en la consulta: " . implode(", ", $pdo->errorInfo()));  // En caso de error
    }

    // Devolver el resultado como un array de categorías
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Función para insertar una nueva categoría
function insertarCategoria($pdo, $nombre_categoria) {
    // Verificar si la categoría ya existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorias WHERE nombre = :nombre");
    $stmt->execute([':nombre' => $nombre_categoria]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        return "La categoría ya existe.";
    }

    // Insertar la nueva categoría
    $stmt = $pdo->prepare("INSERT INTO categorias (nombre) VALUES (:nombre)");
    $stmt->execute([':nombre' => $nombre_categoria]);

    return true;
}

// Inicializar un array para los errores
$errores = [];

// Validar si se ha enviado el formulario para crear una nueva categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger el valor del formulario y limpiarlo
    $nombre_categoria = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';

    // Validación del nombre de la categoría
    if (empty($nombre_categoria)) {
        $errores[] = "El nombre de la categoría es obligatorio.";
    } elseif (strlen($nombre_categoria) > 100) {
        $errores[] = "El nombre de la categoría no puede exceder los 100 caracteres.";
    }

    // Si no hay errores, insertar la categoría en la base de datos
    if (empty($errores)) {
        $resultado = insertarCategoria($pdo, $nombre_categoria);

        if ($resultado === true) {
            // Redirigir a index.php después de la inserción
            header("Location: index.php");
            exit;
        } else {
            $errores[] = $resultado; // En caso de error (categoría ya existe)
        }
    }
}

// Obtener las categorías utilizando la función conseguirCategorias
try {
    $categorias = conseguirCategorias($pdo);
} catch (Exception $e) {
    die("Error al obtener las categorías: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Categorías</title>
</head>
<body>
    <h1>Gestor de Categorías</h1>

    <!-- Mostrar errores de validación -->
    <?php if (!empty($errores)): ?>
        <div style="color: red;">
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Formulario para crear nueva categoría -->
    <h2>Crear Nueva Categoría</h2>
    <form action="categorias.php" method="POST">
        <label for="nombre">Nombre de la categoría:</label>
        <input type="text" name="nombre" id="nombre" maxlength="100" required>
        <br><br>
        <button type="submit">Crear categoría</button>
    </form>

    <!-- Listado de categorías -->
    <h2>Listado de Categorías</h2>
    <ul>
        <?php if (count($categorias) > 0): ?>
            <?php foreach ($categorias as $categoria): ?>
                <li>
                    <?= htmlspecialchars($categoria['nombre']) ?>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>No hay categorías disponibles.</li>
        <?php endif; ?>
    </ul>
</body>
</html>