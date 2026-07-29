# login.php

Pantalla de acceso al sistema para el personal del asadero. También la usan los clientes cuando intentan entrar desde el modal de la página principal.

## Diseño

Dos columnas: a la izquierda el branding con el nombre del asadero, a la derecha el formulario. En móvil el panel izquierdo se oculta y solo queda el formulario.

## Qué tiene

- Campo de correo electrónico
- Campo de contraseña con botón para mostrar/ocultar
- Link "¿Olvidaste tu contraseña?" que lleva a `recuperar.php`
- Botón para ir al registro
- Muestra alertas de error cuando las credenciales son incorrectas o la cuenta está inactiva

## Redirección automática

Si el usuario ya tiene sesión activa lo manda directo a su panel según el rol, para que no tenga que volver a hacer login.

## Envía a

`Controllers/AuthController.php` por método POST
