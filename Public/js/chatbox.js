// ═══════════════════════════════════════════
//  CHATBOX - Asistente Virtual La Parrilla
// ═══════════════════════════════════════════

const RESPUESTAS = [
    // HORARIO Y UBICACIÓN
    {
        palabras: ['horario','hora','abierto','cierra','abre','atienden'],
        respuesta: '🕐 Nuestro horario de atención es de <strong>11:00 AM a 10:00 PM</strong>, todos los días de la semana.'
    },
    {
        palabras: ['ubicacion','ubicación','direccion','dirección','donde','dónde','local','lugar'],
        respuesta: '📍 Nos encontramos en <strong>Calle Principal #123</strong>. ¡Te esperamos!'
    },
    {
        palabras: ['telefono','teléfono','contacto','llamar','whatsapp','numero','número'],
        respuesta: '📞 Puedes contactarnos al <strong>+57 300 123 4567</strong> o escribirnos por WhatsApp.'
    },

    // PEDIDOS
    {
        palabras: ['pedir','pedido','ordenar','orden','como pido','cómo pido','hacer pedido'],
        respuesta: '🛒 Para hacer un pedido:\n1. Inicia sesión o regístrate\n2. Explora el menú y agrega productos al carrito\n3. Selecciona tu mesa o "Para llevar"\n4. Elige el método de pago y confirma\n¡Listo! Tu pedido llega a cocina al instante.'
    },
    {
        palabras: ['estado','seguimiento','donde esta','dónde está','mi pedido','rastrear'],
        respuesta: '📋 Puedes ver el estado de tu pedido en <strong>Mi cuenta → Mis Pedidos</strong>. Los estados son: Recibido → En preparación → Listo → Entregado.'
    },
    {
        palabras: ['cancelar','cancelacion','cancelación','anular'],
        respuesta: '❌ Una vez enviado el pedido a cocina no puedes cancelarlo desde la app. Si necesitas cancelar, comunícate con el personal del asadero directamente.'
    },
    {
        palabras: ['historial','anteriores','pasados','mis pedidos','pedidos anteriores'],
        respuesta: '📜 Puedes ver todos tus pedidos anteriores en <strong>Mi cuenta → Mis Pedidos</strong>. Ahí verás el detalle de cada orden con ítems, total y estado.'
    },

    // MENÚ Y PRODUCTOS
    {
        palabras: ['menu','menú','carta','platos','productos','que tienen','qué tienen','que hay','qué hay'],
        respuesta: '🍗 Tenemos categorías de: <strong>Res, Pollo, Cerdo, Combos, Acompañamientos y Bebidas</strong>. Puedes explorar el menú completo en la sección "Menú" de la página principal.'
    },
    {
        palabras: ['precio','precios','costo','cuanto cuesta','cuánto cuesta','valor'],
        respuesta: '💰 Los precios varían según el plato. Puedes ver todos los precios en el menú. Algunos ejemplos:\n• Pollo asado entero: $42.000\n• Medio pollo: $24.000\n• Combo familiar: $62.000'
    },
    {
        palabras: ['agotado','no disponible','sin stock','acabado'],
        respuesta: '😔 Cuando un producto aparece con la etiqueta <strong>"Agotado"</strong> significa que temporalmente no está disponible. El equipo de cocina lo reactiva cuando los insumos se reponen.'
    },
    {
        palabras: ['popular','recomendado','mejor','especial','favorito'],
        respuesta: '⭐ Los productos marcados con la etiqueta <strong>"Popular"</strong> son los más pedidos y recomendados por nuestros clientes. ¡Son una excelente opción!'
    },

    // CARRITO
    {
        palabras: ['carrito','agregar','añadir','quitar','eliminar del carrito'],
        respuesta: '🛒 Para gestionar tu carrito:\n• <strong>Agregar</strong>: clic en el botón "Agregar" de cada producto\n• <strong>Aumentar/disminuir</strong>: usa los botones + y − en el carrito\n• <strong>Eliminar</strong>: clic en el botón ✕ junto al producto'
    },
    {
        palabras: ['nota','instruccion','instrucción','especial','sin cebolla','termino','término'],
        respuesta: '📝 Puedes agregar una <strong>nota especial</strong> a cada producto (sin sal, término de cocción, sin cebolla, etc.) en el campo "Nota especial" del carrito. Esta nota llega directamente a cocina.'
    },

    // PAGO
    {
        palabras: ['pago','pagar','metodo','método','efectivo','tarjeta','nequi','daviplata','billetera'],
        respuesta: '💳 Aceptamos los siguientes métodos de pago:\n• 💵 Efectivo\n• 💳 Tarjeta débito\n• 💳 Tarjeta crédito\n• 📱 Billetera digital (Nequi / Daviplata)'
    },

    // CUENTA Y REGISTRO
    {
        palabras: ['registrar','registro','crear cuenta','nueva cuenta','como me registro','cómo me registro'],
        respuesta: '👤 Para registrarte:\n1. Haz clic en <strong>"Ingresar"</strong> en la barra superior\n2. Selecciona la pestaña <strong>"Registrarse"</strong>\n3. Completa tus datos y crea tu cuenta\n¡Al registrarte tendrás acceso al historial de pedidos!'
    },
    {
        palabras: ['contraseña','contrasena','olvide','olvidé','recuperar','restablecer'],
        respuesta: '🔑 Si olvidaste tu contraseña:\n1. Ve a la pantalla de login\n2. Haz clic en <strong>"¿Olvidaste tu contraseña?"</strong>\n3. Ingresa tu correo y sigue las instrucciones para restablecerla.'
    },
    {
        palabras: ['iniciar sesion','iniciar sesión','login','entrar','acceder','ingresar'],
        respuesta: '🔐 Para iniciar sesión:\n1. Haz clic en <strong>"Ingresar"</strong> en la barra superior\n2. Ingresa tu correo y contraseña\n3. ¡Listo! Serás redirigido a tu panel.'
    },
    {
        palabras: ['cuenta inactiva','bloqueado','no puedo entrar','acceso denegado'],
        respuesta: '⚠️ Si tu cuenta está inactiva, comunícate con el administrador del asadero para que reactive tu acceso.'
    },

    // MESAS Y DOMICILIO
    {
        palabras: ['mesa','mesas','numero de mesa','número de mesa','donde siento','dónde me siento'],
        respuesta: '🪑 Al confirmar tu pedido puedes seleccionar <strong>"En Mesa"</strong> e ingresar el número de tu mesa. El pedido llegará directamente a tu mesa.'
    },
    {
        palabras: ['domicilio','delivery','a casa','envio','envío','llevar','para llevar'],
        respuesta: '🥡 Selecciona la opción <strong>"Para Llevar"</strong> al confirmar tu pedido. El pedido estará listo para que lo recojas en el local.'
    },

    // SALUDOS
    {
        palabras: ['hola','buenas','buenos dias','buenos días','buenas tardes','buenas noches','hey','hi'],
        respuesta: '👋 ¡Hola! Soy el asistente virtual de <strong>La Parrilla</strong>. ¿En qué puedo ayudarte hoy?\n\nPuedes preguntarme sobre:\n• 🍗 Menú y productos\n• 🛒 Cómo hacer un pedido\n• 💳 Métodos de pago\n• 📍 Horario y ubicación\n• 👤 Tu cuenta'
    },
    {
        palabras: ['gracias','thank','perfecto','excelente','genial','listo','ok','okay'],
        respuesta: '😊 ¡Con gusto! Si tienes más preguntas, aquí estaré. ¡Buen provecho!'
    },
    {
        palabras: ['adios','adiós','chao','bye','hasta luego','nos vemos'],
        respuesta: '👋 ¡Hasta pronto! Gracias por visitarnos. ¡Te esperamos en La Parrilla!'
    },
    {
        palabras: ['ayuda','help','no entiendo','no sé','no se','que puedes','qué puedes'],
        respuesta: '🤖 Puedo ayudarte con:\n• 🍗 Información del menú\n• 🛒 Cómo hacer pedidos\n• 💳 Métodos de pago\n• 📍 Horario y ubicación\n• 👤 Registro e inicio de sesión\n• 🔑 Recuperar contraseña\n• 🪑 Pedidos en mesa o para llevar\n\n¿Sobre qué quieres saber?'
    }
];

