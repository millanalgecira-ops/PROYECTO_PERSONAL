<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Asadero & Restaurante</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --black:   #0e0e0e;
            --dark:    #141414;
            --card:    #1c1a18;
            --card2:   #221f1b;
            --border:  #2e2b27;
            --orange:  #f07000;
            --orange2: #e06500;
            --text:    #f0ece6;
            --muted:   #8a8078;
            --label:   #c8bfb0;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Barlow', sans-serif;
            background: var(--black);
            color: var(--text);
            overflow-x: hidden;
        }

        /* ═══════════════════════ NAV ═══════════════════════ */
        nav {
            position: fixed; top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(14,14,14,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 40px;
            height: 60px;
        }

        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            text-decoration: none;
        }

        .nav-brand-text { line-height: 1; }
        .nav-brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 22px; letter-spacing: 1px; color: var(--text);
        }
        .nav-brand-sub {
            font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
            color: var(--orange);
        }

        .nav-links { display: flex; gap: 32px; list-style: none; }
        .nav-links a {
            font-size: 14px; font-weight: 500; color: var(--label);
            text-decoration: none; transition: color .2s;
        }
        .nav-links a:hover { color: var(--text); }

        .nav-right { display: flex; align-items: center; gap: 14px; }

        .btn-cart {
            position: relative;
            background: none; border: 1px solid var(--border);
            border-radius: 8px; width: 38px; height: 38px;
            display: flex; align-items: center; justify-content: center;
            color: var(--label); cursor: pointer; text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .btn-cart:hover { border-color: var(--orange); color: var(--orange); }
        .cart-badge {
            position: absolute; top: -6px; right: -6px;
            background: var(--orange); color: #fff;
            font-size: 10px; font-weight: 700;
            width: 18px; height: 18px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            display: none;
        }

        .btn-nav-login {
            display: flex; align-items: center; gap: 8px;
            background: none; border: 1px solid var(--border);
            border-radius: 8px; padding: 8px 16px;
            color: var(--label); font-size: 13px; font-family: 'Barlow', sans-serif;
            cursor: pointer; text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .btn-nav-login:hover { border-color: var(--orange); color: var(--orange); }

        /* ═══════════════════════ HERO ═══════════════════════ */
        #inicio {
            position: relative;
            min-height: 100vh;
            display: flex; align-items: center;
            padding: 80px 40px 60px;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute; inset: 0;
            background:
                linear-gradient(to right, rgba(14,14,14,.96) 40%, rgba(14,14,14,.55) 100%),
                url('https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=1400&q=80') center/cover no-repeat;
        }

        .hero-content {
            position: relative; z-index: 1;
            max-width: 560px;
        }

        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(240,112,0,.15);
            border: 1px solid rgba(240,112,0,.35);
            border-radius: 20px;
            padding: 6px 16px;
            font-size: 13px; color: var(--orange);
            margin-bottom: 24px;
        }
        .hero-badge-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--orange);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100%{ opacity:1; transform:scale(1); }
            50%    { opacity:.5; transform:scale(1.4); }
        }

        .hero-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(52px, 7vw, 84px);
            line-height: 1.0;
            color: var(--text);
            margin-bottom: 16px;
        }
        .hero-title span { color: var(--orange); }

        .hero-desc {
            font-size: 15px; font-weight: 300;
            color: var(--label); line-height: 1.75;
            margin-bottom: 36px; max-width: 400px;
        }

        .hero-actions { display: flex; gap: 14px; flex-wrap: wrap; margin-bottom: 52px; }

        .btn-orange {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--orange); color: #fff;
            border: none; border-radius: 8px;
            padding: 14px 26px; font-size: 15px; font-family: 'Barlow', sans-serif;
            font-weight: 600; cursor: pointer; text-decoration: none;
            transition: background .2s, transform .15s;
        }
        .btn-orange:hover { background: var(--orange2); transform: translateY(-1px); }

        .btn-outline {
            display: inline-flex; align-items: center; gap: 8px;
            background: transparent; color: var(--text);
            border: 1.5px solid rgba(255,255,255,.2);
            border-radius: 8px; padding: 14px 26px;
            font-size: 15px; font-family: 'Barlow', sans-serif; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: border-color .2s, background .2s;
        }
        .btn-outline:hover { border-color: var(--text); background: rgba(255,255,255,.06); }

        .hero-info { display: flex; gap: 32px; flex-wrap: wrap; }
        .hero-info-item {
            display: flex; align-items: flex-start; gap: 10px;
        }
        .hero-info-icon {
            width: 36px; height: 36px; border-radius: 50%;
            border: 1.5px solid rgba(240,112,0,.35);
            display: flex; align-items: center; justify-content: center;
            color: var(--orange); flex-shrink: 0;
        }
        .hero-info-text strong { display: block; font-size: 13px; font-weight: 600; }
        .hero-info-text span  { font-size: 13px; color: var(--muted); }

        /* ═══════════════════════ MENU SECTION ═══════════════════════ */
        #menu {
            padding: 90px 40px;
            background: var(--dark);
        }

        .section-tag {
            text-align: center; font-size: 12px; letter-spacing: 4px;
            text-transform: uppercase; color: var(--orange);
            margin-bottom: 12px;
        }

        .section-title {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(36px, 5vw, 56px);
            text-align: center; color: var(--text);
            margin-bottom: 10px;
        }

        .section-desc {
            text-align: center; font-size: 14px; color: var(--muted);
            max-width: 480px; margin: 0 auto 52px;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            max-width: 1100px; margin: 0 auto;
        }

        .menu-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            transition: transform .2s, border-color .2s;
        }
        .menu-card:hover { transform: translateY(-4px); border-color: rgba(240,112,0,.3); }

        .menu-card-img {
            position: relative;
            height: 200px; overflow: hidden;
        }
        .menu-card-img img {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform .35s;
        }
        .menu-card:hover .menu-card-img img { transform: scale(1.05); }

        .tag-popular {
            position: absolute; top: 12px; left: 12px;
            background: var(--orange); color: #fff;
            font-size: 11px; font-weight: 700; padding: 3px 10px;
            border-radius: 20px;
        }

        .menu-card-body { padding: 18px 18px 16px; }

        .menu-card-top {
            display: flex; justify-content: space-between; align-items: baseline;
            margin-bottom: 8px;
        }

        .menu-card-name { font-size: 17px; font-weight: 700; }
        .menu-card-price {
            font-size: 16px; font-weight: 700; color: var(--orange);
            white-space: nowrap;
        }

        .menu-card-desc {
            font-size: 13px; color: var(--muted);
            line-height: 1.6; margin-bottom: 14px;
        }

        .btn-add {
            width: 100%;
            background: rgba(240,112,0,.12);
            border: 1px solid rgba(240,112,0,.25);
            border-radius: 8px;
            padding: 10px;
            color: var(--orange); font-size: 14px; font-family: 'Barlow', sans-serif;
            font-weight: 600; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: background .2s, border-color .2s;
        }
        .btn-add:hover { background: var(--orange); color: #fff; border-color: var(--orange); }

        /* ═══════════════════════ PROMOCIONES ═══════════════════════ */
        #promociones {
            padding: 90px 40px;
            background: var(--black);
        }

        .promo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 16px;
            max-width: 1100px; margin: 0 auto 32px;
        }

        .promo-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 28px 24px;
            transition: border-color .2s;
        }
        .promo-card:hover { border-color: rgba(240,112,0,.3); }

        .promo-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(240,112,0,.15);
            display: flex; align-items: center; justify-content: center;
            color: var(--orange); margin-bottom: 20px;
        }

        .promo-card h3 { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .promo-card p  { font-size: 13px; color: var(--muted); line-height: 1.65; margin-bottom: 18px; }

        .link-orange {
            font-size: 13px; font-weight: 600; color: var(--orange);
            text-decoration: none; display: inline-flex; align-items: center; gap: 4px;
        }
        .link-orange:hover { text-decoration: underline; }

        /* Combo banner */
        .combo-banner {
            max-width: 1100px; margin: 0 auto;
            background: linear-gradient(135deg, #2a1a08 0%, #3d2410 100%);
            border: 1px solid rgba(240,112,0,.25);
            border-radius: 16px;
            padding: 36px 40px;
        }

        .combo-tag {
            display: inline-block;
            background: var(--orange); color: #fff;
            font-size: 11px; font-weight: 700;
            padding: 4px 12px; border-radius: 20px;
            margin-bottom: 14px;
        }

        .combo-banner h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 36px; color: var(--text); margin-bottom: 8px;
        }

        .combo-banner p { font-size: 14px; color: var(--label); margin-bottom: 22px; }

        /* ═══════════════════════ NOSOTROS ═══════════════════════ */
        #nosotros {
            padding: 90px 40px;
            background: var(--dark);
            text-align: center;
        }
        #nosotros p {
            max-width: 600px; margin: 0 auto;
            font-size: 15px; color: var(--muted); line-height: 1.8;
        }

        /* ═══════════════════════ CONTACTO ═══════════════════════ */
        #contacto {
            padding: 90px 40px;
            background: var(--black);
            text-align: center;
        }
        .contacto-info { margin-top: 30px; display: flex; justify-content: center; gap: 40px; flex-wrap: wrap; }
        .contacto-item { font-size: 15px; color: var(--label); }
        .contacto-item strong { color: var(--orange); display: block; margin-bottom: 4px; }

        /* ═══════════════════════ FOOTER ═══════════════════════ */
        footer {
            background: var(--dark);
            border-top: 1px solid var(--border);
            padding: 56px 40px 24px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr 1fr 1.4fr;
            gap: 40px;
            max-width: 1100px; margin: 0 auto 40px;
        }

        .footer-brand-name {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 24px; color: var(--text); margin-bottom: 4px;
        }
        .footer-brand-sub {
            font-size: 9px; letter-spacing: 3px; text-transform: uppercase;
            color: var(--orange); margin-bottom: 14px; display: block;
        }
        .footer-brand-desc { font-size: 13px; color: var(--muted); line-height: 1.75; margin-bottom: 20px; }

        .footer-social { display: flex; gap: 10px; }
        .social-btn {
            width: 34px; height: 34px; border-radius: 8px;
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--muted); text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .social-btn:hover { border-color: var(--orange); color: var(--orange); }

        .footer-col h4 { font-size: 14px; font-weight: 700; margin-bottom: 18px; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 10px; }
        .footer-col a { font-size: 13px; color: var(--muted); text-decoration: none; transition: color .2s; }
        .footer-col a:hover { color: var(--orange); }

        .footer-contact-item {
            display: flex; align-items: flex-start; gap: 10px;
            font-size: 13px; color: var(--muted); margin-bottom: 12px;
        }
        .footer-contact-item svg { color: var(--orange); flex-shrink: 0; margin-top: 1px; }

        .footer-bottom {
            max-width: 1100px; margin: 0 auto;
            border-top: 1px solid var(--border);
            padding-top: 20px;
            display: flex; justify-content: space-between; align-items: center;
            flex-wrap: wrap; gap: 12px;
        }
        .footer-bottom p { font-size: 12px; color: var(--muted); }
        .footer-bottom-links { display: flex; gap: 20px; }
        .footer-bottom-links a { font-size: 12px; color: var(--muted); text-decoration: none; }
        .footer-bottom-links a:hover { color: var(--orange); }

        /* ═══════════════════════ MODAL AUTH ═══════════════════════ */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 300;
            background: rgba(0,0,0,.75); align-items: center; justify-content: center;
            backdrop-filter: blur(4px);
        }
        .modal-overlay.open { display: flex; }
        .modal-auth {
            background: #1a1a1a; border: 1px solid var(--border);
            border-radius: 16px; width: 100%; max-width: 420px;
            overflow: hidden; position: relative;
        }
        .modal-tabs {
            display: flex; border-bottom: 1px solid var(--border);
        }
        .modal-tab {
            flex: 1; padding: 16px; text-align: center;
            font-size: 14px; font-weight: 600; color: var(--muted);
            cursor: pointer; background: none; border: none;
            font-family: 'Barlow', sans-serif;
            border-bottom: 2px solid transparent;
            transition: color .2s, border-color .2s;
        }
        .modal-tab.active { color: var(--orange); border-bottom-color: var(--orange); }
        .modal-body { padding: 28px; }
        .modal-close {
            position: absolute; top: 14px; right: 14px;
            background: none; border: none; color: var(--muted);
            cursor: pointer; font-size: 20px; line-height: 1;
            transition: color .2s;
        }
        .modal-close:hover { color: var(--text); }
        .auth-form { display: none; }
        .auth-form.active { display: block; }
        .auth-form h2 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: 28px; margin-bottom: 6px;
        }
        .auth-form p { font-size: 13px; color: var(--muted); margin-bottom: 22px; }
        .auth-field { margin-bottom: 16px; }
        .auth-field label { display: block; font-size: 13px; color: var(--label); margin-bottom: 6px; }
        .auth-field input {
            width: 100%; background: #222; border: 1px solid var(--border);
            border-radius: 8px; padding: 11px 14px;
            font-size: 14px; font-family: 'Barlow', sans-serif;
            color: var(--text); outline: none; transition: border-color .2s;
        }
        .auth-field input:focus { border-color: var(--orange); }
        .auth-field input::placeholder { color: var(--muted); }
        .btn-auth {
            width: 100%; background: var(--orange); color: #fff;
            border: none; border-radius: 8px; padding: 13px;
            font-size: 15px; font-family: 'Barlow', sans-serif;
            font-weight: 600; cursor: pointer; margin-top: 6px;
            transition: background .2s;
        }
        .btn-auth:hover { background: var(--orange2); }
        .auth-alert {
            padding: 10px 14px; border-radius: 8px;
            font-size: 13px; margin-bottom: 16px; display: none;
        }
        .auth-alert.error   { background: rgba(240,112,0,.12); border: 1px solid var(--orange); color: var(--orange); }
        .auth-alert.success { background: rgba(0,200,100,.1);  border: 1px solid #00c864; color: #00c864; }
        .auth-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 200;
            background: var(--card2); border: 1px solid var(--orange);
            border-radius: 12px; padding: 14px 20px;
            font-size: 14px; color: var(--text);
            transform: translateY(80px); opacity: 0;
            transition: all .3s; pointer-events: none;
        }
        .toast.show { transform: none; opacity: 1; }

        /* ═══════════════════════ CART TOAST ═══════════════════════ */
        @media (max-width: 768px) {
            nav { padding: 0 20px; }
            .nav-links { display: none; }
            #inicio, #menu, #promociones, #nosotros, #contacto { padding-left: 20px; padding-right: 20px; }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            footer { padding: 40px 20px 20px; }
        }
    </style>
