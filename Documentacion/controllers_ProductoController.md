# controllers/ProductoController.php

## Descripción
Controlador exclusivo del administrador para gestionar el catálogo de productos. Permite crear, editar, eliminar y cambiar la disponibilidad de productos.

---

## Ubicación
```
Controllers/ProductoController.php
```

---

## Control de acceso
Solo accesible con rol `administrador`.

---

## Acciones disponibles

### `crear` — POST

Inserta un nuevo producto en la tabla `productos`.

#### Campos del formulario

| Campo          | Requerido | Descripción                        |
|----------------|-----------|------------------------------------|
| `nombre`       | ✅        | Nombre del producto                |
| `descripcion`  | ❌        | Descripción del plato              |
| `imagen_url`   | ❌        | URL de la imagen del producto      |
| `precio`       | ✅        | Precio en pesos colombianos        |
| `categoria_id` | ✅        | ID de la categoría                 |
| `popular`      | ❌        | Checkbox: 1 si es popular          |
| `disponible`   | ❌        | Checkbox: 1 si está disponible     |

**Redirige a:** `Views/dashboard/productos.php`

---

### `editar` — POST

Actualiza un producto existente.

#### Campos adicionales

| Campo | Descripción          |
|-------|----------------------|
| `id`  | ID del producto a editar |

Mismos campos que `crear` más el `id`.

**Redirige a:** `Views/dashboard/productos.php`

---

### `eliminar` — GET

Elimina permanentemente un producto de la BD.

#### Parámetros GET

| Parámetro | Descripción       |
|-----------|-------------------|
| `id`      | ID del producto   |

> ⚠️ Esta acción no se puede deshacer.

**Redirige a:** `Views/dashboard/productos.php`

---

### `toggleDisponible` — GET

Cambia el estado de disponibilidad de un producto (disponible ↔ agotado).

#### Parámetros GET

| Parámetro | Descripción                    |
|-----------|--------------------------------|
| `id`      | ID del producto                |
| `estado`  | Estado actual (0 o 1)          |

**Lógica:** Si `estado == 1` → pone `0` (agotado). Si `estado == 0` → pone `1` (disponible).

**Redirige a:** `Views/dashboard/productos.php`

---

## Impacto en el catálogo público

Cualquier cambio realizado desde este controlador se refleja **inmediatamente** en el menú público (`Public/index.php`) ya que los productos se cargan dinámicamente desde la BD.

---

## Historia de usuario relacionada
**AD-016** — Gestionar productos del menú (crear, editar, desactivar, reactivar, listar).
