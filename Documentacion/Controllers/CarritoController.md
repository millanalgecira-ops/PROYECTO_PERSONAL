# CarritoController.php

Este controller procesa los pedidos que vienen del carrito. A diferencia de los otros controllers, este recibe los datos en formato JSON porque el carrito funciona con JavaScript en el frontend.

## Cómo funciona

El carrito guarda los productos en `localStorage` del navegador. Cuando el cliente confirma el pedido, JavaScript envía todo al servidor con `fetch()` en formato JSON y este controller lo procesa.

**Lo que hace paso a paso:**

1. Recibe el JSON con los productos, tipo de pedido, mesa, nombre del cliente, nota especial y método de pago
2. Si hay sesión activa de cliente, asocia el pedido a su cuenta
3. Si el pedido es en mesa, busca el ID de la mesa y la marca como Ocupada
4. Calcula el total sumando precio × cantidad de cada ítem
5. Genera un número de orden único de 8 caracteres (ej: `A3F9B2C1`)
6. Inserta el pedido en la tabla `pedidos`
7. Inserta cada ítem en `pedido_items`
8. Registra el estado inicial en `pedido_estados_historial`
9. Registra el pago en `pagos`
10. Registra el ingreso en `ingresos`
11. Devuelve el número de orden al frontend

## Respuesta

Si todo sale bien devuelve:
```json
{ "success": true, "numero_orden": "A3F9B2C1", "pedido_id": 15, "total": 76000 }
```

Si algo falla devuelve:
```json
{ "success": false, "message": "descripción del error" }
```

## Tablas que afecta

`pedidos`, `pedido_items`, `pedido_estados_historial`, `pagos`, `ingresos`, `mesas`
