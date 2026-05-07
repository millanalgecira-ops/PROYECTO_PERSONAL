<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header('Location: ../usuarios/login.php');
    exit;
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Panel Administrador</title>
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
        .sidebar {
            width: 240px; min-height: 100vh; background: var(--dark);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            padding: 28px 0; flex-shrink: 0;
        }
        .sidebar-brand {
            display: flex; align-items: center; gap: 10px;
            padding: 0 24px 28px; border-bottom: 1px solid var(--border);
        }
        .sidebar-brand-name { font-family: 'Bebas Neue', sans-serif; font-size: 20px; }
        .sidebar-brand-sub  { font-size: 9px; letter-spacing: 3px; text-transform: uppercase; color: var(--orange); }
        .sidebar-nav { flex: 1; padding: 20px 12px; }
        .nav-label { font-size: 10px; letter-spacing: 3px; text-transform: uppercase; color: var(--muted); padding: 0 12px; margin: 18px 0 8px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 8px;
            font-size: 14px; color: var(--label); text-decoration: none;
            transition: background .2s, color .2s; cursor: pointer; border: none; background: none; width: 100%; text-align: left;
        }
        .nav-item:hover, .nav-item.active { background: rgba(240,112,0,.12); color: var(--orange); }
        .nav-item svg { flex-shrink: 0; }
        .sidebar-footer { padding: 20px 12px 0; border-top: 1px solid var(--border); }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 10px 12px; }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: rgba(240,112,0,.2); border: 1px solid rgba(240,112,0,.4);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: var(--orange); flex-shrink: 0;
        }
        .user-name  { font-size: 13px; font-weight: 600; }
        .user-role  { font-size: 11px; color: var(--orange); }

        /* MAIN */
        .main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .topbar {
            height: 60px; background: var(--dark); border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 32px; flex-shrink: 0;
        }
        .topbar-title { font-family: 'Bebas Neue', sans-serif; font-size: 22px; letter-spacing: 1px; }
        .btn-logout {
            display: flex; align-items: center; gap: 6px;
            background: none; border: 1px solid var(--border); border-radius: 8px;
            padding: 8px 14px; color: var(--muted); font-size: 13px;
            font-family: 'Barlow', sans-serif; cursor: pointer;
            transition: border-color .2s, color .2s; text-decoration: none;
        }
        .btn-logout:hover { border-color: var(--orange); color: var(--orange); }

        .content { flex: 1; padding: 32px; overflow-y: auto; }

        /* STATS */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 12px; padding: 22px 20px;
        }
        .stat-label { font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; }
        .stat-value { font-family: 'Bebas Neue', sans-serif; font-size: 36px; color: var(--text); line-height: 1; }
        .stat-sub   { font-size: 12px; color: var(--orange); margin-top: 4px; }

        /* TABLE */
        .section-card { background: var(--card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
        .section-head {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px; border-bottom: 1px solid var(--border);
        }
        .section-head h2 { font-size: 16px; font-weight: 600; }
        .btn-orange {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--orange); color: #fff; border: none; border-radius: 8px;
            padding: 9px 16px; font-size: 13px; font-family: 'Barlow', sans-serif;
            font-weight: 600; cursor: pointer; text-decoration: none;
            transition: background .2s;
        }
        .btn-orange:hover { background: var(--orange2); }
        table { width: 100%; border-collapse: collapse; }
        th { padding: 12px 20px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); border-bottom: 1px solid var(--border); }
        td { padding: 14px 20px; font-size: 14px; border-bottom: 1px solid rgba(46,43,39,.5); }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background: rgba(255,255,255,.02); }
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .badge-admin    { background: rgba(240,112,0,.15); color: var(--orange); }
        .badge-cocina   { background: rgba(100,180,255,.12); color: #64b4ff; }
        .badge-cliente  { background: rgba(100,220,130,.12); color: #64dc82; }
        .badge-activo   { background: rgba(100,220,130,.12); color: #64dc82; }
        .badge-inactivo { background: rgba(255,80,80,.12);   color: #ff5050; }

        /* MODAL */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 200;
            background: rgba(0,0,0,.7); align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--dark); border: 1px solid var(--border);
            border-radius: 14px; padding: 32px; width: 100%; max-width: 460px;
        }
        .modal h3 { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; color: var(--label); margin-bottom: 6px; }
        .field input, .field select {
            width: 100%; background: #1a1a1a; border: 1px solid var(--border);
            border-radius: 8px; padding: 11px 14px; font-size: 14px;
            font-family: 'Barlow', sans-serif; color: var(--text); outline: none;
            transition: border-color .2s;
        }
        .field input:focus, .field select:focus { border-color: var(--orange); }
        .field select option { background: #1a1a1a; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }
        .btn-cancel {
            background: none; border: 1px solid var(--border); border-radius: 8px;
            padding: 10px 18px; color: var(--muted); font-size: 14px;
            font-family: 'Barlow', sans-serif; cursor: pointer;
            transition: border-color .2s, color .2s;
        }
        .btn-cancel:hover { border-color: var(--orange); color: var(--orange); }

        .alert-box {
            padding: 10px 16px; border-radius: 8px; font-size: 13px; margin-bottom: 20px;
        }
        .alert-success { background: rgba(0,200,100,.1); border: 1px solid #00c864; color: #00c864; }
        .alert-error   { background: rgba(240,112,0,.12); border: 1px solid var(--orange); color: var(--orange); }
        .alert-warning { background: rgba(255,200,0,.1);  border: 1px solid #ffc800; color: #ffc800; }
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
        <a class="nav-item active" href="admin.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <p class="nav-label">Gestión</p>
        <a class="nav-item active" href="admin.php">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Usuarios
        </a>
        <a class="nav-item" href="productos.php">
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
        <span class="topbar-title">Panel de Administración</span>
        <span style="font-size:13px;color:var(--muted)">Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?></span>
    </div>

    <div class="content">

        <?php
        $alert = $_SESSION['alert'] ?? null;
        unset($_SESSION['alert']);
        if ($alert):
            $cls = $alert['icon'] === 'success' ? 'alert-success' : ($alert['icon'] === 'warning' ? 'alert-warning' : 'alert-error');
        ?>
            <div class="alert-box <?= $cls ?>"><?= htmlspecialchars($alert['text']) ?></div>
        <?php endif; ?>

        <?php
        require_once __DIR__ . '/../../Config/database.php';
        require_once __DIR__ . '/../../Models/usuario.php';
        $db = (new Database())->conectar();
        $usuarioModel = new Usuario($db);
        $usuarios = $usuarioModel->obtenerTodos();
        $total = count($usuarios);
        $activos   = count(array_filter($usuarios, fn($u) => $u['activo'] == 1));
        $inactivos = $total - $activos;
        ?>

        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total usuarios</div>
                <div class="stat-value"><?= $total ?></div>
                <div class="stat-sub">Registrados en el sistema</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Activos</div>
                <div class="stat-value"><?= $activos ?></div>
                <div class="stat-sub">Con acceso habilitado</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Inactivos</div>
                <div class="stat-value"><?= $inactivos ?></div>
                <div class="stat-sub">Sin acceso al sistema</div>
            </div>
        </div>

        <!-- TABLA USUARIOS -->
        <div class="section-card">
            <div class="section-head">
                <h2>Usuarios del sistema</h2>
                <button class="btn-orange" onclick="document.getElementById('modalCrear').classList.add('open')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo usuario
                </button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($usuarios as $u):
                    $roles_map = [1 => 'Administrador', 2 => 'Cocina', 3 => 'Cliente'];
                    $rol_nombre = $roles_map[$u['rol_id']] ?? $u['rol_nombre'] ?? 'desconocido';
                    $badge_rol  = 'badge-' . strtolower($roles_map[$u['rol_id']] ?? 'admin');
                ?>
                <tr>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td style="color:var(--muted)"><?= htmlspecialchars($u['correo']) ?></td>
                    <td><span class="badge <?= $badge_rol ?>"><?= ucfirst($rol_nombre) ?></span></td>
                    <td>
                        <span class="badge <?= $u['activo'] ? 'badge-activo' : 'badge-inactivo' ?>">
                            <?= $u['activo'] ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </td>
                    <td style="display:flex;gap:8px">
                        <button class="btn-cancel" style="padding:6px 12px;font-size:12px"
                            onclick="abrirEditar(<?= $u['id'] ?>, '<?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?>', '<?= htmlspecialchars(implode(' ', array_slice(explode(' ', $u['nombre']), 1))) ?>', '<?= htmlspecialchars($u['correo']) ?>', <?= $u['rol_id'] ?>)">
                            Editar
                        </button>
                        <a href="../../Controllers/AdminUsuarioController.php?accion=toggleEstado&id=<?= $u['id'] ?>&estado=<?= $u['activo'] ?>"
                           class="btn-cancel" style="padding:6px 12px;font-size:12px;text-decoration:none">
                            <?= $u['activo'] ? 'Desactivar' : 'Activar' ?>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CREAR -->
<div class="modal-overlay" id="modalCrear">
    <div class="modal">
        <h3>Nuevo usuario</h3>
        <form method="POST" action="../../Controllers/AdminUsuarioController.php?accion=crear">
            <div class="field"><label>Nombres</label><input type="text" name="nombres" required></div>
            <div class="field"><label>Apellidos</label><input type="text" name="apellidos" required></div>
            <div class="field"><label>Correo</label><input type="email" name="email" required></div>
            <div class="field"><label>Teléfono</label><input type="tel" name="telefono"></div>
            <div class="field"><label>Contraseña</label><input type="password" name="password" required></div>
            <div class="field">
                <label>Rol</label>
                <select name="rol">
                    <option value="administrador">Administrador</option>
                    <option value="cocina" selected>Cocina</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="document.getElementById('modalCrear').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn-orange">Crear usuario</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDITAR -->
<div class="modal-overlay" id="modalEditar">
    <div class="modal">
        <h3>Editar usuario</h3>
        <form method="POST" action="../../Controllers/AdminUsuarioController.php?accion=editar">
            <input type="hidden" name="id_usuario" id="edit_id">
            <div class="field"><label>Nombres</label><input type="text" name="nombres" id="edit_nombres" required></div>
            <div class="field"><label>Apellidos</label><input type="text" name="apellidos" id="edit_apellidos" required></div>
            <div class="field"><label>Correo</label><input type="email" name="email" id="edit_email" required></div>
            <div class="field"><label>Nueva contraseña <span style="color:var(--muted)">(dejar vacío para no cambiar)</span></label><input type="password" name="password"></div>
            <div class="field">
                <label>Rol</label>
                <select name="rol" id="edit_rol">
                    <option value="administrador">Administrador</option>
                    <option value="cocina">Cocina</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="document.getElementById('modalEditar').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn-orange">Guardar cambios</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirEditar(id, nombres, apellidos, email, rol_id) {
    const roles = {1:'administrador', 2:'cocina', 3:'cliente'};
    document.getElementById('edit_id').value       = id;
    document.getElementById('edit_nombres').value  = nombres;
    document.getElementById('edit_apellidos').value = apellidos;
    document.getElementById('edit_email').value    = email;
    document.getElementById('edit_rol').value      = roles[rol_id] || 'cocina';
    document.getElementById('modalEditar').classList.add('open');
}
// Cerrar modal al click fuera
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', e => { if (e.target === overlay) overlay.classList.remove('open'); });
});
</script>
</body>
</html>
