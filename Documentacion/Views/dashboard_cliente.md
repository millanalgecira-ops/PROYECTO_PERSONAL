# cliente.php

Panel del cliente después de iniciar sesión. Tiene tres vistas que se manejan con el parámetro `?vista=` en la URL.

## Inicio

Pantalla de bienvenida con el nombre del cliente y un botón para ir al menú.

## Mis Pedidos

Historial de todos los pedidos del cliente ordenados por fecha descendente. Muestra número de orden, fecha, tipo, total y estado con badge de color.

Tiene filtros para organizar los pedidos:
- Por estado (Recibido, En preparación, Listo, Entregado, Pagado, Cancelado)
- Por fecha específica

Si no hay pedidos muestra el mensaje: *"Aún no tienes pedidos. ¡Explora el catálogo y haz tu primer pedido!"*

## Detalle del pedido

Al hacer clic en "Ver detalle" de cualquier pedido muestra la información completa: ítems con cantidades y precios, notas especiales, método de pago y total final.
