# registre.php

Formulario de registro para nuevos clientes. Solo los clientes se registran desde aquí — el personal del asadero lo crea el administrador desde su panel.

## Campos

- Nombres
- Apellidos
- Correo electrónico
- Contraseña (mínimo 6 caracteres)
- Confirmar contraseña

## Comportamiento

Al registrarse exitosamente el sistema inicia sesión automáticamente y redirige al panel del cliente. No tiene sentido que alguien se registre y luego tenga que volver a poner sus datos para entrar.

Si hay algún error (correo ya registrado, contraseñas no coinciden, etc.) muestra el mensaje correspondiente y deja el formulario disponible para corregir.

## Envía a

`Controllers/UsuarioControllers.php` por método POST
