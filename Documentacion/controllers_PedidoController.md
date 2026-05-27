# controllers/PedidoController.php

## Descripción
Controlador compartido entre Administrador y Cocina para gestionar el estado de los pedidos. Permite cambiar el estado de una comanda y cancelar pedidos activos.

---

## Ubicación
```
Controllers/PedidoController.php
```

---

## Control de acceso
Accesible para roles `administrador` y `cocina`.

```php
if (!in_array($_SESSION['usuario']['rol'], ['administrador', 'cocina'])) { ... }
```

---

## Acciones disponibles

### `cambiarEstado` — POST

Actualiza el estado de un pedido y registra el cambio en el historial.

#### Campos POST

| Campo    | Descripción                                                    |
|----------|----------------------------------------------------------------|
| `id`     | ID del pedido                                                  |
| `estado` | Nuevo estado del pedido                                        |

#### Estados válidos

```
Recibido → En preparacion → Listo → Entregado → Pagado → Cancelado
```

#### Flujo
1. Valida que el estado sea uno de los permitidos
2. Actualiza el campo `estado` en la tabla `pedidos`
3. Inserta un registro en `pedido_estados_historial` con el usuario que hizo el cambio
4. Guarda alerta de éxito en sesión
5. Redirige según el rol:
   - `cocina` → `Views/dashboard/cocina.php`
   - `administrador` → `Views/dashboard/pedidos.php`

---

### `cancelar` — GET

Cancela un pedido activo. Solo disponible para el administrador.

#### Parámetros GET

| Parámetro | Descripción     |
|-----------|-----------------|
| `id`      | ID del pedido   |

#### Flujo
1. Verifica que el rol sea `administrador`
2. Actualiza el estado a `Cancelado` y registra `cancelado_por` con el ID del admin
3. Libera la mesa asociada al pedido (si aplica) → estado `Disponible`
4. Redirige a `Views/dashboard/pedidos.php`

#### Restricción
No cancela pedidos que ya estén en estado `Pagado` o `Cancelado`.

---

## Tabla `pedido_estados_historial`

Cada cambio de estado queda registrado con:
- `pedido_id` — ID del pedido
- `estado` — Nuevo estado
- `cambiado_por` — ID del usuario que hizo el cambio
- `cambiado_en` — Fecha y hora del cambio

---

## Historias de usuario relacionadas
- **CO-012** — Cocina cambia el estado del pedido según su etapa de elaboración
- **AD-017** — Administrador actualiza y cancela pedidos manualmente
