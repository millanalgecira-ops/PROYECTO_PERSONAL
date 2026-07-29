# mesas.php

Panel para ver el estado de las mesas del restaurante en tiempo real.

## Qué muestra

Tres tarjetas con el total de mesas, cuántas están disponibles y cuántas ocupadas. Debajo un mapa visual con todas las mesas en tarjetas de colores: verde para disponibles y rojo para ocupadas.

## Acciones

- **Ver pedido activo** — si una mesa está ocupada y tiene pedidos activos, al hacer clic muestra un modal con el detalle del pedido: número de orden, estado, tipo, total, hora e ítems.
- **Liberar mesa** — cambia el estado de una mesa ocupada a disponible y registra la fecha y hora.
- **Liberar todas** — libera todas las mesas de una vez. Útil al cerrar el turno.
