<?php
session_start();
require_once __DIR__ . '/../../Config/database.php';

$mensaje = null;
$tipo    = null;
$paso    = $_GET['paso'] ?? 'solicitar'; // solicitar | restablecer
$token   = $_GET['token'] ?? null;

$db = (new Database())->conectar();

// ── PASO 2: Validar token y mostrar formulario de nueva contraseña ──
if ($paso === 'restablecer' && $token) {
    $stmt = $db->prepare("SELECT * FROM tokens_recuperacion WHERE token = :token AND usado = 0 AND expira_en > NOW() LIMIT 1");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tokenData) {
        $mensaje = 'Este enlace ha expirado o ya fue usado.';
        $tipo    = 'error';
        $paso    = 'expirado';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $tokenData) {
        $nueva    = $_POST['password']  ?? '';
        $confirma = $_POST['confirmar'] ?? '';

        if (strlen($nueva) < 6) {
            $mensaje = 'La contraseña debe tener al menos 6 caracteres.';
            $tipo    = 'error';
        } elseif ($nueva !== $confirma) {
            $mensaje = 'Las contraseñas no coinciden.';
            $tipo    = 'error';
        } else {
            $hash = password_hash($nueva, PASSWORD_DEFAULT);

            if ($tokenData['cliente_id']) {
                $db->prepare("UPDATE clientes SET password_hash = :hash WHERE id = :id")
                   ->execute([':hash' => $hash, ':id' => $tokenData['cliente_id']]);
            } else {
                $db->prepare("UPDATE usuarios SET password_hash = :hash WHERE id = :id")
                   ->execute([':hash' => $hash, ':id' => $tokenData['usuario_id']]);
            }

            $db->prepare("UPDATE tokens_recuperacion SET usado = 1 WHERE token = :token")
               ->execute([':token' => $token]);

            $mensaje = 'Contraseña actualizada exitosamente. Ya puedes iniciar sesión.';
            $tipo    = 'success';
            $paso    = 'completado';
        }
    }
}