</head>
<body>

<!-- ─── NAV ─── -->
<nav>
    <a class="nav-brand" href="#inicio">
        <svg width="22" height="28" viewBox="0 0 32 42" fill="none">
            <path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/>
        </svg>
        <div class="nav-brand-text">
            <div class="nav-brand-name">La Parrilla</div>
            <div class="nav-brand-sub">Asadero &amp; Restaurante</div>
        </div>
    </a>

    <ul class="nav-links">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#menu">Menu</a></li>
        <li><a href="#promociones">Promociones</a></li>
        <li><a href="#nosotros">Nosotros</a></li>
        <li><a href="#contacto">Contacto</a></li>
    </ul>

    <div class="nav-right">
        <?php if(isset($_SESSION['usuario'])): ?>
        <a href="carrito.php" class="btn-cart" id="cartBtn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <span class="cart-badge" id="cartBadge">0</span>
        </a>
        <?php endif; ?>
        <a href="../Views/usuarios/login.php" class="btn-nav-login" id="btnIngresar">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            <?php if(isset($_SESSION['usuario'])): ?>
                <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?>
            <?php else: ?>
                Ingresar
            <?php endif; ?>
        </a>
    </div>
</nav>

<!-- ─── HERO ─── -->
<section id="inicio">
    <div class="hero-bg"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Ahora con servicio a domicilio
        </div>
        <h1 class="hero-title">El autentico<br>sabor de la <span>brasa</span></h1>
        <p class="hero-desc">Desde 1995 preparando el mejor pollo asado con nuestra receta secreta. Carnes jugosas, sabor inigualable y el calor de hogar.</p>
        <div class="hero-actions">
            <a href="#menu" class="btn-orange">Ver Menú &nbsp;→</a>
            <a href="carrito.php" class="btn-outline">Ordenar Ahora</a>
        </div>
        <div class="hero-info">
            <div class="hero-info-item">
                <div class="hero-info-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div class="hero-info-text">
                    <strong>Horario</strong>
                    <span>11:00 AM – 10:00 PM</span>
                </div>
            </div>
            <div class="hero-info-item">
                <div class="hero-info-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="hero-info-text">
                    <strong>Ubicacion</strong>
                    <span>Calle Principal #123</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ─── MENU ─── -->
