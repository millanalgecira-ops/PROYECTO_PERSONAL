<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Parrilla – Carrito</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Barlow:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --black: #0e0e0e; --dark: #141414; --card: #1c1a18; --card2: #221f1b;
            --border: #2e2b27; --orange: #f07000; --orange2: #e06500;
            --text: #f0ece6; --muted: #8a8078; --label: #c8bfb0;
        }
        body { font-family: 'Barlow', sans-serif; background: var(--black); color: var(--text); min-height: 100vh; }

        /* NAV */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(14,14,14,.95); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 24px; height: 60px;
        }
        .nav-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .nav-brand-name { font-family: 'Bebas Neue', sans-serif; font-size: 20px; color: var(--text); }
        .nav-brand-sub  { font-size: 9px; letter-spacing: 3px; text-transform: uppercase; color: var(--orange); }
        .nav-back {
            display: flex; align-items: center; gap: 6px;
            background: none; border: 1px solid var(--border); border-radius: 8px;
            padding: 8px 14px; color: var(--muted); font-size: 13px;
            font-family: 'Barlow', sans-serif; cursor: pointer; text-decoration: none;
            transition: border-color .2s, color .2s;
        }
        .nav-back:hover { border-color: var(--orange); color: var(--orange); }
        .cart-count {
            background: var(--orange); color: #fff; border-radius: 50%;
            width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700;
        }

        /* LAYOUT */
        .page { max-width: 1100px; margin: 0 auto; padding: 32px 24px; display: grid; grid-template-columns: 1fr 420px; gap: 28px; }
        @media (max-width: 860px) { .page { grid-template-columns: 1fr; } }

        /* CARRITO */
        .cart-panel { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
        .cart-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
        .cart-header h2 { font-family: 'Bebas Neue', sans-serif; font-size: 22px; letter-spacing: 1px; }
        .btn-vaciar {
            background: none; border: none; color: var(--muted); font-size: 12px;
            font-family: 'Barlow', sans-serif; cursor: pointer; transition: color .2s;
        }
        .btn-vaciar:hover { color: #ff5050; }

        /* ITEMS */
        .cart-items { padding: 8px 0; }
        .cart-item {
            display: flex; align-items: center; gap: 14px;
            padding: 16px 24px; border-bottom: 1px solid rgba(46,43,39,.5);
            transition: background .15s;
        }
        .cart-item:last-child { border-bottom: none; }
        .cart-item:hover { background: rgba(255,255,255,.02); }
        .item-img {
            width: 72px; height: 72px; border-radius: 10px; object-fit: cover;
            flex-shrink: 0; background: var(--card2);
        }
        .item-info { flex: 1; min-width: 0; }
        .item-name { font-size: 15px; font-weight: 600; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-price { font-size: 14px; color: var(--orange); font-weight: 700; }
        .item-controls { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .qty-btn {
            width: 30px; height: 30px; border-radius: 8px;
            background: var(--card2); border: 1px solid var(--border);
            color: var(--text); font-size: 16px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: border-color .2s, background .2s;
        }
        .qty-btn:hover { border-color: var(--orange); background: rgba(240,112,0,.1); color: var(--orange); }
        .qty-num {
            min-width: 32px; height: 32px; border-radius: 8px;
            background: var(--card2); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 700;
        }
        .btn-remove {
            background: none; border: none; color: var(--muted); cursor: pointer;
            padding: 4px; transition: color .2s; font-size: 16px;
        }
        .btn-remove:hover { color: #ff5050; }

        /* TOTALES */
        .cart-totals { padding: 20px 24px; border-top: 1px solid var(--border); background: var(--card2); }
        .total-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; font-size: 14px; color: var(--label); }
        .total-row.final { font-size: 18px; font-weight: 700; color: var(--text); margin-bottom: 0; padding-top: 10px; border-top: 1px solid var(--border); }
        .total-row.final span:last-child { color: var(--orange); }

        /* EMPTY */
        .cart-empty { padding: 60px 24px; text-align: center; }
        .cart-empty svg { opacity: .3; margin-bottom: 16px; }
        .cart-empty p { font-size: 15px; color: var(--muted); margin-bottom: 20px; }
        .btn-ir-menu {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--orange); color: #fff; border: none; border-radius: 8px;
            padding: 12px 24px; font-size: 14px; font-family: 'Barlow', sans-serif;
            font-weight: 600; cursor: pointer; text-decoration: none; transition: background .2s;
        }
        .btn-ir-menu:hover { background: var(--orange2); }

        /* CHECKOUT PANEL */
        .checkout-panel { display: flex; flex-direction: column; gap: 20px; }
        .checkout-card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; padding: 24px; }
        .checkout-card h3 { font-family: 'Bebas Neue', sans-serif; font-size: 20px; letter-spacing: 1px; margin-bottom: 20px; color: var(--orange); }

        .field { margin-bottom: 16px; }
        .field label { display: block; font-size: 13px; color: var(--label); margin-bottom: 6px; }
        .field input, .field select, .field textarea {
            width: 100%; background: var(--card2); border: 1px solid var(--border);
            border-radius: 8px; padding: 12px 14px; font-size: 14px;
            font-family: 'Barlow', sans-serif; color: var(--text); outline: none;
            transition: border-color .2s;
        }
        .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--orange); }
        .field input::placeholder, .field textarea::placeholder { color: var(--muted); }
        .field select option { background: var(--card2); }

        /* TIPO PEDIDO */
        .tipo-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 16px; }
        .tipo-btn {
            padding: 12px; border-radius: 10px; border: 1.5px solid var(--border);
            background: none; color: var(--muted); font-size: 13px; font-family: 'Barlow', sans-serif;
            font-weight: 600; cursor: pointer; transition: all .2s; text-align: center;
        }
        .tipo-btn.active { border-color: var(--orange); background: rgba(240,112,0,.1); color: var(--orange); }

        /* METODO PAGO */
        .metodo-list { display: flex; flex-direction: column; gap: 10px; }
        .metodo-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px; border-radius: 10px; border: 1.5px solid var(--border);
            cursor: pointer; transition: all .2s;
        }
        .metodo-item.active { border-color: var(--orange); background: rgba(240,112,0,.08); }
        .metodo-item input[type=radio] { accent-color: var(--orange); width: 16px; height: 16px; }
        .metodo-item label { font-size: 14px; cursor: pointer; flex: 1; }
        .metodo-icon { font-size: 18px; }

        /* BTN FINALIZAR */
        .btn-finalizar {
            width: 100%; background: var(--orange); color: #fff; border: none;
            border-radius: 12px; padding: 16px; font-size: 16px;
            font-family: 'Barlow', sans-serif; font-weight: 700; cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            transition: background .2s, transform .15s; letter-spacing: .5px;
        }
        .btn-finalizar:hover { background: var(--orange2); transform: translateY(-1px); }
        .btn-finalizar:disabled { background: var(--muted); cursor: not-allowed; transform: none; }

        /* RESUMEN MINI */
        .resumen-mini { background: var(--card2); border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-top: 16px; }
        .resumen-mini-item { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
        .resumen-mini-item:last-child { margin-bottom: 0; }
        .resumen-mini-img { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; background: var(--card); flex-shrink: 0; }
        .resumen-mini-name { font-size: 13px; font-weight: 600; flex: 1; }
        .resumen-mini-qty  { font-size: 12px; color: var(--muted); }
        .resumen-mini-price { font-size: 13px; color: var(--orange); font-weight: 700; }
        .resumen-divider { border: none; border-top: 1px solid var(--border); margin: 12px 0; }
        .resumen-total-row { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 6px; color: var(--label); }
        .resumen-total-row.bold { font-weight: 700; color: var(--text); font-size: 16px; }
        .resumen-total-row.bold span:last-child { color: var(--orange); }
    </style>
</head>
<body>

<!-- NAV -->
<nav>
    <a class="nav-brand" href="index.php">
        <svg width="18" height="24" viewBox="0 0 32 42" fill="none"><path d="M16 0C16 0 28 10 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 16 8 12 8 12C8 12 8 18 12 20C12 20 10 14 16 8C16 8 14 16 18 20C20 18 22 14 20 8C24 12 28 18 24 26C24 26 27 23 26 19C27.5 21 28 23 28 22C28 29.732 22.627 36 16 36C9.373 36 4 29.732 4 22C4 10 16 0 16 0Z" fill="#f07000"/></svg>
        <div>
            <div class="nav-brand-name">La Parrilla</div>
            <div class="nav-brand-sub">Asadero &amp; Restaurante</div>
        </div>
    </a>
    <div style="display:flex;align-items:center;gap:12px">
        <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted)">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
            <span class="cart-count" id="navCount">0</span>
        </div>
        <a href="index.php" class="nav-back">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Volver al menú
        </a>
    </div>