// ── PASO 1: Solicitar recuperación ──
if ($paso === 'solicitar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje = 'Ingresa un correo electrónico válido.';
        $tipo    = 'error';
    } else {
        // Buscar en clientes
        $stmt = $db->prepare("SELECT id, 'cliente' AS tipo FROM clientes WHERE correo = :correo AND activo = 1 LIMIT 1");
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si no está en clientes, buscar en usuarios
        if (!$cuenta) {
            $stmt = $db->prepare("SELECT id, 'usuario' AS tipo FROM usuarios WHERE correo = :correo AND activo = 1 LIMIT 1");
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if (!$cuenta) {
            $mensaje = 'Este correo no está registrado. Verifica los datos o crea una cuenta nueva.';
            $tipo    = 'error';
        } else {
            // Generar token único
            $tokenStr  = bin2hex(random_bytes(32));
            $expira_en = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $cliente_id = $cuenta['tipo'] === 'cliente' ? $cuenta['id'] : null;
            $usuario_id = $cuenta['tipo'] === 'usuario' ? $cuenta['id'] : null;

            $db->prepare("INSERT INTO tokens_recuperacion (cliente_id, usuario_id, token, expira_en) VALUES (:cid, :uid, :token, :expira)")
               ->execute([':cid' => $cliente_id, ':uid' => $usuario_id, ':token' => $tokenStr, ':expira' => $expira_en]);

            // En producción se enviaría por email. Aquí mostramos el link directamente.
            $link = "http://127.0.0.1:8081/proyecto_personal/Views/usuarios/recuperar.php?paso=restablecer&token={$tokenStr}";
            $mensaje = "Enlace de restablecimiento generado. <a href='{$link}' style='color:var(--orange)'>Haz clic aquí para restablecer tu contraseña</a> (válido por 1 hora).";
            $tipo    = 'success';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Recuperar contraseña</title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{--bg-dark:#1a1a1a;--bg-panel:#222;--bg-input:#2a2a2a;--border:#333;--orange:#f07000;--text:#e8e8e8;--text-muted:#888;--text-label:#ccc}
        html,body{height:100%;font-family:'Barlow',sans-serif;background:var(--bg-dark);color:var(--text)}
        .layout{display:grid;grid-template-columns:1fr 1fr;min-height:100vh}
        .branding{background:var(--bg-dark);display:flex;flex-direction:column;justify-content:space-between;padding:36px 48px}
        .brand-body{flex:1;display:flex;flex-direction:column;justify-content:center}
        .sistema-label{font-size:11px;letter-spacing:5px;text-transform:uppercase;color:var(--text-muted);margin-bottom:8px}
        .brand-name-la{font-family:'Bebas Neue',sans-serif;font-size:72px;line-height:1;color:var(--text);display:block}
        .brand-name-parrilla{font-family:'Bebas Neue',sans-serif;font-size:72px;line-height:1;color:var(--orange);display:block}
        .brand-subtitle{font-size:11px;letter-spacing:5px;text-transform:uppercase;color:var(--orange);margin-top:6px;margin-bottom:28px}
        .brand-desc{font-size:14px;font-weight:300;color:var(--text-muted);line-height:1.7;max-width:280px}
        .footer{font-size:12px;color:var(--text-muted)}
        .form-panel{background:var(--bg-panel);display:flex;align-items:center;justify-content:center;padding:60px 64px}
        .form-box{width:100%;max-width:420px}
        .form-title{font-family:'Bebas Neue',sans-serif;font-size:34px;color:var(--text);margin-bottom:6px}
        .form-subtitle{font-size:14px;color:var(--text-muted);margin-bottom:28px}
        .field{margin-bottom:20px}
        .field label{display:block;font-size:13px;color:var(--text-label);margin-bottom:7px}
        .field input{width:100%;background:var(--bg-input);border:1px solid var(--border);border-radius:8px;padding:13px 16px;font-size:14px;font-family:'Barlow',sans-serif;color:var(--text);outline:none;transition:border-color .2s}
        .field input:focus{border-color:var(--orange)}
        .field input::placeholder{color:var(--text-muted)}
        .btn-primary{width:100%;background:transparent;border:2px solid var(--orange);border-radius:8px;padding:14px;font-size:15px;font-family:'Barlow',sans-serif;font-weight:600;color:var(--orange);cursor:pointer;margin-top:6px;transition:background .2s,color .2s}
        .btn-primary:hover{background:var(--orange);color:#fff}
        .alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:20px;line-height:1.6}
        .alert-error  {background:rgba(240,112,0,.12);border:1px solid var(--orange);color:var(--orange)}
        .alert-success{background:rgba(0,200,100,.1);border:1px solid #00c864;color:#00c864}
        .btn-ghost{background:transparent;border:1px solid var(--border);border-radius:6px;padding:7px 16px;font-size:13px;font-family:'Barlow',sans-serif;color:var(--text-muted);cursor:pointer;transition:border-color .2s,color .2s;text-decoration:none;display:inline-block}
        .btn-ghost:hover{border-color:var(--orange);color:var(--orange)}
        .form-footer{display:flex;align-items:center;justify-content:center;gap:10px;margin-top:24px;font-size:13px;color:var(--text-muted)}
        @media(max-width:700px){.layout{grid-template-columns:1fr}.branding{display:none}.form-panel{padding:40px 24px}}
    </style>
</head>
<body>
<div class="layout">
    <div class="branding">
        <svg width="32" height="42" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div class="brand-body">
            <p class="sistema-label">Sistema de Gestión</p>
            <span class="brand-name-la">La</span>
            <span class="brand-name-parrilla">Parrilla</span>
            <p class="brand-subtitle">Asadero &amp; Restaurante</p>
            <p class="brand-desc">Recupera el acceso a tu cuenta de forma segura.</p>
        </div>
        <p class="footer">© 2026 La Parrilla</p>
    </div>

    <div class="form-panel">
        <div class="form-box">

            <?php if ($paso === 'completado'): ?>
                <h1 class="form-title">¡Listo!</h1>
                <p class="form-subtitle">Tu contraseña fue actualizada</p>
                <div class="alert alert-success"><?= $mensaje ?></div>
                <div class="form-footer">
                    <a href="login.php" class="btn-ghost">Ir al login</a>
                </div>

            <?php elseif ($paso === 'expirado'): ?>
                <h1 class="form-title">Enlace expirado</h1>
                <p class="form-subtitle">El enlace ya no es válido</p>
                <div class="alert alert-error"><?= htmlspecialchars($mensaje) ?></div>
                <div class="form-footer">
                    <span>Solicita uno nuevo:</span>
                    <a href="recuperar.php" class="btn-ghost">Recuperar contraseña</a>
                </div>

            <?php elseif ($paso === 'restablecer' && $tokenData): ?>
                <h1 class="form-title">Nueva contraseña</h1>
                <p class="form-subtitle">Ingresa tu nueva contraseña</p>
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="field">
                        <label>Nueva contraseña</label>
                        <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="field">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="confirmar" placeholder="Repite tu contraseña" required>
                    </div>
                    <button type="submit" class="btn-primary">Guardar nueva contraseña</button>
                </form>

            <?php else: ?>
                <h1 class="form-title">Recuperar contraseña</h1>
                <p class="form-subtitle">Ingresa tu correo para continuar</p>
                <?php if ($mensaje): ?>
                    <div class="alert alert-<?= $tipo ?>"><?= $mensaje ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="field">
                        <label>Correo electrónico</label>
                        <input type="email" name="correo" placeholder="correo@ejemplo.com"
                               value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>
                    </div>
                    <button type="submit" class="btn-primary">Enviar enlace</button>
                </form>
                <div class="form-footer">
                    <span>¿Recordaste tu contraseña?</span>
                    <a href="login.php" class="btn-ghost">Iniciar sesión</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>
</body>
</html>