<section id="menu">
    <p class="section-tag">Nuestro Menu</p>
    <h2 class="section-title">Platos que enamoran</h2>
    <p class="section-desc">Cada plato preparado con ingredientes frescos y el amor de nuestra cocina tradicional</p>

    <div class="menu-grid">
        <?php
        require_once __DIR__ . '/../Config/database.php';
        $db     = (new Database())->conectar();
        $platos = $db->query("
            SELECT p.id, p.nombre, p.descripcion, p.precio, p.popular, p.imagen_url
            FROM productos p
            WHERE p.disponible = 1
            ORDER BY p.popular DESC, p.nombre
        ")->fetchAll(PDO::FETCH_ASSOC);

        // Imágenes por defecto si no hay imagen_url
        $imgs_default = [
            'https://images.unsplash.com/photo-1600891964092-4316c288032e?w=600&q=80',
            'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=600&q=80',
            'https://images.unsplash.com/photo-1562967914-608f82629710?w=600&q=80',
            'https://images.unsplash.com/photo-1527477396000-e27163b481c2?w=600&q=80',
            'https://images.unsplash.com/photo-1516684732162-798a0062be99?w=600&q=80',
            'https://images.unsplash.com/photo-1604908176997-125f25cc6f3d?w=600&q=80',
        ];

        foreach($platos as $i => $p):
            $img   = !empty($p['imagen_url']) ? $p['imagen_url'] : $imgs_default[$i % count($imgs_default)];
            $precio_fmt = '$ ' . number_format($p['precio'], 0, ',', '.');
        ?>
        <div class="menu-card">
            <div class="menu-card-img">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" loading="lazy">
                <?php if($p['popular']): ?><span class="tag-popular">Popular</span><?php endif; ?>
            </div>
            <div class="menu-card-body">
                <div class="menu-card-top">
                    <span class="menu-card-name"><?= htmlspecialchars($p['nombre']) ?></span>
                    <span class="menu-card-price"><?= $precio_fmt ?></span>
                </div>
                <p class="menu-card-desc"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
                <button class="btn-add" onclick="addToCart(<?= $p['id'] ?>, '<?= addslashes($p['nombre']) ?>', '<?= $precio_fmt ?>', '<?= addslashes($img) ?>')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Agregar
                </button>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($platos)): ?>
            <p style="color:var(--muted);text-align:center;grid-column:1/-1;padding:40px">No hay productos disponibles en este momento.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ─── PROMOCIONES ─── -->
