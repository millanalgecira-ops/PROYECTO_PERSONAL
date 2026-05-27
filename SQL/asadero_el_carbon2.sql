-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         8.0.30 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para asadero_el_carbon
CREATE DATABASE IF NOT EXISTS `asadero_el_carbon` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `asadero_el_carbon`;

-- Volcando estructura para tabla asadero_el_carbon.carrito_items
CREATE TABLE IF NOT EXISTS `carrito_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` int unsigned NOT NULL,
  `producto_id` int unsigned NOT NULL,
  `cantidad` smallint unsigned NOT NULL DEFAULT '1',
  `nota_especial` text COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_carrito_cliente_producto` (`cliente_id`,`producto_id`),
  KEY `fk_carrito_producto` (`producto_id`),
  CONSTRAINT `fk_carrito_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_carrito_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_carrito_cantidad` CHECK ((`cantidad` > 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.carrito_items: ~0 rows (aproximadamente)

-- Volcando estructura para tabla asadero_el_carbon.categorias
CREATE TABLE IF NOT EXISTS `categorias` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `orden` smallint unsigned NOT NULL DEFAULT '0',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `idx_categoria_activa_orden` (`activa`,`orden`,`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.categorias: ~6 rows (aproximadamente)
INSERT INTO `categorias` (`id`, `nombre`, `descripcion`, `imagen_url`, `activa`, `orden`, `creado_en`, `actualizado_en`) VALUES
	(1, 'Res', 'Carnes de res asadas al carbon', NULL, 1, 1, '2026-04-29 11:34:58', '2026-04-29 11:34:58'),
	(2, 'Pollo', 'Pollo asado, presas y platos derivados', NULL, 1, 2, '2026-04-29 11:34:58', '2026-04-29 11:34:58'),
	(3, 'Cerdo', 'Carnes de cerdo y costillas', NULL, 1, 3, '2026-04-29 11:34:58', '2026-04-29 11:34:58'),
	(4, 'Combos', 'Combos personales y familiares', NULL, 1, 4, '2026-04-29 11:34:58', '2026-04-29 11:34:58'),
	(5, 'Acompanamientos', 'Papas, arepas, yucas, ensaladas y adicionales', NULL, 1, 5, '2026-04-29 11:34:58', '2026-04-29 11:34:58'),
	(6, 'Bebidas', 'Bebidas frias y jugos', NULL, 1, 6, '2026-04-29 11:34:58', '2026-04-29 11:34:58');

-- Volcando estructura para tabla asadero_el_carbon.clientes
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `correo_confirmado` tinyint(1) NOT NULL DEFAULT '0',
  `ultimo_acceso` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.clientes: ~4 rows (aproximadamente)
INSERT INTO `clientes` (`id`, `nombre`, `correo`, `telefono`, `password_hash`, `activo`, `correo_confirmado`, `ultimo_acceso`, `creado_en`, `actualizado_en`) VALUES
	(2, 'manuel roncancio', 'manuel12@gmail.com', NULL, '$2y$10$5Hv2Lp5cZ017xj2RpiXF9uAYWbxJcNzLauSDT85BdG7SdeH2W1W1q', 1, 0, NULL, '2026-04-29 12:42:11', '2026-04-29 12:42:11'),
	(3, 'camilo trujillo', 'ctrujillo@gmail.com', NULL, '$2y$10$u9EyUUr1vC8u8zDBKg1S9udWBtMAMCkTPfdDOYjp0OwJZI7WA4MhO', 1, 0, NULL, '2026-04-29 13:41:22', '2026-04-29 13:41:22'),
	(4, 'melanie sofia lemus vargas', 'melanycinee@gmail.com', NULL, '$2y$10$XGiaXHxugg7ewNMcHhtphO4UPISmis5KgCGXbtJT1iYFj6zImGOZq', 1, 0, NULL, '2026-04-29 14:29:44', '2026-04-29 14:29:44'),
	(5, 'rubenn el profeta', 'ruben@gmail.com', NULL, '$2y$10$BcqHoqF3ITJMX6YI94dtpuK1UqmAS3E3ly3hsDyfIY7XxuaZ770eW', 1, 0, NULL, '2026-04-29 14:44:46', '2026-04-29 14:44:46');

-- Volcando estructura para tabla asadero_el_carbon.criterios_aceptacion
CREATE TABLE IF NOT EXISTS `criterios_aceptacion` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `historia_id` int unsigned NOT NULL,
  `numero_escenario` tinyint unsigned NOT NULL,
  `titulo` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contexto` text COLLATE utf8mb4_unicode_ci,
  `evento` text COLLATE utf8mb4_unicode_ci,
  `resultado_esperado` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_criterio_historia_escenario` (`historia_id`,`numero_escenario`),
  CONSTRAINT `fk_criterio_historia` FOREIGN KEY (`historia_id`) REFERENCES `historias_usuario` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.criterios_aceptacion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla asadero_el_carbon.historias_usuario
