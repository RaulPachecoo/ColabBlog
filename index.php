<?php
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';
require_once 'categorias.php';
require_once 'usuario.php';  // Incluir la clase Usuario

// Instanciar las clases necesarias con la conexión PDO
$usuarioObj = new Usuario($pdo);
$entradaObj = new Entrada($pdo);
$categoriaObj = new Categoria($pdo);

if (!isset($_SESSION['loginExito']) && isset($_COOKIE['usuario_email']) && isset($_COOKIE['usuario_id'])) {
    $email = $_COOKIE['usuario_email'];
    $userId = $_COOKIE['usuario_id'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email AND id = :id");
    $stmt->execute(['email' => $email, 'id' => $userId]);

    if ($stmt->rowCount() == 1) {
        $user = $stmt->fetch();
        $_SESSION['loginExito'] = true;
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
    }
}

// Obtener las últimas entradas y categorías
$categorias = $categoriaObj->conseguirCategorias();

// Verificar si el usuario está autenticado y tiene los datos de sesión necesarios
if (!isset($_SESSION['usuario_nombre']) || !isset($_SESSION['usuario_apellidos']) || !isset($_SESSION['usuario_email'])) {
    $_SESSION['usuario_nombre'] = '';
    $_SESSION['usuario_apellidos'] = '';
    $_SESSION['usuario_email'] = '';
}

$_SESSION['loginExito'] = $_SESSION['loginExito'] ?? false;

// Verificar si hay un parámetro de búsqueda
$entradas = [];
$searchQuery = isset($_GET['buscar']) ? $_GET['buscar'] : '';

// Si se realizó una búsqueda, filtrar las entradas por título
if ($searchQuery) {
    $entradas = $entradaObj->buscarEntradasPorTitulo($searchQuery);
} else {
    // Obtener todas las entradas si no hay búsqueda
    $entradas = $entradaObj->conseguirUltimasEntradas();
}
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

        // Alternar visibilidad del formulario de editar usuario
        function toggleFormularioEditarUsuario() {
            const formulario = document.getElementById('formularioEditarUsuario');
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
                        <h3><?= htmlspecialchars($entrada['titulo']); ?></h3>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($entrada['categoria_nombre']); ?></p>
                        <p><em>Fecha: <?= htmlspecialchars($entrada['fecha']); ?></em></p>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No se encontraron entradas.</p>
            <?php endif; ?>
            <button onclick="window.location.href='todas_entradas.php'">Ver todas las entradas</button>
        </section>

        <aside>
            <div class="search">
                <h3>Buscar</h3>
                <form method="GET" action="index.php">
                    <input type="text" name="buscar" placeholder="Buscar..." value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>">
                    <button type="submit">Buscar</button>
                </form>
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
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id']; ?>">
                                    <?= htmlspecialchars($categoria['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
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

                <div style="margin-top: 20px;">
                    <!-- Botón de editar perfil -->
                    <button onclick="toggleFormularioEditarUsuario()">Editar Perfil</button>
                </div>

                <!-- Formulario de edición de usuario -->
                <div id="formularioEditarUsuario" style="display: none; margin-top: 20px;">
                    <form method="POST" action="editar_usuario.php">
                        <h3>Editar Perfil</h3>

                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?>" required>

                        <label for="apellidos">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($_SESSION['usuario_apellidos'] ?? '') ?>" required>

                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($_SESSION['usuario_email'] ?? '') ?>" required>

                        <label for="password">Nueva Contraseña:</label>
                        <input type="password" id="password" name="password" placeholder="Deja vacío si no deseas cambiarla">

                        <button type="submit" name="editarUsuario">Actualizar Usuario</button>
                    </form>
                </div>

                <div style="margin-top: 20px;">
                    <button onclick="window.location.href='logout.php'">Cerrar Sesión</button>
                </div>
            <?php else: ?>
                <div class="login">
                    <h3>Identifícate</h3>
                    <form method="POST" action="login.php">
                        <div>
                            <label for="emailLogin">Email:</label>
                            <input type="email" id="emailLogin" name="emailLogin" placeholder="Email" required>
                            <?php if (isset($_SESSION['errorPassLogin'])): ?>
                                <p style="color: red;"><?= htmlspecialchars($_SESSION['errorPassLogin']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="passwordLogin">Contraseña:</label>
                            <input type="password" id="passwordLogin" name="passwordLogin" placeholder="Contraseña" required>
                            <?php if (isset($_SESSION['errorPassLogin'])): ?>
                                <p style="color: red;"><?= htmlspecialchars($_SESSION['errorPassLogin']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <input type="checkbox" id="recordarme" name="recordarme">
                            <label for="recordarme">Recuérdame</label>
                        </div>
                        <button type="submit" name="botonLogin">Entrar</button>
                    </form>
                </div>
                <div class="register">
                    <h3>Regístrate</h3>
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