<section id="promociones">
    <p class="section-tag">Promociones</p>
    <h2 class="section-title">Ofertas especiales</h2>
    <p class="section-desc" style="margin-bottom:40px">&nbsp;</p>

    <div class="promo-grid">
        <div class="promo-card">
            <div class="promo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
            </div>
            <h3>Martes de Descuento</h3>
            <p>20% OFF en pollos enteros todos los martes</p>
            <a href="#" class="link-orange">Ver Oferta →</a>
        </div>
        <div class="promo-card">
            <div class="promo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <h3>Domicilio Gratis</h3>
            <p>En pedidos mayores a $50.000 dentro de la zona</p>
            <a href="carrito.php" class="link-orange">Ordenar →</a>
        </div>
        <div class="promo-card">
            <div class="promo-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><path d="M12 22V7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
            </div>
            <h3>Programa de Puntos</h3>
            <p>Acumula puntos y canjéalos por productos gratis</p>
            <a href="registro.php" class="link-orange">Regístrate →</a>
        </div>
    </div>

    <div class="combo-banner">
        <span class="combo-tag">Oferta Limitada</span>
        <h2>Combo Fin de Semana</h2>
        <p>2 pollos enteros + papas grandes + ensalada familiar + gaseosa 3L por solo $89.900</p>
        <button class="btn-orange">Pedir Ahora</button>
    </div>
