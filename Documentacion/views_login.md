# views/usuarios/login.php

## Descripción
Pantalla de acceso al sistema para el personal del asadero (Administrador y Cocina). Diseño de dos columnas: branding a la izquierda y formulario a la derecha.

---

## Ubicación
```
Views/usuarios/login.php
```

---

## URL de acceso
```
http://127.0.0.1:8081/proyecto_personal/Views/usuarios/login.php
```

---

## Funcionalidades

### Redirección automática
Si el usuario ya tiene sesión activa, redirige automáticamente según su rol:

| Rol             | Redirección                        |
|-----------------|------------------------------------|
| `administrador` | `Views/dashboard/admin.php`        |
| `cocina`        | `Views/dashboard/cocina.php`       |
| `cliente`       | `Views/dashboard/cliente.php`      |

### Formulario de login
- **Campo:** Correo electrónico (`email`)
- **Campo:** Contraseña (`password`) con botón para mostrar/ocultar
- **Envía a:** `Controllers/AuthController.php` (método POST)

### Enlace de recuperación
- Link "¿Olvidaste tu contraseña?" → redirige a `recuperar.php`

### Alertas de sesión
Muestra mensajes de error o éxito guardados en `$_SESSION['alert']`.

### Enlace de registro
- Botón "Regístrate aquí" → redirige a `registre.php`

---

## Diseño
- Fondo oscuro `#1a1a1a`
- Panel izquierdo: logo y nombre "La Parrilla" en tipografía Bebas Neue
- Panel derecho: formulario con inputs oscuros y acento naranja `#f07000`
- Responsive: en móvil oculta el panel izquierdo

---

## Historia de usuario relacionada
**CL-002 / AD-015 / CO-010** — Inicio de sesión para todos los roles del sistema.
