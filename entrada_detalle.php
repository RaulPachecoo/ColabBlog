<?php
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';

// Verificar si se ha pasado un ID de entrada
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$entrada_id = intval($_GET['id']);

// Instanciar la clase Entrada con la conexión PDO
$entradaObj = new Entrada($pdo);

// Obtener la entrada específica por su ID
$entrada = $entradaObj->conseguirEntradaPorId($entrada_id);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de la Entrada - Blog de Videojuegos</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Blog de Videojuegos</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li><a href="todas_entradas.php">Todas las Entradas</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="content">
            <?php if ($entrada): ?>
                <h2><?php echo htmlspecialchars($entrada['entrada_titulo']); ?></h2>
                <p><strong>Categoría:</strong> <?php echo htmlspecialchars($entrada['categoria_nombre']); ?></p>
                <p><em>Fecha: <?php echo htmlspecialchars($entrada['entrada_fecha']); ?></em></p>
                <p><?php echo nl2br(htmlspecialchars($entrada['entrada_descripcion'])); ?></p>

                <!-- Botones para Editar y Borrar (Solo usuarios logueados) -->
                <?php if (isset($_SESSION['loginExito']) && $_SESSION['loginExito']): ?>
                    <div class="actions">
                        <!-- Botón de Editar -->
                        <form method="GET" action="editar_entrada.php" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $entrada_id; ?>">
                            <button type="submit" class="button">Editar</button>
                        </form>

                        <!-- Botón de Borrar -->
                        <form method="POST" action="borrar_entrada.php" style="display: inline;">
                            <input type="hidden" name="entrada_id" value="<?php echo $entrada_id; ?>">
                            <button type="submit" class="button red">Borrar</button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>No se encontró la entrada.</p>
            <?php endif; ?>

            <form action="todas_entradas.php" method="GET">
                <button type="submit" class="button">Volver a Todas las Entradas</button>
            </form>
        </section>
    </main>
</body>

</html>