</section>

<!-- ─── NOSOTROS ─── -->
<section id="nosotros">
    <p class="section-tag">Quiénes Somos</p>
    <h2 class="section-title">Nuestra Historia</h2>
    <p>Desde 1995 somos el asadero de confianza de nuestra ciudad. Nuestro secreto es sencillo: ingredientes frescos, leña seleccionada y la receta que ha pasado de generación en generación. Cada plato que sale de nuestra parrilla lleva el sabor del hogar y la tradición de una familia apasionada por la buena cocina.</p>
</section>

<!-- ─── CONTACTO ─── -->
<section id="contacto">
    <p class="section-tag">Encuéntranos</p>
    <h2 class="section-title">Contacto</h2>
    <div class="contacto-info">
        <div class="contacto-item"><strong>Teléfono</strong>+57 300 123 4567</div>
        <div class="contacto-item"><strong>Correo</strong>info@laparrilla.com</div>
        <div class="contacto-item"><strong>Dirección</strong>Calle Principal #123, Ciudad</div>
    </div>
</section>

<!-- ─── FOOTER ─── -->
<footer>
    <div class="footer-grid">
        <div>
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                <svg width="20" height="26" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
                <div>
                    <div class="footer-brand-name">La Parrilla</div>
                    <span class="footer-brand-sub">Asadero &amp; Restaurante</span>
                </div>
            </div>
            <p class="footer-brand-desc">Desde 1995 llevando el mejor sabor a la brasa a tu mesa. Tradición, calidad y amor en cada plato.</p>
            <div class="footer-social">
                <a href="#" class="social-btn" aria-label="Facebook">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="#" class="social-btn" aria-label="Instagram">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                </a>
            </div>
        </div>

        <div class="footer-col">
            <h4>Menu</h4>
            <ul>
                <li><a href="#menu">Pollos Asados</a></li>
                <li><a href="#menu">Carnes a la Brasa</a></li>
                <li><a href="#menu">Acompañantes</a></li>
                <li><a href="#menu">Bebidas</a></li>
                <li><a href="#menu">Postres</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Empresa</h4>
            <ul>
                <li><a href="#nosotros">Nuestra Historia</a></li>
                <li><a href="#nosotros">Trabaja con Nosotros</a></li>
                <li><a href="#">Franquicias</a></li>
                <li><a href="#">Términos y Condiciones</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4>Contacto</h4>
            <div class="footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.1a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                +57 300 123 4567
            </div>
            <div class="footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                info@laparrilla.com
            </div>
            <div class="footer-contact-item">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Calle Principal #123, Ciudad
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <p>© 2026 La Parrilla. Todos los derechos reservados.</p>
        <div class="footer-bottom-links">
            <a href="#">Política de Privacidad</a>
            <a href="#">Términos de Uso</a>
        </div>
    </div>
