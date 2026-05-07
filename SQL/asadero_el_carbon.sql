-- ============================================================
-- BASE DE DATOS: asadero_el_carbon
-- Sistema de Pedidos Autoservicio - Asadero El Carbon
-- Archivo corregido con base en las historias de usuario del Excel
-- Compatible con MySQL/MariaDB en Laragon
-- ============================================================

CREATE DATABASE IF NOT EXISTS asadero_el_carbon
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE asadero_el_carbon;

SET FOREIGN_KEY_CHECKS = 0;

DROP VIEW IF EXISTS vw_resumen_ventas_diarias;
DROP VIEW IF EXISTS vw_catalogo_productos;

DROP TABLE IF EXISTS ingresos;
DROP TABLE IF EXISTS pagos;
DROP TABLE IF EXISTS pedido_estados_historial;
DROP TABLE IF EXISTS pedido_items;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS carrito_items;
DROP TABLE IF EXISTS tokens_recuperacion;
DROP TABLE IF EXISTS producto_agotamientos;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS mesas;
DROP TABLE IF EXISTS criterios_aceptacion;
DROP TABLE IF EXISTS historias_usuario;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. ROLES Y USUARIOS
-- ============================================================

CREATE TABLE roles (
  id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(30) NOT NULL UNIQUE,
  descripcion VARCHAR(160)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  telefono VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  rol_id TINYINT UNSIGNED NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  ultimo_acceso DATETIME NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id)
    ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  telefono VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  correo_confirmado TINYINT(1) NOT NULL DEFAULT 0,
  ultimo_acceso DATETIME NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tokens_recuperacion (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NULL,
  usuario_id INT UNSIGNED NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  expira_en DATETIME NOT NULL,
  usado TINYINT(1) NOT NULL DEFAULT 0,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_token_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  CONSTRAINT fk_token_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT chk_token_dueno CHECK (
    (cliente_id IS NOT NULL AND usuario_id IS NULL) OR
    (cliente_id IS NULL AND usuario_id IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. CATALOGO
-- ============================================================

CREATE TABLE categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(80) NOT NULL UNIQUE,
  descripcion VARCHAR(255),
  imagen_url VARCHAR(255),
  activa TINYINT(1) NOT NULL DEFAULT 1,
  orden SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_categoria_activa_orden (activa, orden, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE productos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT UNSIGNED NOT NULL,
  nombre VARCHAR(120) NOT NULL,
  descripcion TEXT,
  imagen_url VARCHAR(255),
  precio DECIMAL(10,2) NOT NULL,
  popular TINYINT(1) NOT NULL DEFAULT 0,
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    ON UPDATE CASCADE,
  CONSTRAINT chk_producto_precio CHECK (precio >= 0),
  INDEX idx_producto_categoria (categoria_id),
  INDEX idx_producto_disponible (disponible),
  INDEX idx_producto_popular (popular)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE producto_agotamientos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  producto_id INT UNSIGNED NOT NULL,
  reportado_por INT UNSIGNED NOT NULL,
  motivo VARCHAR(255),
  reportado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_agotado_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
  CONSTRAINT fk_agotado_usuario FOREIGN KEY (reportado_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. MESAS, CARRITO Y PEDIDOS
-- ============================================================

CREATE TABLE mesas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero SMALLINT UNSIGNED NOT NULL UNIQUE,
  estado ENUM('Disponible','Ocupada','Reservada','Inactiva') NOT NULL DEFAULT 'Disponible',
  liberada_en DATETIME NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_mesa_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE carrito_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NOT NULL,
  cantidad SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  nota_especial TEXT,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_carrito_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  CONSTRAINT fk_carrito_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON DELETE CASCADE,
  CONSTRAINT uq_carrito_cliente_producto UNIQUE (cliente_id, producto_id),
  CONSTRAINT chk_carrito_cantidad CHECK (cantidad > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedidos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  numero_orden CHAR(8) NOT NULL UNIQUE,
  cliente_id INT UNSIGNED NULL,
  mesa_id INT UNSIGNED NULL,
  tipo ENUM('En mesa','Para llevar') NOT NULL,
  estado ENUM('Recibido','En preparacion','Listo','Entregado','Pagado','Cancelado') NOT NULL DEFAULT 'Recibido',
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  observaciones TEXT,
  cancelado_por INT UNSIGNED NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE SET NULL,
  CONSTRAINT fk_pedido_mesa FOREIGN KEY (mesa_id) REFERENCES mesas(id) ON DELETE SET NULL,
  CONSTRAINT fk_pedido_cancelado FOREIGN KEY (cancelado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_pedido_estado_fecha (estado, creado_en),
  INDEX idx_pedido_cliente (cliente_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedido_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT UNSIGNED NOT NULL,
  producto_id INT UNSIGNED NOT NULL,
  nombre_producto VARCHAR(120) NOT NULL,
  cantidad SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10,2) NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  nota_especial TEXT,
  CONSTRAINT fk_item_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_item_producto FOREIGN KEY (producto_id) REFERENCES productos(id) ON UPDATE CASCADE,
  CONSTRAINT chk_item_cantidad CHECK (cantidad > 0),
  CONSTRAINT chk_item_subtotal CHECK (subtotal >= 0),
  INDEX idx_item_pedido (pedido_id),
  INDEX idx_item_producto (producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pedido_estados_historial (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT UNSIGNED NOT NULL,
  estado ENUM('Recibido','En preparacion','Listo','Entregado','Pagado','Cancelado') NOT NULL,
  cambiado_por INT UNSIGNED NULL,
  cambiado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_hist_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_hist_usuario FOREIGN KEY (cambiado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_hist_pedido_fecha (pedido_id, cambiado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. PAGOS E INGRESOS
-- ============================================================

CREATE TABLE pagos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT UNSIGNED NOT NULL UNIQUE,
  metodo ENUM('Efectivo','Tarjeta debito','Tarjeta credito','Billetera digital') NOT NULL,
  monto_recibido DECIMAL(10,2) NULL,
  cambio DECIMAL(10,2) NULL,
  total_pagado DECIMAL(10,2) NOT NULL,
  registrado_por INT UNSIGNED NULL,
  pagado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pago_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
  CONSTRAINT fk_pago_usuario FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
  CONSTRAINT chk_pago_total CHECK (total_pagado >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ingresos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT UNSIGNED NULL,
  descripcion VARCHAR(255) NOT NULL,
  metodo ENUM('Efectivo','Tarjeta debito','Tarjeta credito','Billetera digital') NOT NULL,
  monto DECIMAL(10,2) NOT NULL,
  registrado_por INT UNSIGNED NULL,
  fecha DATE NOT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_ingreso_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE SET NULL,
  CONSTRAINT fk_ingreso_usuario FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
  CONSTRAINT chk_ingreso_monto CHECK (monto > 0),
  INDEX idx_ingreso_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. HISTORIAS DE USUARIO Y CRITERIOS DEL EXCEL
-- ============================================================

CREATE TABLE historias_usuario (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  codigo VARCHAR(10) NOT NULL UNIQUE,
  rol VARCHAR(40) NOT NULL,
  funcionalidad TEXT NOT NULL,
  razon TEXT,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE criterios_aceptacion (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  historia_id INT UNSIGNED NOT NULL,
  numero_escenario TINYINT UNSIGNED NOT NULL,
  titulo VARCHAR(220) NOT NULL,
  contexto TEXT,
  evento TEXT,
  resultado_esperado TEXT,
  CONSTRAINT fk_criterio_historia FOREIGN KEY (historia_id) REFERENCES historias_usuario(id) ON DELETE CASCADE,
  UNIQUE KEY uq_criterio_historia_escenario (historia_id, numero_escenario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. VISTAS UTILES
-- ============================================================

CREATE VIEW vw_catalogo_productos AS
SELECT
  c.id AS categoria_id,
  c.nombre AS categoria,
  p.id AS producto_id,
  p.nombre AS producto,
  p.descripcion,
  p.precio,
  p.popular,
  p.disponible
FROM categorias c
LEFT JOIN productos p ON p.categoria_id = c.id
WHERE c.activa = 1
ORDER BY c.orden, c.nombre, p.nombre;

CREATE VIEW vw_resumen_ventas_diarias AS
SELECT
  DATE(p.creado_en) AS fecha,
  COUNT(DISTINCT p.id) AS pedidos,
  COALESCE(SUM(pg.total_pagado), 0) AS total_vendido
FROM pedidos p
LEFT JOIN pagos pg ON pg.pedido_id = p.id
WHERE p.estado IN ('Pagado','Entregado')
GROUP BY DATE(p.creado_en);

-- ============================================================
-- 7. DATOS INICIALES
-- ============================================================

INSERT INTO roles (nombre, descripcion) VALUES
  ('Administrador', 'Acceso completo al panel de administracion'),
  ('Cocina', 'Acceso al panel de comandas y cambio de estado de pedidos'),
  ('Cliente', 'Acceso al catalogo y pedidos propios');

INSERT INTO usuarios (nombre, correo, telefono, password_hash, rol_id, activo) VALUES
  ('Administrador', 'admin@asaderoelcarbon.test', '3000000000', '$2y$10$6czp2jMefVh7kPX/sNiVSuPK58MLyqaSnG88lj2gXamFOBAjZKV4m', 1, 1),
  ('Cocina Principal', 'cocina@asaderoelcarbon.test', '3000000001', '$2y$10$6czp2jMefVh7kPX/sNiVSuPK58MLyqaSnG88lj2gXamFOBAjZKV4m', 2, 1);

INSERT INTO categorias (nombre, descripcion, orden) VALUES
  ('Res', 'Carnes de res asadas al carbon', 1),
  ('Pollo', 'Pollo asado, presas y platos derivados', 2),
  ('Cerdo', 'Carnes de cerdo y costillas', 3),
  ('Combos', 'Combos personales y familiares', 4),
  ('Acompanamientos', 'Papas, arepas, yucas, ensaladas y adicionales', 5),
  ('Bebidas', 'Bebidas frias y jugos', 6);

INSERT INTO productos (categoria_id, nombre, descripcion, precio, popular, disponible) VALUES
  (2, 'Pollo asado entero', 'Pollo asado al carbon con acompanamientos', 42000, 1, 1),
  (2, 'Medio pollo asado', 'Medio pollo asado al carbon', 24000, 1, 1),
  (1, 'Carne asada', 'Porcion de carne de res asada al carbon', 30000, 1, 1),
  (3, 'Costillas BBQ', 'Costillas de cerdo en salsa BBQ', 34000, 0, 1),
  (4, 'Combo familiar', 'Pollo entero, papas, arepas y bebida familiar', 62000, 1, 1),
  (5, 'Papas a la francesa', 'Porcion de papas crocantes', 9000, 0, 1),
  (6, 'Gaseosa personal', 'Bebida gaseosa personal', 5000, 0, 1);

INSERT INTO mesas (numero) VALUES
  (1),(2),(3),(4),(5),(6),(7),(8),(9),(10);

INSERT INTO historias_usuario (codigo, rol, funcionalidad, razon) VALUES
('CL-001', 'Cliente', 'Registrarse en el sistema con datos personales para crear una cuenta propia', 'Para acceder al catálogo, realizar pedidos y consultar el historial de compras en el asadero'),
('CL-002', 'Cliente', 'Iniciar sesión en el sistema con credenciales personales', 'Para acceder a la cuenta, explorar el catálogo y gestionar los pedidos de forma segura y personalizada'),
('CL-003', 'Cliente', 'Recuperar la contraseña en caso de haberla olvidado', 'Para volver a acceder a la cuenta sin crear una nueva y sin intervención del personal del asadero'),
('CL-004', 'Administrador', 'Gestionar categorías y asignar categorías a los productos', 'Para organizar correctamente los productos y garantizar su correcta visualización en el catálogo'),
('CL-005', 'Cliente', 'Ver el catálogo de productos organizado por categorías', 'Para explorar los platos disponibles según el tipo de carne o acompañamiento que desea pedir de forma visual y ordenada'),
('CL-006', 'Cliente', 'Seleccionar productos y armar el carrito de pedido', 'Para acumular los ítems deseados, ajustar cantidades, agregar instrucciones especiales y revisar el total antes de confirmar'),
('CL-007', 'Cliente', 'Editar o eliminar ítems del pedido antes de confirmarlo', 'Para corregir errores o cambiar de opinión antes de que el pedido sea enviado a cocina y ya no pueda modificarse'),
('CL-008', 'Cliente', 'Confirmar y enviar el pedido a cocina', 'Para que el sistema registre la orden formalmente, la envíe al área de cocina y asigne un número de seguimiento'),
('CL-009', 'Cliente', 'Consultar el historial de pedidos anteriores', 'Para revisar qué pidió en visitas previas, verificar los montos facturados y volver a pedir fácilmente los mismos platos'),
('CO-010', 'Cocina', 'Iniciar sesión en la pantalla de cocina con credenciales asignadas', 'Para acceder al panel de comandas y ver en tiempo real los pedidos a preparar, con acceso restringido a las funciones del área de cocina'),
('CO-011', 'Cocina', 'Ver los pedidos entrantes en tiempo real', 'Para saber inmediatamente cuando llega una nueva orden y organizar la preparación sin depender de comandas en papel'),
('CO-012', 'Cocina', 'Cambiar estado del pedido de acuerdo a su etapa de elaboración', 'Con el fin de proporcionar al cliente información oportuna sobre el estado de su pedido durante su preparación, y permitir al administrador realizar el seguimiento y control de cada comanda dentro del sistema.'),
('CO-014', 'Cocina', 'Reportar un producto como agotado cuando los insumos se terminan', 'Para actualizar el catálogo del cliente de inmediato y evitar que se sigan recibiendo pedidos de un producto que ya no puede prepararse'),
('AD-015', 'Administrador', 'Iniciar sesión en el panel de administración con credenciales personales', 'Para acceder a la gestión completa del asadero: inventario del menú, pedidos, mesas, pagos, ingresos y usuarios del sistema'),
('AD-016', 'Administrador', 'Gestionar productos del menú', 'Para mantener el catálogo actualizado con precios, descripciones, disponibilidad y control de los productos del asadero'),
('AD-017', 'Administrador', 'Gestionar los pedidos del asadero en tiempo real', 'Para tener visibilidad completa de las órdenes activas, actualizar su estado, coordinar la operación y resolver incidencias de forma inmediata'),
('AD-018', 'Administrador', 'Gestiónar mesas del restaurante', 'Permitir el control y visualización en tiempo real del estado de las mesas para optimizar la operación'),
('AD-019', 'Administrador', 'Gestión de pagos del sistema', 'Permitir registrar y procesar pagos dentro de la plataforma de forma segura y automática'),
('AD-020', 'Administrador', 'Consultar el historial de ingresos y el resumen de ventas del asadero', 'Para tener visibilidad financiera del negocio, analizar el rendimiento diario y tomar decisiones informadas sobre la operación'),
('AD-021', 'Administrador', 'Gestionar los usuarios y operarios del sistema', 'Para controlar quién tiene acceso, qué rol desempeña cada persona y mantener el directorio de usuarios actualizado conforme cambia el personal');

INSERT INTO criterios_aceptacion (
  historia_id,
  numero_escenario,
  titulo,
  contexto,
  evento,
  resultado_esperado
)
SELECT h.id, x.numero_escenario, x.titulo, x.contexto, x.evento, x.resultado_esperado
FROM (
  SELECT 'TMP' AS codigo, 0 AS numero_escenario, '' AS titulo, '' AS contexto, '' AS evento, '' AS resultado_esperado
  WHERE 1 = 0
  UNION ALL
  SELECT 'CL-001' AS codigo, 1 AS numero_escenario, 'Registro exitoso con datos completos' AS titulo, 'El software debe permitir al cliente acceder por primera vez a la plataforma y completar el formulario solicitando nombre, correo, contraseña y confirmación de contraseña.' AS contexto, 'Cuando el cliente completa todos los campos correctamente y pulsa ''Registrarse''' AS evento, 'el sistema crea la cuenta, envía un correo de confirmación y redirige al cliente al catálogo de productos con su sesión activa.' AS resultado_esperado UNION ALL
  SELECT 'CL-001' AS codigo, 2 AS numero_escenario, 'Correo ya registrado' AS titulo, 'El software debe impedir al cliente registrarse cuando el correo ingresado ya existe en la base de datos asociado a una cuenta activa.' AS contexto, 'Cuando el cliente intenta registrarse con ese correo' AS evento, 'el sistema muestra: ''Este correo ya está registrado. ¿Deseas iniciar sesión?'' y no crea una cuenta duplicada.' AS resultado_esperado UNION ALL
  SELECT 'CL-001' AS codigo, 3 AS numero_escenario, 'Campos obligatorios vacíos' AS titulo, 'El software debe notificar al cliente cuando no completó todos los campos del formulario de registro.' AS contexto, 'Cuando el cliente pulsa ''Registrarse'' con uno o más campos en blanco' AS evento, 'el sistema resalta en rojo los campos vacíos con indicación junto a cada uno y no permite continuar.' AS resultado_esperado UNION ALL
  SELECT 'CL-001' AS codigo, 4 AS numero_escenario, 'Contraseña no cumple requisitos mínimos' AS titulo, 'El software debe validar que la contraseña ingresada por el cliente no sea demasiado corta o no tenga el formato requerido (cantidad mínima de caracteres configurados).' AS contexto, 'Cuando el cliente escribe una contraseña inválida y pulsa ''Registrarse''' AS evento, 'el sistema indica los requisitos mínimos y no permite continuar hasta que se cumpla la condición.' AS resultado_esperado UNION ALL
  SELECT 'CL-001' AS codigo, 5 AS numero_escenario, 'Confirmación de contraseña no coincide' AS titulo, 'El software debe alertar al cliente cuando escribió contraseñas distintas en los campos ''Contraseña'' y ''Confirmar contraseña''.' AS contexto, 'Cuando el cliente intenta registrarse con ambos campos distintos' AS evento, 'el sistema muestra: ''Las contraseñas no coinciden'' y bloquea el registro hasta que ambos campos sean iguales.' AS resultado_esperado UNION ALL
  SELECT 'CL-002' AS codigo, 1 AS numero_escenario, 'Inicio de sesión exitoso' AS titulo, 'El software debe permitir al cliente iniciar sesión cuando tiene una cuenta activa y las credenciales son correctas.' AS contexto, 'Cuando el cliente ingresa correo y contraseña válidos y pulsa ''Ingresar''' AS evento, 'el sistema valida las credenciales y redirige al cliente al catálogo de productos con su sesión activa.' AS resultado_esperado UNION ALL
  SELECT 'CL-002' AS codigo, 2 AS numero_escenario, 'Credenciales incorrectas' AS titulo, 'El software debe impedir el acceso al cliente cuando el correo o contraseña no coinciden con ninguna cuenta activa, sin revelar cuál campo falló por seguridad.' AS contexto, 'Cuando el cliente envía el formulario con datos inválidos' AS evento, 'el sistema muestra: ''Correo o contraseña incorrectos'' sin especificar cuál es el error y deja el formulario disponible para reintentar.' AS resultado_esperado UNION ALL
  SELECT 'CL-002' AS codigo, 3 AS numero_escenario, 'Campos vacíos al iniciar sesión' AS titulo, 'El software debe notificar al cliente cuando no completó uno o ambos campos del formulario antes de intentar ingresar.' AS contexto, 'Cuando el cliente pulsa ''Ingresar'' con campos en blanco' AS evento, 'el sistema impide el envío e indica los campos obligatorios vacíos sin realizar ninguna consulta de autenticación.' AS resultado_esperado UNION ALL
  SELECT 'CL-002' AS codigo, 4 AS numero_escenario, 'Cuenta inactiva o bloqueada' AS titulo, 'El software debe informar al cliente que su cuenta fue desactivada por el administrador del asadero.' AS contexto, 'Cuando el cliente intenta iniciar sesión con esa cuenta' AS evento, 'el sistema muestra: ''Tu cuenta está inactiva. Contacta al administrador del asadero.'' y no permite el acceso.' AS resultado_esperado UNION ALL
  SELECT 'CL-002' AS codigo, 5 AS numero_escenario, 'Cerrar sesión' AS titulo, 'El software debe permitir al cliente cerrar sesión cuando terminó su visita o desea salir del sistema desde un dispositivo compartido.' AS contexto, 'Cuando el cliente pulsa ''Cerrar sesión''' AS evento, 'el sistema cierra la sesión, borra el token de autenticación y redirige a la pantalla de inicio del sistema.' AS resultado_esperado UNION ALL
  SELECT 'CL-003' AS codigo, 1 AS numero_escenario, 'Solicitud de recuperación con correo registrado' AS titulo, 'El software debe permitir al cliente recuperar su contraseña cuando la olvidó y selecciona ''¿Olvidaste tu contraseña?'' en el formulario de login, siempre que el correo ingresado esté registrado.' AS contexto, 'Cuando el cliente ingresa su correo y pulsa ''Enviar enlace''' AS evento, 'el sistema envía un enlace de restablecimiento al correo y muestra: ''Revisa tu correo para restablecer tu contraseña.''' AS resultado_esperado UNION ALL
  SELECT 'CL-003' AS codigo, 2 AS numero_escenario, 'Correo no registrado en el sistema' AS titulo, 'El software debe notificar al cliente cuando ingresa un correo que no está asociado a ninguna cuenta activa.' AS contexto, 'Cuando el cliente pulsa ''Enviar enlace'' con ese correo' AS evento, 'el sistema muestra: ''Este correo no está registrado. Verifica los datos o crea una cuenta nueva.'' y no envía ningún correo.' AS resultado_esperado UNION ALL
  SELECT 'CL-003' AS codigo, 3 AS numero_escenario, 'Restablecimiento de contraseña exitoso' AS titulo, 'El software debe permitir al cliente restablecer su contraseña cuando abre el enlace de restablecimiento dentro del tiempo de vigencia y accede al formulario de nueva contraseña.' AS contexto, 'Cuando el cliente ingresa una nueva contraseña válida, la confirma y pulsa ''Guardar nueva contraseña''' AS evento, 'el sistema actualiza la contraseña, invalida el enlace y redirige al login con el mensaje: ''Contraseña actualizada exitosamente.''' AS resultado_esperado UNION ALL
  SELECT 'CL-003' AS codigo, 4 AS numero_escenario, 'Enlace de recuperación expirado' AS titulo, 'El software debe informar al cliente que el enlace de restablecimiento venció cuando intentó usarlo después de su tiempo de vigencia.' AS contexto, 'Cuando el cliente accede al enlace expirado' AS evento, 'el sistema muestra: ''Este enlace ha expirado.'' y ofrece la opción de solicitar uno nuevo desde el formulario de recuperación.' AS resultado_esperado UNION ALL
  SELECT 'CL-004' AS codigo, 1 AS numero_escenario, 'Crear categoria' AS titulo, 'El software debe permitir al cliente visualizar los productos de la categoría del menú que eligió, siempre que dicha categoría tenga productos activos.' AS contexto, 'Cuando crea una nueva categoría o edita una existente ingresando nombre e imagen' AS evento, 'el sistema guarda o actualiza la información correctamente' AS resultado_esperado UNION ALL
  SELECT 'CL-004' AS codigo, NULL AS numero_escenario, 'Editar categoria' AS titulo, 'El software debe permitir al administrador ver la información ya guardada de una categoría cuando accede al modo de edición.' AS contexto, 'Cuando selecciona el icono "editar"' AS evento, 'el sistema responde mostrando un formulario/pantalla con la información existente lista para modificar.' AS resultado_esperado UNION ALL
  SELECT 'CL-004' AS codigo, 2 AS numero_escenario, 'Asignar categoría a producto' AS titulo, 'El software debe permitir al administrador gestionar la asignación de productos a categorías cuando ha iniciado sesión y existen productos y categorías previamente registradas.' AS contexto, 'Cuando selecciona un producto y le asigna una categoría' AS evento, 'el producto queda vinculado y organizado dentro de dicha categoría' AS resultado_esperado UNION ALL
  SELECT 'CL-004' AS codigo, 3 AS numero_escenario, 'Eliminar categoría' AS titulo, 'El software debe permitir al administrador editar una categoría existente cuando ha iniciado sesión y la categoría está registrada en el sistema.' AS contexto, 'Cuando intenta eliminar la categoría' AS evento, 'el sistema la elimina si no tiene productos asociados o impide la acción si existen productos vinculados' AS resultado_esperado UNION ALL
  SELECT 'CL-004' AS codigo, 4 AS numero_escenario, 'Visualizar organización' AS titulo, 'El software debe permitir al administrador visualizar las categorías con sus productos asociados cuando ha iniciado sesión.' AS contexto, 'Cuando consulta el listado de categorías' AS evento, 'el sistema muestra los productos organizados por cada categoría' AS resultado_esperado UNION ALL
  SELECT 'CL-005' AS codigo, 1 AS numero_escenario, 'Catálogo muestra las categorías disponibles' AS titulo, 'El software debe permitir al cliente ver el catálogo con categorías activas que tengan al menos un producto disponible.' AS contexto, 'Cuando el cliente accede al catálogo del asadero' AS evento, 'el sistema hace quese despliegan las categorías (Res, Pollo, Cerdo, Combos, Acompañamientos, Bebidas) con imagen representativa y número de productos disponibles en cada una.' AS resultado_esperado UNION ALL
  SELECT 'CL-005' AS codigo, 2 AS numero_escenario, 'Categoría sin productos disponibles' AS titulo, 'El software debe informar al cliente cuando todos los productos de una categoría están desactivados o agotados en ese momento.' AS contexto, 'Cuando el cliente selecciona esa categoría' AS evento, 'el sistema muestra: ''Sin productos disponibles en este momento'' y sugiere explorar otras categorías.' AS resultado_esperado UNION ALL
  SELECT 'CL-005' AS codigo, 3 AS numero_escenario, 'Filtro ''Todos'' muestra el menú completo' AS titulo, 'El software debe permitir al cliente ver todos los platos del asadero sin aplicar ningún filtro.' AS contexto, 'Cuando el cliente selecciona la opción ''Todos'' en el catálogo' AS evento, 'el sistema muestra todos los productos activos agrupados visualmente por categoría.' AS resultado_esperado UNION ALL
  SELECT 'CL-005' AS codigo, 4 AS numero_escenario, 'Producto marcado como popular se destaca visualmente' AS titulo, 'El software debe mostrar al cliente los productos que tienen el atributo ''popular'' activado por el administrador.' AS contexto, 'Cuando el cliente navega por el catálogo' AS evento, 'el producto muestra una etiqueta visible ''⭐ Popular'' que lo diferencia visualmente de los demás.' AS resultado_esperado UNION ALL
  SELECT 'CL-005' AS codigo, 5 AS numero_escenario, 'Producto agotado visible pero no disponible' AS titulo, 'El software debe indicar al cliente cuando un producto está marcado como no disponible por cocina o el administrador.' AS contexto, 'Cuando el cliente visualiza el catálogo' AS evento, 'el producto aparece con opacidad reducida, etiqueta ''Agotado'' y sin botón de agregar; el cliente puede verlo pero no pedirlo.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 1 AS numero_escenario, 'Agregar producto disponible al carrito' AS titulo, 'El software debe permitir al cliente agregar al carrito un producto que está activo y disponible en el catálogo.' AS contexto, 'Cuando el cliente pulsa el botón ''+'' o ''Agregar'' sobre un producto' AS evento, 'el producto se añade al carrito, el contador del ícono de carrito se incrementa y aparece una confirmación breve en pantalla.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 2 AS numero_escenario, 'Abrir detalle del producto antes de agregar' AS titulo, 'El software debe permitir al cliente leer la descripción completa del plato antes de decidir si lo agrega.' AS contexto, 'Cuando el cliente pulsa sobre la tarjeta del producto' AS evento, 'se abre un modal con imagen ampliada, nombre, descripción completa, precio y campo para agregar una nota especial.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 3 AS numero_escenario, 'Agregar nota especial a un producto' AS titulo, 'El software debe permitir al cliente ingresar instrucciones específicas como término de cocción, sin sal, extra salsa, sin cebolla, etc.' AS contexto, 'Cuando el cliente escribe en el campo ''Nota especial'' antes de agregar el producto al carrito' AS evento, 'la nota queda asociada al ítem en el carrito y se incluirá en la comanda enviada a cocina.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 4 AS numero_escenario, 'Aumentar cantidad de un ítem en el carrito' AS titulo, 'El software debe permitir al cliente aumentar la cantidad de un plato desde el panel del carrito cuando quiere pedir más de una unidad del mismo.' AS contexto, 'Cuando el cliente pulsa el botón ''+'' junto al ítem en el carrito' AS evento, 'la cantidad sube en 1 y el subtotal del ítem y el total general del carrito se recalculan en tiempo real.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 5 AS numero_escenario, 'Disminuir cantidad de un ítem en el carrito' AS titulo, 'El software debe permitir al cliente reducir la cantidad de un plato que ya tiene en el carrito.' AS contexto, 'Cuando el cliente pulsa el botón ''−'' junto al ítem' AS evento, 'la cantidad baja en 1; si llega a 0 el ítem se elimina automáticamente y el total se actualiza.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 6 AS numero_escenario, 'Eliminar un ítem del carrito' AS titulo, 'El software debe permitir al cliente quitar completamente un plato de su pedido.' AS contexto, 'Cuando el cliente pulsa el ícono de eliminar junto al ítem' AS evento, 'el ítem desaparece del carrito, el total se actualiza y si el carrito queda vacío se muestra: ''Tu carrito está vacío.''' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 7 AS numero_escenario, 'El carrito persiste mientras el cliente navega por el catálogo' AS titulo, 'El software debe permitir al cliente regresar al catálogo para seguir explorando y agregar más productos de otras categorías.' AS contexto, 'Cuando el cliente cierra el panel del carrito y navega a otra categoría' AS evento, 'los ítems previamente agregados se conservan en el carrito al regresar y el contador permanece visible.' AS resultado_esperado UNION ALL
  SELECT 'CL-006' AS codigo, 8 AS numero_escenario, 'El carrito muestra el total actualizado en todo momento' AS titulo, 'El software debe mostrar al cliente el total acumulado en su pedido antes de que lo confirme.' AS contexto, 'Cuando el cliente abre el panel del carrito' AS evento, 'se muestra el desglose con cantidad, precio unitario, subtotal por ítem y el total general al pie del carrito.' AS resultado_esperado UNION ALL
  SELECT 'CL-007' AS codigo, 1 AS numero_escenario, 'Editar la cantidad de un ítem antes de confirmar' AS titulo, 'El software debe permitir al cliente editar el carrito cuando el pedido aún no ha sido enviado a cocina.' AS contexto, 'Cuando el cliente modifica la cantidad de un ítem desde el carrito' AS evento, 'el sistema actualiza la cantidad, recalcula el subtotal del ítem y el total general en tiempo real.' AS resultado_esperado UNION ALL
  SELECT 'CL-007' AS codigo, 2 AS numero_escenario, 'Eliminar un ítem antes de confirmar' AS titulo, 'El software debe permitir al cliente eliminar un plato específico que decidió no incluir en su pedido.' AS contexto, 'Cuando el cliente pulsa el ícono de eliminar junto al ítem en el carrito' AS evento, 'el ítem desaparece del carrito y el total se actualiza. Si el carrito queda vacío se muestra: ''Tu carrito está vacío. ¡Agrega lo que más te guste!''' AS resultado_esperado UNION ALL
  SELECT 'CL-007' AS codigo, 3 AS numero_escenario, 'Vaciar el carrito completo' AS titulo, 'El software debe permitir al cliente vaciar todos los ítems del carrito para empezar de cero.' AS contexto, 'Cuando el cliente pulsa ''Vaciar carrito'' o elimina todos los ítems uno a uno' AS evento, 'el sistema elimina todos los ítems y muestra: ''Tu carrito está vacío. ¡Agrega lo que más te guste!''' AS resultado_esperado UNION ALL
  SELECT 'CL-007' AS codigo, 4 AS numero_escenario, 'Editar nota especial de un ítem' AS titulo, 'El software debe permitir al cliente cambiar o corregir las instrucciones de preparación de un plato ya agregado al carrito.' AS contexto, 'Cuando el cliente edita el campo de nota especial desde el carrito' AS evento, 'la nota actualizada queda asociada al ítem y se enviará a cocina con la comanda.' AS resultado_esperado UNION ALL
  SELECT 'CL-007' AS codigo, 5 AS numero_escenario, 'No se puede editar el pedido tras confirmarlo' AS titulo, 'El software debe impedir al cliente editar el pedido cuando ya fue enviado a cocina y su estado es ''Recibido'' o superior.' AS contexto, 'Cuando el cliente intenta modificar un pedido ya confirmado' AS evento, 'el sistema muestra: ''Tu pedido ya fue enviado a cocina y no puede modificarse. Solicita ayuda al personal.'' El pedido permanece intacto.' AS resultado_esperado UNION ALL
  SELECT 'CL-008' AS codigo, 1 AS numero_escenario, 'Confirmar pedido para consumo en mesa' AS titulo, 'El software debe permitir al cliente confirmar y enviar el pedido cuando el carrito tiene al menos un producto y el cliente indicó el número de mesa donde está sentado.' AS contexto, 'Cuando el cliente escribe el número de mesa y pulsa ''Confirmar pedido''' AS evento, 'el sistema registra el pedido como ''En mesa'', lo envía a cocina, asigna un número de orden (#XXXX) y muestra la pantalla de seguimiento.' AS resultado_esperado UNION ALL
  SELECT 'CL-008' AS codigo, 2 AS numero_escenario, 'Confirmar pedido para llevar' AS titulo, 'El software debe permitir al cliente confirmar el pedido en modalidad ''Para llevar'' cuando no desea consumir en el local.' AS contexto, 'Cuando el cliente selecciona ''Para llevar'' y confirma' AS evento, 'el sistema registra el pedido con esa indicación, lo envía a cocina y muestra la pantalla de seguimiento con el número de orden.' AS resultado_esperado UNION ALL
  SELECT 'CL-008' AS codigo, 3 AS numero_escenario, 'Intento de confirmar con carrito vacío' AS titulo, 'El software debe impedir al cliente confirmar el pedido cuando pulsó ''Confirmar pedido'' sin haber agregado ningún producto al carrito.' AS contexto, 'Cuando el cliente pulsa ''Confirmar pedido'' con el carrito vacío' AS evento, 'el sistema bloquea la acción y muestra: ''Debes agregar al menos un producto para continuar.''' AS resultado_esperado UNION ALL
  SELECT 'CL-008' AS codigo, 4 AS numero_escenario, 'Número de mesa inválido al confirmar en mesa' AS titulo, 'El software debe notificar al cliente cuando seleccionó ''En mesa'' e ingresó un número de mesa que no existe o está fuera del rango habilitado en el sistema.' AS contexto, 'Cuando el cliente pulsa ''Confirmar pedido'' con ese número' AS evento, 'el sistema bloquea la confirmación y muestra: ''El número de mesa ingresado no es válido. Verifica con el personal del asadero.'' sin borrar los demás datos del pedido.' AS resultado_esperado UNION ALL
  SELECT 'CL-008' AS codigo, NULL AS numero_escenario, 'El cliente selecciona la mesa donde se encuentra ubicado' AS titulo, 'El software debe permitir al cliente seleccionar la mesa donde se encuentra ubicado dentro del listado de mesas disponibles.' AS contexto, 'Cuando el cliente realiza la selección de la mesa en el sistema' AS evento, 'el sistema registra la mesa seleccionada y la asocia al pedido del cliente para continuar con el proceso' AS resultado_esperado UNION ALL
  SELECT 'CL-008' AS codigo, 5 AS numero_escenario, 'Pantalla de confirmación muestra el resumen del pedido' AS titulo, 'El software debe mostrar al cliente la confirmación del pedido cuando lo confirmó exitosamente y el sistema procesó la orden.' AS contexto, 'Cuando el sistema procesa la confirmación' AS evento, 'se despliega pantalla con: número de orden, lista de ítems pedidos, tipo de pedido (En mesa / Para llevar) y estado inicial ''Recibido''.' AS resultado_esperado UNION ALL
  SELECT 'CL-009' AS codigo, 1 AS numero_escenario, 'Ver historial completo de pedidos anteriores' AS titulo, 'El software debe permitir al cliente consultar sus pedidos registrados cuando accede a la sección ''Mis pedidos''.' AS contexto, 'Cuando el cliente accede a esa sección' AS evento, 'el sistema muestra el listado de pedidos ordenado por fecha descendente con: número de orden, fecha, tipo (mesa/para llevar), ítems y total pagado.' AS resultado_esperado UNION ALL
  SELECT 'CL-009' AS codigo, 2 AS numero_escenario, 'Ver detalle de un pedido anterior' AS titulo, 'El software debe permitir al cliente ver el detalle exacto de los platos incluidos en un pedido específico del pasado.' AS contexto, 'Cuando el cliente pulsa sobre un pedido en el historial' AS evento, 'el sistema muestra el detalle: ítems con cantidades y precios, notas especiales, método de pago y total final.' AS resultado_esperado UNION ALL
  SELECT 'CL-009' AS codigo, 3 AS numero_escenario, 'Historial vacío en primer uso' AS titulo, 'El software debe informar al cliente que no tiene pedidos cuando acaba de crear su cuenta o aún no ha realizado ninguno.' AS contexto, 'Cuando el cliente accede a ''Mis pedidos'' sin tener pedidos anteriores' AS evento, 'el sistema muestra: ''Aún no tienes pedidos. ¡Explora el catálogo y haz tu primer pedido!''' AS resultado_esperado UNION ALL
  SELECT 'CO-010' AS codigo, 1 AS numero_escenario, 'Inicio de sesión exitoso y acceso al panel de cocina' AS titulo, 'El software debe permitir al operario de cocina iniciar sesión cuando tiene credenciales asignadas por el administrador y su rol es ''Cocina''.' AS contexto, 'Cuando el operario ingresa usuario y contraseña correctos y pulsa ''Ingresar''' AS evento, 'el sistema valida las credenciales, identifica el rol Cocina y muestra el panel de pedidos entrantes sin acceso a módulos de inventario, mesas, pagos ni usuarios.' AS resultado_esperado UNION ALL
  SELECT 'CO-010' AS codigo, 2 AS numero_escenario, 'Credenciales incorrectas' AS titulo, 'El software debe impedir el acceso al operario cuando ingresó un usuario o contraseña que no coinciden con su cuenta.' AS contexto, 'Cuando el operario envía el formulario con datos inválidos' AS evento, 'el sistema muestra: ''Usuario o contraseña incorrectos.'' y deja el formulario disponible para un nuevo intento.' AS resultado_esperado UNION ALL
  SELECT 'CO-010' AS codigo, 3 AS numero_escenario, 'Cerrar sesión desde el panel de cocina' AS titulo, 'El software debe permitir al operario de cocina cerrar sesión cuando terminó su turno y necesita salir del sistema.' AS contexto, 'Cuando el operario pulsa ''Cerrar sesión''' AS evento, 'el sistema cierra la sesión, borra el token y redirige a la pantalla de login.' AS resultado_esperado UNION ALL
  SELECT 'CO-011' AS codigo, 1 AS numero_escenario, 'Ver pedidos entrantes con estado Recibido' AS titulo, 'El software debe mostrar al operario de cocina los nuevos pedidos entrantes en tiempo real cuando un cliente confirmó un pedido y el panel de cocina está abierto.' AS contexto, 'Cuando el sistema recibe el nuevo pedido' AS evento, 'el pedido aparece automáticamente con: número de orden, tipo (mesa/para llevar), número de mesa si aplica, ítems con cantidades y notas especiales.' AS resultado_esperado UNION ALL
  SELECT 'CO-011' AS codigo, 2 AS numero_escenario, 'Ver detalle completo de una comanda' AS titulo, 'El software debe permitir al operario de cocina revisar los detalles específicos de un pedido.' AS contexto, 'Cuando el operario pulsa sobre una comanda en el panel' AS evento, 'el sistema muestra: número de orden, tipo de pedido, ítems con cantidades, notas especiales por ítem y hora de llegada.' AS resultado_esperado UNION ALL
  SELECT 'CO-011' AS codigo, 3 AS numero_escenario, 'Filtrar pedidos por estado' AS titulo, 'El software debe permitir al operario de cocina filtrar la vista para ver únicamente los pedidos en un estado específico.' AS contexto, 'Cuando el operario selecciona un filtro de estado: Recibido, En preparación o Listo' AS evento, 'el sistema muestra únicamente los pedidos que coincidan con el estado seleccionado.' AS resultado_esperado UNION ALL
  SELECT 'CO-011' AS codigo, 4 AS numero_escenario, 'Panel sin pedidos activos' AS titulo, 'El software debe informar al operario de cocina que no hay pedidos activos en ese momento del día.' AS contexto, 'Cuando el operario abre el panel' AS evento, 'el sistema muestra: ''Sin pedidos activos en este momento.'' y queda a la espera de nuevas comandas.' AS resultado_esperado UNION ALL
  SELECT 'CO-012' AS codigo, 1 AS numero_escenario, 'Estado' AS titulo, 'El software debe permitir al operario de cocina cambiar el estado del pedido cuando ha sido preparado completamente, entregado al cliente o el pago ha sido registrado en el sistema.' AS contexto, 'Cuando el sistema valida la condición correspondiente' AS evento, 'el sistema actualiza el estado del pedido según corresponda a “Listo para recoger”, “Entregado” o “Pagado”, mostrando el mensaje asociado y gestionando su visibilidad en el seguimiento' AS resultado_esperado UNION ALL
  SELECT 'CO-012' AS codigo, 2 AS numero_escenario, 'Intento de retroceder el estado de un pedido' AS titulo, 'El software debe impedir al operario de cocina retroceder etapas en el flujo de estados, ya que es unidireccional.' AS contexto, 'Cuando el operario intenta cambiar el estado a uno anterior al actual' AS evento, 'Entonces el sistema bloquea la acción y muestra: ''No es posible retroceder el estado de un pedido. Contacta al administrador si necesitas hacer una corrección.''' AS resultado_esperado UNION ALL
  SELECT 'CO-012' AS codigo, 2 AS numero_escenario, 'Generar alerta de cambio de estado' AS titulo, 'El software debe permitir al operario de cocina o al administrador actualizar el estado de un pedido activo durante el flujo de atención.' AS contexto, 'Cuando se produce un cambio en el estado del pedido (por ejemplo, preparado, listo para recoger, entregado o pagado)' AS evento, 'el sistema genera automáticamente una alerta visual destacada para el cliente, notificando el nuevo estado del pedido de forma clara y oportuna, garantizando la continuidad del proceso de atención' AS resultado_esperado UNION ALL
  SELECT 'CO-014' AS codigo, 1 AS numero_escenario, 'Reportar producto agotado exitosamente' AS titulo, 'El software debe permitir al operario de cocina reportar un producto como agotado cuando los insumos se acabaron durante el servicio y necesita desactivarlo de inmediato.' AS contexto, 'Cuando el operario selecciona el producto en el panel y pulsa ''Reportar agotado''' AS evento, 'el sistema desactiva el producto en el catálogo del cliente (etiqueta ''Agotado'', sin botón de agregar) y notifica al administrador del cambio.' AS resultado_esperado UNION ALL
  SELECT 'CO-014' AS codigo, 2 AS numero_escenario, 'Reactivar producto cuando los insumos se reponen' AS titulo, 'El software debe permitir al operario de cocina volver a activar un producto cuando los insumos del producto agotado se repusieron.' AS contexto, 'Cuando el operario selecciona el producto y pulsa ''Marcar disponible''' AS evento, 'el sistema reactiva el producto en el catálogo del cliente de forma inmediata y notifica al administrador de la reactivación.' AS resultado_esperado UNION ALL
  SELECT 'AD-015' AS codigo, 1 AS numero_escenario, 'Inicio de sesión exitoso con credenciales válidas' AS titulo, 'El software debe permitir al administrador iniciar sesión cuando tiene credenciales válidas y el panel contiene todos los módulos de gestión del asadero.' AS contexto, 'Cuando el administrador ingresa usuario y contraseña correctos y envía el formulario' AS evento, 'el sistema valida las credenciales y redirige al panel con todos los módulos habilitados: Inventario, Pedidos, Mesas, Ventas, Ingresos y Usuarios.' AS resultado_esperado UNION ALL
  SELECT 'AD-015' AS codigo, 2 AS numero_escenario, 'Credenciales incorrectas' AS titulo, 'El software debe impedir el acceso al administrador cuando los datos ingresados no coinciden con ninguna cuenta de administrador activa.' AS contexto, 'Cuando el administrador envía el formulario con datos inválidos' AS evento, 'el sistema deniega el acceso, resalta los campos y muestra: ''Usuario o contraseña incorrectos.''' AS resultado_esperado UNION ALL
  SELECT 'AD-015' AS codigo, 3 AS numero_escenario, 'Campo vacío al intentar iniciar sesión' AS titulo, 'El software debe notificar al administrador cuando dejó uno o ambos campos del formulario de login en blanco.' AS contexto, 'Cuando el administrador pulsa ''Ingresar'' sin completar los campos' AS evento, 'el sistema impide el envío y muestra indicación de campos obligatorios junto a cada campo vacío.' AS resultado_esperado UNION ALL
  SELECT 'AD-015' AS codigo, 4 AS numero_escenario, 'Error de conectividad al iniciar sesión' AS titulo, 'El software debe informar al administrador cuando el servidor no responde o no hay red disponible.' AS contexto, 'Cuando el administrador intenta iniciar sesión sin conexión' AS evento, 'el sistema muestra: ''Error de conexión. Por favor intenta de nuevo más tarde.'' sin borrar los datos ingresados.' AS resultado_esperado UNION ALL
  SELECT 'AD-015' AS codigo, 5 AS numero_escenario, 'Cerrar sesión desde el panel' AS titulo, 'El software debe permitir al administrador cerrar sesión cuando terminó su turno.' AS contexto, 'Cuando el administrador pulsa ''Cerrar sesión''' AS evento, 'el sistema cierra la sesión, borra el token de acceso y redirige a la pantalla de login.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 1 AS numero_escenario, 'Crear un nuevo producto' AS titulo, 'El software debe permitir al administrador registrar un nuevo corte o plato cuando necesita incorporarlo al menú.' AS contexto, 'Cuando el administrador completa nombre, categoría, precio, descripción y emoji, y guarda' AS evento, 'el producto aparece disponible en el catálogo del cliente de forma inmediata dentro de la categoría asignada.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 2 AS numero_escenario, 'Validación de campos obligatorios al crear producto' AS titulo, 'El software debe impedir al administrador guardar un producto cuando no ingresó nombre, precio o categoría.' AS contexto, 'Cuando el administrador pulsa ''Guardar'' con campos obligatorios incompletos' AS evento, 'el sistema impide guardar y señala en rojo los campos obligatorios faltantes: nombre, precio y categoría.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 3 AS numero_escenario, 'Editar el precio de un producto existente' AS titulo, 'El software debe permitir al administrador actualizar el precio de un corte cuando cambió por variación en costos o por una promoción.' AS contexto, 'Cuando el administrador modifica el precio y guarda' AS evento, 'el nuevo precio se refleja de inmediato en el catálogo visible para el cliente.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 4 AS numero_escenario, 'Editar la descripción o emoji de un producto' AS titulo, 'El software debe permitir al administrador actualizar la descripción de un producto cuando necesita hacerla más precisa.' AS contexto, 'Cuando el administrador edita el campo y guarda' AS evento, 'los cambios se reflejan en la tarjeta del producto en el catálogo del cliente sin necesidad de recargar.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 5 AS numero_escenario, 'Desactivar un producto temporalmente' AS titulo, 'El software debe permitir al administrador desactivar temporalmente un producto cuando no está disponible pero se quiere conservar para reactivarlo.' AS contexto, 'Cuando el administrador pulsa ''Desactivar'' sobre el producto' AS evento, 'el producto aparece con etiqueta ''Agotado'' en el catálogo del cliente pero queda guardado para reactivarlo después.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 6 AS numero_escenario, 'Reactivar un producto previamente desactivado' AS titulo, 'El software debe permitir al administrador reactivar un producto cuando los insumos se repusieron.' AS contexto, 'Cuando el administrador pulsa ''Activar'' sobre el producto' AS evento, 'el producto vuelve a aparecer disponible en el catálogo del cliente en tiempo real.' AS resultado_esperado UNION ALL
  SELECT 'AD-016' AS codigo, 7 AS numero_escenario, 'Ver listado de productos con su' AS titulo, 'El software debe permitir al administrador consultar el listado de productos para revisar cuáles están activos o inactivos.' AS contexto, 'Cuando el administrador accede al módulo de Inventario' AS evento, 'se muestra la lista con: emoji, nombre, categoría, precio y estado (activo/inactivo) para cada producto.' AS resultado_esperado UNION ALL
  SELECT 'AD-017' AS codigo, 1 AS numero_escenario, 'Ver todos los pedidos activos del día' AS titulo, 'El software debe permitir al administrador supervisar el estado de todos los pedidos en curso en tiempo real.' AS contexto, 'Cuando el administrador accede al módulo de Pedidos' AS evento, 'el sistema muestra todos los pedidos activos con: número de orden, tipo (mesa/para llevar), mesa, ítems, estado y hora de creación.' AS resultado_esperado UNION ALL
  SELECT 'AD-017' AS codigo, 2 AS numero_escenario, 'Filtrar pedidos por estado' AS titulo, 'El software debe permitir al administrador filtrar la vista de pedidos para enfocarse en un estado específico.' AS contexto, 'Cuando el administrador selecciona el filtro: Recibido, En preparación, Listo o Entregado' AS evento, 'el sistema muestra únicamente los pedidos con ese estado, actualizándose en tiempo real.' AS resultado_esperado UNION ALL
  SELECT 'AD-017' AS codigo, 3 AS numero_escenario, 'Ver detalle completo de un pedido' AS titulo, 'El software debe permitir al administrador ver todos los ítems, notas y datos de un pedido específico.' AS contexto, 'Cuando el administrador pulsa sobre un pedido en la lista' AS evento, 'se muestra el detalle: número de orden, ítems con cantidades y notas especiales, tipo, mesa y estado actual.' AS resultado_esperado UNION ALL
  SELECT 'AD-017' AS codigo, 4 AS numero_escenario, 'Actualizar el estado de un pedido manualmente' AS titulo, 'El software debe permitir al administrador cambiar el estado de un pedido cuando cocina no lo actualizó desde su panel.' AS contexto, 'Cuando el administrador cambia el estado desde el panel' AS evento, 'el nuevo estado se refleja en tiempo real en el panel del administrador y en el tracker del cliente.' AS resultado_esperado UNION ALL
  SELECT 'AD-017' AS codigo, 5 AS numero_escenario, 'Cancelar un pedido activo' AS titulo, 'El software debe permitir al administrador anular un pedido cuando debe cancelarse por error del cliente o por problema operativo del asadero.' AS contexto, 'Cuando el administrador pulsa ''Cancelar pedido'' y confirma la acción' AS evento, 'el pedido se marca como cancelado, la mesa se libera y el cliente ve: ''Tu pedido fue cancelado.''' AS resultado_esperado UNION ALL
  SELECT 'AD-018' AS codigo, 1 AS numero_escenario, 'Ver mapa de mesas con estado actual' AS titulo, 'El software debe permitir al administrador gestionar las mesas cuando el sistema tiene mesas registradas y ha iniciado sesión correctamente en el panel de control.' AS contexto, 'El administrador accede a la pestaña "Mesas"' AS evento, 'El sistema muestra un mapa visual: verde = disponible, rojo = ocupada, con número visible' AS resultado_esperado UNION ALL
  SELECT 'AD-018' AS codigo, 2 AS numero_escenario, 'Mesa se ocupa al confirmar pedido' AS titulo, 'El software debe permitir al cliente seleccionar una mesa cuando existe un flujo de pedido activo y ha ingresado un número de mesa válido que está disponible.' AS contexto, 'Se confirma el pedido con número de mesa' AS evento, 'La mesa cambia automáticamente a estado "Ocupada" en tiempo real' AS resultado_esperado UNION ALL
  SELECT 'AD-018' AS codigo, 3 AS numero_escenario, 'Liberar mesa manual' AS titulo, 'El software debe permitir al administrador liberar una mesa cuando se encuentra en estado ''Ocupada'' y no tiene pedidos activos pendientes o el cliente ya abandonó el lugar.' AS contexto, 'El administrador selecciona una mesa ocupada' AS evento, 'La mesa cambia a "Disponible" y se registra fecha y hora' AS resultado_esperado UNION ALL
  SELECT 'AD-018' AS codigo, 4 AS numero_escenario, 'Liberar todas las mesas' AS titulo, 'El software debe permitir al administrador reiniciar el estado de todas las mesas cuando el sistema está en operación y existen múltiples mesas en diferentes estados al finalizar la jornada laboral.' AS contexto, 'El administrador presiona "Liberar todas las mesas"' AS evento, 'Todas las mesas cambian a "Disponible" y se registra la acción' AS resultado_esperado UNION ALL
  SELECT 'AD-018' AS codigo, 5 AS numero_escenario, 'Ver pedido de mesa activa' AS titulo, 'El software debe permitir al administrador consultar los detalles del pedido activo cuando la mesa se encuentra en estado ''Ocupada'' y tiene un pedido registrado.' AS contexto, 'El administrador selecciona una mesa ocupada en el mapa' AS evento, 'El sistema muestra número de pedido, ítems, estado y hora de creación' AS resultado_esperado UNION ALL
  SELECT 'AD-019' AS codigo, 1 AS numero_escenario, 'Registrar pago en efectivo' AS titulo, 'El software debe permitir al administrador procesar el pago de un pedido activo cuando se encuentra en el módulo de pagos del sistema.' AS contexto, 'El administrador ingresa el monto recibido en efectivo y confirma el pago en el sistema' AS evento, 'El sistema calcula el cambio automáticamente, registra el pago, marca el pedido como "Pagado" y libera la mesa' AS resultado_esperado UNION ALL
  SELECT 'AD-019' AS codigo, 2 AS numero_escenario, 'Registrar pago con tarjeta (débito/crédito)' AS titulo, 'El software debe permitir al administrador registrar un pago electrónico cuando existe un pedido activo y el sistema dispone de esa opción dentro de la plataforma.' AS contexto, 'El administrador selecciona el método "Tarjeta" y confirma el pago en el sistema' AS evento, 'El sistema procesa o registra el pago con tarjeta, marca el pedido como "Pagado" y libera la mesa automáticamente' AS resultado_esperado UNION ALL
  SELECT 'AD-019' AS codigo, 3 AS numero_escenario, 'Registrar pago con billetera digital' AS titulo, 'El software debe permitir al administrador seleccionar métodos de pago digitales cuando existe un pedido activo.' AS contexto, 'El administrador selecciona el método digital y confirma el pago en el sistema' AS evento, 'El sistema registra el pago digital, marca el pedido como "Pagado" y libera la mesa' AS resultado_esperado UNION ALL
  SELECT 'AD-019' AS codigo, 5 AS numero_escenario, 'Ver detalle del pedido antes de cobrar' AS titulo, 'El software debe permitir al administrador revisar los ítems del pedido que tuvo el cliente antes de presentar el total.' AS contexto, 'Cuando el administrador abre el pedido para cobro' AS evento, 'se muestra: ítems, cantidades, notas, subtotales por ítem, tipo de pedido y total final con cada método de pago disponible.' AS resultado_esperado UNION ALL
  SELECT 'AD-019' AS codigo, 6 AS numero_escenario, 'Pedido queda marcado como pagado tras confirmar' AS titulo, 'El software debe confirmar al administrador que el cobro fue realizado correctamente.' AS contexto, 'Cuando el administrador confirma el pago del pedido' AS evento, 'el pedido cambia a ''Pagado'', se genera un registro automático en el historial de ingresos y la mesa queda liberada.' AS resultado_esperado UNION ALL
  SELECT 'AD-020' AS codigo, 1 AS numero_escenario, 'Ver historial completo de ingresos registrados' AS titulo, 'El software debe permitir al administrador revisar todos los ingresos, tanto los generados por pedidos pagados como los manuales.' AS contexto, 'Cuando el administrador accede al módulo de Ingresos' AS evento, 'se muestra la lista con: fecha, número de orden si aplica, descripción, método de pago, monto y total acumulado.' AS resultado_esperado UNION ALL
  SELECT 'AD-020' AS codigo, 2 AS numero_escenario, 'Registrar ingreso manual no asociado a un pedido' AS titulo, 'El software debe permitir al administrador registrar un pago manual cuando recibe un pago fuera del flujo normal, como anticipos, eventos especiales o ajustes.' AS contexto, 'Cuando el administrador completa fecha, monto, descripción y método, y guarda' AS evento, 'el ingreso queda registrado en el historial con todos sus datos sin número de orden asociado.' AS resultado_esperado UNION ALL
  SELECT 'AD-020' AS codigo, 3 AS numero_escenario, 'Validación de monto obligatorio y mayor a cero' AS titulo, 'El software debe impedir al administrador guardar un ingreso manual cuando no ingresó monto o el valor es cero.' AS contexto, 'Cuando el administrador envía el formulario con monto vacío o igual a cero' AS evento, 'el sistema impide guardar y muestra: ''El monto es obligatorio y debe ser mayor a cero.''' AS resultado_esperado UNION ALL
  SELECT 'AD-020' AS codigo, 4 AS numero_escenario, 'Filtrar ingresos por rango de fechas' AS titulo, 'El software debe permitir al administrador filtrar los ingresos por período específico para revisar los datos de un rango de fechas.' AS contexto, 'Cuando el administrador ingresa una fecha de inicio y fin y aplica el filtro' AS evento, 'el sistema muestra únicamente los ingresos dentro del rango seleccionado con el subtotal del período.' AS resultado_esperado UNION ALL
  SELECT 'AD-020' AS codigo, 5 AS numero_escenario, 'Ver resumen de ventas del día actual' AS titulo, 'El software debe permitir al administrador consultar el resumen de rendimiento financiero del día.' AS contexto, 'Cuando el administrador accede al resumen diario' AS evento, 'el sistema muestra: total de pedidos del día, total facturado, método de pago más utilizado y producto más vendido del día.' AS resultado_esperado UNION ALL
  SELECT 'AD-020' AS codigo, 6 AS numero_escenario, 'El ingreso de un pedido pagado se registra automáticamente' AS titulo, 'El software debe registrar automáticamente el ingreso cuando el administrador confirmó el pago de un pedido.' AS contexto, 'Cuando el administrador confirma el pago desde el módulo de Ventas' AS evento, 'el sistema genera automáticamente un registro en el historial con: monto, método de pago y número de orden asociado.' AS resultado_esperado UNION ALL
  SELECT 'AD-021' AS codigo, 1 AS numero_escenario, 'Crear usuario con rol de cocina' AS titulo, 'El software debe permitir al administrador crear un usuario por medio de un formulario con datos básicos como nombre, teléfono, correo electrónico e imagen.' AS contexto, 'Cuando el administrador completa nombre, contraseña y asigna rol ''Cocina'', y guarda' AS evento, 'el sistema crea la cuenta y el operario puede iniciar sesión y ver la pantalla de pedidos de cocina.' AS resultado_esperado UNION ALL
  SELECT 'AD-021' AS codigo, 2 AS numero_escenario, 'Validación de campos obligatorios al crear usuario' AS titulo, 'El software debe impedir al administrador guardar un usuario cuando no completó los campos mínimos requeridos del formulario.' AS contexto, 'Cuando el administrador pulsa ''Guardar'' con campos vacíos' AS evento, 'el sistema impide guardar y señala los campos obligatorios: nombre, contraseña y rol.' AS resultado_esperado UNION ALL
  SELECT 'AD-021' AS codigo, 3 AS numero_escenario, 'Modificar nombre o contraseña de usuario existente' AS titulo, 'El software debe permitir al administrador actualizar los datos de un operario cuando deben corregirse por error o por cambio de turno.' AS contexto, 'Cuando el administrador edita los datos del usuario y guarda' AS evento, 'el sistema actualiza la información de inmediato; el operario deberá usar la nueva contraseña en su próximo inicio de sesión.' AS resultado_esperado UNION ALL
  SELECT 'AD-021' AS codigo, 4 AS numero_escenario, 'Desactivar acceso de un usuario que ya no trabaja' AS titulo, 'El software debe permitir al administrador desactivar a un operario para revocar su acceso cuando dejó el asadero.' AS contexto, 'Cuando el administrador cambia el estado del usuario a ''Inactivo''' AS evento, 'el sistema bloquea el acceso y el operario no puede iniciar sesión hasta ser reactivado.' AS resultado_esperado UNION ALL
  SELECT 'AD-021' AS codigo, 5 AS numero_escenario, 'Reactivar un usuario previamente inactivo' AS titulo, 'El software debe permitir al administrador reactivar a un operario para que recupere su acceso cuando regresó a trabajar.' AS contexto, 'Cuando el administrador cambia el estado del usuario a ''Activo''' AS evento, 'el sistema reactiva la cuenta y el operario puede iniciar sesión normalmente.' AS resultado_esperado UNION ALL
  SELECT 'AD-021' AS codigo, 6 AS numero_escenario, 'Ver listado de todos los usuarios con su estado' AS titulo, 'El software debe permitir al administrador consultar el listado de usuarios para revisar quiénes tienen acceso activo y qué rol desempeña cada uno.' AS contexto, 'Cuando el administrador accede a la sección de Usuarios' AS evento, 'el sistema muestra: nombre, rol, estado (activo/inactivo) y fecha de creación de cada usuario.' AS resultado_esperado
) x
JOIN historias_usuario h ON h.codigo = x.codigo;

-- ============================================================
-- 8. CONSULTAS DE VERIFICACION
-- ============================================================
-- SELECT COUNT(*) AS historias FROM historias_usuario;          -- esperado: 20
-- SELECT COUNT(*) AS criterios FROM criterios_aceptacion;       -- esperado: 97
-- SELECT * FROM vw_catalogo_productos;
asadero_el_carbonasadero_el_carbonasadero_el_carbonasadero_el_carbonasadero_el_carbonasadero_el_carbonasadero_el_carbonasadero_el_carbon