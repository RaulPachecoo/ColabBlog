<?php

// Clase Categoria
class Categoria {
    private $pdo;

    // Constructor de la clase
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Método para obtener una categoría por su ID
    public function obtenerCategoriaPorId($categoria_id) {
        // Consulta SQL para obtener la categoría por su ID
        $sql = "SELECT * FROM categorias WHERE id = :id LIMIT 1";  // Usamos LIMIT 1 porque esperamos solo una categoría
        $stmt = $this->pdo->prepare($sql);

        // Ejecutar la consulta
        $stmt->execute([':id' => $categoria_id]);

        // Obtener el resultado
        $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no se encuentra la categoría, retornar null
        return $categoria ? $categoria : null;
    }

    function conseguirCategorias() {
        $sql = "SELECT * FROM categorias ORDER BY nombre ASC";  // Consulta SQL
        $stmt = $this->pdo->query($sql);  // Ejecutar la consulta con PDO
    
        if (!$stmt) {
            throw new Exception("Error en la consulta: " . implode(", ", $this->pdo->errorInfo()));  // En caso de error
        }
    
        // Devolver el resultado como un array de categorías
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Función para insertar una nueva categoría
    function insertarCategoria($nombre_categoria) {
        // Verificar si la categoría ya existe
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM categorias WHERE nombre = :nombre");
        $stmt->execute([':nombre' => $nombre_categoria]);
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            return "La categoría ya existe.";
        }
    
        // Insertar la nueva categoría
        $stmt = $this->pdo->prepare("INSERT INTO categorias (nombre) VALUES (:nombre)");
        $stmt->execute([':nombre' => $nombre_categoria]);
    
        return true;
    }
}

?>

