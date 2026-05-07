<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'cocina') {
    header('Location: ../usuarios/login.php'); exit;
}
$usuario = $_SESSION['usuario'];
$alert   = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

require_once __DIR__ . '/../../Config/database.php';
$db = (new Database())->conectar();

$vista = $_GET['vista'] ?? 'comandas';

// Acciones de productos
if (isset($_GET['accion'])) {
    $id = intval($_GET['id'] ?? 0);
    if ($_GET['accion'] === 'agotar' && $id) {
        $db->prepare("UPDATE productos SET disponible=0 WHERE id=:id")->execute([':id'=>$id]);
        $uid = $_SESSION['usuario']['id_usuario'];
        $db->prepare("INSERT INTO producto_agotamientos (producto_id,reportado_por,motivo,reportado_en) VALUES(:p,:u,'Reportado por cocina',NOW())")->execute([':p'=>$id,':u'=>$uid]);
        $_SESSION['alert'] = ['icon'=>'warning','text'=>'Producto marcado como agotado'];
        header("Location: cocina.php?vista=productos"); exit;
    }
    if ($_GET['accion'] === 'activar' && $id) {
        $db->prepare("UPDATE productos SET disponible=1 WHERE id=:id")->execute([':id'=>$id]);
        $_SESSION['alert'] = ['icon'=>'success','text'=>'Producto reactivado en el catálogo'];
        header("Location: cocina.php?vista=productos"); exit;
    }
}

$pedidos = $db->query("SELECT p.id,p.numero_orden,p.tipo,p.estado,p.observaciones,p.creado_en,m.numero AS mesa_numero,c.nombre AS cliente_nombre FROM pedidos p LEFT JOIN mesas m ON m.id=p.mesa_id LEFT JOIN clientes c ON c.id=p.cliente_id WHERE p.estado NOT IN ('Pagado','Cancelado') ORDER BY FIELD(p.estado,'Recibido','En preparacion','Listo','Entregado'),p.creado_en ASC")->fetchAll(PDO::FETCH_ASSOC);

$items_map = [];
foreach ($db->query("SELECT pi.pedido_id,pi.nombre_producto,pi.cantidad,pi.nota_especial FROM pedido_items pi INNER JOIN pedidos p ON p.id=pi.pedido_id WHERE p.estado NOT IN ('Pagado','Cancelado')")->fetchAll(PDO::FETCH_ASSOC) as $item) {
    $items_map[$item['pedido_id']][] = $item;
}

$productos = $db->query("SELECT p.id,p.nombre,p.disponible,c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON c.id=p.categoria_id ORDER BY p.disponible DESC,c.nombre,p.nombre")->fetchAll(PDO::FETCH_ASSOC);

