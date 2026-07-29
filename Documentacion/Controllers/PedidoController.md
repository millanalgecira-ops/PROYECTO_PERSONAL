# PedidoController.php

Maneja los cambios de estado de los pedidos. Lo pueden usar tanto el administrador como el personal de cocina.

## Acciones

**cambiarEstado**
Actualiza el estado de un pedido. Los estados válidos son: Recibido, En preparacion, Listo, Entregado, Pagado, Cancelado.

Cada cambio queda registrado en la tabla `pedido_estados_historial` con el ID del usuario que lo hizo y la fecha. Así siempre hay trazabilidad de quién cambió qué.

Después de cambiar el estado redirige según el rol:
- Si es cocina → vuelve al panel de cocina
- Si es administrador → vuelve al panel de pedidos

**cancelar**
Solo el administrador puede cancelar pedidos. Cuando se cancela un pedido también se libera la mesa asociada automáticamente para que quede disponible de nuevo.

No cancela pedidos que ya estén en estado Pagado o Cancelado.

## Por qué registré el historial

Lo hice porque en las historias de usuario se pedía que el administrador pudiera hacer seguimiento de cada comanda. Con el historial se puede ver exactamente cuándo cambió cada estado y quién lo hizo.
