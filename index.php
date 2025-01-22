<?php
// Iniciar sesión
session_start();
require_once 'requires/conexion.php'; // Archivo con la conexión a la base de datos
require_once 'entradas.php'; // Clase Entrada para manejar las entradas

// Instanciar la clase Entrada
$entradaObj = new Entrada($pdo);

// Obtener las últimas entradas
$entradas = $entradaObj->conseguirUltimasEntradas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog de Videojuegos</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>
<body>
    <header>
        <h1>Blog de Videojuegos</h1>
        <nav>
            <ul>
                <li><a href="#">Inicio</a></li>
                <li><a href="#">Acción</a></li>
                <li><a href="#">Rol</a></li>
                <li><a href="#">Deportes</a></li>
                <li><a href="#">Responsabilidad</a></li>
                <li><a href="#">Contacto</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="content">
            <h2>Últimas entradas</h2>

            <!-- Mostrar las últimas entradas dinámicamente -->
            <?php if ($entradas): ?>
                <?php foreach ($entradas as $entrada): ?>
                    <article>
                        <h3><?php echo htmlspecialchars($entrada['entrada_titulo']); ?></h3>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($entrada['categoria_nombre']); ?></p>
                        <p><?php echo htmlspecialchars($entrada['entrada_descripcion']); ?></p>
                        <p><em>Fecha: <?php echo htmlspecialchars($entrada['entrada_fecha']); ?></em></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay entradas disponibles por el momento.</p>
            <?php endif; ?>

            <button>Ver todas las entradas</button>
        </section>
        <aside>
            <div class="search">
                <h3>Buscar</h3>
                <input type="text" placeholder="Buscar...">
                <button>Buscar</button>
            </div>
            <div class="login">
                <h3>Identifícate</h3>
                <form action="login.php" method="POST">
                    <input type="email" name="emailLogin" placeholder="Email">
                    <input type="password" name="passwordLogin" placeholder="Contraseña">
                    <button type="submit" name="login">Entrar</button>
                </form>
            </div>
            <div class="register">
                <h3>Regístrate</h3>
                <?php
                if (isset($_SESSION['registro_mensaje'])) {
                    echo "<p style='background-color: green; color: white;'>{$_SESSION['registro_mensaje']}</p>";
                    unset($_SESSION['registro_mensaje']);
                }
                ?>
                <form method="POST" action="registro.php">
                    <input type="text" name="nombreRegistro" placeholder="Nombre" required>
                    <input type="text" name="apellidosRegistro" placeholder="Apellidos" required>
                    <input type="email" name="emailRegistro" placeholder="Email" required>
                    <input type="password" name="passwordRegistro" placeholder="Contraseña" required>
                    <button type="submit" name="register">Registrar</button>
                </form>
            </div>
        </aside>
    </main>
</body>
</html>
