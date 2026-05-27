# models/Usuario.php

## Descripción
Contiene dos clases modelo que gestionan el acceso a datos de los usuarios del sistema. `Usuario` maneja el personal interno (Administrador y Cocina), mientras que `Cliente` maneja los clientes registrados públicamente.

---

## Ubicación
```
Models/usuario.php
```

---

## Clase: `Usuario`

Gestiona la tabla `usuarios` de la base de datos (personal del asadero).

### Constructor
```php
public function __construct($db)
```
Recibe una instancia PDO de conexión.

---

### Métodos

#### `existeCorreo($email)`
Verifica si un correo ya está registrado en la tabla `usuarios`.

| Parámetro | Tipo     | Descripción          |
|-----------|----------|----------------------|
| `$email`  | `string` | Correo a verificar   |

**Retorna:** `bool` — `true` si existe, `false` si no.

---

#### `obtenerPorEmail($email)`
Busca un usuario activo por su correo electrónico.

| Parámetro | Tipo     | Descripción          |
|-----------|----------|----------------------|
| `$email`  | `string` | Correo del usuario   |

**Retorna:** `array|false` — datos del usuario o `false` si no existe/está inactivo.

---

#### `registrar($datos)`
Inserta un nuevo usuario en la tabla `usuarios`.

| Clave en `$datos` | Descripción                              |
|-------------------|------------------------------------------|
| `nombre`          | Nombre completo                          |
| `correo`          | Correo electrónico                       |
| `telefono`        | Número de teléfono                       |
| `password`        | Hash de la contraseña (ya hasheado)      |
| `id_rol`          | ID del rol (1=Admin, 2=Cocina, 3=Cliente)|

**Retorna:** `true` si fue exitoso, `string` con el error si falló.

---

#### `obtenerTodos()`
Retorna todos los usuarios con su nombre de rol.

**Retorna:** `array` — lista de usuarios con columnas: `id`, `nombre`, `correo`, `rol_id`, `activo`, `creado_en`, `rol_nombre`.

---

#### `obtenerPorId($id)`
Busca un usuario por su ID.

**Retorna:** `array|false`

---

#### `actualizar($id, $datos)`
Actualiza nombre, correo, rol y opcionalmente la contraseña de un usuario.

**Retorna:** `true` si fue exitoso, `string` con el error si falló.

---

#### `cambiarEstado($id, $activo)`
Activa o desactiva un usuario (campo `activo`: 1 = activo, 0 = inactivo).

**Retorna:** `true` si fue exitoso, `string` con el error si falló.

---

## Clase: `Cliente`

Gestiona la tabla `clientes` de la base de datos (clientes registrados públicamente).

### Métodos

#### `existeCorreo($email)`
Verifica si un correo ya está registrado en la tabla `clientes`.

**Retorna:** `bool`

---

#### `registrar($datos)`
Inserta un nuevo cliente en la tabla `clientes`.

| Clave en `$datos` | Descripción                         |
|-------------------|-------------------------------------|
| `nombre`          | Nombre completo                     |
| `correo`          | Correo electrónico                  |
| `password`        | Hash de la contraseña (ya hasheado) |

**Retorna:** `true` si fue exitoso, `string` con el error si falló.

---

#### `obtenerPorEmail($email)`
Busca un cliente activo por su correo electrónico.

**Retorna:** `array|false`

---

## Tablas relacionadas

| Clase     | Tabla      |
|-----------|------------|
| `Usuario` | `usuarios` |
| `Cliente` | `clientes` |
