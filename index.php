<?php
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';
require_once 'categorias.php';

// Instanciar la clase Entrada con la conexión PDO
$entradaObj = new Entrada($pdo);
$categoriaObj = new Categoria($pdo);

// Obtener las últimas entradas
$entradas = $entradaObj->conseguirUltimasEntradas();
$categorias = $categoriaObj->conseguirCategorias();

$_SESSION['loginExito'] = $_SESSION['loginExito'] ?? false;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog de Videojuegos</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
    <script>
        // Alternar visibilidad del formulario de crear entrada
        function toggleFormularioCrearEntrada() {
            const formulario = document.getElementById('formularioCrearEntrada');
            formulario.style.display = formulario.style.display === 'none' ? 'block' : 'none';
        }

        // Alternar visibilidad del formulario de crear categoría
        function toggleFormularioCrearCategoria() {
            const formulario = document.getElementById('formularioCrearCategoria');
            formulario.style.display = formulario.style.display === 'none' ? 'block' : 'none';
        }
    </script>
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
                <li><a href="contacto.php">Contacto</a></li>
            </ul>
        </nav>

    </header>
    <main>
        <section class="content">
            <h2>Últimas entradas</h2>
            <?php if ($entradas): ?>
                <?php foreach ($entradas as $entrada): ?>
                    <article>
                        <h3><?php echo htmlspecialchars($entrada['entrada_titulo']); ?></h3>
                        <p><strong>Categoría:</strong> <?php echo htmlspecialchars($entrada['categoria_nombre']); ?></p>
                        <p><em>Fecha: <?php echo htmlspecialchars($entrada['entrada_fecha']); ?></em></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay entradas disponibles por el momento.</p>
            <?php endif; ?>
            <button onclick="window.location.href='todas_entradas.php'">Ver todas las entradas</button>
        </section>
        <aside>
            <div class="search">
                <h3>Buscar</h3>
                <input type="text" placeholder="Buscar...">
                <button>Buscar</button>
            </div>

            <?php if ($_SESSION['loginExito']): ?>
                <div>
                    <button onclick="toggleFormularioCrearEntrada()">Crear Entrada</button>
                    <form id="formularioCrearEntrada" method="POST" action="crear_entrada.php" style="display: none; margin-top: 10px;">
                        <h3>Nueva Entrada</h3>
                        <label for="titulo">Título:</label>
                        <input type="text" id="titulo" name="titulo" required>

                        <label for="descripcion">Descripción:</label>
                        <textarea id="descripcion" name="descripcion" required></textarea>

                        <label for="categoria">Categoría:</label>
                        <select id="categoria" name="categoria_id" required>
                            <?php
                            $categorias = $entradaObj->conseguirCategorias();
                            if ($categorias):
                                foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo $categoria['id']; ?>">
                                        <?php echo htmlspecialchars($categoria['nombre']); ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>

                        <button type="submit">Guardar Entrada</button>
                    </form>
                </div>
                <div style="margin-top: 20px;">
                    <button onclick="toggleFormularioCrearCategoria()">Crear Categoría</button>
                    <form id="formularioCrearCategoria" method="POST" action="crear_categoria.php" style="display: none; margin-top: 10px;">
                        <h3>Nueva Categoría</h3>
                        <label for="nombreCategoria">Nombre de la categoría:</label>
                        <input type="text" id="nombreCategoria" name="nombreCategoria" required>

                        <button type="submit">Guardar Categoría</button>
                    </form>
                </div>
                <div>
                    <form method="POST" action="logout.php">
                        <button type="submit" name="botonCerrarSesion">Cerrar Sesión</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="login">
                    <h3>Identifícate</h3>
                    <?php if (isset($_SESSION['errorPassLogin'])) echo $_SESSION['errorPassLogin']; ?>
                    <form method="POST" action="login.php">
                        <input type="email" name="emailLogin" placeholder="Email">
                        <input type="password" name="passwordLogin" placeholder="Contraseña">
                        <button type="submit" name="botonLogin">Entrar</button>
                    </form>
                </div>
                <div class="register">
                    <h3>Regístrate</h3>
                    <?php if (isset($_SESSION['success_message'])) echo $_SESSION['success_message']; ?>
                    <form method="POST" action="registro.php">
                        <input type="text" name="nombreRegistro" placeholder="Nombre">
                        <input type="text" name="apellidosRegistro" placeholder="Apellidos">
                        <input type="email" name="emailRegistro" placeholder="Email">
                        <input type="password" name="passwordRegistro" placeholder="Contraseña">
                        <button type="submit" name="botonRegistro">Registrar</button>
                    </form>
                </div>
            <?php endif; ?>
        </aside>
    </main>
</body>

</html>