# ProductoController.php

Maneja todo lo del catálogo de productos desde el panel del administrador. Solo el administrador puede usarlo.

## Acciones

**crear**
Recibe los datos del formulario y agrega un producto nuevo a la BD. Los campos obligatorios son nombre, precio y categoría. La imagen es opcional — si no se pone, el sistema usa una imagen por defecto según la posición del producto.

**editar**
Actualiza un producto existente. Recibe el ID del producto más los mismos campos que crear.

**eliminar**
Borra un producto permanentemente. Esta acción no se puede deshacer, por eso en la vista le puse un modal de confirmación antes de ejecutarla.

**toggleDisponible**
Cambia el estado del producto entre disponible y agotado. Lo usa tanto el administrador desde el panel de productos como el personal de cocina desde su panel. Cuando un producto se marca como agotado desaparece el botón de agregar en el catálogo del cliente.

## Impacto en el catálogo

Cualquier cambio que se haga aquí se refleja de inmediato en la página principal porque los productos se cargan dinámicamente desde la BD cada vez que alguien entra al menú.
