# controllers/CarritoController.php

## Descripción
Controlador que procesa los pedidos enviados desde el carrito del cliente. Recibe los datos en formato JSON, crea el pedido en la BD, registra el pago y el ingreso, y retorna la respuesta al frontend.

---

## Ubicación
```
Controllers/CarritoController.php
```

---

## Tipo de petición
`POST` con `Content-Type: application/json`

---

## Datos recibidos (JSON)

| Campo            | Tipo     | Descripción                                      |
|------------------|----------|--------------------------------------------------|
| `cart`           | `array`  | Lista de productos del carrito                   |
| `tipo`           | `string` | `"En mesa"` o `"Para llevar"`                    |
| `mesa_numero`    | `int`    | Número de mesa (si aplica)                       |
| `nombre_cliente` | `string` | Nombre del cliente                               |
| `nota_especial`  | `string` | Instrucciones especiales del pedido              |
| `metodo_pago`    | `string` | `Efectivo`, `Tarjeta debito`, etc.               |

### Estructura de cada ítem en `cart`

| Campo    | Descripción                    |
|----------|--------------------------------|
| `id`     | ID del producto                |
| `nombre` | Nombre del producto            |
| `precio` | Precio (número o string)       |
| `qty`    | Cantidad solicitada            |

---

## Flujo paso a paso

1. Decodifica el JSON recibido
2. Valida que el carrito no esté vacío
3. Obtiene `cliente_id` de la sesión (si hay sesión activa de cliente)
4. Busca el `mesa_id` por número de mesa y la marca como `Ocupada`
5. Calcula el total sumando `precio × cantidad` de cada ítem
6. Genera un número de orden único de 8 caracteres (ej: `A3F9B2C1`)
7. Inserta el pedido en la tabla `pedidos` con estado `Recibido`
8. Inserta cada ítem en `pedido_items` buscando el `producto_id` por nombre
9. Registra el estado inicial en `pedido_estados_historial`
10. Registra el pago en la tabla `pagos`
11. Registra el ingreso en la tabla `ingresos`
12. Retorna JSON con `success: true` y el número de orden

---

## Respuesta exitosa

```json
{
    "success": true,
    "numero_orden": "A3F9B2C1",
    "pedido_id": 15,
    "total": 76000
}
```

## Respuesta de error

```json
{
    "success": false,
    "message": "Descripción del error"
}
```

---

## Tablas afectadas

| Tabla                       | Operación |
|-----------------------------|-----------|
| `pedidos`                   | INSERT    |
| `pedido_items`              | INSERT    |
| `pedido_estados_historial`  | INSERT    |
| `pagos`                     | INSERT    |
| `ingresos`                  | INSERT    |
| `mesas`                     | UPDATE    |

---

## Historias de usuario relacionadas
- **CL-008** — El cliente confirma y envía el pedido a cocina
- **AD-019** — El pago queda registrado automáticamente
- **AD-020** — El ingreso queda registrado automáticamente
