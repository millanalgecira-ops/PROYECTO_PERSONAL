<?php
session_start();

require_once __DIR__ . '/../Config/database.php';

if (!isset($_SESSION['usuario']) || !in_array($_SESSION['usuario']['rol'], ['administrador', 'cocina'])) {
    header("Location: ../Views/usuarios/login.php");
    exit;
}

$db     = (new Database())->conectar();
$accion = $_GET['accion'] ?? '';

switch ($accion) {

    case 'cancelar':
        $id = intval($_GET['id'] ?? 0);
        if ($id && $_SESSION['usuario']['rol'] === 'administrador') {
            $stmt = $db->prepare("UPDATE pedidos SET estado='Cancelado', cancelado_por=:uid WHERE id=:id AND estado NOT IN ('Pagado','Cancelado')");
            $stmt->execute([':uid' => $_SESSION['usuario']['id_usuario'], ':id' => $id]);
            // Liberar mesa si aplica
            $db->prepare("UPDATE mesas SET estado='Disponible', liberada_en=NOW() WHERE id=(SELECT mesa_id FROM pedidos WHERE id=:id)")->execute([':id'=>$id]);
            $_SESSION['alert'] = ['icon'=>'success','text'=>'Pedido cancelado correctamente'];
        }
        header("Location: ../Views/dashboard/pedidos.php");
        exit;

    case 'cambiarEstado':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id     = $_POST['id']     ?? null;
            $estado = $_POST['estado'] ?? null;
            $estados_validos = ['Recibido','En preparacion','Listo','Entregado','Pagado','Cancelado'];

            if ($id && in_array($estado, $estados_validos)) {
                $stmt = $db->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id");
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':id',     $id, PDO::PARAM_INT);
                $stmt->execute();

                // Registrar en historial
                $user_id = $_SESSION['usuario']['id_usuario'];
                $stmt2 = $db->prepare("INSERT INTO pedido_estados_historial (pedido_id, estado, cambiado_por) VALUES (:pedido_id, :estado, :user_id)");
                $stmt2->bindParam(':pedido_id', $id,      PDO::PARAM_INT);
                $stmt2->bindParam(':estado',    $estado);
                $stmt2->bindParam(':user_id',   $user_id, PDO::PARAM_INT);
                $stmt2->execute();

                $_SESSION['alert'] = ['icon'=>'success','text'=>'Estado actualizado a: '.$estado];
            }
        }
        $redirect = $_SESSION['usuario']['rol'] === 'cocina' ? '../Views/dashboard/cocina.php' : '../Views/dashboard/pedidos.php';
        header("Location: $redirect");
        exit;

    default:
        header("Location: ../Views/dashboard/pedidos.php");
        exit;
}
?>
