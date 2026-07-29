# usuario.php

Este archivo tiene dos clases: `Usuario` y `Cliente`. Las separé porque en la base de datos el personal del asadero (administrador y cocina) va en la tabla `usuarios`, mientras que los clientes que se registran desde la app van en la tabla `clientes`. Son tablas distintas con estructuras distintas.

## Clase Usuario

Maneja todo lo relacionado con el personal interno del asadero.

**Tabla:** `usuarios`

### Métodos

**existeCorreo($email)**
Revisa si ya hay alguien registrado con ese correo. Lo uso antes de crear un usuario nuevo para evitar duplicados.

**obtenerPorEmail($email)**
Busca un usuario activo por su correo. Solo devuelve usuarios con `activo = 1`, así que si el admin desactivó a alguien, no puede entrar.

**registrar($datos)**
Inserta un nuevo usuario. La contraseña ya llega hasheada desde el controller, el modelo solo la guarda.

**obtenerTodos()**
Trae todos los usuarios con su nombre de rol haciendo un JOIN con la tabla `roles`. Lo uso en el panel del administrador para mostrar la lista.

**obtenerPorId($id)**
Busca un usuario específico por ID. Lo uso cuando el admin va a editar a alguien.

**actualizar($id, $datos)**
Actualiza nombre, correo y rol. Si se envió una nueva contraseña también la actualiza, si no la deja igual.

**cambiarEstado($id, $activo)**
Activa o desactiva un usuario. Cuando se desactiva, el usuario ya no puede iniciar sesión.

---

## Clase Cliente

Maneja los clientes que se registran públicamente desde la página.

**Tabla:** `clientes`

### Métodos

**existeCorreo($email)**
Igual que en Usuario, verifica duplicados antes de registrar.

**registrar($datos)**
Inserta el cliente en la tabla `clientes`. Esta tabla no tiene `rol_id` ni `telefono` porque los clientes no son personal del asadero.

**obtenerPorEmail($email)**
Busca un cliente activo por correo para el proceso de login.
