# controllers/AdminUsuarioController.php

## Descripción
Controlador exclusivo del administrador para gestionar los usuarios del sistema (Administrador, Cocina). Permite crear, editar y activar/desactivar usuarios.

---

## Ubicación
```
Controllers/AdminUsuarioController.php
```

---

## Dependencias
```php
require_once Config/database.php
require_once Models/usuario.php
```

---

## Control de acceso
Solo accesible si el usuario tiene rol `administrador`. De lo contrario redirige al login.

```php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] !== 'administrador') {
    header("Location: ../Views/usuarios/login.php");
    exit;
}
```

---

## Clase: `AdminUsuarioController`

### Roles disponibles

| Nombre          | ID en BD |
|-----------------|----------|
| `administrador` | 1        |
| `cocina`        | 2        |
| `cliente`       | 3        |

---

### Método: `crear()`

#### Descripción
Crea un nuevo usuario del sistema (Administrador o Cocina).

#### Campos requeridos
`nombres`, `apellidos`, `email`, `password`, `rol`, `telefono` (opcional)

#### Flujo
1. Valida que todos los campos obligatorios estén completos
2. Verifica que el correo no esté ya registrado
3. Hashea la contraseña con `password_hash()`
4. Llama a `Usuario::registrar()` con los datos
5. Redirige al panel admin con mensaje de éxito o error

---

### Método: `editar()`

#### Descripción
Actualiza los datos de un usuario existente.

#### Campos
`id_usuario`, `nombres`, `apellidos`, `email`, `rol`, `password` (opcional)

#### Flujo
1. Verifica que se proporcionó `id_usuario`
2. Construye el array de datos a actualizar
3. Si se envió `password`, lo hashea y lo incluye
4. Llama a `Usuario::actualizar()`
5. Redirige con mensaje de resultado

---

### Método: `toggleEstado()`

#### Descripción
Activa o desactiva un usuario. Invierte el estado actual.

#### Parámetros GET
| Parámetro | Descripción                    |
|-----------|--------------------------------|
| `id`      | ID del usuario                 |
| `estado`  | Estado actual (0 o 1)          |

#### Lógica
- Si `estado == 1` → cambia a `0` (desactiva)
- Si `estado == 0` → cambia a `1` (activa)

---

### Método privado: `setAlert($icon, $title, $text)`
Guarda un mensaje de alerta en sesión para mostrarlo en la vista.

---

## URLs de acceso

| Acción        | URL                                                          |
|---------------|--------------------------------------------------------------|
| Crear         | `Controllers/AdminUsuarioController.php?accion=crear`        |
| Editar        | `Controllers/AdminUsuarioController.php?accion=editar`       |
| Toggle estado | `Controllers/AdminUsuarioController.php?accion=toggleEstado&id=X&estado=Y` |
