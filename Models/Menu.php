<?php
require_once __DIR__ . '/../Config/database.php';

class Menu {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->conectar();
    }

    public function getCategoriasActivas() {
        $stmt = $this->db->query("
            SELECT c.id, c.nombre, c.imagen_url,
                   COUNT(p.id) AS total_productos,
                   SUM(CASE WHEN p.disponible=1 THEN 1 ELSE 0 END) AS disponibles
            FROM categorias c
            LEFT JOIN productos p ON p.categoria_id = c.id
            WHERE c.activa = 1
            GROUP BY c.id
            ORDER BY c.orden, c.nombre
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTodosProductos() {
        $stmt = $this->db->query("
            SELECT p.id, p.nombre, p.descripcion, p.precio, p.popular, p.disponible,
                   p.imagen_url, p.categoria_id, c.nombre AS categoria_nombre
            FROM productos p
            LEFT JOIN categorias c ON c.id = p.categoria_id
            WHERE c.activa = 1
            ORDER BY p.popular DESC, p.nombre
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
