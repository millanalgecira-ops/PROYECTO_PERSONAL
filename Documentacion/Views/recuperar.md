# recuperar.php

Página para recuperar la contraseña. Funciona en tres pasos según el parámetro `?paso=` en la URL.

## Paso 1 — Solicitar recuperación

El usuario ingresa su correo. El sistema busca en `clientes` y en `usuarios`. Si lo encuentra, genera un token único con vigencia de 1 hora y lo guarda en `tokens_recuperacion`. Muestra el enlace de restablecimiento en pantalla.

Si el correo no existe muestra el mensaje: *"Este correo no está registrado. Verifica los datos o crea una cuenta nueva."* — sin enviar nada.

## Paso 2 — Restablecer contraseña

El usuario abre el enlace con el token. El sistema valida que el token exista, no haya sido usado y no haya expirado. Si todo está bien muestra el formulario para la nueva contraseña.

Al confirmar actualiza el `password_hash` en la BD e invalida el token para que no se pueda usar de nuevo.

## Paso 3 — Confirmación

Muestra mensaje de éxito con enlace al login.

## Enlace expirado

Si el token venció muestra: *"Este enlace ha expirado."* y ofrece solicitar uno nuevo.

## Nota

En producción el enlace se enviaría por correo electrónico. Por ahora se muestra directamente en pantalla porque el hosting gratuito no tiene servidor de correo configurado.
