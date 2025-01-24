<?php
require_once 'requires/conexion.php';

$resultados = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['busqueda'])) {
    $busqueda = trim($_POST['busqueda']);

    if (!empty($busqueda)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM entradas WHERE titulo LIKE :busqueda");
            $stmt->execute(['busqueda' => '%' . $busqueda . '%']);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error en la base de datos: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color:red;'>Por favor, ingresa un término de búsqueda.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar en el Blog</title>
</head>
<body>
    <h1>Buscar Entradas en el Blog</h1>
    <form method="POST" action="">
        <label for="busqueda">Título de la entrada:</label><br>
        <input type="text" id="busqueda" name="busqueda" placeholder="Buscar..." required><br><br>
        <button type="submit">Buscar</button>
    </form>

    <h2>Resultados de la búsqueda</h2>
    <?php if (!empty($resultados)): ?>
        <ul>
            <?php foreach ($resultados as $entrada): ?>
                <li>
                    <strong><?= htmlspecialchars($entrada['titulo']); ?></strong><br>
                    <?= htmlspecialchars($entrada['contenido']); ?><br>
                    <small>Publicado el <?= htmlspecialchars($entrada['fecha']); ?></small>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
        <p>No se encontraron resultados para "<?= htmlspecialchars($busqueda); ?>".</p>
    <?php endif; ?>
</body>
</html>
