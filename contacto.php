<?php
// Conectar a la base de datos usando PDO
require_once 'requires/conexion.php';
require_once 'categorias.php'; 

$categoriaObj = new Categoria($pdo); 
$categorias = $categoriaObj->conseguirCategorias();
// Inicializar un array para los errores
$errores = [];
$exito = '';  // Variable para mostrar el mensaje de éxito

// Validar si se ha enviado el formulario de contacto
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger los valores del formulario y limpiarlos
    $gmail = isset($_POST['gmail']) ? trim($_POST['gmail']) : '';
    $asunto = isset($_POST['asunto']) ? trim($_POST['asunto']) : '';
    $texto = isset($_POST['texto']) ? trim($_POST['texto']) : '';

    // Validación del correo electrónico
    if (empty($gmail)) {
        $errores[] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($gmail, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electrónico no es válido.";
    }

    // Validación del asunto
    if (empty($asunto)) {
        $errores[] = "El asunto es obligatorio.";
    }

    // Validación del texto
    if (empty($texto)) {
        $errores[] = "El mensaje es obligatorio.";
    }

    // Si no hay errores, insertar el mensaje en la base de datos
    if (empty($errores)) {
        // Preparar la consulta SQL para insertar los datos
        $sql = "INSERT INTO contacto (gmail, asunto, texto) VALUES (:gmail, :asunto, :texto)";
        
        try {
            // Preparar la declaración
            $stmt = $pdo->prepare($sql);

            // Ejecutar la consulta con los parámetros
            $stmt->execute([
                ':gmail' => $gmail,
                ':asunto' => $asunto,
                ':texto' => $texto
            ]);

            // Mensaje de éxito
            $exito = "Mensaje enviado con éxito. ¡Gracias por contactarnos!";
        } catch (PDOException $e) {
            $errores[] = "Error al enviar el mensaje: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Blog de Videojuegos</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
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
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="content">
            <h2>Formulario de Contacto</h2>

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

            <!-- Formulario de contacto -->
            <form action="contacto.php" method="POST">
                <label for="gmail">Correo Electrónico:</label>
                <input type="email" name="gmail" id="gmail" required>
                <br><br>

                <label for="asunto">Asunto:</label>
                <input type="text" name="asunto" id="asunto" required>
                <br><br>

                <label for="texto">Mensaje:</label>
                <textarea name="texto" id="texto" rows="5" required></textarea>
                <br><br>

                <button type="submit">Enviar Mensaje</button>
            </form>

            <!-- Mostrar mensaje de éxito debajo del botón -->
            <?php if ($exito): ?>
                <div style="color: green; margin-top: 20px;">
                    <p><?= htmlspecialchars($exito) ?></p>
                </div>
            <?php endif; ?>
        </section>

        <aside>
            <div class="search">
                <h3>Buscar</h3>
                <input type="text" placeholder="Buscar...">
                <button>Buscar</button>
            </div>
        </aside>
    </main>
</body>

</html>
