<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Asadero & Restaurante</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= URL_PUBLIC ?>/css/index.css">
    <link rel="stylesheet" href="<?= URL_PUBLIC ?>/css/chatbox.css">
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
            <?php if(isset($_SESSION['usuario'])): ?>
                <a href="carrito.php" class="btn-outline">Ordenar Ahora</a>
            <?php else: ?>
                <a href="#" class="btn-outline" onclick="abrirLoginParaOrdenar(event)">Ordenar Ahora</a>
            <?php endif; ?>
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



    <!-- FILTROS DE CATEGORÍA -->
    <div class="cat-filtros">
        <button class="cat-btn active" onclick="filtrarCategoria('todos', this)">
            <span class="cat-btn-nombre">Todos</span>
            <span class="cat-btn-count"><?= count($todos_productos) ?></span>
        </button>
        <?php foreach($categorias_menu as $cat): ?>
        <button class="cat-btn" onclick="filtrarCategoria('cat-<?= $cat['id'] ?>', this)">
            <span class="cat-btn-nombre"><?= htmlspecialchars($cat['nombre']) ?></span>
            <span class="cat-btn-count"><?= $cat['total_productos'] ?></span>
        </button>
        <?php endforeach; ?>
    </div>

    <!-- GRID DE PRODUCTOS -->
    <div class="menu-grid" id="menuGrid">
        <?php if(empty($todos_productos)): ?>
            <p style="color:var(--muted);text-align:center;grid-column:1/-1;padding:40px">No hay productos disponibles en este momento.</p>
        <?php else: ?>
        <?php foreach($todos_productos as $i => $p):
            $img        = !empty($p['imagen_url']) ? $p['imagen_url'] : $imgs_default[$i % count($imgs_default)];
            $precio_fmt = '$ ' . number_format($p['precio'], 0, ',', '.');
            $agotado    = !$p['disponible'];
        ?>
        <div class="menu-card <?= $agotado ? 'agotado' : '' ?>" data-cat="cat-<?= $p['categoria_id'] ?>">
            <div class="menu-card-img">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['nombre']) ?>" loading="lazy"
                     style="<?= $agotado ? 'opacity:.45;filter:grayscale(.4)' : '' ?>">
                <?php if($p['popular'] && !$agotado): ?>
                    <span class="tag-popular">⭐ Popular</span>
                <?php endif; ?>
                <?php if($agotado): ?>
                    <span class="tag-agotado">Agotado</span>
                <?php endif; ?>
            </div>
            <div class="menu-card-body">
                <div class="menu-card-top">
                    <span class="menu-card-name"><?= htmlspecialchars($p['nombre']) ?></span>
                    <span class="menu-card-price" style="<?= $agotado ? 'color:var(--muted)' : '' ?>"><?= $precio_fmt ?></span>
                </div>
                <p class="menu-card-desc"><?= htmlspecialchars($p['descripcion'] ?? '') ?></p>
                <?php if($agotado): ?>
                    <button class="btn-add" disabled style="opacity:.4;cursor:not-allowed;background:rgba(255,255,255,.04);color:var(--muted);border-color:var(--border)">
                        Sin disponibilidad
                    </button>
                <?php else: ?>
                    <button class="btn-add" onclick="agregarProducto(<?= $p['id'] ?>, '<?= addslashes($p['nombre']) ?>', '<?= $precio_fmt ?>', '<?= addslashes($img) ?>')">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Agregar
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- MENSAJE CATEGORÍA VACÍA -->
    <div id="msgCatVacia" style="display:none;text-align:center;padding:48px 20px;color:var(--muted)">
        <p style="font-size:15px;margin-bottom:8px">Sin productos disponibles en este momento</p>
        <p style="font-size:13px">Explora otras categorías del menú</p>
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
            <a href="<?= isset($_SESSION['usuario']) ? 'carrito.php' : '#' ?>" 
               class="link-orange"
               <?= !isset($_SESSION['usuario']) ? 'onclick="abrirLoginParaOrdenar(event)"' : '' ?>>Ordenar →</a>
        </div>
        </div>
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
const SESION_ACTIVA = <?= isset($_SESSION['usuario']) ? 'true' : 'false' ?>;

function abrirLoginParaOrdenar(e) {
    e.preventDefault();
    document.getElementById('modalAuth').classList.add('open');
    switchTab('login');
    const alerta = document.getElementById('alertLogin');
    alerta.textContent = 'Debes iniciar sesión para realizar un pedido.';
    alerta.className = 'auth-alert error';
    alerta.style.display = 'block';
}

function agregarProducto(id, nombre, precio, img) {
    if (!SESION_ACTIVA) {
        // Mostrar modal de login con mensaje
        document.getElementById('modalAuth').classList.add('open');
        switchTab('login');
        const alerta = document.getElementById('alertLogin');
        alerta.textContent = 'Debes iniciar sesión para agregar productos al carrito.';
        alerta.className = 'auth-alert error';
        alerta.style.display = 'block';
        return;
    }
    addToCart(id, nombre, precio, img);
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

// ─── FILTRO DE CATEGORÍAS ───
function filtrarCategoria(cat, btn) {
    document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cards    = document.querySelectorAll('.menu-card');
    const msgVacia = document.getElementById('msgCatVacia');
    const grid     = document.getElementById('menuGrid');
    let visibles   = 0;
    cards.forEach(card => {
        const mostrar = cat === 'todos' || card.dataset.cat === cat;
        card.style.display = mostrar ? '' : 'none';
        if (mostrar) visibles++;
    });
    if (visibles === 0) {
        grid.style.display = 'none';
        msgVacia.style.display = 'block';
    } else {
        grid.style.display = 'grid';
        msgVacia.style.display = 'none';
    }
}
</script>
<script src="<?= URL_PUBLIC ?>/js/chatbox.js"></script>
</body>
</html>