$colores = [
    'Recibido'       => ['bg'=>'rgba(100,180,255,.12)','border'=>'rgba(100,180,255,.3)','color'=>'#64b4ff'],
    'En preparacion' => ['bg'=>'rgba(255,200,0,.12)',  'border'=>'rgba(255,200,0,.3)',  'color'=>'#ffc800'],
    'Listo'          => ['bg'=>'rgba(100,220,130,.12)','border'=>'rgba(100,220,130,.3)','color'=>'#64dc82'],
    'Entregado'      => ['bg'=>'rgba(240,112,0,.12)',  'border'=>'rgba(240,112,0,.3)',  'color'=>'#f07000'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>La Parrilla – Cocina</title>
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--black:#0e0e0e;--dark:#141414;--card:#1c1a18;--border:#2e2b27;--orange:#f07000;--text:#f0ece6;--muted:#8a8078;--label:#c8bfb0;--blue:#64b4ff}
body{font-family:'Barlow',sans-serif;background:var(--black);color:var(--text);min-height:100vh;display:flex}
.sidebar{width:220px;min-height:100vh;background:var(--dark);border-right:1px solid var(--border);display:flex;flex-direction:column;padding:24px 0;flex-shrink:0}
.sidebar-brand{display:flex;align-items:center;gap:10px;padding:0 20px 24px;border-bottom:1px solid var(--border)}
.sidebar-brand-name{font-family:'Bebas Neue',sans-serif;font-size:20px}
.sidebar-brand-sub{font-size:9px;letter-spacing:3px;text-transform:uppercase;color:var(--orange)}
.sidebar-nav{flex:1;padding:16px 10px}
.nav-label{font-size:10px;letter-spacing:3px;text-transform:uppercase;color:var(--muted);padding:0 10px;margin:16px 0 6px}
.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;font-size:14px;color:var(--label);text-decoration:none;transition:background .2s,color .2s}
.nav-item:hover,.nav-item.active{background:rgba(240,112,0,.12);color:var(--orange)}
.sidebar-footer{padding:16px 10px 0;border-top:1px solid var(--border)}
.user-info{display:flex;align-items:center;gap:10px;padding:10px 12px}
.user-avatar{width:34px;height:34px;border-radius:50%;background:rgba(100,180,255,.15);border:1px solid rgba(100,180,255,.3);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--blue);flex-shrink:0}
.user-name{font-size:13px;font-weight:600}
.user-role{font-size:11px;color:var(--blue)}
.btn-logout{display:flex;align-items:center;gap:6px;background:none;border:1px solid var(--border);border-radius:8px;padding:8px 14px;color:var(--muted);font-size:13px;font-family:'Barlow',sans-serif;cursor:pointer;transition:border-color .2s,color .2s;text-decoration:none}
.btn-logout:hover{border-color:var(--orange);color:var(--orange)}
.main{flex:1;display:flex;flex-direction:column;overflow:hidden}
.topbar{height:60px;background:var(--dark);border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;padding:0 28px;flex-shrink:0}
.topbar-title{font-family:'Bebas Neue',sans-serif;font-size:22px;letter-spacing:1px}
.live-badge{display:flex;align-items:center;gap:6px;font-size:12px;color:#64dc82}
.live-dot{width:8px;height:8px;border-radius:50%;background:#64dc82;animation:pulse 1.5s infinite}
@keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.3)}}
.content{flex:1;padding:24px 28px;overflow-y:auto}
.alert-box{padding:10px 16px;border-radius:8px;font-size:13px;margin-bottom:20px}
.alert-success{background:rgba(0,200,100,.1);border:1px solid #00c864;color:#00c864}
.alert-warning{background:rgba(255,200,0,.1);border:1px solid #ffc800;color:#ffc800}
.filtros{display:flex;gap:8px;margin-bottom:24px;flex-wrap:wrap}
.btn-filtro{padding:7px 16px;border-radius:20px;font-size:13px;font-family:'Barlow',sans-serif;font-weight:600;cursor:pointer;text-decoration:none;border:1px solid var(--border);color:var(--muted);background:none;transition:all .2s}
.btn-filtro:hover,.btn-filtro.active{background:rgba(240,112,0,.12);border-color:var(--orange);color:var(--orange)}
.count-badge{display:inline-flex;align-items:center;justify-content:center;width:18px;height:18px;border-radius:50%;background:var(--orange);color:#fff;font-size:10px;font-weight:700;margin-left:4px}
.comandas-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:16px}
.comanda-card{background:var(--card);border:1px solid var(--border);border-radius:14px;overflow:hidden}
.comanda-header{padding:14px 18px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border)}
.comanda-orden{font-family:'Bebas Neue',sans-serif;font-size:20px;color:var(--orange)}
.comanda-tiempo{font-size:11px;color:var(--muted)}
.estado-badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700}
.comanda-meta{padding:10px 18px;display:flex;gap:8px;border-bottom:1px solid var(--border);flex-wrap:wrap}
.meta-tag{display:inline-flex;align-items:center;gap:5px;font-size:12px;color:var(--label);background:rgba(255,255,255,.04);border:1px solid var(--border);border-radius:6px;padding:4px 10px}
.comanda-items{padding:14px 18px}
.comanda-item{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px}
.comanda-item:last-child{margin-bottom:0}
.item-qty{min-width:28px;height:28px;border-radius:6px;background:rgba(240,112,0,.15);border:1px solid rgba(240,112,0,.25);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:var(--orange);flex-shrink:0}
.item-nombre{font-size:14px;font-weight:600}
.item-nota{font-size:12px;color:#ffc800;margin-top:2px;font-style:italic}
.comanda-footer{padding:12px 18px;border-top:1px solid var(--border);background:rgba(0,0,0,.2)}
.btn-estado{width:100%;padding:10px;border-radius:8px;font-size:13px;font-family:'Barlow',sans-serif;font-weight:700;cursor:pointer;border:none;transition:all .2s}
.btn-iniciar{background:rgba(255,200,0,.15);color:#ffc800;border:1px solid rgba(255,200,0,.3)}
.btn-iniciar:hover{background:#ffc800;color:#000}
.btn-listo{background:rgba(100,220,130,.15);color:#64dc82;border:1px solid rgba(100,220,130,.3)}
.btn-listo:hover{background:#64dc82;color:#000}
.btn-entregar{background:rgba(240,112,0,.15);color:var(--orange);border:1px solid rgba(240,112,0,.3)}
.btn-entregar:hover{background:var(--orange);color:#fff}
.btn-completado{background:rgba(100,220,130,.08);color:var(--muted);border:1px solid var(--border);cursor:default}
.empty-state{text-align:center;padding:80px 20px;color:var(--muted)}
.empty-state h3{font-family:'Bebas Neue',sans-serif;font-size:24px;margin-bottom:8px;color:var(--label)}
.empty-state p{font-size:14px}
.prod-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:14px}
.prod-card{background:var(--card);border-radius:12px;padding:18px;display:flex;flex-direction:column;gap:12px}
.prod-name{font-size:15px;font-weight:700}
.prod-cat{font-size:12px;color:var(--muted);margin-top:2px}
.btn-agotar{display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:8px;background:rgba(255,80,80,.1);border:1px solid rgba(255,80,80,.25);color:#ff5050;font-size:13px;font-weight:600;text-decoration:none;transition:background .2s}
.btn-agotar:hover{background:rgba(255,80,80,.25)}
.btn-activar{display:flex;align-items:center;justify-content:center;gap:6px;padding:10px;border-radius:8px;background:rgba(100,220,130,.1);border:1px solid rgba(100,220,130,.25);color:#64dc82;font-size:13px;font-weight:600;text-decoration:none;transition:background .2s}
.btn-activar:hover{background:rgba(100,220,130,.25)}
</style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand">
        <svg width="20" height="26" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div><div class="sidebar-brand-name">La Parrilla</div><div class="sidebar-brand-sub">Cocina</div></div>
    </div>
    <nav class="sidebar-nav">
        <p class="nav-label">Panel</p>
        <a class="nav-item <?= $vista==='comandas'?'active':'' ?>" href="cocina.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
            Comandas
        </a>
        <a class="nav-item <?= $vista==='productos'?'active':'' ?>" href="cocina.php?vista=productos">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Productos
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($usuario['nombre'],0,1)) ?></div>
            <div><div class="user-name"><?= htmlspecialchars($usuario['nombre']) ?></div><div class="user-role">Cocina</div></div>
        </div>
        <a href="../../Controllers/AuthController.php?accion=logout" class="btn-logout" style="width:100%;margin-top:8px;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>

<div class="main">
    <div class="topbar">
        <span class="topbar-title"><?= $vista==='productos' ? 'Disponibilidad de Productos' : 'Comandas en curso' ?></span>
        <div style="display:flex;align-items:center;gap:16px">
            <?php if($vista==='comandas'): ?>
            <div class="live-badge"><span class="live-dot"></span>En vivo — <?= count($pedidos) ?> activo(s)</div>
            <a href="cocina.php" style="font-size:12px;color:var(--muted);text-decoration:none;border:1px solid var(--border);border-radius:6px;padding:6px 12px">↻ Actualizar</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <?php if ($alert): ?>
        <div class="alert-box <?= $alert['icon']==='success'?'alert-success':'alert-warning' ?>"><?= htmlspecialchars($alert['text']) ?></div>
        <?php endif; ?>

        <?php if ($vista === 'productos'): ?>
        <div class="prod-grid">
            <?php foreach($productos as $prod): ?>
            <div class="prod-card" style="border:1px solid <?= $prod['disponible']?'var(--border)':'rgba(255,80,80,.3)' ?>">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <div><div class="prod-name"><?= htmlspecialchars($prod['nombre']) ?></div><div class="prod-cat"><?= htmlspecialchars($prod['categoria']) ?></div></div>
                    <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;background:<?= $prod['disponible']?'rgba(100,220,130,.12)':'rgba(255,80,80,.12)' ?>;color:<?= $prod['disponible']?'#64dc82':'#ff5050' ?>;border:1px solid <?= $prod['disponible']?'rgba(100,220,130,.3)':'rgba(255,80,80,.3)' ?>">
                        <?= $prod['disponible']?'Disponible':'Agotado' ?>
                    </span>
                </div>
                <?php if($prod['disponible']): ?>
                <a href="cocina.php?accion=agotar&id=<?= $prod['id'] ?>" class="btn-agotar" onclick="return confirm('¿Reportar como agotado?')">⚠️ Reportar agotado</a>
                <?php else: ?>
                <a href="cocina.php?accion=activar&id=<?= $prod['id'] ?>" class="btn-activar" onclick="return confirm('¿Marcar como disponible?')">✅ Marcar disponible</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <?php $filtro = $_GET['estado'] ?? 'todos'; $conteo = array_count_values(array_column($pedidos,'estado')); ?>
        <div class="filtros">
            <a href="cocina.php" class="btn-filtro <?= $filtro==='todos'?'active':'' ?>">Todos <span class="count-badge"><?= count($pedidos) ?></span></a>
            <?php foreach(['Recibido','En preparacion','Listo','Entregado'] as $e): ?>
            <a href="cocina.php?estado=<?= urlencode($e) ?>" class="btn-filtro <?= $filtro===$e?'active':'' ?>"><?= $e ?><?php if(isset($conteo[$e])): ?><span class="count-badge"><?= $conteo[$e] ?></span><?php endif; ?></a>
            <?php endforeach; ?>
        </div>
        <?php $lista = $filtro==='todos' ? $pedidos : array_filter($pedidos, fn($p)=>$p['estado']===$filtro); ?>
        <?php if(empty($lista)): ?>
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.2;margin-bottom:16px"><path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/></svg>
            <h3>Sin comandas activas</h3>
            <p>Los nuevos pedidos aparecerán aquí automáticamente</p>
        </div>
        <?php else: ?>
        <div class="comandas-grid">
        <?php foreach($lista as $p):
            $estado  = $p['estado'];
            $color   = $colores[$estado] ?? $colores['Recibido'];
            $items   = $items_map[$p['id']] ?? [];
            $minutos = round((time()-strtotime($p['creado_en']))/60);
        ?>
        <div class="comanda-card">
            <div class="comanda-header">
                <div>
                    <div class="comanda-orden">#<?= htmlspecialchars($p['numero_orden']) ?></div>
                    <div class="comanda-tiempo"><?= $minutos<1?'Hace un momento':($minutos<60?"Hace {$minutos} min":date('H:i',strtotime($p['creado_en']))) ?></div>
                </div>
                <span class="estado-badge" style="background:<?= $color['bg'] ?>;border:1px solid <?= $color['border'] ?>;color:<?= $color['color'] ?>"><?= $estado ?></span>
            </div>
            <div class="comanda-meta">
                <span class="meta-tag"><?= $p['tipo']==='En mesa'?'🍽️':'🥡' ?> <?= htmlspecialchars($p['tipo']) ?></span>
                <?php if($p['mesa_numero']): ?><span class="meta-tag">🪑 Mesa <?= $p['mesa_numero'] ?></span><?php endif; ?>
                <?php if($p['cliente_nombre']): ?><span class="meta-tag">👤 <?= htmlspecialchars($p['cliente_nombre']) ?></span><?php endif; ?>
            </div>
            <div class="comanda-items">
                <?php foreach($items as $item): ?>
                <div class="comanda-item">
                    <div class="item-qty"><?= $item['cantidad'] ?>x</div>
                    <div>
                        <div class="item-nombre"><?= htmlspecialchars($item['nombre_producto']) ?></div>
                        <?php if(!empty($item['nota_especial'])): ?><div class="item-nota">⚠️ <?= htmlspecialchars($item['nota_especial']) ?></div><?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(!empty($p['observaciones'])): ?>
                <div style="margin-top:10px;padding:8px 10px;background:rgba(255,200,0,.08);border:1px solid rgba(255,200,0,.2);border-radius:8px;font-size:12px;color:#ffc800">📝 <?= htmlspecialchars($p['observaciones']) ?></div>
                <?php endif; ?>
            </div>
            <div class="comanda-footer">
                <?php if($estado==='Recibido'): ?>
                <form method="POST" action="../../Controllers/PedidoController.php?accion=cambiarEstado">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="estado" value="En preparacion">
                    <button type="submit" class="btn-estado btn-iniciar">🔥 Iniciar preparación</button>
                </form>
                <?php elseif($estado==='En preparacion'): ?>
                <form method="POST" action="../../Controllers/PedidoController.php?accion=cambiarEstado">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="estado" value="Listo">
                    <button type="submit" class="btn-estado btn-listo">✅ Marcar como listo</button>
                </form>
                <?php elseif($estado==='Listo'): ?>
                <form method="POST" action="../../Controllers/PedidoController.php?accion=cambiarEstado">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>"><input type="hidden" name="estado" value="Entregado">
                    <button type="submit" class="btn-estado btn-entregar">🛎️ Marcar como entregado</button>
                </form>
                <?php else: ?>
                <button class="btn-estado btn-completado" disabled>✓ Entregado</button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<script>
setTimeout(()=>location.reload(), 30000);
<?php if($alert && $vista==='comandas'): ?>
window.addEventListener('load',()=>{
    const d=document.createElement('div');
    d.style.cssText='position:fixed;top:80px;right:24px;z-index:500;background:#1c1a18;border:1px solid #64dc82;border-radius:12px;padding:16px 20px;font-size:14px;color:#64dc82;box-shadow:0 8px 24px rgba(0,0,0,.4)';
    d.textContent='✅ <?= addslashes($alert["text"]) ?>';
    document.body.appendChild(d);
    setTimeout(()=>d.remove(),3000);
});
<?php endif; ?>
</script>
</body>
</html>
