# confirmacion.php

Página que aparece después de confirmar un pedido exitosamente.

## Qué muestra

- Ícono de check verde
- Mensaje "¡Pedido Recibido!"
- Número de orden en grande (ej: `#A3F9B2C1`)
- Badge animado con el estado actual
- Tarjetas con: tipo de pedido, mesa, método de pago y estado
- Lista detallada de los ítems con cantidades y subtotales
- Total del pedido
- Nota especial si se ingresó alguna
- Botón para volver al menú

## Cómo funciona

Recibe el número de orden por parámetro GET (`?orden=A3F9B2C1`) y consulta la BD para traer todos los datos del pedido. Si el número de orden no existe o no pertenece al cliente, redirige al inicio.
