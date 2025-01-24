
<?php
require_once 'requires/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id']; // El ID del usuario no se modifica
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);

    $errores = [];

    // Validaciones básicas
    if (empty($nombre)) {
        $errores[] = "El nombre no puede estar vacío.";
    }
    if (empty($email)) {
        $errores[] = "El email no puede estar vacío.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El formato del email no es válido.";
    }

    if (empty($errores)) {
        try {
            // Verificar si el email ya existe para otro usuario
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email AND id != :id");
            $stmt->execute(['email' => $email, 'id' => $id]);
            $emailExistente = $stmt->fetchColumn();

            if ($emailExistente > 0) {
                $errores[] = "El email ya está registrado por otro usuario.";
            } else {
                // Actualizar los datos del usuario
                $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id");
                $stmt->execute([
                    'nombre' => $nombre,
                    'email' => $email,
                    'id' => $id
                ]);
                echo "Datos actualizados correctamente.";
            }
        } catch (PDOException $e) {
            $errores[] = "Error en la base de datos: " . $e->getMessage();
        }
    }

    if (!empty($errores)) {
        foreach ($errores as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            die("Usuario no encontrado.");
        }
    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
} else {
    die("ID de usuario no especificado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Usuario</title>
</head>
<body>
    <h1>Actualizar Datos del Usuario</h1>
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']); ?>">
        <label for="nombre">Nombre:</label><br>
        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($usuario['nombre']); ?>" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email']); ?>" required><br><br>

        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
