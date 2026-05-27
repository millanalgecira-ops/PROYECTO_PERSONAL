# views/dashboard/productos.php

## Descripción
Panel del administrador para gestionar el catálogo de productos del menú. Permite crear, editar, eliminar y cambiar la disponibilidad de cada producto.

---

## Ubicación
```
Views/dashboard/productos.php
```

---

## Contenido

### Barra de herramientas
- Botón **Añadir Producto** → abre modal de creación
- Campo de búsqueda en tiempo real (filtra la tabla sin recargar)

### Tabla de productos
Muestra todos los productos con: nombre, descripción, categoría, precio, badge popular, estado (Disponible/Agotado) y acciones.

### Acciones por producto

| Acción   | Descripción                                                  |
|----------|--------------------------------------------------------------|
| Editar   | Abre modal con datos actuales del producto                   |
| Agotar   | Marca el producto como no disponible en el catálogo          |
| Activar  | Reactiva un producto agotado                                 |
| Eliminar | Elimina permanentemente el producto (con modal de confirmación)|

### Modal: Nuevo producto
Campos: nombre, descripción, URL de imagen, precio, categoría, checkboxes popular y disponible.

### Modal: Editar producto
Mismo formulario pre-llenado con los datos actuales.

---

## Impacto inmediato
Cualquier cambio se refleja de inmediato en el menú público (`Public/index.php`).

---

## Historia de usuario relacionada
**AD-016** — Gestionar productos del menú.
