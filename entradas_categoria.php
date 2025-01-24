<?php
// Conectar a la base de datos usando PDO
require_once 'requires/conexion.php';
require_once 'categorias.php';
require_once 'entradas.php';

// Instanciar la clase Categoria con la conexión PDO
$categoriaObj = new Categoria($pdo);

// Obtener el id de la categoría desde la URL
$categoria_id = isset($_GET['categoria_id']) ? (int)$_GET['categoria_id'] : 0;

// Si no se pasa un id de categoría válido, redirigir o mostrar un error
if ($categoria_id <= 0) {
    die("Categoría no válida.");
}

// Obtener la categoría con el ID proporcionado
$categoria = $categoriaObj->obtenerCategoriaPorId($categoria_id);

// Si no se encuentra la categoría, mostrar un error
if (!$categoria) {
    die("Categoría no encontrada.");
}

// Obtener las entradas asociadas a la categoría
$entradaObj = new Entrada($pdo);
$entradas = $entradaObj->conseguirEntradasPorCategoria($categoria_id);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entradas de <?= htmlspecialchars($categoria['nombre']) ?></title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Blog de Videojuegos</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <!-- Mostrar categorías dinámicamente -->
                <?php
                $categorias = $categoriaObj->conseguirCategorias();
                if ($categorias):
                    foreach ($categorias as $cat): ?>
                        <li>
                            <a href="entradas_categoria.php?categoria_id=<?= htmlspecialchars($cat['id']) ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach;
                else: ?>
                    <li><a href="#">No hay categorías</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <section class="content">
            <h2>Entradas de la categoría: <?= htmlspecialchars($categoria['nombre']) ?></h2>
            <?php if ($entradas): ?>
                <?php foreach ($entradas as $entrada): ?>
                    <article>
                        <h3><?= htmlspecialchars($entrada['titulo']) ?></h3>
                        <p><strong>Descripción:</strong> <?= htmlspecialchars($entrada['descripcion']) ?></p>
                        <p><em>Fecha: <?= htmlspecialchars($entrada['fecha']) ?></em></p>
                        <p><strong>Autor:</strong> <?= htmlspecialchars($entrada['usuario_nombre']) ?></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay entradas disponibles en esta categoría.</p>
            <?php endif; ?>
            <button onclick="window.location.href='index.php'">Volver al Inicio</button>
        </section>
        
    </main>
</body>

</html>
