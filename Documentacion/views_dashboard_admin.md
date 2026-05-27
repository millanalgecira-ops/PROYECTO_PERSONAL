# views/dashboard/admin.php

## Descripción
Panel principal del administrador. Muestra estadísticas de usuarios y la tabla completa de usuarios del sistema con opciones de gestión.

---

## Ubicación
```
Views/dashboard/admin.php
```

---

## Control de acceso
Solo accesible con rol `administrador`.

---

## Contenido

### Estadísticas
- Total de usuarios registrados
- Usuarios activos
- Usuarios inactivos

### Tabla de usuarios
Muestra todos los usuarios con: nombre, correo, rol (badge de color), estado y acciones.

### Acciones por usuario
| Acción      | Descripción                                      |
|-------------|--------------------------------------------------|
| Editar      | Abre modal para modificar datos del usuario      |
| Activar     | Activa un usuario inactivo                       |
| Desactivar  | Desactiva un usuario activo                      |

### Modal: Nuevo usuario
Formulario para crear usuarios del sistema con campos: nombres, apellidos, correo, teléfono, contraseña y rol.

### Modal: Editar usuario
Formulario pre-llenado con los datos actuales del usuario seleccionado.

---

## Sidebar — Módulos de gestión

| Módulo           | Enlace              |
|------------------|---------------------|
| Dashboard        | `admin.php`         |
| Usuarios         | `admin.php`         |
| Productos        | `productos.php`     |
| Pedidos          | `pedidos.php`       |
| Mesas            | `mesas.php`         |
| Ventas e Ingresos| `ventas.php`        |

---

## Historia de usuario relacionada
**AD-021** — Gestionar usuarios y operarios del sistema.
