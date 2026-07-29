# rutas.php

Este archivo lo agregué para resolver un problema que tuve al subir el proyecto al hosting. Las rutas relativas (`../Views/...`) funcionan bien en Laragon pero en el hosting fallaban porque la estructura de carpetas es diferente.

## ¿Qué hace?

Detecta automáticamente la URL base del proyecto y define constantes que se usan en los controllers para las redirecciones.

## Constantes que define

| Constante        | Ejemplo de valor                                      |
|------------------|-------------------------------------------------------|
| `ROOT_PATH`      | Ruta absoluta del servidor a la raíz del proyecto     |
| `BASE_URL`       | `https://laparrilla.byethost18.com/proyecto_personal` |
| `URL_PUBLIC`     | `BASE_URL/Public`                                     |
| `URL_VIEWS`      | `BASE_URL/Views`                                      |
| `URL_CONTROLLERS`| `BASE_URL/Controllers`                                |
| `URL_LOGIN`      | `BASE_URL/Views/usuarios/login.php`                   |
| `URL_REGISTRO`   | `BASE_URL/Views/usuarios/registre.php`                |
| `URL_INDEX`      | `BASE_URL/Public/index.php`                           |

## Cómo funciona la detección automática

Revisa la URL actual y busca en qué parte del path están las carpetas del proyecto (`Public`, `Views`, `Controllers`, etc.) para calcular la URL base correcta sin importar dónde esté alojado.
