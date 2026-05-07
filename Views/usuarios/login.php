<?php
session_start();

// Si ya está logueado, redirigir al dashboard
if (isset($_SESSION['usuario'])) {
    $rol = $_SESSION['usuario']['rol'] ?? '';
    switch ($rol) {
        case 'administrador': header('Location: ../dashboard/admin.php'); break;
        case 'cocina':        header('Location: ../dashboard/cocina.php'); break;
        case 'cliente':       header('Location: ../dashboard/cliente.php'); break;
        default:              header('Location: login.php'); break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Acceso al sistema</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:ital,wght@0,300;0,400;0,600;1,400&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-dark:    #1a1a1a;
            --bg-panel:   #222222;
            --bg-input:   #2a2a2a;
            --border:     #333333;
            --orange:     #f07000;
            --orange-dim: #c05500;
            --text:       #e8e8e8;
            --text-muted: #888888;
            --text-label: #cccccc;
        }

        html, body {
            height: 100%;
            font-family: 'Barlow', sans-serif;
            background: var(--bg-dark);
            color: var(--text);
        }

        .layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* ─── PANEL IZQUIERDO ─── */
        .branding {
            background: var(--bg-dark);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 36px 48px;
        }

        .flame-icon {
            width: 32px;
            height: 42px;
        }

        .brand-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .sistema-label {
            font-size: 11px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--text-muted);
            margin-bottom: 8px;
        }

        .brand-name-la {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 72px;
            line-height: 1;
            color: var(--text);
            display: block;
        }

        .brand-name-parrilla {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 72px;
            line-height: 1;
            color: var(--orange);
            display: block;
        }

        .brand-subtitle {
            font-size: 11px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: var(--orange);
            margin-top: 6px;
            margin-bottom: 28px;
        }

        .brand-desc {
            font-size: 14px;
            font-weight: 300;
            color: var(--text-muted);
            line-height: 1.7;
            max-width: 260px;
        }

        .footer {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ─── PANEL DERECHO ─── */
        .form-panel {
            background: var(--bg-panel);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 64px;
        }

        .form-box {
            width: 100%;
            max-width: 420px;
        }

        .form-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 38px;
            font-weight: 400;
            letter-spacing: 1px;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 36px;
        }

        .field { margin-bottom: 22px; }

        .field label {
            display: block;
            font-size: 13px;
            color: var(--text-label);
            margin-bottom: 8px;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap input {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 13px 48px 13px 16px;
            font-size: 14px;
            font-family: 'Barlow', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color .2s;
        }

        .input-wrap input::placeholder { color: var(--text-muted); }
        .input-wrap input:focus { border-color: var(--orange); }

        .input-icon {
            position: absolute;
            right: 14px;
            color: var(--orange);
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        /* Para contraseña: ojo + candado */
        .pass-icons {
            position: absolute;
            right: 12px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .pass-icons button {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            transition: color .15s;
        }
        .pass-icons button:hover { color: var(--orange); }

        .pass-icons .lock-icon { color: var(--orange); pointer-events: none; }

        .input-wrap input.has-pass-icons { padding-right: 72px; }

        .error-msg {
            background: rgba(240,112,0,.12);
            border: 1px solid var(--orange);
            color: var(--orange);
            border-radius: 8px;
            padding: 10px 16px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .btn-primary {
            width: 100%;
            background: transparent;
            border: 2px solid var(--orange);
            border-radius: 8px;
            padding: 15px;
            font-size: 16px;
            font-family: 'Barlow', sans-serif;
            font-weight: 600;
            color: var(--orange);
            cursor: pointer;
            margin-top: 10px;
            letter-spacing: .5px;
            transition: background .2s, color .2s;
        }
        .btn-primary:hover {
            background: var(--orange);
            color: #fff;
        }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 28px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 7px 16px;
            font-size: 13px;
            font-family: 'Barlow', sans-serif;
            color: var(--text-muted);
            cursor: pointer;
            transition: border-color .2s, color .2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn-ghost:hover { border-color: var(--orange); color: var(--orange); }

        @media (max-width: 700px) {
            .layout { grid-template-columns: 1fr; }
            .branding { display: none; }
            .form-panel { padding: 40px 24px; }
        }
    </style>
</head>
<body>
<div class="layout">

    <!-- ─── IZQUIERDA ─── -->
    <div class="branding">
        <svg class="flame-icon" viewBox="0 0 32 42" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/>
        </svg>

        <div class="brand-body">
            <p class="sistema-label">Sistema de Gestión</p>
            <span class="brand-name-la">La</span>
            <span class="brand-name-parrilla">Parrilla</span>
            <p class="brand-subtitle">Asadero &amp; Restaurante</p>
            <p class="brand-desc">Plataforma de administración interna para el equipo de La Parrilla. Acceso exclusivo para personal autorizado.</p>
        </div>

        <p class="footer">© 2025 La Parrilla</p>
    </div>

    <!-- ─── DERECHA ─── -->
    <div class="form-panel">
        <div class="form-box">
            <h1 class="form-title">Acceso al sistema</h1>
            <p class="form-subtitle">Ingresa tus credenciales para continuar</p>

            <?php
            $alert = $_SESSION['alert'] ?? null;
            unset($_SESSION['alert']);
            if ($alert): ?>
                <div class="error-msg"><?= htmlspecialchars($alert['text']) ?></div>
            <?php endif; ?>

            <form method="POST" action="../../Controllers/AuthController.php">
                <div class="field">
                    <label for="email">Correo electrónico</label>
                    <div class="input-wrap">
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="correo@ejemplo.com"
                            value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            autocomplete="username"
                        >
                        <span class="input-icon">
                            <!-- Person icon -->
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="field">
                    <label for="password">Contraseña</label>
                    <div class="input-wrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            class="has-pass-icons"
                            autocomplete="current-password"
                        >
                        <div class="pass-icons">
                            <button type="button" onclick="togglePass('password', this)" title="Mostrar contraseña">
                                <!-- Eye icon -->
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <span class="lock-icon">
                                <!-- Lock icon -->
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">Ingresar</button>
            </form>

            <div style="text-align:center;margin-top:16px">
                <a href="recuperar.php" style="font-size:13px;color:var(--text-muted);text-decoration:none;transition:color .2s" onmouseover="this.style.color='var(--orange)'" onmouseout="this.style.color='var(--text-muted)'">¿Olvidaste tu contraseña?</a>
            </div>

            <div class="form-footer">
                <span>¿No tienes cuenta?</span>
                <a href="registre.php" class="btn-ghost">Regístrate aquí</a>
            </div>
        </div>
    </div>

</div>

<script>
function togglePass(id, btn) {
    const input = document.getElementById(id);
    const isText = input.type === 'text';
    input.type = isText ? 'password' : 'text';
    btn.style.color = isText ? '' : 'var(--orange)';
}
</script>
</body>
</html>