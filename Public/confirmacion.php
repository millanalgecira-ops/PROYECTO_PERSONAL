<?php
session_start();
require_once __DIR__ . '/../Config/database.php';

$numero_orden = $_GET['orden'] ?? null;
if (!$numero_orden) {
    header('Location: index.php');
    exit;
}

$db = (new Database())->conectar();

$stmt = $db->prepare("
    SELECT p.*, m.numero AS mesa_numero, c.nombre AS cliente_nombre,
           pg.metodo AS metodo_pago
    FROM pedidos p
    LEFT JOIN mesas m ON m.id = p.mesa_id
    LEFT JOIN clientes c ON c.id = p.cliente_id
    LEFT JOIN pagos pg ON pg.pedido_id = p.id
    WHERE p.numero_orden = :numero_orden LIMIT 1
");
$stmt->bindParam(':numero_orden', $numero_orden);
$stmt->execute();
$pedido = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pedido) {
    header('Location: index.php');
    exit;
}

$items = $db->prepare("SELECT * FROM pedido_items WHERE pedido_id = :id");
$items->bindParam(':id', $pedido['id'], PDO::PARAM_INT);
$items->execute();
$items = $items->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Pedido Confirmado</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --black:#0e0e0e;--dark:#141414;--card:#1c1a18;--card2:#221f1b;--border:#2e2b27;--orange:#f07000;--orange2:#e06500;--text:#f0ece6;--muted:#8a8078;--label:#c8bfb0; }
        body { font-family:'Barlow',sans-serif; background:var(--black); color:var(--text); min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:24px; }
        nav { position:fixed; top:0; left:0; right:0; z-index:100; background:rgba(14,14,14,.95); backdrop-filter:blur(12px); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 24px; height:60px; }
        .nav-brand { display:flex; align-items:center; gap:10px; text-decoration:none; }
        .nav-brand-name { font-family:'Bebas Neue',sans-serif; font-size:20px; color:var(--text); }
        .nav-brand-sub  { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--orange); }
        .confirm-box { background:var(--card); border:1px solid var(--border); border-radius:20px; padding:40px; max-width:520px; width:100%; margin-top:80px; text-align:center; }
        .check-icon { width:72px; height:72px; border-radius:50%; background:rgba(0,200,100,.12); border:2px solid #00c864; display:flex; align-items:center; justify-content:center; margin:0 auto 24px; }
        .confirm-title { font-family:'Bebas Neue',sans-serif; font-size:32px; margin-bottom:8px; }
        .confirm-sub { font-size:14px; color:var(--muted); margin-bottom:28px; }
        .orden-badge { display:inline-block; background:rgba(240,112,0,.15); border:1px solid rgba(240,112,0,.3); border-radius:10px; padding:10px 24px; font-family:'Bebas Neue',sans-serif; font-size:28px; color:var(--orange); letter-spacing:2px; margin-bottom:28px; }
        .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:24px; }
        .info-item { background:var(--card2); border:1px solid var(--border); border-radius:10px; padding:14px; text-align:left; }
        .info-label { font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:4px; }
        .info-value { font-size:14px; font-weight:600; }
        .items-list { background:var(--card2); border:1px solid var(--border); border-radius:12px; overflow:hidden; margin-bottom:24px; text-align:left; }
        .items-list-header { padding:12px 16px; border-bottom:1px solid var(--border); font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); }
        .item-row { display:flex; justify-content:space-between; align-items:center; padding:12px 16px; border-bottom:1px solid rgba(46,43,39,.4); }
        .item-row:last-child { border-bottom:none; }
        .item-row-name { font-size:14px; font-weight:600; }
        .item-row-qty  { font-size:12px; color:var(--muted); }
        .item-row-price { font-size:14px; color:var(--orange); font-weight:700; }
        .total-section { background:var(--card2); border:1px solid var(--border); border-radius:12px; padding:16px; margin-bottom:28px; }
        .total-line { display:flex; justify-content:space-between; font-size:14px; color:var(--label); margin-bottom:8px; }
        .total-line.bold { font-size:18px; font-weight:700; color:var(--text); margin-bottom:0; padding-top:10px; border-top:1px solid var(--border); }
        .total-line.bold span:last-child { color:var(--orange); }
        .btn-volver { display:inline-flex; align-items:center; gap:8px; background:var(--orange); color:#fff; border:none; border-radius:10px; padding:14px 28px; font-size:15px; font-family:'Barlow',sans-serif; font-weight:700; cursor:pointer; text-decoration:none; transition:background .2s; }
        .btn-volver:hover { background:var(--orange2); }
        .estado-badge { display:inline-flex; align-items:center; gap:6px; background:rgba(100,180,255,.12); border:1px solid rgba(100,180,255,.25); border-radius:20px; padding:6px 14px; font-size:13px; color:#64b4ff; margin-bottom:20px; }
        .dot-pulse { width:8px; height:8px; border-radius:50%; background:#64b4ff; animation:pulse 1.5s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
    </style>
</head>
<body>
<nav>
    <a class="nav-brand" href="index.php">
        <svg width="18" height="24" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div><div class="nav-brand-name">La Parrilla</div><div class="nav-brand-sub">Asadero &amp; Restaurante</div></div>
    </a>
</nav>

<div class="confirm-box">
    <div class="check-icon">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#00c864" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
    </div>

    <h1 class="confirm-title">¡Pedido Recibido!</h1>
    <p class="confirm-sub">Tu pedido fue enviado a cocina exitosamente</p>

    <div class="estado-badge">
        <span class="dot-pulse"></span>
        En preparación
    </div>

    <div class="orden-badge">#<?= htmlspecialchars($pedido['numero_orden']) ?></div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Tipo</div>
            <div class="info-value"><?= htmlspecialchars($pedido['tipo']) ?></div>
        </div>
        <?php if ($pedido['mesa_numero']): ?>
        <div class="info-item">
            <div class="info-label">Mesa</div>
            <div class="info-value">Mesa <?= $pedido['mesa_numero'] ?></div>
        </div>
        <?php endif; ?>
        <div class="info-item">
            <div class="info-label">Método de pago</div>
            <div class="info-value"><?= htmlspecialchars($pedido['metodo_pago'] ?? 'Efectivo') ?></div>
        </div>
        <div class="info-item">
            <div class="info-label">Estado</div>
            <div class="info-value" style="color:#64b4ff"><?= htmlspecialchars($pedido['estado']) ?></div>
        </div>
    </div>

    <div class="items-list">
        <div class="items-list-header">Detalle del pedido</div>
        <?php foreach($items as $item): ?>
        <div class="item-row">
            <div>
                <div class="item-row-name"><?= htmlspecialchars($item['nombre_producto']) ?></div>
                <div class="item-row-qty">x<?= $item['cantidad'] ?></div>
            </div>
            <div class="item-row-price">$<?= number_format($item['subtotal'],0,',','.') ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="total-section">
        <div class="total-line"><span>Subtotal</span><span>$<?= number_format($pedido['subtotal'],0,',','.') ?></span></div>
        <div class="total-line bold"><span>Total</span><span>$<?= number_format($pedido['total'],0,',','.') ?></span></div>
    </div>

    <?php if ($pedido['observaciones']): ?>
    <div style="background:var(--card2);border:1px solid var(--border);border-radius:10px;padding:14px;margin-bottom:24px;text-align:left">
        <div style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px">Nota especial</div>
        <div style="font-size:14px"><?= htmlspecialchars($pedido['observaciones']) ?></div>
    </div>
    <?php endif; ?>

    <a href="index.php" class="btn-volver">← Volver al menú</a>
</div>
</body>
</html>
