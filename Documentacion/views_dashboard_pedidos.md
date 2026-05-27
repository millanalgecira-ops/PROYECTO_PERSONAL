# views/dashboard/pedidos.php

## Descripción
Panel del administrador para monitorear y gestionar todos los pedidos del asadero en tiempo real.

---

## Ubicación
```
Views/dashboard/pedidos.php
```

---

## Contenido

### Filtros por estado
Botones para filtrar pedidos por: Todos, Recibido, En preparacion, Listo, Entregado, Pagado, Cancelado.

### Tabla de pedidos
Muestra todos los pedidos con: número de orden, cliente, tipo, mesa, total, estado (badge de color), fecha y acción.

### Acciones por pedido

| Acción          | Descripción                                                    |
|-----------------|----------------------------------------------------------------|
| Cambiar estado  | Selector desplegable que actualiza el estado al cambiar        |
| Cancelar        | Cancela el pedido y libera la mesa (con modal de confirmación) |

El botón **Cancelar** solo aparece en pedidos que no estén en estado `Pagado` o `Cancelado`.

---

## Colores de estado

| Estado          | Color    |
|-----------------|----------|
| Recibido        | Azul     |
| En preparacion  | Amarillo |
| Listo           | Verde    |
| Entregado       | Naranja  |
| Pagado          | Verde oscuro |
| Cancelado       | Rojo     |

---

## Historia de usuario relacionada
**AD-017** — Gestionar pedidos del asadero en tiempo real.
