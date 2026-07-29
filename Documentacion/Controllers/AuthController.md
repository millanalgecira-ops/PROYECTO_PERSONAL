# AuthController.php

Este es el controller que maneja el login y el logout. Decidí que buscara primero en la tabla `usuarios` (personal del asadero) y si no encuentra, busca en `clientes`. Así con un solo formulario de login entran todos los roles.

## login()

Recibe el correo y la contraseña del formulario y los valida.

**Flujo:**

1. Valida que los campos no estén vacíos
2. Valida que el correo tenga formato válido
3. Busca en `usuarios` (Administrador / Cocina)
   - Si existe y la contraseña es correcta → guarda sesión y redirige según el rol
   - Si la contraseña es incorrecta → mensaje de error
4. Si no está en `usuarios`, busca en `clientes`
   - Si existe pero está inactivo → mensaje específico: *"Tu cuenta está inactiva. Contacta al administrador."*
   - Si no existe → mensaje genérico de credenciales incorrectas
   - Si existe y la contraseña es correcta → redirige al panel del cliente

**Redirecciones según rol:**

| Rol             | Destino                          |
|-----------------|----------------------------------|
| administrador   | `Views/dashboard/admin.php`      |
| cocina          | `Views/dashboard/cocina.php`     |
| cliente         | `Views/dashboard/cliente.php`    |

**Datos que guarda en sesión:**
```php
$_SESSION['usuario'] = [
    'id_usuario', 'nombre', 'correo', 'id_rol', 'rol'
]
```

## logout()

Destruye la sesión y redirige al login. Simple.

## Seguridad

- Las contraseñas se verifican con `password_verify()`, nunca se comparan en texto plano
- Al iniciar sesión se regenera el ID de sesión con `session_regenerate_id(true)` para evitar ataques de fijación de sesión
- Todas las consultas usan parámetros preparados
