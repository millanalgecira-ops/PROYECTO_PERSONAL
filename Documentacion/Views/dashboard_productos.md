# productos.php

Panel para gestionar el catálogo de productos del menú.

## Qué tiene

Una tabla con todos los productos mostrando nombre, descripción corta, categoría, precio, si es popular y si está disponible o agotado.

Hay un buscador en tiempo real que filtra la tabla mientras escribes, sin recargar la página.

## Acciones por producto

- **Editar** — abre un modal con los datos actuales para modificarlos
- **Agotar / Activar** — cambia la disponibilidad del producto en el catálogo
- **Eliminar** — borra el producto permanentemente (pide confirmación antes)

## Campos al crear o editar

Nombre, descripción, URL de imagen, precio, categoría, y dos checkboxes: uno para marcarlo como popular y otro para indicar si está disponible.

## Impacto inmediato

Cualquier cambio se ve de inmediato en el menú público porque los productos se cargan desde la BD cada vez que alguien entra a la página.
