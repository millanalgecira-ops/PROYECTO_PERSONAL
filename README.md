# 🔥 La Parrilla – Sistema de Pedidos Autoservicio

Sistema web de pedidos para el Asadero El Carbón, desarrollado en PHP con arquitectura MVC, MySQL y diseño oscuro personalizado.

---

## 📋 Descripción

Plataforma de autoservicio que permite a los clientes explorar el catálogo, armar su pedido y enviarlo directamente a cocina. El administrador gestiona productos, pedidos, mesas, usuarios y ventas desde un panel centralizado.

---

## 🚀 Tecnologías utilizadas

| Tecnología | Uso |
|---|---|
| PHP 8+ | Backend y lógica del servidor |
| MySQL / MariaDB | Base de datos relacional |
| PDO | Conexión segura a la BD |
| HTML5 / CSS3 | Estructura y estilos |
| JavaScript (Vanilla) | Interactividad del cliente |
| localStorage | Carrito de compras del cliente |
| Laragon | Entorno de desarrollo local |

---

## 🗂️ Estructura del proyecto

```
proyecto_personal/
├── Config/
│   └── database.php          # Configuración de conexión a la BD
├── Controllers/
│   ├── AuthController.php        # Login y logout
│   ├── UsuarioControllers.php    # Registro de clientes
│   ├── AdminUsuarioController.php # CRUD de usuarios (admin)
│   ├── ProductoController.php    # CRUD de productos (admin)
│   ├── PedidoController.php      # Cambio de estado de pedidos
│   └── CarritoController.php     # Procesamiento de pedidos
├── Models/
│   └── usuario.php           # Modelos Usuario y Cliente
├── Views/
│   ├── usuarios/
│   │   ├── login.php         # Pantalla de acceso al sistema
│   │   └── registre.php      # Registro de nuevos clientes
│   └── dashboard/
│       ├── admin.php         # Panel administrador – Usuarios
│       ├── productos.php     # Panel administrador – Productos
│       ├── pedidos.php       # Panel administrador – Pedidos
│       ├── mesas.php         # Panel administrador – Mesas
│       ├── ventas.php        # Panel administrador – Ventas
│       ├── cocina.php        # Panel de cocina
│       └── cliente.php       # Panel del cliente
├── Public/
│   ├── index.php             # Página principal (menú público)
│   ├── carrito.php           # Carrito y checkout
│   └── confirmacion.php      # Confirmación de pedido
├── SQL/
│   └── asadero_el_carbon.sql # Script completo de la BD
└── README.md
```

---

## 🗄️ Base de datos

**Nombre:** `asadero_el_carbon`  
**Puerto:** `3320` (Laragon)  
**Usuario:** `root`  
**Contraseña:** *(vacía)*

### Tablas principales

| Tabla | Descripción |
|---|---|
| `roles` | Roles del sistema: Administrador, Cocina, Cliente |
| `usuarios` | Personal del sistema (admin y cocina) |
| `clientes` | Clientes registrados públicamente |
| `categorias` | Categorías del menú |
| `productos` | Productos del catálogo |
| `mesas` | Mesas del restaurante |
| `pedidos` | Pedidos realizados |
| `pedido_items` | Ítems de cada pedido |
| `pedido_estados_historial` | Historial de cambios de estado |
| `pagos` | Registro de pagos |
| `ingresos` | Historial de ingresos |
| `carrito_items` | Carrito persistente por cliente |
| `tokens_recuperacion` | Tokens para recuperación de contraseña |

---

## 👥 Roles del sistema

| Rol | Acceso |
|---|---|
| **Administrador** | Panel completo: usuarios, productos, pedidos, mesas, ventas |
| **Cocina** | Panel de comandas y cambio de estado de pedidos |
| **Cliente** | Catálogo, carrito, pedidos propios |

---

## 🔐 Credenciales de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `millanalgecira@gmail.com` | `camilo123` |
| Administrador | `admin@asaderoelcarbon.test` | `password` |
| Cocina | `cocina@asaderoelcarbon.test` | `password` |

> Los clientes se registran desde el formulario público en `index.php`

---

## ⚙️ Instalación y configuración

### Requisitos
- Laragon (Apache + PHP 8+ + MySQL/MariaDB)
- Navegador moderno

### Pasos

1. **Clonar el repositorio** en `C:\laragon\www\`:
```bash
git clone https://github.com/tu-usuario/proyecto_personal.git
```

2. **Crear la base de datos** — abrir HeidiSQL o phpMyAdmin y ejecutar:
```
SQL/asadero_el_carbon.sql
```

3. **Verificar configuración** en `Config/database.php`:
```php
private $host     = "127.0.0.1";
private $port     = "3320";
private $db_name  = "asadero_el_carbon";
private $username = "root";
private $password = "";
```

4. **Acceder al sistema:**
```
http://127.0.0.1:8081/proyecto_personal/Public/index.php
```

---

## 🌐 URLs principales

| Página | URL |
|---|---|
| Menú público | `/Public/index.php` |
| Login del sistema | `/Views/usuarios/login.php` |
| Registro de cliente | `/Views/usuarios/registre.php` |
| Carrito | `/Public/carrito.php` |
| Panel admin | `/Views/dashboard/admin.php` |
| Gestión productos | `/Views/dashboard/productos.php` |
| Gestión pedidos | `/Views/dashboard/pedidos.php` |
| Gestión mesas | `/Views/dashboard/mesas.php` |
| Ventas e ingresos | `/Views/dashboard/ventas.php` |
| Panel cocina | `/Views/dashboard/cocina.php` |

---

## 🔄 Flujo del sistema

### Cliente
1. Entra a `index.php` → explora el catálogo
2. Agrega productos al carrito (localStorage)
3. Va a `carrito.php` → selecciona mesa o para llevar, método de pago
4. Finaliza el pedido → se guarda en la BD
5. Ve la confirmación con número de orden

### Administrador
1. Inicia sesión en `login.php`
2. Ve el panel con estadísticas de usuarios
3. Gestiona productos (crear, editar, eliminar, activar/desactivar)
4. Monitorea pedidos en tiempo real y cambia estados
5. Controla el estado de las mesas
6. Consulta ventas e ingresos por rango de fechas

### Cocina
1. Inicia sesión → ve el panel de comandas
2. Recibe pedidos en estado "Recibido"
3. Actualiza el estado: En preparación → Listo → Entregado

---

## 🔒 Seguridad implementada

- Contraseñas hasheadas con `password_hash()` (bcrypt)
- Verificación con `password_verify()`
- Sesiones PHP con `session_regenerate_id()` al login
- Validación de roles en cada vista y controller
- Consultas con PDO y parámetros preparados (prevención SQL injection)
- `htmlspecialchars()` en todas las salidas (prevención XSS)

---

## 📱 Características del diseño

- Diseño oscuro con paleta naranja (`#f07000`)
- Tipografías: Bebas Neue (títulos) + Barlow (texto)
- Responsive para móvil y escritorio
- Modal de autenticación integrado en el menú principal
- Carrito persistente con localStorage

---

## 👨‍💻 Autor

Desarrollado como proyecto personal académico.  
Institución: Sena  
Año: 2026
