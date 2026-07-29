<?php
require_once __DIR__ . '/../Models/Menu.php';

class HomeController {
    public function index() {
        $menuModel = new Menu();
        
        // Obtener datos del modelo
        $categorias_menu = $menuModel->getCategoriasActivas();
        $todos_productos = $menuModel->getTodosProductos();

        $imgs_default = [
            'https://images.unsplash.com/photo-1600891964092-4316c288032e?w=600&q=80',
            'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=600&q=80',
            'https://images.unsplash.com/photo-1562967914-608f82629710?w=600&q=80',
            'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=600&q=80',
            'https://images.unsplash.com/photo-1516684732162-798a0062be99?w=600&q=80',
            'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=600&q=80',
        ];

        // Incluir la vista y pasarle las variables implícitamente
        require_once __DIR__ . '/../Views/home/index.php';
    }
}
?>
