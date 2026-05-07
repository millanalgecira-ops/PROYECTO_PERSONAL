<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}
$usuario = $_SESSION['usuario'];
$alert   = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);

require_once __DIR__ . '/../../Config/database.php';
$db = (new Database())->conectar();

// Acción liberar mesa
if (isset($_GET['accion'])) {
    if ($_GET['accion'] === 'liberar' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $db->prepare("UPDATE mesas SET estado='Disponible', liberada_en=NOW() WHERE id=:id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $_SESSION['alert'] = ['icon'=>'success','text'=>'Mesa liberada correctamente'];
        header("Location: mesas.php"); exit;
    }
    if ($_GET['accion'] === 'liberarTodas') {
        $db->exec("UPDATE mesas SET estado='Disponible', liberada_en=NOW()");
        $_SESSION['alert'] = ['icon'=>'success','text'=>'Todas las mesas han sido liberadas'];
        header("Location: mesas.php"); exit;
    }
}

$mesas = $db->query("
    SELECT m.*, 
           (SELECT COUNT(*) FROM pedidos p WHERE p.mesa_id = m.id AND p.estado NOT IN ('Pagado','Cancelado')) AS pedidos_activos
    FROM mesas m ORDER BY m.numero
")->fetchAll(PDO::FETCH_ASSOC);

$disponibles = count(array_filter($mesas, fn($m) => $m['estado'] === 'Disponible'));
$ocupadas    = count(array_filter($mesas, fn($m) => $m['estado'] === 'Ocupada'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Mesas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
        :root { --black:#0e0e0e;--dark:#141414;--card:#1c1a18;--border:#2e2b27;--orange:#f07000;--orange2:#e06500;--text:#f0ece6;--muted:#8a8078;--label:#c8bfb0; }
        body { font-family:'Barlow',sans-serif; background:var(--black); color:var(--text); min-height:100vh; display:flex; }
        .sidebar { width:240px; min-height:100vh; background:var(--dark); border-right:1px solid var(--border); display:flex; flex-direction:column; padding:28px 0; flex-shrink:0; }
        .sidebar-brand { display:flex; align-items:center; gap:10px; padding:0 24px 28px; border-bottom:1px solid var(--border); }
        .sidebar-brand-name { font-family:'Bebas Neue',sans-serif; font-size:20px; }
        .sidebar-brand-sub  { font-size:9px; letter-spacing:3px; text-transform:uppercase; color:var(--orange); }
        .sidebar-nav { flex:1; padding:20px 12px; }
        .nav-label { font-size:10px; letter-spacing:3px; text-transform:uppercase; color:var(--muted); padding:0 12px; margin:18px 0 8px; }
        .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:8px; font-size:14px; color:var(--label); text-decoration:none; transition:background .2s,color .2s; }
        .nav-item:hover,.nav-item.active { background:rgba(240,112,0,.12); color:var(--orange); }
        .sidebar-footer { padding:20px 12px 0; border-top:1px solid var(--border); }
        .user-info { display:flex; align-items:center; gap:10px; padding:10px 12px; }
        .user-avatar { width:34px; height:34px; border-radius:50%; background:rgba(240,112,0,.2); border:1px solid rgba(240,112,0,.4); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:var(--orange); flex-shrink:0; }
        .user-name { font-size:13px; font-weight:600; }
        .user-role { font-size:11px; color:var(--orange); }
        .btn-logout { display:flex; align-items:center; gap:6px; background:none; border:1px solid var(--border); border-radius:8px; padding:8px 14px; color:var(--muted); font-size:13px; font-family:'Barlow',sans-serif; cursor:pointer; transition:border-color .2s,color .2s; text-decoration:none; }
        .btn-logout:hover { border-color:var(--orange); color:var(--orange); }
        .main { flex:1; display:flex; flex-direction:column; }
        .topbar { height:60px; background:var(--dark); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 32px; flex-shrink:0; }
        .topbar-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:1px; }
        .content { flex:1; padding:32px; overflow-y:auto; }
        .alert-box { padding:10px 16px; border-radius:8px; font-size:13px; margin-bottom:20px; }
        .alert-success { background:rgba(0,200,100,.1); border:1px solid #00c864; color:#00c864; }
        .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(180px,1fr)); gap:16px; margin-bottom:28px; }
        .stat-card { background:var(--card); border:1px solid var(--border); border-radius:12px; padding:20px; }
        .stat-label { font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:1px; margin-bottom:8px; }
        .stat-value { font-family:'Bebas Neue',sans-serif; font-size:36px; line-height:1; }
        .stat-sub { font-size:12px; color:var(--orange); margin-top:4px; }
        .toolbar { display:flex; justify-content:flex-end; margin-bottom:20px; }
        .btn-orange { display:inline-flex; align-items:center; gap:6px; background:var(--orange); color:#fff; border:none; border-radius:8px; padding:10px 18px; font-size:13px; font-family:'Barlow',sans-serif; font-weight:600; cursor:pointer; text-decoration:none; transition:background .2s; }
        .btn-orange:hover { background:var(--orange2); }
        .mesas-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:16px; }
        .mesa-card { background:var(--card); border:2px solid var(--border); border-radius:12px; padding:24px 16px; text-align:center; transition:border-color .2s; }
        .mesa-card.disponible { border-color:rgba(100,220,130,.3); }
        .mesa-card.ocupada    { border-color:rgba(255,80,80,.3); }
        .mesa-numero { font-family:'Bebas Neue',sans-serif; font-size:42px; line-height:1; margin-bottom:8px; }
        .mesa-card.disponible .mesa-numero { color:#64dc82; }
        .mesa-card.ocupada    .mesa-numero { color:#ff5050; }
        .mesa-estado { font-size:12px; font-weight:600; margin-bottom:16px; }
        .mesa-card.disponible .mesa-estado { color:#64dc82; }
        .mesa-card.ocupada    .mesa-estado { color:#ff5050; }
        .btn-liberar { display:inline-block; padding:6px 14px; border-radius:6px; font-size:12px; font-family:'Barlow',sans-serif; font-weight:600; text-decoration:none; background:rgba(255,80,80,.1); border:1px solid rgba(255,80,80,.25); color:#ff5050; transition:background .2s; }
        .btn-liberar:hover { background:rgba(255,80,80,.25); }
        .pedidos-badge { font-size:11px; color:var(--muted); margin-top:4px; }
    </style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand">
        <svg width="20" height="26" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div><div class="sidebar-brand-name">La Parrilla</div><div class="sidebar-brand-sub">Administrador</div></div>
    </div>
    <nav class="sidebar-nav">
        <p class="nav-label">Principal</p>
        <a class="nav-item" href="admin.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
        <p class="nav-label">Gestión</p>
        <a class="nav-item" href="admin.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Usuarios</a>
        <a class="nav-item" href="productos.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>Productos</a>
        <a class="nav-item" href="pedidos.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>Pedidos</a>
        <a class="nav-item active" href="mesas.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>Mesas</a>
        <a class="nav-item" href="ventas.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>Ventas e Ingresos</a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($usuario['nombre'],0,1)) ?></div>
            <div><div class="user-name"><?= htmlspecialchars($usuario['nombre']) ?></div><div class="user-role">Administrador</div></div>
        </div>
        <a href="../../Controllers/AuthController.php?accion=logout" class="btn-logout" style="width:100%;margin-top:8px;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>
<div class="main">
    <div class="topbar">
        <span class="topbar-title">Gestión de Mesas</span>
        <span style="font-size:13px;color:var(--muted)">Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?></span>
    </div>
    <div class="content">
        <?php if ($alert): ?>
            <div class="alert-box alert-success"><?= htmlspecialchars($alert['text']) ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total mesas</div>
                <div class="stat-value"><?= count($mesas) ?></div>
                <div class="stat-sub">Registradas en el sistema</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Disponibles</div>
                <div class="stat-value" style="color:#64dc82"><?= $disponibles ?></div>
                <div class="stat-sub">Listas para ocupar</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Ocupadas</div>
                <div class="stat-value" style="color:#ff5050"><?= $ocupadas ?></div>
                <div class="stat-sub">Con clientes activos</div>
            </div>
        </div>

        <div class="toolbar">
            <a href="mesas.php?accion=liberarTodas" class="btn-orange"
               onclick="return confirm('¿Liberar todas las mesas?')">
                Liberar todas las mesas
            </a>
        </div>

        <div class="mesas-grid">
            <?php foreach($mesas as $m):
                $cls = strtolower($m['estado']);
            ?>
            <div class="mesa-card <?= $cls ?>" <?= $m['estado']==='Ocupada' && $m['pedidos_activos']>0 ? "onclick=\"verPedidoMesa({$m['id']},{$m['numero']})\" style=\"cursor:pointer\"" : '' ?>>
                <div class="mesa-numero"><?= $m['numero'] ?></div>
                <div class="mesa-estado"><?= $m['estado'] ?></div>
                <?php if ($m['pedidos_activos'] > 0): ?>
                    <div class="pedidos-badge"><?= $m['pedidos_activos'] ?> pedido(s) activo(s)</div>
                    <div style="font-size:11px;color:var(--muted);margin-top:2px">Clic para ver detalle</div>
                <?php endif; ?>
                <?php if ($m['estado'] === 'Ocupada'): ?>
                    <br>
                    <a href="mesas.php?accion=liberar&id=<?= $m['id'] ?>" class="btn-liberar"
                       onclick="event.stopPropagation();return confirm('¿Liberar mesa <?= $m['numero'] ?>?')">Liberar</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- MODAL DETALLE PEDIDO DE MESA -->
<div id="modalMesa" style="display:none;position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.75);align-items:center;justify-content:center">
    <div style="background:#141414;border:1px solid #2e2b27;border-radius:16px;padding:28px;width:100%;max-width:480px;max-height:80vh;overflow-y:auto">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
            <h3 style="font-family:'Bebas Neue',sans-serif;font-size:22px" id="modalMesaTitulo">Mesa</h3>
            <button onclick="cerrarModalMesa()" style="background:none;border:none;color:#8a8078;font-size:20px;cursor:pointer">✕</button>
        </div>
        <div id="modalMesaContenido" style="font-size:14px;color:#8a8078">Cargando...</div>
    </div>
</div>

<?php
// Preparar datos de pedidos por mesa para el modal
$pedidos_por_mesa = [];
foreach ($mesas as $m) {
    if ($m['estado'] === 'Ocupada' && $m['pedidos_activos'] > 0) {
        $stmt = $db->prepare("
            SELECT p.numero_orden, p.estado, p.total, p.creado_en, p.tipo,
                   GROUP_CONCAT(pi.nombre_producto, ' x', pi.cantidad SEPARATOR ', ') AS items
            FROM pedidos p
            LEFT JOIN pedido_items pi ON pi.pedido_id = p.id
            WHERE p.mesa_id = :mid AND p.estado NOT IN ('Pagado','Cancelado')
            GROUP BY p.id
            ORDER BY p.creado_en DESC LIMIT 1
        ");
        $stmt->execute([':mid' => $m['id']]);
        $pedidos_por_mesa[$m['id']] = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
<script>
const pedidosMesa = <?= json_encode($pedidos_por_mesa) ?>;

function verPedidoMesa(mesaId, mesaNum) {
    const modal = document.getElementById('modalMesa');
    const titulo = document.getElementById('modalMesaTitulo');
    const contenido = document.getElementById('modalMesaContenido');
    titulo.textContent = 'Mesa ' + mesaNum;
    const p = pedidosMesa[mesaId];
    if (p) {
        contenido.innerHTML = `
            <div style="background:#1c1a18;border:1px solid #2e2b27;border-radius:10px;padding:16px;margin-bottom:12px">
                <div style="font-family:'Bebas Neue',sans-serif;font-size:20px;color:#f07000;margin-bottom:8px">#${p.numero_orden}</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px">
                    <div><div style="font-size:11px;color:#8a8078;text-transform:uppercase;letter-spacing:1px">Estado</div><div style="font-size:13px;font-weight:600">${p.estado}</div></div>
                    <div><div style="font-size:11px;color:#8a8078;text-transform:uppercase;letter-spacing:1px">Tipo</div><div style="font-size:13px;font-weight:600">${p.tipo}</div></div>
                    <div><div style="font-size:11px;color:#8a8078;text-transform:uppercase;letter-spacing:1px">Total</div><div style="font-size:13px;font-weight:700;color:#f07000">$${parseInt(p.total).toLocaleString('es-CO')}</div></div>
                    <div><div style="font-size:11px;color:#8a8078;text-transform:uppercase;letter-spacing:1px">Hora</div><div style="font-size:13px">${p.creado_en.substring(11,16)}</div></div>
                </div>
                <div style="font-size:11px;color:#8a8078;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px">Ítems</div>
                <div style="font-size:13px;color:#f0ece6">${p.items || 'Sin ítems'}</div>
            </div>`;
    } else {
        contenido.innerHTML = '<p>No se encontró información del pedido.</p>';
    }
    modal.style.display = 'flex';
}

function cerrarModalMesa() {
    document.getElementById('modalMesa').style.display = 'none';
}
document.getElementById('modalMesa').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalMesa();
});
</script>
</body>
</html>
