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

$filtro = $_GET['estado'] ?? 'todos';

$sql = "SELECT p.*, c.nombre AS cliente_nombre, m.numero AS mesa_numero
        FROM pedidos p
        LEFT JOIN clientes c ON c.id = p.cliente_id
        LEFT JOIN mesas m ON m.id = p.mesa_id";
if ($filtro !== 'todos') {
    $sql .= " WHERE p.estado = " . $db->quote($filtro);
}
$sql .= " ORDER BY p.creado_en DESC";
$pedidos = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$estados = ['Recibido','En preparacion','Listo','Entregado','Pagado','Cancelado'];
$colores  = [
    'Recibido'       => 'badge-recibido',
    'En preparacion' => 'badge-preparacion',
    'Listo'          => 'badge-listo',
    'Entregado'      => 'badge-entregado',
    'Pagado'         => 'badge-pagado',
    'Cancelado'      => 'badge-cancelado',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Pedidos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
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
        .main { flex:1; display:flex; flex-direction:column; overflow:hidden; }
        .topbar { height:60px; background:var(--dark); border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; padding:0 32px; flex-shrink:0; }
        .topbar-title { font-family:'Bebas Neue',sans-serif; font-size:22px; letter-spacing:1px; }
        .content { flex:1; padding:32px; overflow-y:auto; }
        .alert-box { padding:10px 16px; border-radius:8px; font-size:13px; margin-bottom:20px; }
        .alert-success { background:rgba(0,200,100,.1); border:1px solid #00c864; color:#00c864; }
        .alert-error   { background:rgba(240,112,0,.12); border:1px solid var(--orange); color:var(--orange); }
        .filtros { display:flex; gap:8px; margin-bottom:20px; flex-wrap:wrap; }
        .btn-filtro { padding:8px 16px; border-radius:20px; font-size:13px; font-family:'Barlow',sans-serif; font-weight:600; cursor:pointer; text-decoration:none; border:1px solid var(--border); color:var(--muted); transition:all .2s; }
        .btn-filtro:hover,.btn-filtro.active { background:rgba(240,112,0,.12); border-color:var(--orange); color:var(--orange); }
        .section-card { background:var(--card); border:1px solid var(--border); border-radius:12px; overflow:hidden; }
        table { width:100%; border-collapse:collapse; }
        th { padding:12px 16px; text-align:left; font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); border-bottom:1px solid var(--border); }
        td { padding:14px 16px; font-size:14px; border-bottom:1px solid rgba(46,43,39,.5); vertical-align:middle; }
        tr:last-child td { border-bottom:none; }
        tr:hover td { background:rgba(255,255,255,.02); }
        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-recibido    { background:rgba(100,180,255,.12); color:#64b4ff; }
        .badge-preparacion { background:rgba(255,200,0,.12);   color:#ffc800; }
        .badge-listo       { background:rgba(100,220,130,.12); color:#64dc82; }
        .badge-entregado   { background:rgba(240,112,0,.15);   color:var(--orange); }
        .badge-pagado      { background:rgba(100,220,130,.2);  color:#00c864; }
        .badge-cancelado   { background:rgba(255,80,80,.12);   color:#ff5050; }
        .numero-orden { font-family:'Bebas Neue',sans-serif; font-size:16px; color:var(--orange); }
        .empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
        .select-estado { background:#1a1a1a; border:1px solid var(--border); border-radius:6px; padding:6px 10px; color:var(--text); font-size:12px; font-family:'Barlow',sans-serif; cursor:pointer; outline:none; }
        .select-estado:focus { border-color:var(--orange); }
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
        <a class="nav-item" href="admin.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>Dashboard</a>
        <p class="nav-label">Gestión</p>
        <a class="nav-item" href="admin.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Usuarios</a>
        <a class="nav-item" href="productos.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>Productos</a>
        <a class="nav-item active" href="pedidos.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>Pedidos</a>
        <a class="nav-item" href="mesas.php"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>Mesas</a>
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
        <span class="topbar-title">Gestión de Pedidos</span>
        <span style="font-size:13px;color:var(--muted)">Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?></span>
    </div>
    <div class="content">
        <?php if ($alert): $cls = $alert['icon']==='success'?'alert-success':'alert-error'; ?>
            <div class="alert-box <?= $cls ?>"><?= htmlspecialchars($alert['text']) ?></div>
        <?php endif; ?>

        <div class="filtros">
            <a href="pedidos.php" class="btn-filtro <?= $filtro==='todos'?'active':'' ?>">Todos</a>
            <?php foreach($estados as $e): ?>
                <a href="pedidos.php?estado=<?= urlencode($e) ?>" class="btn-filtro <?= $filtro===$e?'active':'' ?>"><?= $e ?></a>
            <?php endforeach; ?>
        </div>

        <div class="section-card">
            <?php if (empty($pedidos)): ?>
                <div class="empty-state"><p>No hay pedidos <?= $filtro !== 'todos' ? "con estado \"$filtro\"" : 'registrados' ?></p></div>
            <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Mesa</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($pedidos as $p): ?>
                <tr>
                    <td><span class="numero-orden">#<?= htmlspecialchars($p['numero_orden']) ?></span></td>
                    <td><?= htmlspecialchars($p['cliente_nombre'] ?? 'Invitado') ?></td>
                    <td><?= htmlspecialchars($p['tipo']) ?></td>
                    <td><?= $p['mesa_numero'] ? 'Mesa '.$p['mesa_numero'] : '—' ?></td>
                    <td style="color:var(--orange);font-weight:700">$<?= number_format($p['total'],0,',','.') ?></td>
                    <td><span class="badge <?= $colores[$p['estado']] ?? '' ?>"><?= $p['estado'] ?></span></td>
                    <td style="color:var(--muted);font-size:12px"><?= date('d/m/Y H:i', strtotime($p['creado_en'])) ?></td>
                    <td>
                        <div style="display:flex;gap:6px;align-items:center">
                        <form method="POST" action="../../Controllers/PedidoController.php?accion=cambiarEstado">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <select name="estado" class="select-estado" onchange="this.form.submit()">
                                <?php foreach($estados as $e): ?>
                                    <option value="<?= $e ?>" <?= $p['estado']===$e?'selected':'' ?>><?= $e ?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                        <?php if(!in_array($p['estado'],['Cancelado','Pagado'])): ?>
                        <a href="../../Controllers/PedidoController.php?accion=cancelar&id=<?= $p['id'] ?>"
                           style="padding:6px 10px;border-radius:6px;background:rgba(255,80,80,.1);border:1px solid rgba(255,80,80,.25);color:#ff5050;font-size:11px;text-decoration:none;white-space:nowrap"
                           onclick="return confirm('¿Cancelar este pedido?')">Cancelar</a>
                        <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
