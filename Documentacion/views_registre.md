# views/usuarios/registre.php

## Descripción
Formulario de registro público para nuevos clientes del asadero. Al registrarse exitosamente, el sistema inicia sesión automáticamente.

---

## Ubicación
```
Views/usuarios/registre.php
```

---

## Funcionalidades

### Formulario de registro
Envía los datos a `Controllers/UsuarioControllers.php` por método POST.

| Campo                | Tipo       | Descripción                        |
|----------------------|------------|------------------------------------|
| `nombres`            | text       | Nombres del cliente                |
| `apellidos`          | text       | Apellidos del cliente              |
| `email`              | email      | Correo electrónico                 |
| `password`           | password   | Contraseña (mínimo 6 caracteres)   |
| `confirmar_password` | password   | Confirmación de contraseña         |

### Alertas
Muestra mensajes de error o éxito desde `$_SESSION['alert']`.

### Enlace al login
- Botón "Inicia sesión" → redirige a `login.php`

---

## Diseño
Mismo diseño que `login.php`: dos columnas, branding izquierda, formulario derecha.

---

## Historia de usuario relacionada
**CL-001** — Registro exitoso con sesión automática y redirección al catálogo.
