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
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS roles;

SET FOREIGN_KEY_CHECKS = 1;

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
  CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON UPDATE CASCADE
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
  CONSTRAINT fk_token_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  CONSTRAINT fk_producto_categoria FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON UPDATE CASCADE,
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
  CONSTRAINT uq_carrito_cliente_producto UNIQUE (cliente_id, producto_id)
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
  CONSTRAINT fk_pago_usuario FOREIGN KEY (registrado_por) REFERENCES usuarios(id) ON DELETE SET NULL
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
  INDEX idx_ingreso_fecha (fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE VIEW vw_catalogo_productos AS
SELECT
  c.id AS categoria_id, c.nombre AS categoria,
  p.id AS producto_id, p.nombre AS producto,
  p.descripcion, p.precio, p.popular, p.disponible
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

INSERT INTO roles (nombre, descripcion) VALUES
  ('Administrador', 'Acceso completo al panel de administracion'),
  ('Cocina',        'Acceso al panel de comandas y cambio de estado de pedidos'),
  ('Cliente',       'Acceso al catalogo y pedidos propios');

INSERT INTO usuarios (nombre, correo, telefono, password_hash, rol_id, activo) VALUES
  ('Administrador',   'admin@asaderoelcarbon.test',  '3000000000', '$2y$10$6czp2jMefVh7kPX/sNiVSuPK58MLyqaSnG88lj2gXamFOBAjZKV4m', 1, 1),
  ('Cocina Principal','cocina@asaderoelcarbon.test', '3000000001', '$2y$10$6czp2jMefVh7kPX/sNiVSuPK58MLyqaSnG88lj2gXamFOBAjZKV4m', 2, 1);

INSERT INTO categorias (nombre, descripcion, orden) VALUES
  ('Res',             'Carnes de res asadas al carbon',         1),
  ('Pollo',           'Pollo asado, presas y platos derivados', 2),
  ('Cerdo',           'Carnes de cerdo y costillas',            3),
  ('Combos',          'Combos personales y familiares',         4),
  ('Acompanamientos', 'Papas, arepas, yucas, ensaladas',       5),
  ('Bebidas',         'Bebidas frias y jugos',                  6);

INSERT INTO productos (categoria_id, nombre, descripcion, precio, popular, disponible) VALUES
  (2, 'Pollo asado entero', 'Pollo asado al carbon con acompanamientos', 42000, 1, 1),
  (2, 'Medio pollo asado',  'Medio pollo asado al carbon',               24000, 1, 1),
  (1, 'Carne asada',        'Porcion de carne de res asada al carbon',   30000, 1, 1),
  (3, 'Costillas BBQ',      'Costillas de cerdo en salsa BBQ',           34000, 0, 1),
  (4, 'Combo familiar',     'Pollo entero, papas, arepas y bebida',      62000, 1, 1),
  (5, 'Papas a la francesa','Porcion de papas crocantes',                 9000, 0, 1),
  (6, 'Gaseosa personal',   'Bebida gaseosa personal',                    5000, 0, 1);

INSERT INTO mesas (numero) VALUES
  (1),(2),(3),(4),(5),(6),(7),(8),(9),(10);
