<?php
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';
require_once 'categorias.php'; 

// Instanciar la clase Entrada con la conexión PDO
$entradaObj = new Entrada($pdo);
$categoriaObj = new Categoria($pdo);
// Obtener todas las entradas
$entradas = $entradaObj->conseguirUltimasEntradas();
$categorias = $categoriaObj->conseguirCategorias();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todas las Entradas - Blog de Videojuegos</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Blog de Videojuegos</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <!-- Mostrar categorías dinámicamente -->
                <?php if ($categorias): ?>
                    <?php foreach ($categorias as $categoria): ?>
                        <li>
                            <a href="entradas_categoria.php?categoria_id=<?= htmlspecialchars($categoria['id']) ?>">
                                <?= htmlspecialchars($categoria['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><a href="#">No hay categorías</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <section class="content">
            <h2>Todas las Entradas</h2>

            <?php if ($entradas): ?>
                <?php foreach ($entradas as $entrada): ?>
                    <article>
                        <h3>
                            <a href="entrada_detalle.php?id=<?php echo htmlspecialchars($entrada['entrada_id']); ?>">
                                <?php echo htmlspecialchars($entrada['titulo']); ?>
                            </a>
                        </h3>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($entrada['categoria_nombre']); ?></p>
                        <p><em>Fecha: <?php echo htmlspecialchars($entrada['fecha']); ?></em></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay entradas disponibles en este momento.</p>
            <?php endif; ?>

            <!-- Botón para Volver al Inicio -->
            <form action="index.php" method="GET">
                <button type="submit" class="button">Volver al Inicio</button>
            </form>
        </section>
    </main>
</body>

</html>
