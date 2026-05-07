<?php
session_start();

require_once __DIR__ . '/../Config/database.php';

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header("Location: ../Views/usuarios/login.php");
    exit;
}

$db = (new Database())->conectar();

$accion = $_GET['accion'] ?? '';

switch ($accion) {

    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre      = trim($_POST['nombre']      ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio      = trim($_POST['precio']      ?? '');
            $categoria   = trim($_POST['categoria_id']?? '');
            $imagen_url  = trim($_POST['imagen_url']  ?? '');
            $popular     = isset($_POST['popular']) ? 1 : 0;
            $disponible  = isset($_POST['disponible']) ? 1 : 0;

            if (empty($nombre) || empty($precio) || empty($categoria)) {
                $_SESSION['alert'] = ['icon'=>'warning','title'=>'Campos incompletos','text'=>'Nombre, precio y categoría son obligatorios'];
            } else {
                $sql  = "INSERT INTO productos (categoria_id, nombre, descripcion, imagen_url, precio, popular, disponible)
                         VALUES (:categoria_id, :nombre, :descripcion, :imagen_url, :precio, :popular, :disponible)";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':categoria_id', $categoria, PDO::PARAM_INT);
                $stmt->bindParam(':nombre',       $nombre);
                $stmt->bindParam(':descripcion',  $descripcion);
                $stmt->bindParam(':imagen_url',   $imagen_url);
                $stmt->bindParam(':precio',       $precio);
                $stmt->bindParam(':popular',      $popular, PDO::PARAM_INT);
                $stmt->bindParam(':disponible',   $disponible, PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Éxito','text'=>'Producto creado correctamente'];
            }
        }
        header("Location: ../Views/dashboard/productos.php");
        exit;

    case 'editar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = $_POST['id']          ?? null;
            $nombre      = trim($_POST['nombre']      ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio      = trim($_POST['precio']      ?? '');
            $categoria   = trim($_POST['categoria_id']?? '');
            $imagen_url  = trim($_POST['imagen_url']  ?? '');
            $popular     = isset($_POST['popular']) ? 1 : 0;
            $disponible  = isset($_POST['disponible']) ? 1 : 0;

            if (!$id || empty($nombre) || empty($precio) || empty($categoria)) {
                $_SESSION['alert'] = ['icon'=>'warning','title'=>'Campos incompletos','text'=>'Nombre, precio y categoría son obligatorios'];
            } else {
                $sql  = "UPDATE productos SET categoria_id=:categoria_id, nombre=:nombre, descripcion=:descripcion,
                         imagen_url=:imagen_url, precio=:precio, popular=:popular, disponible=:disponible WHERE id=:id";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':categoria_id', $categoria, PDO::PARAM_INT);
                $stmt->bindParam(':nombre',       $nombre);
                $stmt->bindParam(':descripcion',  $descripcion);
                $stmt->bindParam(':imagen_url',   $imagen_url);
                $stmt->bindParam(':precio',       $precio);
                $stmt->bindParam(':popular',      $popular,   PDO::PARAM_INT);
                $stmt->bindParam(':disponible',   $disponible,PDO::PARAM_INT);
                $stmt->bindParam(':id',           $id,        PDO::PARAM_INT);
                $stmt->execute();
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Éxito','text'=>'Producto actualizado correctamente'];
            }
        }
        header("Location: ../Views/dashboard/productos.php");
        exit;

    case 'eliminar':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $db->prepare("DELETE FROM productos WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Eliminado','text'=>'Producto eliminado correctamente'];
        }
        header("Location: ../Views/dashboard/productos.php");
        exit;

    case 'toggleDisponible':
        $id     = $_GET['id']     ?? null;
        $estado = $_GET['estado'] ?? null;
        if ($id !== null && $estado !== null) {
            $nuevo = $estado == 1 ? 0 : 1;
            $stmt  = $db->prepare("UPDATE productos SET disponible = :disponible WHERE id = :id");
            $stmt->bindParam(':disponible', $nuevo, PDO::PARAM_INT);
            $stmt->bindParam(':id',         $id,    PDO::PARAM_INT);
            $stmt->execute();
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Éxito','text'=>'Estado del producto actualizado'];
        }
        header("Location: ../Views/dashboard/productos.php");
        exit;

    default:
        header("Location: ../Views/dashboard/productos.php");
        exit;
}
?>