</nav>

<div class="page">

    <!-- PANEL IZQUIERDO: CARRITO -->
    <div>
        <div class="cart-panel" id="cartPanel">
            <div class="cart-header">
                <h2>Tu Pedido</h2>
                <button class="btn-vaciar" onclick="vaciarCarrito()">Vaciar carrito</button>
            </div>
            <div class="cart-items" id="cartItems"></div>
            <div class="cart-totals" id="cartTotals" style="display:none">
                <div class="total-row"><span>Subtotal</span><span id="subtotalVal">$0</span></div>
                <div class="total-row final"><span>Total</span><span id="totalVal">$0</span></div>
            </div>
        </div>
    </div>

    <!-- PANEL DERECHO: CHECKOUT -->
    <div class="checkout-panel" id="checkoutPanel" style="display:none">
        <div class="checkout-card">
            <h3>Detalles del Pedido</h3>

            <!-- TIPO -->
            <div class="field">
                <label>Tipo de pedido</label>
                <div class="tipo-grid">
                    <button class="tipo-btn active" id="btnMesa" onclick="setTipo('mesa')">
                        🍽️ En Mesa
                    </button>
                    <button class="tipo-btn" id="btnLlevar" onclick="setTipo('llevar')">
                        🥡 Para Llevar
                    </button>
                </div>
            </div>

            <!-- MESA (solo si es en mesa) -->
            <div class="field" id="campoMesa">
                <label>Número de mesa</label>
                <select id="numeroMesa">
                    <option value="">Selecciona tu mesa...</option>
                    <?php for($i=1;$i<=10;$i++): ?>
                        <option value="<?= $i ?>">Mesa <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- NOMBRE -->
            <div class="field">
                <label>Nombre completo</label>
                <input type="text" id="nombreCliente" placeholder="Tu nombre"
                    value="<?= isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']['nombre']) : '' ?>">
            </div>

            <!-- NOTA -->
            <div class="field">
                <label>Nota especial <span style="color:var(--muted)">(opcional)</span></label>
                <textarea id="notaEspecial" placeholder="Sin cebolla, extra salsa, término de cocción..." rows="2"></textarea>
            </div>

            <!-- MÉTODO DE PAGO -->
            <div class="field">
                <label>Método de pago</label>
                <div class="metodo-list">
                    <div class="metodo-item active" onclick="setMetodo(this,'Efectivo')">
                        <span class="metodo-icon">💵</span>
                        <input type="radio" name="metodo" value="Efectivo" checked>
                        <label>Efectivo</label>
                    </div>
                    <div class="metodo-item" onclick="setMetodo(this,'Tarjeta debito')">
                        <span class="metodo-icon">💳</span>
                        <input type="radio" name="metodo" value="Tarjeta debito">
                        <label>Tarjeta débito</label>
                    </div>
                    <div class="metodo-item" onclick="setMetodo(this,'Tarjeta credito')">
                        <span class="metodo-icon">💳</span>
                        <input type="radio" name="metodo" value="Tarjeta credito">
                        <label>Tarjeta crédito</label>
                    </div>
                    <div class="metodo-item" onclick="setMetodo(this,'Billetera digital')">
                        <span class="metodo-icon">📱</span>
                        <input type="radio" name="metodo" value="Billetera digital">
                        <label>Billetera digital (Nequi / Daviplata)</label>
                    </div>
                </div>
            </div>

            <!-- RESUMEN MINI -->
            <div class="resumen-mini" id="resumenMini"></div>

            <button class="btn-finalizar" id="btnFinalizar" onclick="finalizarPedido()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Finalizar Pedido
            </button>
        </div>
    </div>