</footer>

<!-- TOAST -->
<div class="toast" id="toast">✓ Producto añadido al carrito</div>

<!-- ─── MODAL AUTH ─── -->
<div class="modal-overlay" id="modalAuth">
    <div class="modal-auth">
        <button class="modal-close" onclick="cerrarModal()">✕</button>
        <div class="modal-tabs">
            <button class="modal-tab active" id="tabLogin" onclick="switchTab('login')">Iniciar sesión</button>
            <button class="modal-tab" id="tabRegistro" onclick="switchTab('registro')">Registrarse</button>
        </div>
        <div class="modal-body">

            <!-- FORM LOGIN -->
            <div class="auth-form active" id="formLogin">
                <h2>Bienvenido</h2>
                <p>Ingresa tus credenciales para continuar</p>
                <div class="auth-alert" id="alertLogin"></div>
                <form method="POST" action="../Controllers/AuthController.php">
                    <div class="auth-field">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="auth-field">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-auth">Ingresar</button>
                </form>
                <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--muted)">
                    ¿No tienes cuenta? <a href="#" onclick="switchTab('registro')" style="color:var(--orange)">Regístrate aquí</a>
                </p>
            </div>

            <!-- FORM REGISTRO -->
            <div class="auth-form" id="formRegistro">
                <h2>Crear cuenta</h2>
                <p>Completa tus datos para registrarte</p>
                <div class="auth-alert" id="alertRegistro"></div>
                <form method="POST" action="../Controllers/UsuarioControllers.php">
                    <div class="auth-row">
                        <div class="auth-field">
                            <label>Nombres</label>
                            <input type="text" name="nombres" placeholder="Tu nombre" required>
                        </div>
                        <div class="auth-field">
                            <label>Apellidos</label>
                            <input type="text" name="apellidos" placeholder="Tus apellidos" required>
                        </div>
                    </div>
                    <div class="auth-field">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" placeholder="correo@ejemplo.com" required>
                    </div>
                    <div class="auth-field">
                        <label>Contraseña</label>
                        <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                    </div>
                    <div class="auth-field">
                        <label>Confirmar contraseña</label>
                        <input type="password" name="confirmar_password" placeholder="Repite tu contraseña" required>
                    </div>
                    <button type="submit" class="btn-auth">Crear cuenta</button>
                </form>
                <p style="text-align:center;margin-top:16px;font-size:13px;color:var(--muted)">
                    ¿Ya tienes cuenta? <a href="#" onclick="switchTab('login')" style="color:var(--orange)">Inicia sesión</a>
                </p>
            </div>

        </div>
    </div>
