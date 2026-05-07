<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../Config/database.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit;
}

try {
    $db = (new Database())->conectar();

    $cart           = $input['cart']           ?? [];
    $tipo           = $input['tipo']           ?? 'En mesa';
    $mesa_numero    = $input['mesa_numero']    ?? null;
    $nombre_cliente = $input['nombre_cliente'] ?? 'Invitado';
    $nota_especial  = $input['nota_especial']  ?? '';
    $metodo_pago    = $input['metodo_pago']    ?? 'Efectivo';

    if (empty($cart)) {
        echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
        exit;
    }

    // Obtener cliente_id si hay sesión
    $cliente_id = null;
    if (isset($_SESSION['usuario']) && $_SESSION['usuario']['rol'] === 'cliente') {
        $cliente_id = $_SESSION['usuario']['id_usuario'];
    }

    // Obtener mesa_id si aplica
    $mesa_id = null;
    if ($mesa_numero) {
        $stmt = $db->prepare("SELECT id FROM mesas WHERE numero = :numero LIMIT 1");
        $stmt->bindParam(':numero', $mesa_numero, PDO::PARAM_INT);
        $stmt->execute();
        $mesa = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($mesa) {
            $mesa_id = $mesa['id'];
            // Marcar mesa como ocupada
            $db->prepare("UPDATE mesas SET estado = 'Ocupada' WHERE id = :id")
               ->execute([':id' => $mesa_id]);
        }
    }

    // Calcular total
    $total = 0;
    foreach ($cart as $item) {
        $precio = is_numeric($item['precio']) ? $item['precio'] : intval(preg_replace('/[^0-9]/', '', $item['precio']));
        $total += $precio * $item['qty'];
    }

    // Generar número de orden único
    $numero_orden = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

    // Insertar pedido
    $stmt = $db->prepare("
        INSERT INTO pedidos (numero_orden, cliente_id, mesa_id, tipo, estado, subtotal, total, observaciones, creado_en)
        VALUES (:numero_orden, :cliente_id, :mesa_id, :tipo, 'Recibido', :subtotal, :total, :observaciones, NOW())
    ");
    $stmt->execute([
        ':numero_orden'  => $numero_orden,
        ':cliente_id'    => $cliente_id,
        ':mesa_id'       => $mesa_id,
        ':tipo'          => $tipo,
        ':subtotal'      => $total,
        ':total'         => $total,
        ':observaciones' => $nota_especial
    ]);
    $pedido_id = $db->lastInsertId();

    // Insertar items del pedido
    foreach ($cart as $item) {
        $precio_unitario = is_numeric($item['precio']) ? $item['precio'] : intval(preg_replace('/[^0-9]/', '', $item['precio']));
        $cantidad        = intval($item['qty']);
        $subtotal_item   = $precio_unitario * $cantidad;
        $nombre_producto = $item['nombre'];

        // Buscar producto_id por nombre
        $stmtP = $db->prepare("SELECT id FROM productos WHERE nombre = :nombre LIMIT 1");
        $stmtP->bindParam(':nombre', $nombre_producto);
        $stmtP->execute();
        $prod = $stmtP->fetch(PDO::FETCH_ASSOC);
        $producto_id = $prod ? $prod['id'] : 1;

        $stmtItem = $db->prepare("
            INSERT INTO pedido_items (pedido_id, producto_id, nombre_producto, cantidad, precio_unitario, subtotal)
            VALUES (:pedido_id, :producto_id, :nombre_producto, :cantidad, :precio_unitario, :subtotal)
        ");
        $stmtItem->execute([
            ':pedido_id'       => $pedido_id,
            ':producto_id'     => $producto_id,
            ':nombre_producto' => $nombre_producto,
            ':cantidad'        => $cantidad,
            ':precio_unitario' => $precio_unitario,
            ':subtotal'        => $subtotal_item
        ]);
    }

    // Registrar historial de estado
    $db->prepare("
        INSERT INTO pedido_estados_historial (pedido_id, estado, cambiado_en)
        VALUES (:pedido_id, 'Recibido', NOW())
    ")->execute([':pedido_id' => $pedido_id]);

    // Registrar pago
    $db->prepare("
        INSERT INTO pagos (pedido_id, metodo, total_pagado, pagado_en)
        VALUES (:pedido_id, :metodo, :total, NOW())
    ")->execute([
        ':pedido_id' => $pedido_id,
        ':metodo'    => $metodo_pago,
        ':total'     => $total
    ]);

    // Registrar ingreso
    $db->prepare("
        INSERT INTO ingresos (pedido_id, descripcion, metodo, monto, fecha, creado_en)
        VALUES (:pedido_id, :descripcion, :metodo, :monto, CURDATE(), NOW())
    ")->execute([
        ':pedido_id'   => $pedido_id,
        ':descripcion' => 'Pedido #' . $numero_orden . ' - ' . $nombre_cliente,
        ':metodo'      => $metodo_pago,
        ':monto'       => $total
    ]);

    echo json_encode([
        'success'      => true,
        'numero_orden' => $numero_orden,
        'pedido_id'    => $pedido_id,
        'total'        => $total
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
