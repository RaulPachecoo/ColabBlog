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
                    e.titulo AS entrada_titulo,
                    e.descripcion AS entrada_descripcion,
                    e.fecha AS entrada_fecha,
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
}