</div>

<script>
const CART_KEY = 'laparrilla_cart';
let tipoSeleccionado = 'mesa';
let metodoSeleccionado = 'Efectivo';

function getCart() { return JSON.parse(localStorage.getItem(CART_KEY) || '[]'); }
function saveCart(c) { localStorage.setItem(CART_KEY, JSON.stringify(c)); renderCart(); }

function parsePrecio(str) {
    if (typeof str === 'number') return str;
    return parseInt(str.replace(/[^0-9]/g,'')) || 0;
}

function formatPrecio(n) {
    return '$ ' + n.toLocaleString('es-CO');
}

function renderCart() {
    const cart = getCart();
    const itemsEl   = document.getElementById('cartItems');
    const totalsEl  = document.getElementById('cartTotals');
    const checkoutEl= document.getElementById('checkoutPanel');
    const navCount  = document.getElementById('navCount');

    const totalQty = cart.reduce((s,i)=>s+i.qty,0);
    navCount.textContent = totalQty;

    if (cart.length === 0) {
        itemsEl.innerHTML = `
            <div class="cart-empty">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
                <p>Tu carrito está vacío</p>
                <a href="index.php#menu" class="btn-ir-menu">Ver Menú →</a>
            </div>`;
        totalsEl.style.display  = 'none';
        checkoutEl.style.display = 'none';
        return;
    }

    let subtotal = 0;
    let html = '';
    cart.forEach((item, idx) => {
        const precio = parsePrecio(item.precio);
        const sub    = precio * item.qty;
        subtotal    += sub;
        html += `
        <div class="cart-item">
            <img class="item-img" src="${item.img}" alt="${item.nombre}" onerror="this.src='https://via.placeholder.com/72x72/1c1a18/f07000?text=🍗'">
            <div class="item-info">
                <div class="item-name">${item.nombre}</div>
                <div class="item-price">${formatPrecio(precio)}</div>
            </div>
            <div class="item-controls">
                <button class="qty-btn" onclick="cambiarQty(${idx},-1)">−</button>
                <div class="qty-num">${item.qty}</div>
                <button class="qty-btn" onclick="cambiarQty(${idx},1)">+</button>
                <button class="btn-remove" onclick="eliminarItem(${idx})" title="Eliminar">✕</button>
            </div>
        </div>`;
    });

    itemsEl.innerHTML = html;
    totalsEl.style.display = 'block';
    checkoutEl.style.display = 'flex';

    document.getElementById('subtotalVal').textContent = formatPrecio(subtotal);
    document.getElementById('totalVal').textContent    = formatPrecio(subtotal);

    // Resumen mini en checkout
    let miniHtml = '';
    cart.forEach(item => {
        const precio = parsePrecio(item.precio);
        miniHtml += `
        <div class="resumen-mini-item">
            <img class="resumen-mini-img" src="${item.img}" alt="${item.nombre}" onerror="this.src='https://via.placeholder.com/44x44/1c1a18/f07000?text=🍗'">
            <div class="resumen-mini-name">${item.nombre}</div>
            <div class="resumen-mini-qty">x${item.qty}</div>
            <div class="resumen-mini-price">${formatPrecio(precio * item.qty)}</div>
        </div>`;
    });
    miniHtml += `<hr class="resumen-divider">
        <div class="resumen-total-row"><span>Subtotal</span><span>${formatPrecio(subtotal)}</span></div>
        <div class="resumen-total-row bold"><span>Total</span><span>${formatPrecio(subtotal)}</span></div>`;
    document.getElementById('resumenMini').innerHTML = miniHtml;
}

