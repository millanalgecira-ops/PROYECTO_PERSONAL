# config/Database.php

## Descripción
Clase encargada de gestionar la conexión a la base de datos MySQL mediante PDO. Es el único punto de configuración de la base de datos en todo el sistema.

---

## Ubicación
```
Config/database.php
```

---

## Clase: `Database`

### Propiedades privadas

| Propiedad    | Valor por defecto       | Descripción                        |
|--------------|-------------------------|------------------------------------|
| `$host`      | `127.0.0.1`             | Dirección del servidor MySQL       |
| `$port`      | `3320`                  | Puerto de conexión (Laragon)       |
| `$db_name`   | `asadero_el_carbon`     | Nombre de la base de datos         |
| `$username`  | `root`                  | Usuario de MySQL                   |
| `$password`  | *(vacío)*               | Contraseña de MySQL                |

### Propiedad pública

| Propiedad | Tipo  | Descripción                        |
|-----------|-------|------------------------------------|
| `$conn`   | `PDO` | Instancia de la conexión activa    |

---

## Método: `conectar()`

### Descripción
Establece la conexión a la base de datos usando PDO con el DSN configurado.

### Retorna
- `PDO` — objeto de conexión activo

### Comportamiento
1. Construye el DSN con host, puerto, nombre de BD y charset `utf8mb4`
2. Crea una instancia PDO con las credenciales configuradas
3. Activa el modo de errores por excepción (`ERRMODE_EXCEPTION`)
4. Si falla, detiene la ejecución con `die()` mostrando el error

### Ejemplo de uso
```php
$database = new Database();
$db = $database->conectar();
```

---

## Notas
- El puerto `3320` es el predeterminado de Laragon. En otros entornos puede ser `3306`.
- Se usa `utf8mb4` para soporte completo de caracteres especiales y emojis.
- PDO previene inyección SQL cuando se usan consultas preparadas.
