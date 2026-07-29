# pedidos.php

Panel para ver y gestionar todos los pedidos del asadero en tiempo real.

## Qué muestra

Una tabla con todos los pedidos: número de orden, cliente, tipo (en mesa o para llevar), mesa si aplica, total, estado con badge de color y fecha.

## Filtros

Hay botones para filtrar por estado: Todos, Recibido, En preparación, Listo, Entregado, Pagado, Cancelado.

## Acciones

- **Cambiar estado** — un selector desplegable que actualiza el estado al cambiar. Útil cuando cocina no actualizó el estado desde su panel.
- **Cancelar** — aparece solo en pedidos activos. Al cancelar también libera la mesa automáticamente.

## Colores de estado

Cada estado tiene un color diferente para identificarlos rápido: azul para Recibido, amarillo para En preparación, verde para Listo, naranja para Entregado, verde oscuro para Pagado y rojo para Cancelado.