function obtenerRespuesta(mensaje) {
    const texto = mensaje.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
    for (const item of RESPUESTAS) {
        if (item.palabras.some(p => texto.includes(p.normalize('NFD').replace(/[\u0300-\u036f]/g, '')))) {
            return item.respuesta;
        }
    }
    return '🤔 No entendí bien tu pregunta. Puedes preguntarme sobre el <strong>menú</strong>, <strong>pedidos</strong>, <strong>pagos</strong>, <strong>horario</strong> o <strong>tu cuenta</strong>. También puedes llamarnos al <strong>+57 300 123 4567</strong>.';
}

// ─── INICIALIZAR CHATBOX ───
document.addEventListener('DOMContentLoaded', function () {

    // Crear HTML del chatbox
    const html = `
    <div id="chatbox-btn" onclick="toggleChat()" title="Asistente virtual">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span id="chat-notif">1</span>
    </div>

    <div id="chatbox-panel">
        <div id="chat-header">
            <div style="display:flex;align-items:center;gap:10px">
                <div style="width:36px;height:36px;border-radius:50%;background:rgba(240,112,0,.2);border:1px solid rgba(240,112,0,.4);display:flex;align-items:center;justify-content:center;font-size:18px">🔥</div>
                <div>
                    <div style="font-weight:700;font-size:14px">Asistente La Parrilla</div>
                    <div style="font-size:11px;color:#64dc82;display:flex;align-items:center;gap:4px"><span style="width:6px;height:6px;border-radius:50%;background:#64dc82;display:inline-block"></span>En línea</div>
                </div>
            </div>
            <button onclick="toggleChat()" style="background:none;border:none;color:#8a8078;font-size:18px;cursor:pointer;line-height:1">✕</button>
        </div>
        <div id="chat-messages"></div>
        <div id="chat-sugerencias">
            <button onclick="enviarSugerencia('¿Cuál es el horario?')">🕐 Horario</button>
            <button onclick="enviarSugerencia('¿Cómo hago un pedido?')">🛒 Pedidos</button>
            <button onclick="enviarSugerencia('¿Qué métodos de pago aceptan?')">💳 Pagos</button>
            <button onclick="enviarSugerencia('Ver el menú')">🍗 Menú</button>
        </div>
        <div id="chat-input-area">
            <input type="text" id="chat-input" placeholder="Escribe tu pregunta..." onkeydown="if(event.key==='Enter')enviarMensaje()">
            <button onclick="enviarMensaje()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
            </button>
        </div>
    </div>`;

    const wrapper = document.createElement('div');
    wrapper.innerHTML = html;
    document.body.appendChild(wrapper);

    // Mensaje de bienvenida
    setTimeout(() => {
        agregarMensaje('bot', '👋 ¡Hola! Soy el asistente de <strong>La Parrilla</strong>. ¿En qué puedo ayudarte?');
        document.getElementById('chat-notif').style.display = 'flex';
    }, 1500);
});

