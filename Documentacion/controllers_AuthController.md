# controllers/AuthController.php

## Descripción
Controlador encargado de gestionar la autenticación del sistema: inicio de sesión y cierre de sesión para todos los roles (Administrador, Cocina y Cliente).

---

## Ubicación
```
Controllers/AuthController.php
```

---

## Dependencias
```php
require_once Config/database.php
require_once Models/usuario.php
```

---

## Clase: `AuthController`

### Método: `login()`

#### Descripción
Procesa el formulario de inicio de sesión. Busca primero en la tabla `usuarios` (Administrador/Cocina) y luego en `clientes`.

#### Flujo paso a paso

1. Verifica que la petición sea `POST`, si no redirige al login
2. Valida que `email` y `password` no estén vacíos
3. Valida formato de correo con `filter_var()`
4. Busca en tabla `usuarios`:
   - Si existe → verifica contraseña con `password_verify()`
   - Obtiene el nombre del rol desde la tabla `roles`
   - Guarda sesión y redirige según rol:
     - `administrador` → `Views/dashboard/admin.php`
     - `cocina` → `Views/dashboard/cocina.php`
5. Si no está en `usuarios`, busca en tabla `clientes`:
   - Verifica si existe pero está inactivo → mensaje específico
   - Si no existe → mensaje de credenciales incorrectas
   - Si existe → verifica contraseña
   - Guarda sesión y redirige a `Views/dashboard/cliente.php`

#### Datos guardados en sesión
```php
$_SESSION['usuario'] = [
    'id_usuario' => int,
    'nombre'     => string,
    'correo'     => string,
    'id_rol'     => int,
    'rol'        => string  // 'administrador' | 'cocina' | 'cliente'
];
```

#### Mensajes de error

| Situación                  | Mensaje mostrado                                          |
|----------------------------|-----------------------------------------------------------|
| Campos vacíos              | "Debe ingresar correo y contraseña"                       |
| Correo inválido            | "Ingrese un correo electrónico válido"                    |
| Contraseña incorrecta      | "Correo o contraseña incorrectos"                         |
| Cuenta inactiva            | "Tu cuenta está inactiva. Contacta al administrador."     |
| Usuario no encontrado      | "Correo o contraseña incorrectos"                         |

---

### Método: `logout()`

#### Descripción
Cierra la sesión activa del usuario.

#### Flujo
1. Ejecuta `session_unset()` para limpiar variables de sesión
2. Ejecuta `session_destroy()` para destruir la sesión
3. Redirige a `Views/usuarios/login.php`

---

## Ejecución del controlador

```php
$accion = $_GET['accion'] ?? 'login';

if ($accion === 'logout') {
    $controller->logout();
} else {
    $controller->login();
}
```

### URLs de acceso

| Acción  | URL                                              |
|---------|--------------------------------------------------|
| Login   | `Controllers/AuthController.php`                 |
| Logout  | `Controllers/AuthController.php?accion=logout`   |

---

## Seguridad implementada
- `password_verify()` para comparar contraseñas hasheadas
- `session_regenerate_id(true)` al iniciar sesión para prevenir session fixation
- Validación de formato de correo antes de consultar la BD
- Consultas con parámetros preparados (PDO) para prevenir SQL injection
