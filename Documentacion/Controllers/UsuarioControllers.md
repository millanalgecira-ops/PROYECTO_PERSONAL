# UsuarioControllers.php

Maneja el registro público de clientes. Cuando alguien se registra desde la página principal, los datos van a la tabla `clientes`, no a `usuarios`.

## registrar()

Recibe los datos del formulario de registro y crea la cuenta.

**Validaciones que hice:**
- Todos los campos obligatorios deben estar completos
- El correo debe tener formato válido
- Las contraseñas deben coincidir
- La contraseña debe tener mínimo 6 caracteres
- El correo no puede estar ya registrado

**Si todo está bien:**
1. Hashea la contraseña con `password_hash()`
2. Inserta el cliente en la BD
3. Inicia sesión automáticamente (para que no tenga que volver a hacer login)
4. Redirige al panel del cliente

Decidí iniciar sesión automáticamente al registrarse porque me pareció mejor experiencia de usuario — no tiene sentido que alguien se registre y luego tenga que volver a poner sus datos para entrar.
