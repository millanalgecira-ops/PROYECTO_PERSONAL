# index.php (Página principal)

Es la página pública del asadero. Cualquier persona puede verla sin necesidad de iniciar sesión, pero para agregar productos al carrito u ordenar sí se requiere cuenta.

## Secciones

**Hero** — imagen de fondo con el nombre del asadero, botones para ver el menú y ordenar, horario y dirección.

**Menú** — catálogo de productos cargado desde la BD. Tiene filtros por categoría (Res, Pollo, Cerdo, Combos, Acompañamientos, Bebidas). Los productos agotados aparecen con opacidad reducida y sin botón de agregar. Los populares tienen una etiqueta naranja.

**Promociones** — tarjetas con las ofertas del asadero.

**Nosotros** — historia del asadero.

**Contacto** — teléfono, correo y dirección.

## Modal de login/registro

El botón "Ingresar" de la barra de navegación abre un modal con dos pestañas: una para iniciar sesión y otra para registrarse. Así el usuario no tiene que salir de la página para autenticarse.

Si el usuario ya tiene sesión, el botón muestra su nombre y lleva directo a su panel.

## Chatbox

Hay un botón flotante naranja en la esquina inferior derecha que abre el asistente virtual. Responde preguntas sobre horario, menú, pedidos, pagos, registro y más.

## CSS y JS externos

El CSS está en `Public/css/index.css` y el chatbox en `Public/css/chatbox.css` y `Public/js/chatbox.js`. Los separé del HTML para que el archivo no quedara demasiado largo.