function cambiarQty(idx, delta) {
    const cart = getCart();
    cart[idx].qty += delta;
    if (cart[idx].qty <= 0) cart.splice(idx, 1);
    saveCart(cart);
}

function eliminarItem(idx) {
    const cart = getCart();
    cart.splice(idx, 1);
    saveCart(cart);
}

function vaciarCarrito() {
    if (confirm('¿Vaciar el carrito?')) { localStorage.removeItem(CART_KEY); renderCart(); }
}

function setTipo(tipo) {
    tipoSeleccionado = tipo;
    document.getElementById('btnMesa').classList.toggle('active', tipo === 'mesa');
    document.getElementById('btnLlevar').classList.toggle('active', tipo === 'llevar');
    document.getElementById('campoMesa').style.display = tipo === 'mesa' ? 'block' : 'none';
}

function setMetodo(el, metodo) {
    metodoSeleccionado = metodo;
    document.querySelectorAll('.metodo-item').forEach(m => m.classList.remove('active'));
    el.classList.add('active');
    el.querySelector('input[type=radio]').checked = true;
}

function finalizarPedido() {
    const cart   = getCart();
    const nombre = document.getElementById('nombreCliente').value.trim();
    const nota   = document.getElementById('notaEspecial').value.trim();
    const mesa   = document.getElementById('numeroMesa')?.value;

    if (!nombre) { alert('Por favor ingresa tu nombre'); return; }
    if (tipoSeleccionado === 'mesa' && !mesa) { alert('Por favor selecciona tu mesa'); return; }
    if (cart.length === 0) { alert('Tu carrito está vacío'); return; }

    const btn = document.getElementById('btnFinalizar');
    btn.disabled = true;
    btn.textContent = 'Procesando...';

    const datos = {
        cart,
        tipo: tipoSeleccionado === 'mesa' ? 'En mesa' : 'Para llevar',
        mesa_numero: mesa || null,
        nombre_cliente: nombre,
        nota_especial: nota,
        metodo_pago: metodoSeleccionado
    };

    fetch('../Controllers/CarritoController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(datos)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            localStorage.removeItem(CART_KEY);
            window.location.href = 'confirmacion.php?orden=' + res.numero_orden;
        } else {
            alert('Error: ' + (res.message || 'No se pudo procesar el pedido'));
            btn.disabled = false;
            btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Finalizar Pedido';
        }
    })
    .catch(() => {
        alert('Error de conexión. Intenta de nuevo.');
        btn.disabled = false;
        btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> Finalizar Pedido';
    });
}

// Inicializar
renderCart();
</script>
</body>
</html>
