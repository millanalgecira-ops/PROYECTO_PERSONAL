# controllers/UsuarioControllers.php

## Descripción
Controlador encargado del registro público de nuevos clientes. Al registrarse exitosamente, inicia sesión automáticamente y redirige al panel del cliente.

---

## Ubicación
```
Controllers/UsuarioControllers.php
```

---

## Dependencias
```php
require_once Config/database.php
require_once Models/usuario.php
```

---

## Clase: `UsuarioControllers`

### Método: `registrar()`

#### Descripción
Procesa el formulario de registro de nuevos clientes. Inserta en la tabla `clientes` (no en `usuarios`).

#### Campos del formulario requeridos

| Campo                | Descripción                        |
|----------------------|------------------------------------|
| `nombres`            | Nombres del cliente                |
| `apellidos`          | Apellidos del cliente              |
| `email`              | Correo electrónico                 |
| `password`           | Contraseña (mínimo 6 caracteres)   |
| `confirmar_password` | Confirmación de contraseña         |

#### Flujo paso a paso

1. Verifica que la petición sea `POST`
2. Recoge y limpia los campos del formulario con `trim()`
3. Combina nombres y apellidos en `$nombre_completo`
4. Valida que todos los campos estén completos
5. Valida formato de correo con `filter_var()`
6. Verifica que las contraseñas coincidan
7. Verifica que la contraseña tenga mínimo 6 caracteres
8. Verifica que el correo no esté ya registrado en `clientes`
9. Hashea la contraseña con `password_hash(PASSWORD_DEFAULT)`
10. Registra el cliente en la BD mediante `Cliente::registrar()`
11. Si es exitoso:
    - Obtiene los datos del cliente recién creado
    - Regenera el ID de sesión
    - Guarda la sesión con rol `cliente`
    - Redirige a `Views/dashboard/cliente.php`
12. Si falla: muestra el error y redirige al formulario

#### Validaciones y mensajes de error

| Validación                    | Mensaje                                        |
|-------------------------------|------------------------------------------------|
| Campos vacíos                 | "Debe completar todos los campos"              |
| Correo inválido               | "Ingrese un correo válido"                     |
| Contraseñas no coinciden      | "Las contraseñas no coinciden"                 |
| Contraseña muy corta          | "La contraseña debe tener al menos 6 caracteres"|
| Correo ya registrado          | "Este correo ya está registrado"               |

---

## Historia de usuario relacionada
**CL-001** — El cliente se registra y es redirigido automáticamente al catálogo con sesión activa.

---

## Notas
- El registro público siempre crea un **Cliente** (tabla `clientes`), nunca un usuario del sistema.
- Para crear Administradores o personal de Cocina se usa `AdminUsuarioController`.
- La contraseña se hashea en el controller antes de pasarla al modelo.
