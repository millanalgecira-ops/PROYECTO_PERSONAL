# carrito.php

Página del carrito de compras y checkout. Se divide en dos paneles: el carrito a la izquierda y los detalles del pedido a la derecha.

## Panel izquierdo — Tu Pedido

Muestra los productos que el cliente agregó con imagen, nombre, precio y controles de cantidad. Los botones + y − ajustan la cantidad en tiempo real. El botón ✕ elimina el ítem. Si el carrito queda vacío muestra el mensaje correspondiente con un enlace para volver al menú.

El carrito usa `localStorage` del navegador para guardar los productos, así persisten aunque el cliente navegue por otras páginas.

## Panel derecho — Detalles del Pedido

Aparece solo cuando hay productos en el carrito. Tiene:

- **Tipo de pedido** — En Mesa o Para Llevar
- **Número de mesa** — selector con las 10 mesas del restaurante (solo aparece si eligió En Mesa)
- **Nombre del cliente** — se llena automáticamente si hay sesión activa
- **Nota especial** — instrucciones para cocina (sin sal, término de cocción, etc.)
- **Método de pago** — Efectivo, Tarjeta débito, Tarjeta crédito, Billetera digital
- **Resumen mini** — lista de ítems con subtotales y total

## Finalizar pedido

Al confirmar, JavaScript envía los datos al `CarritoController.php` con `fetch()`. Si todo sale bien redirige a la página de confirmación con el número de orden. Si hay algún error muestra un modal con el mensaje.

Las validaciones antes de enviar: nombre no vacío, mesa seleccionada si es en mesa, carrito no vacío.
