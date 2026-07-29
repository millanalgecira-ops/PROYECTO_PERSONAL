# ventas.php

Panel de ventas e ingresos para que el administrador tenga visibilidad financiera del negocio.

## Resumen del día

Cuatro tarjetas con: ventas totales del día, pedidos completados, ticket promedio y total del rango de fechas seleccionado.

## Historial de ingresos

Tabla con todos los ingresos registrados. Se puede filtrar por rango de fechas con los campos "Desde" y "Hasta". Al final de la tabla muestra el subtotal del período.

## Cómo se registran los ingresos

Cada vez que un cliente confirma un pedido, el sistema registra automáticamente el ingreso en la tabla `ingresos` con el monto, método de pago y número de orden. No hay que hacerlo manualmente.
