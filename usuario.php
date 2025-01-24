<?php

class Usuario
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // Método para obtener un usuario por su ID
    public function obtenerUsuarioPorId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Método para editar los detalles de un usuario
    public function editarUsuario($usuarioId, $nombre, $apellidos, $email, $password = null)
    {
        try {
            // Si la contraseña es proporcionada, actualízala
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email, password = :password WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindParam(':password', $hashedPassword);
            } else {
                // Si no hay contraseña nueva, actualiza solo el nombre, apellidos y email
                $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, email = :email WHERE id = :id";
                $stmt = $this->pdo->prepare($sql);
            }

            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':apellidos', $apellidos);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':id', $usuarioId);

            // Ejecución de la consulta
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return true; // Si alguna fila fue actualizada
            } else {
                throw new Exception('No se realizaron cambios. Verifica si los datos son los mismos.');
            }

        } catch (Exception $e) {
            error_log('Error al actualizar el usuario: ' . $e->getMessage());
            throw new Exception('Error al actualizar el usuario');
        }
    }
}

?>