function toggleChat() {
    const panel = document.getElementById('chatbox-panel');
    const notif = document.getElementById('chat-notif');
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) {
        notif.style.display = 'none';
        document.getElementById('chat-input').focus();
    }
}

function agregarMensaje(tipo, texto) {
    const msgs = document.getElementById('chat-messages');
    const div  = document.createElement('div');
    div.className = 'chat-msg chat-msg-' + tipo;
    div.innerHTML = texto.replace(/\n/g, '<br>');
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function mostrarTyping() {
    const msgs = document.getElementById('chat-messages');
    const div  = document.createElement('div');
    div.className = 'chat-msg chat-msg-bot chat-typing';
    div.id = 'typing-indicator';
    div.innerHTML = '<span></span><span></span><span></span>';
    msgs.appendChild(div);
    msgs.scrollTop = msgs.scrollHeight;
}

function quitarTyping() {
    const t = document.getElementById('typing-indicator');
    if (t) t.remove();
}

function enviarMensaje() {
    const input = document.getElementById('chat-input');
    const texto = input.value.trim();
    if (!texto) return;
    input.value = '';
    agregarMensaje('user', texto);
    mostrarTyping();
    setTimeout(() => {
        quitarTyping();
        agregarMensaje('bot', obtenerRespuesta(texto));
    }, 700);
}

function enviarSugerencia(texto) {
    agregarMensaje('user', texto);
    mostrarTyping();
    setTimeout(() => {
        quitarTyping();
        agregarMensaje('bot', obtenerRespuesta(texto));
    }, 700);
}