CREATE TABLE IF NOT EXISTS `historias_usuario` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funcionalidad` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `razon` text COLLATE utf8mb4_unicode_ci,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.historias_usuario: ~20 rows (aproximadamente)
INSERT INTO `historias_usuario` (`id`, `codigo`, `rol`, `funcionalidad`, `razon`, `creado_en`) VALUES
	(1, 'CL-001', 'Cliente', 'Registrarse en el sistema con datos personales para crear una cuenta propia', 'Para acceder al catálogo, realizar pedidos y consultar el historial de compras en el asadero', '2026-04-29 11:34:58'),
	(2, 'CL-002', 'Cliente', 'Iniciar sesión en el sistema con credenciales personales', 'Para acceder a la cuenta, explorar el catálogo y gestionar los pedidos de forma segura y personalizada', '2026-04-29 11:34:58'),
	(3, 'CL-003', 'Cliente', 'Recuperar la contraseña en caso de haberla olvidado', 'Para volver a acceder a la cuenta sin crear una nueva y sin intervención del personal del asadero', '2026-04-29 11:34:58'),
	(4, 'CL-004', 'Administrador', 'Gestionar categorías y asignar categorías a los productos', 'Para organizar correctamente los productos y garantizar su correcta visualización en el catálogo', '2026-04-29 11:34:58'),
	(5, 'CL-005', 'Cliente', 'Ver el catálogo de productos organizado por categorías', 'Para explorar los platos disponibles según el tipo de carne o acompañamiento que desea pedir de forma visual y ordenada', '2026-04-29 11:34:58'),
	(6, 'CL-006', 'Cliente', 'Seleccionar productos y armar el carrito de pedido', 'Para acumular los ítems deseados, ajustar cantidades, agregar instrucciones especiales y revisar el total antes de confirmar', '2026-04-29 11:34:58'),
	(7, 'CL-007', 'Cliente', 'Editar o eliminar ítems del pedido antes de confirmarlo', 'Para corregir errores o cambiar de opinión antes de que el pedido sea enviado a cocina y ya no pueda modificarse', '2026-04-29 11:34:58'),
	(8, 'CL-008', 'Cliente', 'Confirmar y enviar el pedido a cocina', 'Para que el sistema registre la orden formalmente, la envíe al área de cocina y asigne un número de seguimiento', '2026-04-29 11:34:58'),
	(9, 'CL-009', 'Cliente', 'Consultar el historial de pedidos anteriores', 'Para revisar qué pidió en visitas previas, verificar los montos facturados y volver a pedir fácilmente los mismos platos', '2026-04-29 11:34:58'),
	(10, 'CO-010', 'Cocina', 'Iniciar sesión en la pantalla de cocina con credenciales asignadas', 'Para acceder al panel de comandas y ver en tiempo real los pedidos a preparar, con acceso restringido a las funciones del área de cocina', '2026-04-29 11:34:58'),
	(11, 'CO-011', 'Cocina', 'Ver los pedidos entrantes en tiempo real', 'Para saber inmediatamente cuando llega una nueva orden y organizar la preparación sin depender de comandas en papel', '2026-04-29 11:34:58'),
	(12, 'CO-012', 'Cocina', 'Cambiar estado del pedido de acuerdo a su etapa de elaboración', 'Con el fin de proporcionar al cliente información oportuna sobre el estado de su pedido durante su preparación, y permitir al administrador realizar el seguimiento y control de cada comanda dentro del sistema.', '2026-04-29 11:34:58'),
	(13, 'CO-014', 'Cocina', 'Reportar un producto como agotado cuando los insumos se terminan', 'Para actualizar el catálogo del cliente de inmediato y evitar que se sigan recibiendo pedidos de un producto que ya no puede prepararse', '2026-04-29 11:34:58'),
	(14, 'AD-015', 'Administrador', 'Iniciar sesión en el panel de administración con credenciales personales', 'Para acceder a la gestión completa del asadero: inventario del menú, pedidos, mesas, pagos, ingresos y usuarios del sistema', '2026-04-29 11:34:58'),
	(15, 'AD-016', 'Administrador', 'Gestionar productos del menú', 'Para mantener el catálogo actualizado con precios, descripciones, disponibilidad y control de los productos del asadero', '2026-04-29 11:34:58'),
	(16, 'AD-017', 'Administrador', 'Gestionar los pedidos del asadero en tiempo real', 'Para tener visibilidad completa de las órdenes activas, actualizar su estado, coordinar la operación y resolver incidencias de forma inmediata', '2026-04-29 11:34:58'),
	(17, 'AD-018', 'Administrador', 'Gestiónar mesas del restaurante', 'Permitir el control y visualización en tiempo real del estado de las mesas para optimizar la operación', '2026-04-29 11:34:58'),
	(18, 'AD-019', 'Administrador', 'Gestión de pagos del sistema', 'Permitir registrar y procesar pagos dentro de la plataforma de forma segura y automática', '2026-04-29 11:34:58'),
	(19, 'AD-020', 'Administrador', 'Consultar el historial de ingresos y el resumen de ventas del asadero', 'Para tener visibilidad financiera del negocio, analizar el rendimiento diario y tomar decisiones informadas sobre la operación', '2026-04-29 11:34:58'),
	(20, 'AD-021', 'Administrador', 'Gestionar los usuarios y operarios del sistema', 'Para controlar quién tiene acceso, qué rol desempeña cada persona y mantener el directorio de usuarios actualizado conforme cambia el personal', '2026-04-29 11:34:58');

-- Volcando estructura para tabla asadero_el_carbon.ingresos
CREATE TABLE IF NOT EXISTS `ingresos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` int unsigned DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metodo` enum('Efectivo','Tarjeta debito','Tarjeta credito','Billetera digital') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `registrado_por` int unsigned DEFAULT NULL,
  `fecha` date NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_ingreso_pedido` (`pedido_id`),
  KEY `fk_ingreso_usuario` (`registrado_por`),
  KEY `idx_ingreso_fecha` (`fecha`),
  CONSTRAINT `fk_ingreso_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_ingreso_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_ingreso_monto` CHECK ((`monto` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.ingresos: ~15 rows (aproximadamente)
INSERT INTO `ingresos` (`id`, `pedido_id`, `descripcion`, `metodo`, `monto`, `registrado_por`, `fecha`, `creado_en`) VALUES
	(1, 1, 'Pedido #CC6A0650 - rubenn el profeta', 'Billetera digital', 144000.00, NULL, '2026-04-29', '2026-04-29 14:46:25'),
	(2, 2, 'Pedido #73AC5589 - camilo trujillo', 'Tarjeta credito', 75000.00, NULL, '2026-04-29', '2026-04-29 19:36:22'),
	(3, 3, 'Pedido #4ADEA089 - camilo trujillo', 'Billetera digital', 44000.00, NULL, '2026-04-29', '2026-04-29 19:43:54'),
	(4, 4, 'Pedido #2CA6EFBD - camilo trujillo', 'Tarjeta credito', 56000.00, NULL, '2026-05-05', '2026-05-05 09:13:44'),
	(5, 5, 'Pedido #BDB507EC - camilo trujillo', 'Efectivo', 68000.00, NULL, '2026-05-05', '2026-05-05 09:28:53'),
	(6, 6, 'Pedido #9408B0E8 - camilo trujillo', 'Efectivo', 78000.00, NULL, '2026-05-05', '2026-05-05 11:14:04'),
	(7, 7, 'Pedido #F08CADBE - camilo trujillo', 'Tarjeta credito', 127000.00, NULL, '2026-05-05', '2026-05-05 13:26:49'),
	(8, 8, 'Pedido #C8A6DE1A - camilo trujillo', 'Billetera digital', 39000.00, NULL, '2026-05-05', '2026-05-05 13:28:35'),
	(9, 9, 'Pedido #A747189F - camilo trujillo', 'Tarjeta credito', 69000.00, NULL, '2026-05-06', '2026-05-06 07:56:08'),
	(10, 10, 'Pedido #85F6010F - camilo trujillo', 'Billetera digital', 64000.00, NULL, '2026-05-06', '2026-05-06 10:54:22'),
	(11, 11, 'Pedido #ECC6C1B2 - camilo trujillo', 'Efectivo', 55000.00, NULL, '2026-05-06', '2026-05-06 13:38:49'),
	(12, 12, 'Pedido #055669D1 - camilo trujillo', 'Efectivo', 25000.00, NULL, '2026-05-06', '2026-05-06 13:40:44'),
	(13, 13, 'Pedido #40FAC280 - camilo trujillo', 'Efectivo', 76000.00, NULL, '2026-05-07', '2026-05-07 08:22:00'),
	(14, 14, 'Pedido #198137EE - camilo trujillo', 'Billetera digital', 62000.00, NULL, '2026-05-07', '2026-05-07 08:31:06'),
	(15, 15, 'Pedido #8F109D7A - rubenn el profeta', 'Efectivo', 94000.00, NULL, '2026-05-08', '2026-05-08 12:01:48');

-- Volcando estructura para tabla asadero_el_carbon.mesas
CREATE TABLE IF NOT EXISTS `mesas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `numero` smallint unsigned NOT NULL,
  `estado` enum('Disponible','Ocupada','Reservada','Inactiva') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Disponible',
  `liberada_en` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero` (`numero`),
  KEY `idx_mesa_estado` (`estado`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.mesas: ~10 rows (aproximadamente)
INSERT INTO `mesas` (`id`, `numero`, `estado`, `liberada_en`, `creado_en`, `actualizado_en`) VALUES
	(1, 1, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(2, 2, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(3, 3, 'Disponible', '2026-05-07 08:27:29', '2026-04-29 11:34:58', '2026-05-07 08:27:29'),
	(4, 4, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(5, 5, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(6, 6, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(7, 7, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(8, 8, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(9, 9, 'Disponible', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-06 13:39:11'),
	(10, 10, 'Ocupada', '2026-05-06 13:39:11', '2026-04-29 11:34:58', '2026-05-07 08:21:59');

-- Volcando estructura para tabla asadero_el_carbon.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` int unsigned NOT NULL,
  `metodo` enum('Efectivo','Tarjeta debito','Tarjeta credito','Billetera digital') COLLATE utf8mb4_unicode_ci NOT NULL,
  `monto_recibido` decimal(10,2) DEFAULT NULL,
  `cambio` decimal(10,2) DEFAULT NULL,
  `total_pagado` decimal(10,2) NOT NULL,
  `registrado_por` int unsigned DEFAULT NULL,
  `pagado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pedido_id` (`pedido_id`),
  KEY `fk_pago_usuario` (`registrado_por`),
  CONSTRAINT `fk_pago_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pago_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `chk_pago_total` CHECK ((`total_pagado` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.pagos: ~15 rows (aproximadamente)
INSERT INTO `pagos` (`id`, `pedido_id`, `metodo`, `monto_recibido`, `cambio`, `total_pagado`, `registrado_por`, `pagado_en`) VALUES
	(1, 1, 'Billetera digital', NULL, NULL, 144000.00, NULL, '2026-04-29 14:46:25'),
	(2, 2, 'Tarjeta credito', NULL, NULL, 75000.00, NULL, '2026-04-29 19:36:22'),
	(3, 3, 'Billetera digital', NULL, NULL, 44000.00, NULL, '2026-04-29 19:43:54'),
	(4, 4, 'Tarjeta credito', NULL, NULL, 56000.00, NULL, '2026-05-05 09:13:44'),
	(5, 5, 'Efectivo', NULL, NULL, 68000.00, NULL, '2026-05-05 09:28:53'),
	(6, 6, 'Efectivo', NULL, NULL, 78000.00, NULL, '2026-05-05 11:14:04'),
	(7, 7, 'Tarjeta credito', NULL, NULL, 127000.00, NULL, '2026-05-05 13:26:49'),
	(8, 8, 'Billetera digital', NULL, NULL, 39000.00, NULL, '2026-05-05 13:28:35'),
	(9, 9, 'Tarjeta credito', NULL, NULL, 69000.00, NULL, '2026-05-06 07:56:08'),
	(10, 10, 'Billetera digital', NULL, NULL, 64000.00, NULL, '2026-05-06 10:54:22'),
	(11, 11, 'Efectivo', NULL, NULL, 55000.00, NULL, '2026-05-06 13:38:49'),
	(12, 12, 'Efectivo', NULL, NULL, 25000.00, NULL, '2026-05-06 13:40:44'),
	(13, 13, 'Efectivo', NULL, NULL, 76000.00, NULL, '2026-05-07 08:21:59'),
	(14, 14, 'Billetera digital', NULL, NULL, 62000.00, NULL, '2026-05-07 08:31:06'),
	(15, 15, 'Efectivo', NULL, NULL, 94000.00, NULL, '2026-05-08 12:01:48');

-- Volcando estructura para tabla asadero_el_carbon.pedidos
CREATE TABLE IF NOT EXISTS `pedidos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `numero_orden` char(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_id` int unsigned DEFAULT NULL,
  `mesa_id` int unsigned DEFAULT NULL,
  `tipo` enum('En mesa','Para llevar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('Recibido','En preparacion','Listo','Entregado','Pagado','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Recibido',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `cancelado_por` int unsigned DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_orden` (`numero_orden`),
  KEY `fk_pedido_mesa` (`mesa_id`),
  KEY `fk_pedido_cancelado` (`cancelado_por`),
  KEY `idx_pedido_estado_fecha` (`estado`,`creado_en`),
  KEY `idx_pedido_cliente` (`cliente_id`),
  CONSTRAINT `fk_pedido_cancelado` FOREIGN KEY (`cancelado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pedido_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pedido_mesa` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.pedidos: ~15 rows (aproximadamente)
INSERT INTO `pedidos` (`id`, `numero_orden`, `cliente_id`, `mesa_id`, `tipo`, `estado`, `subtotal`, `total`, `observaciones`, `cancelado_por`, `creado_en`, `actualizado_en`) VALUES
	(1, 'CC6A0650', 5, NULL, 'Para llevar', 'Pagado', 144000.00, 144000.00, 'sin cebolla ni carne', NULL, '2026-04-29 14:46:25', '2026-05-05 09:14:23'),
	(2, '73AC5589', 3, 3, 'En mesa', 'Pagado', 75000.00, 75000.00, '', NULL, '2026-04-29 19:36:22', '2026-05-05 13:25:53'),
	(3, '4ADEA089', 3, NULL, 'Para llevar', 'Entregado', 44000.00, 44000.00, '', NULL, '2026-04-29 19:43:54', '2026-05-05 09:14:31'),
	(4, '2CA6EFBD', 3, NULL, 'Para llevar', 'Cancelado', 56000.00, 56000.00, '', NULL, '2026-05-05 09:13:44', '2026-05-05 13:25:55'),
	(5, 'BDB507EC', 3, NULL, 'Para llevar', 'Entregado', 68000.00, 68000.00, 'sin cebolla', NULL, '2026-05-05 09:28:53', '2026-05-05 10:56:55'),
	(6, '9408B0E8', 3, 3, 'En mesa', 'Entregado', 78000.00, 78000.00, 'sin cebolla, miguel cabezon', NULL, '2026-05-05 11:14:04', '2026-05-05 11:15:00'),
	(7, 'F08CADBE', 3, 6, 'En mesa', 'Cancelado', 127000.00, 127000.00, 'con salsa', 3, '2026-05-05 13:26:49', '2026-05-06 13:39:06'),
	(8, 'C8A6DE1A', 3, 2, 'En mesa', 'Pagado', 39000.00, 39000.00, '', NULL, '2026-05-05 13:28:35', '2026-05-06 08:40:13'),
	(9, 'A747189F', 3, 10, 'Para llevar', 'Pagado', 69000.00, 69000.00, 'odio a cristian', NULL, '2026-05-06 07:56:08', '2026-05-06 08:40:10'),
	(10, '85F6010F', 3, 3, 'En mesa', 'Cancelado', 64000.00, 64000.00, '', 3, '2026-05-06 10:54:22', '2026-05-07 08:27:29'),
	(11, 'ECC6C1B2', 3, NULL, 'Para llevar', 'Cancelado', 55000.00, 55000.00, '', 3, '2026-05-06 13:38:49', '2026-05-07 08:27:32'),
	(12, '055669D1', 3, NULL, 'Para llevar', 'Recibido', 25000.00, 25000.00, '', NULL, '2026-05-06 13:40:44', '2026-05-06 13:40:44'),
	(13, '40FAC280', 3, 10, 'En mesa', 'Recibido', 76000.00, 76000.00, '', NULL, '2026-05-07 08:21:59', '2026-05-07 08:21:59'),
	(14, '198137EE', 3, NULL, 'Para llevar', 'Recibido', 62000.00, 62000.00, 'extra de salsa de maiz', NULL, '2026-05-07 08:31:06', '2026-05-07 08:33:06'),
	(15, '8F109D7A', 5, 10, 'En mesa', 'En preparacion', 94000.00, 94000.00, '', NULL, '2026-05-08 12:01:48', '2026-05-08 12:03:14');

-- Volcando estructura para tabla asadero_el_carbon.pedido_estados_historial
CREATE TABLE IF NOT EXISTS `pedido_estados_historial` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` int unsigned NOT NULL,
  `estado` enum('Recibido','En preparacion','Listo','Entregado','Pagado','Cancelado') COLLATE utf8mb4_unicode_ci NOT NULL,
  `cambiado_por` int unsigned DEFAULT NULL,
  `cambiado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_hist_usuario` (`cambiado_por`),
  KEY `idx_hist_pedido_fecha` (`pedido_id`,`cambiado_en`),
  CONSTRAINT `fk_hist_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_hist_usuario` FOREIGN KEY (`cambiado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.pedido_estados_historial: ~55 rows (aproximadamente)
INSERT INTO `pedido_estados_historial` (`id`, `pedido_id`, `estado`, `cambiado_por`, `cambiado_en`) VALUES
	(1, 1, 'Recibido', NULL, '2026-04-29 14:46:25'),
	(2, 1, 'En preparacion', 3, '2026-04-29 14:47:42'),
	(3, 1, 'Listo', 3, '2026-04-29 14:47:45'),
	(4, 1, 'Entregado', 3, '2026-04-29 14:47:54'),
	(5, 1, 'En preparacion', 3, '2026-04-29 14:48:00'),
	(6, 1, 'Entregado', 3, '2026-04-29 19:17:02'),
	(7, 2, 'Recibido', NULL, '2026-04-29 19:36:22'),
	(8, 3, 'Recibido', NULL, '2026-04-29 19:43:54'),
	(9, 4, 'Recibido', NULL, '2026-05-05 09:13:44'),
	(10, 1, 'Pagado', 3, '2026-05-05 09:14:23'),
	(11, 2, 'Pagado', 3, '2026-05-05 09:14:27'),
	(12, 3, 'Entregado', 3, '2026-05-05 09:14:31'),
	(13, 4, 'Entregado', 3, '2026-05-05 09:14:33'),
	(14, 5, 'Recibido', NULL, '2026-05-05 09:28:53'),
	(15, 5, 'Cancelado', 3, '2026-05-05 09:29:11'),
	(16, 5, 'Recibido', 3, '2026-05-05 10:45:51'),
	(17, 5, 'Entregado', 3, '2026-05-05 10:45:54'),
	(18, 5, 'En preparacion', 3, '2026-05-05 10:56:16'),
	(19, 4, 'Listo', 3, '2026-05-05 10:56:20'),
	(20, 2, 'Cancelado', 3, '2026-05-05 10:56:30'),
	(21, 5, 'Listo', 2, '2026-05-05 10:56:52'),
	(22, 5, 'Entregado', 2, '2026-05-05 10:56:55'),
	(23, 4, 'Entregado', 2, '2026-05-05 10:56:57'),
	(24, 6, 'Recibido', NULL, '2026-05-05 11:14:04'),
	(25, 6, 'En preparacion', 3, '2026-05-05 11:14:24'),
	(26, 6, 'Pagado', 3, '2026-05-05 11:14:26'),
	(27, 6, 'En preparacion', 3, '2026-05-05 11:14:28'),
	(28, 6, 'Listo', 2, '2026-05-05 11:14:55'),
	(29, 6, 'Entregado', 2, '2026-05-05 11:15:01'),
	(30, 2, 'Pagado', 3, '2026-05-05 13:25:53'),
	(31, 4, 'Cancelado', 3, '2026-05-05 13:25:55'),
	(32, 7, 'Recibido', NULL, '2026-05-05 13:26:49'),
	(33, 8, 'Recibido', NULL, '2026-05-05 13:28:35'),
	(34, 8, 'Cancelado', 3, '2026-05-05 13:29:57'),
	(35, 9, 'Recibido', NULL, '2026-05-06 07:56:08'),
	(36, 9, 'En preparacion', 2, '2026-05-06 08:37:19'),
	(37, 9, 'Listo', 2, '2026-05-06 08:37:22'),
	(38, 9, 'Entregado', 2, '2026-05-06 08:37:24'),
	(39, 7, 'En preparacion', 2, '2026-05-06 08:37:26'),
	(40, 7, 'Listo', 2, '2026-05-06 08:37:27'),
	(41, 7, 'Entregado', 2, '2026-05-06 08:37:28'),
	(42, 9, 'Pagado', 3, '2026-05-06 08:40:10'),
	(43, 8, 'Pagado', 3, '2026-05-06 08:40:13'),
	(44, 10, 'Recibido', NULL, '2026-05-06 10:54:22'),
	(45, 10, 'En preparacion', 2, '2026-05-06 11:12:50'),
	(46, 11, 'Recibido', NULL, '2026-05-06 13:38:49'),
	(47, 12, 'Recibido', NULL, '2026-05-06 13:40:44'),
	(48, 13, 'Recibido', NULL, '2026-05-07 08:21:59'),
	(49, 14, 'Recibido', NULL, '2026-05-07 08:31:06'),
	(50, 14, 'En preparacion', 2, '2026-05-07 08:31:56'),
	(51, 14, 'Listo', 2, '2026-05-07 08:32:39'),
	(52, 14, 'Entregado', 2, '2026-05-07 08:32:46'),
	(53, 14, 'Recibido', 3, '2026-05-07 08:33:06'),
	(54, 15, 'Recibido', NULL, '2026-05-08 12:01:48'),
	(55, 15, 'En preparacion', 2, '2026-05-08 12:03:14');

-- Volcando estructura para tabla asadero_el_carbon.pedido_items
CREATE TABLE IF NOT EXISTS `pedido_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` int unsigned NOT NULL,
  `producto_id` int unsigned NOT NULL,
  `nombre_producto` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` smallint unsigned NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `nota_especial` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_item_pedido` (`pedido_id`),
  KEY `idx_item_producto` (`producto_id`),
  CONSTRAINT `fk_item_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_item_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_item_cantidad` CHECK ((`cantidad` > 0)),
  CONSTRAINT `chk_item_subtotal` CHECK ((`subtotal` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.pedido_items: ~41 rows (aproximadamente)
INSERT INTO `pedido_items` (`id`, `pedido_id`, `producto_id`, `nombre_producto`, `cantidad`, `precio_unitario`, `subtotal`, `nota_especial`) VALUES
	(1, 1, 1, 'Pollo Entero Asado', 1, 35000.00, 35000.00, NULL),
	(2, 1, 1, 'Arroz con Pollo', 1, 25000.00, 25000.00, NULL),
	(3, 1, 1, 'Alitas Picantes', 3, 28000.00, 84000.00, NULL),
	(4, 2, 1, 'Medio Pollo', 1, 20000.00, 20000.00, NULL),
	(5, 2, 5, 'Combo Familiar', 1, 55000.00, 55000.00, NULL),
	(6, 3, 3, 'Carne asada', 1, 30000.00, 30000.00, NULL),
	(7, 3, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(8, 3, 6, 'Papas a la francesa', 1, 9000.00, 9000.00, NULL),
	(9, 4, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(10, 4, 6, 'Papas a la francesa', 1, 9000.00, 9000.00, NULL),
	(11, 4, 1, 'Pollo asado entero', 1, 42000.00, 42000.00, NULL),
	(12, 5, 9, 'Pollo BBQ', 1, 25000.00, 25000.00, NULL),
	(13, 5, 6, 'Papas a la francesa', 1, 9000.00, 9000.00, NULL),
	(14, 5, 4, 'Costillas BBQ', 1, 34000.00, 34000.00, NULL),
	(15, 6, 3, 'Carne asada', 1, 30000.00, 30000.00, NULL),
	(16, 6, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(17, 6, 6, 'Papas a la francesa', 1, 9000.00, 9000.00, NULL),
	(18, 6, 4, 'Costillas BBQ', 1, 34000.00, 34000.00, NULL),
	(19, 7, 3, 'Carne asada', 1, 30000.00, 30000.00, NULL),
	(20, 7, 8, 'Carne sudada GULASH', 1, 30000.00, 30000.00, NULL),
	(21, 7, 5, 'Combo familiar', 1, 62000.00, 62000.00, NULL),
	(22, 7, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(23, 8, 4, 'Costillas BBQ', 1, 34000.00, 34000.00, NULL),
	(24, 8, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(25, 9, 3, 'Carne asada', 1, 30000.00, 30000.00, NULL),
	(26, 9, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(27, 9, 6, 'Papas a la francesa', 1, 9000.00, 9000.00, NULL),
	(28, 9, 9, 'Pollo BBQ', 1, 25000.00, 25000.00, NULL),
	(29, 10, 4, 'Costillas BBQ', 1, 34000.00, 34000.00, NULL),
	(30, 10, 9, 'Pollo BBQ', 1, 25000.00, 25000.00, NULL),
	(31, 10, 7, 'Gaseosa personal', 1, 5000.00, 5000.00, NULL),
	(32, 11, 3, 'Carne asada', 1, 30000.00, 30000.00, NULL),
	(33, 11, 9, 'Pollo BBQ', 1, 25000.00, 25000.00, NULL),
	(34, 12, 9, 'Pollo BBQ', 1, 25000.00, 25000.00, NULL),
	(35, 13, 9, 'Pollo BBQ', 1, 25000.00, 25000.00, NULL),
	(36, 13, 6, 'Papas a la francesa', 1, 9000.00, 9000.00, NULL),
	(37, 13, 1, 'Pollo asado entero', 1, 42000.00, 42000.00, NULL),
	(38, 14, 5, 'Combo familiar', 1, 62000.00, 62000.00, NULL),
	(39, 15, 4, 'Costillas BBQ', 1, 34000.00, 34000.00, NULL),
	(40, 15, 3, 'Carne asada', 1, 30000.00, 30000.00, NULL),
	(41, 15, 8, 'Carne sudada GULASH', 1, 30000.00, 30000.00, NULL);

-- Volcando estructura para tabla asadero_el_carbon.productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` int unsigned NOT NULL,
  `nombre` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `imagen_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `popular` tinyint(1) NOT NULL DEFAULT '0',
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_producto_categoria` (`categoria_id`),
  KEY `idx_producto_disponible` (`disponible`),
  KEY `idx_producto_popular` (`popular`),
  CONSTRAINT `fk_producto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `chk_producto_precio` CHECK ((`precio` >= 0))
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.productos: ~8 rows (aproximadamente)
INSERT INTO `productos` (`id`, `categoria_id`, `nombre`, `descripcion`, `imagen_url`, `precio`, `popular`, `disponible`, `creado_en`, `actualizado_en`) VALUES
	(1, 2, 'Pollo asado entero', 'Pollo asado al carbon con acompanamientos', 'https://images.unsplash.com/photo-1598515214211-89d3c73ae83b?w=600&q=80', 42000.00, 1, 0, '2026-04-29 11:34:58', '2026-05-07 08:29:42'),
	(3, 1, 'Carne asada', 'Porcion de carne de res asada al carbon', 'https://images.unsplash.com/photo-1544025162-d76694265947?w=600&q=80', 30000.00, 1, 1, '2026-04-29 11:34:58', '2026-05-05 10:28:58'),
	(4, 3, 'Costillas BBQ', 'Costillas de cerdo en salsa BBQ', 'https://images.unsplash.com/photo-1529193591184-b1d58069ecdd?w=600&q=80', 34000.00, 0, 1, '2026-04-29 11:34:58', '2026-05-05 10:28:58'),
	(5, 4, 'Combo familiar', 'Pollo entero, papas, arepas y bebida familiar', 'https://images.unsplash.com/photo-1562967914-608f82629710?w=600&q=80', 62000.00, 1, 1, '2026-04-29 11:34:58', '2026-05-05 10:28:58'),
	(6, 5, 'Papas a la francesa', 'Porcion de papas crocantes', 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?w=600&q=80', 9000.00, 0, 1, '2026-04-29 11:34:58', '2026-05-05 10:28:58'),
	(7, 6, 'Gaseosa personal', 'Bebida gaseosa personal', 'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=600&q=80', 5000.00, 0, 1, '2026-04-29 11:34:58', '2026-05-05 10:28:58'),
	(8, 3, 'Carne sudada GULASH', 'Porcion de carne en gulash, deliciosa para chuparse los dedos', NULL, 30000.00, 1, 1, '2026-04-29 14:25:11', '2026-05-07 08:32:15'),
	(9, 2, 'Pollo BBQ', 'Delicioso pollo a la BBQ', NULL, 25000.00, 0, 1, '2026-04-29 19:38:17', '2026-04-29 19:38:17');

-- Volcando estructura para tabla asadero_el_carbon.producto_agotamientos
CREATE TABLE IF NOT EXISTS `producto_agotamientos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` int unsigned NOT NULL,
  `reportado_por` int unsigned NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reportado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_agotado_producto` (`producto_id`),
  KEY `fk_agotado_usuario` (`reportado_por`),
  CONSTRAINT `fk_agotado_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_agotado_usuario` FOREIGN KEY (`reportado_por`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.producto_agotamientos: ~2 rows (aproximadamente)
INSERT INTO `producto_agotamientos` (`id`, `producto_id`, `reportado_por`, `motivo`, `reportado_en`) VALUES
	(1, 8, 2, 'Reportado por cocina', '2026-05-07 08:29:34'),
	(2, 1, 2, 'Reportado por cocina', '2026-05-07 08:29:42');

-- Volcando estructura para tabla asadero_el_carbon.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.roles: ~3 rows (aproximadamente)
INSERT INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
	(1, 'Administrador', 'Acceso completo al panel de administracion'),
	(2, 'Cocina', 'Acceso al panel de comandas y cambio de estado de pedidos'),
	(3, 'Cliente', 'Acceso al catalogo y pedidos propios');

-- Volcando estructura para tabla asadero_el_carbon.tokens_recuperacion
CREATE TABLE IF NOT EXISTS `tokens_recuperacion` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `cliente_id` int unsigned DEFAULT NULL,
  `usuario_id` int unsigned DEFAULT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expira_en` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT '0',
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `fk_token_cliente` (`cliente_id`),
  KEY `fk_token_usuario` (`usuario_id`),
  CONSTRAINT `fk_token_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_token_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_token_dueno` CHECK ((((`cliente_id` is not null) and (`usuario_id` is null)) or ((`cliente_id` is null) and (`usuario_id` is not null))))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.tokens_recuperacion: ~0 rows (aproximadamente)

-- Volcando estructura para tabla asadero_el_carbon.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `correo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol_id` tinyint unsigned NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_acceso` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  KEY `fk_usuarios_rol` (`rol_id`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Volcando datos para la tabla asadero_el_carbon.usuarios: ~3 rows (aproximadamente)
INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `telefono`, `password_hash`, `rol_id`, `activo`, `ultimo_acceso`, `creado_en`, `actualizado_en`) VALUES
	(1, 'Administrador', 'admin@asaderoelcarbon.test', '3000000000', '$2y$10$6czp2jMefVh7kPX/sNiVSuPK58MLyqaSnG88lj2gXamFOBAjZKV4m', 1, 1, NULL, '2026-04-29 11:34:58', '2026-04-29 11:34:58'),
	(2, 'Cocina Principal', 'cocina@gmail.com', '3000000001', '$2y$10$gsauRFv3waI/3hYcs6z2hOTAuTfFWCbjAeOImRBftF5h1Wu530sSG', 2, 1, NULL, '2026-04-29 11:34:58', '2026-04-29 13:44:17'),
	(3, 'Andres Millan', 'millanalgecira@gmail.com', '', '$2y$10$DD.mxcnH6xLAyJduL3zhVefG.EWe342o8DVa59J12Zq5bnQNiF/T2', 1, 1, NULL, '2026-04-29 11:56:30', '2026-04-29 19:16:07');

-- Volcando estructura para vista asadero_el_carbon.vw_catalogo_productos
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `vw_catalogo_productos` (
	`categoria_id` INT(10) UNSIGNED NOT NULL,
	`categoria` VARCHAR(80) NOT NULL COLLATE 'utf8mb4_unicode_ci',
	`producto_id` INT(10) UNSIGNED NULL,
	`producto` VARCHAR(120) NULL COLLATE 'utf8mb4_unicode_ci',
	`descripcion` TEXT NULL COLLATE 'utf8mb4_unicode_ci',
	`precio` DECIMAL(10,2) NULL,
	`popular` TINYINT(1) NULL,
	`disponible` TINYINT(1) NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista asadero_el_carbon.vw_resumen_ventas_diarias
-- Creando tabla temporal para superar errores de dependencia de VIEW
CREATE TABLE `vw_resumen_ventas_diarias` (
	`fecha` DATE NULL,
	`pedidos` BIGINT(19) NOT NULL,
	`total_vendido` DECIMAL(32,2) NOT NULL
) ENGINE=MyISAM;

-- Volcando estructura para vista asadero_el_carbon.vw_catalogo_productos
-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `vw_catalogo_productos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_catalogo_productos` AS select `c`.`id` AS `categoria_id`,`c`.`nombre` AS `categoria`,`p`.`id` AS `producto_id`,`p`.`nombre` AS `producto`,`p`.`descripcion` AS `descripcion`,`p`.`precio` AS `precio`,`p`.`popular` AS `popular`,`p`.`disponible` AS `disponible` from (`categorias` `c` left join `productos` `p` on((`p`.`categoria_id` = `c`.`id`))) where (`c`.`activa` = 1) order by `c`.`orden`,`c`.`nombre`,`p`.`nombre`;

-- Volcando estructura para vista asadero_el_carbon.vw_resumen_ventas_diarias
-- Eliminando tabla temporal y crear estructura final de VIEW
DROP TABLE IF EXISTS `vw_resumen_ventas_diarias`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_resumen_ventas_diarias` AS select cast(`p`.`creado_en` as date) AS `fecha`,count(distinct `p`.`id`) AS `pedidos`,coalesce(sum(`pg`.`total_pagado`),0) AS `total_vendido` from (`pedidos` `p` left join `pagos` `pg` on((`pg`.`pedido_id` = `p`.`id`))) where (`p`.`estado` in ('Pagado','Entregado')) group by cast(`p`.`creado_en` as date);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