</div>

<script>
// ─── MODAL AUTH ───
<?php if(isset($_SESSION['usuario'])): ?>
document.getElementById('btnIngresar').href = '../Views/dashboard/<?= $_SESSION['usuario']['rol'] === 'administrador' ? 'admin' : $_SESSION['usuario']['rol'] ?>.php';
<?php else: ?>
document.getElementById('btnIngresar').addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('modalAuth').classList.add('open');
});
<?php endif; ?>

function cerrarModal() {
    document.getElementById('modalAuth').classList.remove('open');
}
document.getElementById('modalAuth').addEventListener('click', function(e){
    if (e.target === this) cerrarModal();
});
function switchTab(tab) {
    document.getElementById('formLogin').classList.toggle('active', tab === 'login');
    document.getElementById('formRegistro').classList.toggle('active', tab === 'registro');
    document.getElementById('tabLogin').classList.toggle('active', tab === 'login');
    document.getElementById('tabRegistro').classList.toggle('active', tab === 'registro');
}

// Mostrar alerta si viene de login/registro
<?php
$alert = $_SESSION['alert'] ?? null;
unset($_SESSION['alert']);
if ($alert):
    $tab = isset($alert['redirect']) ? 'login' : 'registro';
    $cls = $alert['icon'] === 'success' ? 'success' : 'error';
?>
window.addEventListener('load', function(){
    document.getElementById('modalAuth').classList.add('open');
    switchTab('<?= $tab ?>');
    const el = document.getElementById('alert<?= ucfirst($tab) ?>');
    el.textContent = '<?= addslashes($alert['text']) ?>';
    el.className = 'auth-alert <?= $cls ?>';
    el.style.display = 'block';
});
<?php endif; ?>

// ─── CART LOGIC ───
function getCart() {
    return JSON.parse(localStorage.getItem('laparrilla_cart') || '[]');
}
function saveCart(cart) {
    localStorage.setItem('laparrilla_cart', JSON.stringify(cart));
    updateBadge();
}
function updateBadge() {
    const cart = getCart();
    const count = cart.reduce((s,i)=>s+i.qty,0);
    const badge = document.getElementById('cartBadge');
    badge.textContent = count;
    badge.style.display = count > 0 ? 'flex' : 'none';
}
function addToCart(id, nombre, precio, img) {
    const cart = getCart();
    const idx  = cart.findIndex(i=>i.id===id);
    if (idx>=0) cart[idx].qty++;
    else cart.push({id, nombre, precio, img, qty:1});
    saveCart(cart);
    showToast('✓ ' + nombre + ' añadido');
}
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'), 2500);
}
updateBadge();
</script>
</body>
</html>