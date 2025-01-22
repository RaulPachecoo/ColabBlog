<?php
session_start();
require_once 'requires/conexion.php';
require_once 'entradas.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loginExito']) || !$_SESSION['loginExito']) {
    header("Location: index.php");
    exit();
}

// Instanciar la clase Entrada
$entradaObj = new Entrada($pdo);

// Verificar si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entrada_id'])) {
    $entrada_id = intval($_POST['entrada_id']);
    $titulo = trim($_POST['titulo']);
    $descripcion = trim($_POST['descripcion']);
    $categoria_id = intval($_POST['categoria_id']);

    if ($entradaObj->editarEntrada($entrada_id, $titulo, $descripcion, $categoria_id)) {
        header("Location: entrada_detalle.php?id=$entrada_id&mensaje=Entrada actualizada");
        exit();
    } else {
        echo "Error al actualizar la entrada.";
    }
}

// Obtener los datos de la entrada para mostrar en el formulario
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $entrada_id = intval($_GET['id']);
    $entrada = $entradaObj->conseguirEntradaPorId($entrada_id);
} else {
    header("Location: todas_entradas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Entrada</title>
    <link rel="stylesheet" href="assets/css/estilos.css">
</head>

<body>
    <header>
        <h1>Editar Entrada</h1>
    </header>
    <main>
        <form method="POST" action="editar_entrada.php">
            <input type="hidden" name="entrada_id" value="<?php echo htmlspecialchars($entrada['entrada_id']); ?>">
            <label for="titulo">Título</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo htmlspecialchars($entrada['entrada_titulo']); ?>" required>

            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" required><?php echo htmlspecialchars($entrada['entrada_descripcion']); ?></textarea>

            <label for="categoria_id">Categoría</label>
            <select id="categoria_id" name="categoria_id" required>
                <?php
                $categorias = $entradaObj->conseguirCategorias();
                foreach ($categorias as $categoria) {
                    $selected = $categoria['id'] == $entrada['categoria_id'] ? 'selected' : '';
                    echo "<option value='{$categoria['id']}' $selected>{$categoria['nombre']}</option>";
                }
                ?>
            </select>

            <button type="submit">Actualizar Entrada</button>
        </form>
    </main>
</body>

</html>
