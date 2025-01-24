<?php
class Entrada
{
    private $pdo;

    // Constructor que recibe la conexión PDO
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Función para conseguir las últimas entradas con su categoría.
     * 
     * @return array|false Devuelve un array de entradas o false si ocurre un error.
     */
    public function conseguirUltimasEntradas()
    {
        try {
            // Consulta SQL para obtener las entradas con sus categorías
            $sql = "
                SELECT 
                    e.id AS entrada_id,
                    e.titulo,
                    e.fecha,
                    c.id AS categoria_id,
                    c.nombre AS categoria_nombre
                FROM entradas e
                INNER JOIN categorias c ON e.categoria_id = c.id
                ORDER BY e.id DESC
            ";

            // Preparar y ejecutar la consulta
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();

            // Devolver los resultados como un array asociativo
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en la consulta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Función para crear una nueva entrada en la base de datos.
     * 
     * @param int $usuarioId El ID del usuario que crea la entrada.
     * @param int $categoriaId El ID de la categoría de la entrada.
     * @param string $titulo El título de la entrada.
     * @param string $descripcion La descripción de la entrada.
     * @return bool Devuelve true si la entrada se crea correctamente, false en caso contrario.
     */
    public function crearEntrada($usuarioId, $categoriaId, $titulo, $descripcion)
    {
        try {
            $sql = "
            INSERT INTO entradas (usuario_id, categoria_id, titulo, descripcion, fecha)
            VALUES (:usuario_id, :categoria_id, :titulo, :descripcion, CURDATE())
        ";

            $stmt = $this->pdo->prepare($sql);

            // Asignar parámetros
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':categoria_id', $categoriaId, PDO::PARAM_INT);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error al crear la entrada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Función para obtener las categorías disponibles.
     * 
     * @return array|false Devuelve un array de categorías o false si ocurre un error.
     */
    public function conseguirCategorias()
    {
        try {
            $sql = "SELECT id, nombre FROM categorias";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener categorías: " . $e->getMessage());
            return false;
        }
    }

    public function conseguirEntradaPorId($id)
    {
        try {
            // Consulta SQL para obtener una entrada específica por su ID
            $sql = "
            SELECT 
                e.id AS entrada_id,
                e.titulo AS entrada_titulo,
                e.descripcion AS entrada_descripcion,
                e.fecha AS entrada_fecha,
                c.id AS categoria_id,
                c.nombre AS categoria_nombre
            FROM entradas e
            INNER JOIN categorias c ON e.categoria_id = c.id
            WHERE e.id = :id
            LIMIT 1
        ";

            // Preparar y ejecutar la consulta
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            // Devolver la entrada como un array asociativo
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejo de errores
            error_log("Error en la consulta: " . $e->getMessage());
            return false;
        }
    }

    public function borrarEntrada($id)
    {
        try {
            $sql = "DELETE FROM entradas WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al borrar entrada: " . $e->getMessage());
            return false;
        }
    }

    public function editarEntrada($id, $titulo, $descripcion, $categoria_id)
    {
        try {
            $sql = "
            UPDATE entradas
            SET titulo = :titulo, descripcion = :descripcion, categoria_id = :categoria_id
            WHERE id = :id
        ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':titulo', $titulo, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindParam(':categoria_id', $categoria_id, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al editar entrada: " . $e->getMessage());
            return false;
        }
    }

    public function conseguirEntradasPorCategoria($categoria_id)
    {
        // Consulta SQL para obtener las entradas de una categoría específica
        $sql = "SELECT e.id, e.titulo, e.descripcion, e.fecha, u.nombre AS usuario_nombre 
                FROM entradas e
                JOIN usuarios u ON e.usuario_id = u.id
                WHERE e.categoria_id = :categoria_id
                ORDER BY e.fecha DESC";  // Ordenamos por fecha de manera descendente

        // Preparamos y ejecutamos la consulta
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':categoria_id' => $categoria_id]);

        // Devolver las entradas obtenidas como un array
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // En la clase Entrada
    public function buscarEntradasPorTitulo($titulo)
    {
        $sql = "SELECT e.*, c.nombre AS categoria_nombre
            FROM entradas e
            LEFT JOIN categorias c ON e.categoria_id = c.id
            WHERE e.titulo LIKE :titulo  -- Cambiar 'e.entrada_titulo' a 'e.titulo'
            ORDER BY e.fecha DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':titulo', '%' . $titulo . '%', PDO::PARAM_STR); // El '%' es para búsqueda parcial
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
