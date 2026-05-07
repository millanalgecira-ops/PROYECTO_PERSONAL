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

// Obtener productos con categoría
$productos = $db->query("
    SELECT p.*, c.nombre AS categoria_nombre
    FROM productos p
    LEFT JOIN categorias c ON c.id = p.categoria_id
    ORDER BY c.nombre, p.nombre
")->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías para el select
$categorias = $db->query("SELECT id, nombre FROM categorias WHERE activa = 1 ORDER BY orden, nombre")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Gestión de Productos</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --black: #0e0e0e; --dark: #141414; --card: #1c1a18;
            --border: #2e2b27; --orange: #f07000; --orange2: #e06500;
            --text: #f0ece6; --muted: #8a8078; --label: #c8bfb0;
        }
        body { font-family: 'Barlow', sans-serif; background: var(--black); color: var(--text); min-height: 100vh; display: flex; }

        /* SIDEBAR */
        .sidebar { width: 240px; min-height: 100vh; background: var(--dark); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 28px 0; flex-shrink: 0; }
        .sidebar-brand { display: flex; align-items: center; gap: 10px; padding: 0 24px 28px; border-bottom: 1px solid var(--border); }
        .sidebar-brand-name { font-family: 'Bebas Neue', sans-serif; font-size: 20px; }
        .sidebar-brand-sub  { font-size: 9px; letter-spacing: 3px; text-transform: uppercase; color: var(--orange); }
        .sidebar-nav { flex: 1; padding: 20px 12px; }
        .nav-label { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: var(--muted); padding: 0 12px; margin: 18px 0 8px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px; font-size: 14px; color: var(--label); text-decoration: none; transition: background .2s, color .2s; }
        .nav-item:hover, .nav-item.active { background: rgba(240,112,0,.12); color: var(--orange); }
        .sidebar-footer { padding: 20px 12px 0; border-top: 1px solid var(--border); }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 10px 12px; }
        .user-avatar { width: 34px; height: 34px; border-radius: 50%; background: rgba(240,112,0,.2); border: 1px solid rgba(240,112,0,.4); display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; color: var(--orange); flex-shrink: 0; }
        .user-name { font-size: 13px; font-weight: 600; }
        .user-role { font-size: 11px; color: var(--orange); }
        .btn-logout { display: flex; align-items: center; gap: 6px; background: none; border: 1px solid var(--border); border-radius: 8px; padding: 8px 14px; color: var(--muted); font-size: 13px; font-family: 'Barlow', sans-serif; cursor: pointer; transition: border-color .2s, color .2s; text-decoration: none; }
        .btn-logout:hover { border-color: var(--orange); color: var(--orange); }

        /* MAIN */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar { height: 60px; background: var(--dark); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; flex-shrink: 0; }
        .topbar-title { font-family: 'Bebas Neue', sans-serif; font-size: 22px; letter-spacing: 1px; }
        .content { flex: 1; padding: 32px; overflow-y: auto; }

        /* ALERTS */
        .alert-box { padding: 10px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; }
        .alert-success { background: rgba(0,200,100,.1); border: 1px solid #00c864; color: #00c864; }
        .alert-error   { background: rgba(240,112,0,.12); border: 1px solid var(--orange); color: var(--orange); }
        .alert-warning { background: rgba(255,200,0,.1);  border: 1px solid #ffc800; color: #ffc800; }

        /* TOOLBAR */
        .toolbar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; flex-wrap: wrap; gap: 12px; }
        .toolbar-left { display: flex; gap: 10px; }
        .btn-orange { display: inline-flex; align-items: center; gap: 6px; background: var(--orange); color: #fff; border: none; border-radius: 8px; padding: 10px 18px; font-size: 13px; font-family: 'Barlow', sans-serif; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .2s; }
        .btn-orange:hover { background: var(--orange2); }
        .btn-outline-orange { display: inline-flex; align-items: center; gap: 6px; background: transparent; color: var(--orange); border: 1px solid var(--orange); border-radius: 8px; padding: 10px 18px; font-size: 13px; font-family: 'Barlow', sans-serif; font-weight: 600; cursor: pointer; text-decoration: none; transition: background .2s, color .2s; }
        .btn-outline-orange:hover { background: var(--orange); color: #fff; }

        /* SEARCH */
        .search-input { background: var(--card); border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; font-size: 13px; font-family: 'Barlow', sans-serif; color: var(--text); outline: none; width: 220px; transition: border-color .2s; }
        .search-input:focus { border-color: var(--orange); }
        .search-input::placeholder { color: var(--muted); }

        /* TABLE */
        .section-card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 16px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); border-bottom: 1px solid var(--border); }
        td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid rgba(46,43,39,.5); vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-disponible  { background: rgba(100,220,130,.12); color: #64dc82; }
        .badge-agotado     { background: rgba(255,80,80,.12);   color: #ff5050; }
        .badge-popular     { background: rgba(240,112,0,.15);   color: var(--orange); }

        .precio { font-weight: 700; color: var(--orange); }
        .categoria-tag { font-size: 12px; color: var(--muted); background: rgba(255,255,255,.05); padding: 3px 8px; border-radius: 6px; }

        .actions { display: flex; gap: 6px; }
        .btn-edit { background: rgba(100,180,255,.1); border: 1px solid rgba(100,180,255,.25); border-radius: 6px; padding: 6px 12px; color: #64b4ff; font-size: 12px; font-family: 'Barlow', sans-serif; cursor: pointer; transition: background .2s; }
        .btn-edit:hover { background: rgba(100,180,255,.25); }
        .btn-delete { background: rgba(255,80,80,.1); border: 1px solid rgba(255,80,80,.25); border-radius: 6px; padding: 6px 12px; color: #ff5050; font-size: 12px; font-family: 'Barlow', sans-serif; cursor: pointer; transition: background .2s; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-delete:hover { background: rgba(255,80,80,.25); }
        .btn-toggle { background: none; border: 1px solid var(--border); border-radius: 6px; padding: 6px 12px; color: var(--muted); font-size: 12px; font-family: 'Barlow', sans-serif; cursor: pointer; transition: all .2s; text-decoration: none; display: inline-flex; align-items: center; }
        .btn-toggle:hover { border-color: var(--orange); color: var(--orange); }

        /* MODAL */
        .modal-overlay { display: none; position: fixed; inset: 0; z-index: 200; background: rgba(0,0,0,.7); align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: var(--dark); border: 1px solid var(--border); border-radius: 14px; padding: 32px; width: 100%; max-width: 500px; max-height: 90vh; overflow-y: auto; }
        .modal h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; color: var(--label); margin-bottom: 6px; }
        .field input, .field select, .field textarea { width: 100%; background: #1a1a1a; border: 1px solid var(--border); border-radius: 8px; padding: 11px 14px; font-size: 14px; font-family: 'Barlow', sans-serif; color: var(--text); outline: none; transition: border-color .2s; }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--orange); }
        .field select option { background: #1a1a1a; }
        .field textarea { resize: vertical; min-height: 80px; }
        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .field-check { display: flex; align-items: center; gap: 8px; }
        .field-check input[type=checkbox] { width: 16px; height: 16px; accent-color: var(--orange); }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .btn-cancel { background: none; border: 1px solid var(--border); border-radius: 8px; padding: 10px 18px; color: var(--muted); font-size: 14px; font-family: 'Barlow', sans-serif; cursor: pointer; transition: border-color .2s, color .2s; }
        .btn-cancel:hover { border-color: var(--orange); color: var(--orange); }

        .empty-state { text-align: center; padding: 60px 20px; color: var(--muted); }
        .empty-state svg { margin-bottom: 16px; opacity: .4; }
        .empty-state p { font-size: 14px; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <svg width="20" height="26" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div>
            <div class="sidebar-brand-name">La Parrilla</div>
            <div class="sidebar-brand-sub">Administrador</div>
        </div>
    </div>
    <nav class="sidebar-nav">
        <p class="nav-label">Principal</p>
        <a class="nav-item" href="admin.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <p class="nav-label">Gestión</p>
        <a class="nav-item" href="admin.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Usuarios
        </a>
        <a class="nav-item active" href="productos.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            Productos
        </a>
        <a class="nav-item" href="pedidos.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="2"/></svg>
            Pedidos
        </a>
        <a class="nav-item" href="mesas.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
            Mesas
        </a>
        <a class="nav-item" href="ventas.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Ventas e Ingresos
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar"><?= strtoupper(substr($usuario['nombre'], 0, 1)) ?></div>
            <div>
                <div class="user-name"><?= htmlspecialchars($usuario['nombre']) ?></div>
                <div class="user-role">Administrador</div>
            </div>
        </div>
        <a href="../../Controllers/AuthController.php?accion=logout" class="btn-logout" style="width:100%;margin-top:8px;justify-content:center;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <div class="topbar">
        <span class="topbar-title">Gestión de Productos</span>
        <span style="font-size:13px;color:var(--muted)">Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?></span>
    </div>
    <div class="content">

        <?php if ($alert): 
            $cls = $alert['icon'] === 'success' ? 'alert-success' : ($alert['icon'] === 'warning' ? 'alert-warning' : 'alert-error');
        ?>
            <div class="alert-box <?= $cls ?>"><?= htmlspecialchars($alert['text']) ?></div>
        <?php endif; ?>

        <div class="toolbar">
            <div class="toolbar-left">
                <button class="btn-orange" onclick="abrirModalCrear()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Añadir Producto
                </button>
            </div>
            <input type="text" class="search-input" id="searchInput" placeholder="Buscar producto..." onkeyup="filtrarTabla()">
        </div>

        <div class="section-card">
            <?php if (empty($productos)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    <p>No hay productos registrados aún</p>
                </div>
            <?php else: ?>
            <table id="tablaProductos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Popular</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $p): ?>
                <tr>
                    <td>
                        <strong><?= htmlspecialchars($p['nombre']) ?></strong>
                        <?php if (!empty($p['descripcion'])): ?>
                            <div style="font-size:12px;color:var(--muted);margin-top:2px"><?= htmlspecialchars(substr($p['descripcion'], 0, 60)) ?>...</div>
                        <?php endif; ?>
                    </td>
                    <td><span class="categoria-tag"><?= htmlspecialchars($p['categoria_nombre']) ?></span></td>
                    <td><span class="precio">$<?= number_format($p['precio'], 0, ',', '.') ?></span></td>
                    <td>
                        <?php if ($p['popular']): ?>
                            <span class="badge badge-popular">⭐ Popular</span>
                        <?php else: ?>
                            <span style="color:var(--muted);font-size:12px">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge <?= $p['disponible'] ? 'badge-disponible' : 'badge-agotado' ?>">
                            <?= $p['disponible'] ? 'Disponible' : 'Agotado' ?>
                        </span>
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn-edit" onclick="abrirModalEditar(
                                <?= $p['id'] ?>,
                                '<?= htmlspecialchars(addslashes($p['nombre'])) ?>',
                                '<?= htmlspecialchars(addslashes($p['descripcion'] ?? '')) ?>',
                                <?= $p['precio'] ?>,
                                <?= $p['categoria_id'] ?>,
                                <?= $p['popular'] ?>,
                                <?= $p['disponible'] ?>,
                                '<?= htmlspecialchars(addslashes($p['imagen_url'] ?? '')) ?>'
                            )">Editar</button>
                            <a class="btn-toggle" href="../../Controllers/ProductoController.php?accion=toggleDisponible&id=<?= $p['id'] ?>&estado=<?= $p['disponible'] ?>">
                                <?= $p['disponible'] ? 'Agotar' : 'Activar' ?>
                            </a>
                            <a class="btn-delete" href="../../Controllers/ProductoController.php?accion=eliminar&id=<?= $p['id'] ?>"
                               onclick="return confirm('¿Eliminar este producto? Esta acción no se puede deshacer.')">
                                Eliminar
                            </a>
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

<!-- MODAL CREAR -->
<div class="modal-overlay" id="modalCrear">
    <div class="modal">
        <h3>Nuevo producto</h3>
        <form method="POST" action="../../Controllers/ProductoController.php?accion=crear">
            <div class="field">
                <label>Nombre del producto *</label>
                <input type="text" name="nombre" placeholder="Ej: Pollo asado entero" required>
            </div>
            <div class="field">
                <label>Descripción</label>
                <textarea name="descripcion" placeholder="Descripción del producto..."></textarea>
            </div>
            <div class="field">
                <label>URL de imagen <span style="color:var(--muted)">(opcional)</span></label>
                <input type="url" name="imagen_url" placeholder="https://...">
            </div>
            <div class="field-row">
                <div class="field">
                    <label>Precio *</label>
                    <input type="number" name="precio" placeholder="42000" min="0" step="100" required>
                </div>
                <div class="field">
                    <label>Categoría *</label>
                    <select name="categoria_id" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field" style="display:flex;gap:24px">
                <label class="field-check">
                    <input type="checkbox" name="popular" value="1"> Popular
                </label>
                <label class="field-check">
                    <input type="checkbox" name="disponible" value="1" checked> Disponible
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModales()">Cancelar</button>
                <button type="submit" class="btn-orange">Crear producto</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal">
        <h3>Editar producto</h3>
        <form method="POST" action="../../Controllers/ProductoController.php?accion=editar">
            <input type="hidden" name="id" id="edit_id">
            <div class="field">
                <label>Nombre del producto *</label>
                <input type="text" name="nombre" id="edit_nombre" required>
            </div>
            <div class="field">
                <label>Descripción</label>
                <textarea name="descripcion" id="edit_descripcion"></textarea>
            </div>
            <div class="field">
                <label>URL de imagen <span style="color:var(--muted)">(opcional)</span></label>
                <input type="url" name="imagen_url" id="edit_imagen_url" placeholder="https://...">
            </div>
            <div class="field-row">
                <div class="field">
                    <label>Precio *</label>
                    <input type="number" name="precio" id="edit_precio" min="0" step="100" required>
                </div>
                <div class="field">
                    <label>Categoría *</label>
                    <select name="categoria_id" id="edit_categoria" required>
                        <option value="">Seleccionar...</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="field" style="display:flex;gap:24px">
                <label class="field-check">
                    <input type="checkbox" name="popular" id="edit_popular" value="1"> Popular
                </label>
                <label class="field-check">
                    <input type="checkbox" name="disponible" id="edit_disponible" value="1"> Disponible
                </label>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="cerrarModales()">Cancelar</button>
                <button type="submit" class="btn-orange">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModalCrear() {
    document.getElementById('modalCrear').classList.add('open');
}
function abrirModalEditar(id, nombre, descripcion, precio, categoria_id, popular, disponible, imagen_url) {
    document.getElementById('edit_id').value           = id;
    document.getElementById('edit_nombre').value       = nombre;
    document.getElementById('edit_descripcion').value  = descripcion;
    document.getElementById('edit_precio').value       = precio;
    document.getElementById('edit_categoria').value    = categoria_id;
    document.getElementById('edit_popular').checked    = popular == 1;
    document.getElementById('edit_disponible').checked = disponible == 1;
    document.getElementById('edit_imagen_url').value   = imagen_url || '';
    document.getElementById('modalEditar').classList.add('open');
}
function cerrarModales() {
    document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('open'));
}
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) cerrarModales(); });
});
function filtrarTabla() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#tablaProductos tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>
