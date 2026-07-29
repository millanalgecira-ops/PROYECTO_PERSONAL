# AdminUsuarioController.php

Este controller es solo para el administrador. Permite crear, editar y activar/desactivar usuarios del sistema (administrador y cocina). Si alguien que no sea administrador intenta acceder, lo manda al login.

## crear()

Crea un nuevo usuario del sistema. El administrador elige el rol desde el formulario.

Roles disponibles:
- `administrador` → ID 1
- `cocina` → ID 2
- `cliente` → ID 3

## editar()

Actualiza los datos de un usuario existente. Si se envía una nueva contraseña la hashea y la actualiza, si el campo viene vacío deja la contraseña actual sin cambios.

## toggleEstado()

Activa o desactiva un usuario. Funciona al revés del estado actual: si está activo lo desactiva, si está inactivo lo activa. Lo uso para el botón "Activar/Desactivar" en la tabla de usuarios del panel admin.

## Notas

- Solo el administrador puede usar este controller
- Los usuarios desactivados no pueden iniciar sesión
- Para crear clientes se usa `UsuarioControllers.php`, no este
