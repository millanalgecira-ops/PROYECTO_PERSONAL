# cocina.php

Panel exclusivo para el personal de cocina. Tiene dos secciones: Comandas y Productos.

## Comandas

Muestra todas las comandas activas (pedidos que no están pagados ni cancelados) en tarjetas visuales. Cada tarjeta tiene el número de orden, tiempo transcurrido desde que llegó, tipo de pedido, mesa si aplica, nombre del cliente, lista de ítems con cantidades y notas especiales.

Los pedidos se ordenan por estado y hora de llegada para que los más urgentes estén primero.

**Filtros:** se puede filtrar por estado para ver solo los Recibidos, los que están En preparación, los Listos o los Entregados.

**Botones de acción según estado:**
- Recibido → "Iniciar preparación"
- En preparación → "Marcar como listo"
- Listo → "Marcar como entregado"

Cada cambio de estado genera una alerta visual en la esquina de la pantalla para confirmar la acción.

El panel se actualiza automáticamente cada 30 segundos.

## Productos

Lista todos los productos con su estado actual. Desde aquí cocina puede:
- Reportar un producto como agotado cuando se acaban los insumos — el producto desaparece del catálogo del cliente de inmediato
- Reactivar un producto cuando los insumos se reponen
