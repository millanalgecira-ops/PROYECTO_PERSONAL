# views/usuarios/recuperar.php

## Descripción
Página de recuperación de contraseña para clientes y usuarios del sistema. Maneja tres pasos: solicitud, restablecimiento y confirmación.

---

## Ubicación
```
Views/usuarios/recuperar.php
```

---

## Pasos del flujo

### Paso 1: Solicitar recuperación (`?paso=solicitar`)
- El usuario ingresa su correo
- El sistema busca en `clientes` y luego en `usuarios`
- Si existe: genera un token único en `tokens_recuperacion` con vigencia de 1 hora
- Muestra el enlace de restablecimiento en pantalla
- Si no existe: muestra mensaje de error sin enviar nada

### Paso 2: Restablecer contraseña (`?paso=restablecer&token=XXX`)
- Valida que el token exista, no haya sido usado y no haya expirado
- Muestra formulario para ingresar nueva contraseña
- Al confirmar: actualiza el `password_hash` en la BD e invalida el token

### Paso 3: Completado
- Muestra mensaje de éxito con enlace al login

### Enlace expirado
- Si el token venció, muestra mensaje y opción de solicitar uno nuevo

---

## Tabla utilizada
`tokens_recuperacion` — almacena el token, el ID del cliente o usuario, fecha de expiración y si fue usado.

---

## Historias de usuario relacionadas
- **CL-003** criterio 1 — Solicitud con correo registrado
- **CL-003** criterio 2 — Correo no registrado
- **CL-003** criterio 3 — Restablecimiento exitoso
- **CL-003** criterio 4 — Enlace expirado
