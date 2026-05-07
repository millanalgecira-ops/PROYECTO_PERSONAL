<?php
session_start();
if (!isset($_SESSION['usuario']) || strtolower(trim($_SESSION['usuario']['rol'])) !== 'cliente') {
    header("Location: ../usuarios/login.php"); exit;
}
$usuario = $_SESSION['usuario'];
$vista   = $_GET['vista'] ?? 'inicio';

require_once __DIR__ . '/../../Config/database.php';
$db = (new Database())->conectar();

// Historial de pedidos del cliente
$pedidos = [];
$pedido_detalle = null;
if ($vista === 'pedidos' || $vista === 'detalle') {
    $stmt = $db->prepare("
        SELECT p.id, p.numero_orden, p.tipo, p.estado, p.total, p.creado_en,
               m.numero AS mesa_numero, pg.metodo AS metodo_pago
        FROM pedidos p
        LEFT JOIN mesas m ON m.id = p.mesa_id
        LEFT JOIN pagos pg ON pg.pedido_id = p.id
        WHERE p.cliente_id = :cid
        ORDER BY p.creado_en DESC
    ");
    $stmt->execute([':cid' => $usuario['id_usuario']]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($vista === 'detalle' && isset($_GET['id'])) {
        $sid = $db->prepare("SELECT p.*,m.numero AS mesa_numero,pg.metodo AS metodo_pago FROM pedidos p LEFT JOIN mesas m ON m.id=p.mesa_id LEFT JOIN pagos pg ON pg.pedido_id=p.id WHERE p.id=:id AND p.cliente_id=:cid LIMIT 1");
        $sid->execute([':id'=>$_GET['id'],':cid'=>$usuario['id_usuario']]);
        $pedido_detalle = $sid->fetch(PDO::FETCH_ASSOC);
        if ($pedido_detalle) {
            $sitems = $db->prepare("SELECT * FROM pedido_items WHERE pedido_id=:id");
            $sitems->execute([':id'=>$pedido_detalle['id']]);
            $pedido_detalle['items'] = $sitems->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>La Parrilla – Mi cuenta</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--black:#0e0e0e;--dark:#141414;--card:#1c1a18;--card2:#221f1b;--border:#2e2b27;--orange:#f07000;--orange2:#e06500;--text:#f0ece6;--muted:#8a8078;--label:#c8bfb0}
body{font-family:'Barlow',sans-serif;background:var(--black);color:var(--text);min-height:100vh;display:flex}
.sidebar{width:240px;min-height:100vh;background:var(--dark);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:28px 0;flex-shrink:0}
.sidebar-brand{display:flex;align-items:center;gap:10px;padding:0 24px 28px;border-bottom:1px solid var(--border)}
.sidebar-brand-name{font-family:'Bebas Neue',sans-serif;font-size:20px}
.sidebar-brand-sub{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--orange)}
.sidebar-nav{flex:1;padding:20px 12px}
.nav-label{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);padding:0 12px;margin:18px 0 8px}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:14px;color:var(--label);text-decoration:none;transition:background .2s,color .2s}
.nav-item:hover,.nav-item.active{background:rgba(240,112,0,.12);color:var(--orange)}
.sidebar-footer{padding:20px 12px 0;border-top:1px solid var(--border)}
.user-info{display:flex;align-items:center;gap:10px;padding:10px 12px}
.user-avatar{width:34px;height:34px;border-radius:50%;background:rgba(240,112,0,.15);border:1px solid rgba(240,112,0,.3);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--orange);flex-shrink:0}
.user-name{font-size:13px;font-weight:600}
.user-role{font-size:11px;color:var(--orange)}
.btn-logout{display:flex;align-items:center;gap:6px;background:none;border:1px solid var(--border);border-radius:8px;padding:8px 14px;color:var(--muted);font-size:13px;font-family:'Barlow',sans-serif;cursor:pointer;transition:border-color .2s,color .2s;text-decoration:none}
.btn-logout:hover{border-color:var(--orange);color:var(--orange)}
.main{flex:1;display:flex;flex-direction:column}
.topbar{height:60px;background:var(--dark);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 32px}
.topbar-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px}
.content{flex:1;padding:32px;overflow-y:auto}
.welcome-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:40px;text-align:center;max-width:500px;margin:40px auto 0}
.welcome-icon{width:64px;height:64px;border-radius:50%;background:rgba(240,112,0,.12);border:1px solid rgba(240,112,0,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 20px;color:var(--orange)}
.welcome-card h2{font-family:'Bebas Neue',sans-serif;font-size:28px;margin-bottom:8px}
.welcome-card p{font-size:14px;color:var(--muted);line-height:1.7;margin-bottom:24px}
.btn-orange{display:inline-flex;align-items:center;gap:8px;background:var(--orange);color:#fff;border:none;border-radius:8px;padding:12px 24px;font-size:15px;font-family:'Barlow',sans-serif;font-weight:600;cursor:pointer;text-decoration:none;transition:background .2s}
.btn-orange:hover{background:var(--orange2)}
/* PEDIDOS */
.section-card{background:var(--card);border:1px solid var(--border);border-radius:12px;overflow:hidden}
.section-head{padding:18px 24px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.section-head h2{font-size:16px;font-weight:600}
table{width:100%;border-collapse:collapse}
th{padding:12px 16px;text-align:left;font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--muted);border-bottom:1px solid var(--border)}
td{padding:14px 16px;font-size:14px;border-bottom:1px solid rgba(46,43,39,.5);vertical-align:middle}
tr:last-child td{border-bottom:none}
tr:hover td{background:rgba(255,255,255,.02)}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-recibido{background:rgba(100,180,255,.12);color:#64b4ff}
.badge-preparacion{background:rgba(255,200,0,.12);color:#ffc800}
.badge-listo{background:rgba(100,220,130,.12);color:#64dc82}
.badge-entregado{background:rgba(240,112,0,.15);color:var(--orange)}
.badge-pagado{background:rgba(100,220,130,.2);color:#00c864}
.badge-cancelado{background:rgba(255,80,80,.12);color:#ff5050}
.numero-orden{font-family:'Bebas Neue',sans-serif;font-size:16px;color:var(--orange)}
.btn-ver{background:rgba(240,112,0,.1);border:1px solid rgba(240,112,0,.25);border-radius:6px;padding:5px 12px;color:var(--orange);font-size:12px;font-family:'Barlow',sans-serif;text-decoration:none;transition:background .2s}
.btn-ver:hover{background:rgba(240,112,0,.25)}
.empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
.empty-state p{font-size:14px;margin-bottom:20px}
/* DETALLE */
.detalle-card{background:var(--card);border:1px solid var(--border);border-radius:12px;padding:24px;max-width:600px}
.detalle-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px}
.detalle-item{background:var(--card2);border:1px solid var(--border);border-radius:8px;padding:12px}
.detalle-label{font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px}
.detalle-value{font-size:14px;font-weight:600}
.item-row{display:flex;justify-content:space-between;align-items:center;padding:12px 0;border-bottom:1px solid rgba(46,43,39,.4)}
.item-row:last-child{border-bottom:none}
.total-line{display:flex;justify-content:space-between;font-size:16px;font-weight:700;padding-top:14px;border-top:1px solid var(--border);margin-top:4px}
.total-line span:last-child{color:var(--orange)}
.btn-back{display:inline-flex;align-items:center;gap:6px;background:none;border:1px solid var(--border);border-radius:8px;padding:8px 14px;color:var(--muted);font-size:13px;font-family:'Barlow',sans-serif;text-decoration:none;margin-bottom:20px;transition:border-color .2s,color .2s}
.btn-back:hover{border-color:var(--orange);color:var(--orange)}
</style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand">
        <svg width="20" height="26" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div><div class="sidebar-brand-name">La Parrilla</div><div class="sidebar-brand-sub">Mi cuenta</div></div>
    </div>
    <nav class="sidebar-nav">
        <p class="nav-label">Principal</p>
        <a class="nav-item <?= $vista==='inicio'?'active':'' ?>" href="cliente.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Inicio
        </a>
        <a class="nav-item" href="../../Public/index.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Ver Menú
        </a>
        <a class="nav-item <?= ($vista==='pedidos'||$vista==='detalle')?'active':'' ?>" href="cliente.php?vista=pedidos">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
            Mis Pedidos
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($usuario['nombre'],0,1)) ?></div>
            <div><div class="user-name"><?= htmlspecialchars($usuario['nombre']) ?></div><div class="user-role">Cliente</div></div>
        </div>
        <a href="../../Controllers/AuthController.php?accion=logout" class="btn-logout" style="width:100%;margin-top:8px;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <span class="topbar-title"><?= $vista==='pedidos'?'Mis Pedidos':($vista==='detalle'?'Detalle del Pedido':'Bienvenido') ?></span>
        <span style="font-size:13px;color:var(--muted)"><?= htmlspecialchars($usuario['nombre']) ?></span>
    </div>
    <div class="content">

        <?php if ($vista === 'inicio'): ?>
        <div class="welcome-card">
            <div class="welcome-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <h2>Hola, <?= htmlspecialchars(explode(' ',$usuario['nombre'])[0]) ?>!</h2>
            <p>Explora nuestro menú y realiza tu pedido fácilmente. También puedes consultar tus pedidos anteriores.</p>
            <a href="../../Public/index.php" class="btn-orange">Ver Menú →</a>
        </div>

        <?php elseif ($vista === 'detalle' && $pedido_detalle): ?>
        <a href="cliente.php?vista=pedidos" class="btn-back">← Volver a mis pedidos</a>
        <div class="detalle-card">
            <div style="margin-bottom:20px">
                <div style="font-family:'Bebas Neue',sans-serif;font-size:28px;color:var(--orange)">#<?= htmlspecialchars($pedido_detalle['numero_orden']) ?></div>
                <div style="font-size:12px;color:var(--muted)"><?= date('d/m/Y H:i',strtotime($pedido_detalle['creado_en'])) ?></div>
            </div>
            <div class="detalle-grid">
                <div class="detalle-item"><div class="detalle-label">Tipo</div><div class="detalle-value"><?= htmlspecialchars($pedido_detalle['tipo']) ?></div></div>
                <?php if($pedido_detalle['mesa_numero']): ?>
                <div class="detalle-item"><div class="detalle-label">Mesa</div><div class="detalle-value">Mesa <?= $pedido_detalle['mesa_numero'] ?></div></div>
                <?php endif; ?>
                <div class="detalle-item"><div class="detalle-label">Estado</div><div class="detalle-value"><?= htmlspecialchars($pedido_detalle['estado']) ?></div></div>
                <div class="detalle-item"><div class="detalle-label">Método de pago</div><div class="detalle-value"><?= htmlspecialchars($pedido_detalle['metodo_pago'] ?? 'Efectivo') ?></div></div>
            </div>
            <div style="background:var(--card2);border:1px solid var(--border);border-radius:10px;padding:16px;margin-bottom:16px">
                <?php foreach($pedido_detalle['items'] as $item): ?>
                <div class="item-row">
                    <div>
                        <div style="font-size:14px;font-weight:600"><?= htmlspecialchars($item['nombre_producto']) ?></div>
                        <div style="font-size:12px;color:var(--muted)">x<?= $item['cantidad'] ?> × $<?= number_format($item['precio_unitario'],0,',','.') ?></div>
                        <?php if(!empty($item['nota_especial'])): ?><div style="font-size:12px;color:#ffc800;margin-top:2px">⚠️ <?= htmlspecialchars($item['nota_especial']) ?></div><?php endif; ?>
                    </div>
                    <div style="color:var(--orange);font-weight:700">$<?= number_format($item['subtotal'],0,',','.') ?></div>
                </div>
                <?php endforeach; ?>
                <div class="total-line"><span>Total</span><span>$<?= number_format($pedido_detalle['total'],0,',','.') ?></span></div>
            </div>
        </div>

        <?php elseif ($vista === 'pedidos'): ?>
        <div class="section-card">
            <div class="section-head"><h2>Historial de pedidos</h2></div>
            <?php if(empty($pedidos)): ?>
            <div class="empty-state">
                <p>Aún no tienes pedidos. ¡Explora el catálogo y haz tu primer pedido!</p>
                <a href="../../Public/index.php" class="btn-orange">Ver Menú →</a>
            </div>
            <?php else: ?>
            <table>
                <thead><tr><th>Orden</th><th>Fecha</th><th>Tipo</th><th>Total</th><th>Estado</th><th>Detalle</th></tr></thead>
                <tbody>
                <?php
                $badge_map = ['Recibido'=>'badge-recibido','En preparacion'=>'badge-preparacion','Listo'=>'badge-listo','Entregado'=>'badge-entregado','Pagado'=>'badge-pagado','Cancelado'=>'badge-cancelado'];
                foreach($pedidos as $p): ?>
                <tr>
                    <td><span class="numero-orden">#<?= htmlspecialchars($p['numero_orden']) ?></span></td>
                    <td style="color:var(--muted);font-size:12px"><?= date('d/m/Y H:i',strtotime($p['creado_en'])) ?></td>
                    <td><?= htmlspecialchars($p['tipo']) ?><?= $p['mesa_numero']?' · Mesa '.$p['mesa_numero']:'' ?></td>
                    <td style="color:var(--orange);font-weight:700">$<?= number_format($p['total'],0,',','.') ?></td>
                    <td><span class="badge <?= $badge_map[$p['estado']] ?? '' ?>"><?= $p['estado'] ?></span></td>
                    <td><a href="cliente.php?vista=detalle&id=<?= $p['id'] ?>" class="btn-ver">Ver detalle</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>
