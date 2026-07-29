# database.php

Este archivo maneja la conexión a la base de datos. Lo hice como una clase para poder reutilizarla fácilmente desde cualquier parte del proyecto sin repetir código.

## ¿Qué hace?

Básicamente crea una conexión PDO con los datos del servidor MySQL. Si la conexión falla, muestra el error directamente.

## Configuración

```php
private $host     = "127.0.0.1";
private $port     = "3320";        // Puerto de Laragon (en hosting cambiar a 3306)
private $db_name  = "asadero_el_carbon";
private $username = "root";
private $password = "";
```

> Si vas a subir el proyecto a un hosting, estos valores cambian según las credenciales que te den en el panel de control.

## Cómo se usa

En cualquier controller o vista que necesite la BD:

```php
require_once __DIR__ . '/../Config/database.php';
$db = (new Database())->conectar();
```

## Por qué usé PDO

Usé PDO en lugar de mysqli porque permite usar consultas preparadas fácilmente, lo que protege contra inyección SQL. Además si en algún momento se cambia el motor de BD, el código cambia muy poco.
