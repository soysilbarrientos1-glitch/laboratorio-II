-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         9.1.0 - MySQL Community Server - GPL
-- SO del servidor:              Win64
-- HeidiSQL Versión:             12.11.0.7065
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Volcando estructura de base de datos para semis_marie
CREATE DATABASE IF NOT EXISTS `semis_marie` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `semis_marie`;

-- Volcando estructura para tabla semis_marie.citas
CREATE TABLE IF NOT EXISTS `citas` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_servicio` int NOT NULL,
  `id_empleado` int NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
  PRIMARY KEY (`id_cita`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_servicio` (`id_servicio`),
  KEY `id_empleado` (`id_empleado`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.citas: 7 rows
/*!40000 ALTER TABLE `citas` DISABLE KEYS */;
INSERT IGNORE INTO `citas` (`id_cita`, `id_cliente`, `id_servicio`, `id_empleado`, `fecha`, `hora`, `total`, `estado`) VALUES
	(1, 1, 1, 1, '2025-10-26', '10:00:00', 25000.00, 'confirmada'),
	(2, 1, 1, 2, '2025-10-26', '10:00:00', 25000.00, 'confirmada'),
	(3, 8, 3, 4, '2025-10-27', '16:00:00', 50000.00, 'confirmada'),
	(4, 8, 3, 5, '2025-10-28', '11:00:00', 50000.00, ''),
	(5, 8, 3, 5, '2025-10-28', '10:00:00', 50000.00, ''),
	(6, 15, 6, 4, '2025-10-29', '16:00:00', 10000.00, ''),
	(7, 16, 3, 5, '2025-10-29', '15:00:00', 50000.00, '');
/*!40000 ALTER TABLE `citas` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.empleados
CREATE TABLE IF NOT EXISTS `empleados` (
  `id_empleado` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `especialidad` enum('manicura','pedicura','ambas') NOT NULL,
  PRIMARY KEY (`id_empleado`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.empleados: 0 rows
/*!40000 ALTER TABLE `empleados` DISABLE KEYS */;
/*!40000 ALTER TABLE `empleados` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.horarios_disponibles
CREATE TABLE IF NOT EXISTS `horarios_disponibles` (
  `id_horario` int NOT NULL AUTO_INCREMENT,
  `id_especialista` int NOT NULL,
  `dia_semana` tinyint NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  PRIMARY KEY (`id_horario`),
  KEY `id_especialista` (`id_especialista`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.horarios_disponibles: 0 rows
/*!40000 ALTER TABLE `horarios_disponibles` DISABLE KEYS */;
/*!40000 ALTER TABLE `horarios_disponibles` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.notificaciones
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id_notificacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `mensaje` text NOT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_cita` int DEFAULT NULL,
  PRIMARY KEY (`id_notificacion`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_cita` (`id_cita`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.notificaciones: 0 rows
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.pagos
CREATE TABLE IF NOT EXISTS `pagos` (
  `id_pago` int NOT NULL AUTO_INCREMENT,
  `id_cita` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','tarjeta','transferencia','otro') NOT NULL,
  `estado_pago` enum('pendiente','completado','fallido') DEFAULT 'pendiente',
  `fecha_pago` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  KEY `id_cita` (`id_cita`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.pagos: 0 rows
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.rol
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre_rol` (`nombre_rol`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.rol: 5 rows
/*!40000 ALTER TABLE `rol` DISABLE KEYS */;
INSERT IGNORE INTO `rol` (`id_rol`, `nombre_rol`) VALUES
	(1, 'cliente'),
	(2, 'manicurista'),
	(3, 'administrador'),
	(4, 'pedicurista'),
	(5, 'secretaria');
/*!40000 ALTER TABLE `rol` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.servicios
CREATE TABLE IF NOT EXISTS `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `duracion_minutos` int NOT NULL,
  `tipo` enum('manicura','pedicura','combo') NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_servicio`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.servicios: 6 rows
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT IGNORE INTO `servicios` (`id_servicio`, `nombre`, `descripcion`, `precio`, `duracion_minutos`, `tipo`, `imagen_url`) VALUES
	(1, 'Manicura Básica', 'Limpieza, corte, limado y esmalte', 25000.00, 45, 'manicura', 'Imagenes/optimizadas/manos.jpg'),
	(2, 'Pedicura Completa', 'Exfoliación, corte, limado y esmalte', 35000.00, 60, 'pedicura', 'Imagenes/optimizadas/pies.jpg'),
	(3, 'Combo Manos y Pies', 'Manicura y pedicura en una sola cita', 50000.00, 90, 'combo', 'Imagenes/optimizadas/combos.jpg'),
	(4, 'Soft Gel', 'Refuerzo con gel flexible y natural. Ideal para uñas débiles.', 35000.00, 90, 'manicura', 'Imagenes/optimizadas/softgel.jpg'),
	(5, 'Esculpidas', 'Uñas esculpidas con acrílico para mayor durabilidad y diseño personalizado.', 45000.00, 120, 'manicura', 'Imagenes/optimizadas/esculpidas.jpg'),
	(6, 'Remoción', 'Retiro seguro y sin daño del esmaltado semipermanente o gel.', 10000.00, 30, 'manicura', 'Imagenes/optimizadas/remocion.jpg');
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;

-- Volcando estructura para tabla semis_marie.usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `id_rol` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Volcando datos para la tabla semis_marie.usuarios: 13 rows
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT IGNORE INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `telefono`, `password`, `id_rol`, `activo`, `fecha_registro`) VALUES
	(4, 'Mariel', 'Barrientos', 'mariel@salon.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, '2025-10-25 17:24:13'),
	(5, 'Gabriela', 'Baez', 'gabriela@salon.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2, 1, '2025-10-25 17:24:13'),
	(6, 'Marina', 'Refojos', 'marina@salon.com', '1122334455', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 1, '2025-10-25 17:24:13'),
	(7, 'Valeria', 'Díaz', 'valeria@salon.com', '5544332211', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4, 1, '2025-10-25 17:24:13'),
	(8, 'Marta Nuñez', '', 'marta43@gmail.com', '3794243414', '$2y$10$swegUvYYv9uY7zUWE/k75OVa3cL2WJzq42uJyYnz.5mp3z6ab5D6y', 1, 1, '2025-10-25 17:43:35'),
	(9, 'Silvia Sodana', '', 'silsol@gmail.com', '3795674532', '$2y$10$lqxDIDRrddzO8uuo0/w17OluJT39WGo.u8/ZiGj3V7Y2luAybK/2a', 1, 1, '2025-10-25 17:46:04'),
	(10, 'Pía Salvia', '', 'pisal@gmail.com', '3796780987', '$2y$10$C4SyxzR3S/PE6DLeL5o3XOLAUwSWfeeJTQHNRkqmOPdziCv1Xj/SS', 1, 1, '2025-10-25 18:22:42'),
	(11, 'Cristina Sena', '', 'cris78@gmail.com', '3777543432', '$2y$10$jBYWEJYzM1b0RYwN5Ie69uwohAb7kZB6YxKrRYhrDCPFDSEYetycC', 1, 1, '2025-10-25 18:24:16'),
	(12, 'Laura Machuca', '', 'lau12@gmail.com', '3777980767', '$2y$10$5aIzhxAQAYUQ.UxSZ4.ejePsw5SCFcrlhIA6LI3/KNCOqwJjmpFr6', 1, 1, '2025-10-25 18:39:56'),
	(13, 'Mercedes Gill', '', 'merce@gmail.com', '3777098765', '$2y$10$oCLe9Hcrm8C15ZDiOyZxTuexvYXm.bs02YcCQKhsefqSYLrpANfl2', 2, 1, '2025-10-26 23:32:04'),
	(14, 'Daniela Hermil', '', 'dani@gmail.com', '3777654323', '$2y$10$r6mYXBhhuAnXdepYpgqIwevJpQA12HgAkaJbLNVcyJWhUExzELsWm', 2, 1, '2025-10-26 23:42:44'),
	(15, 'Noelia Ávalos', '', 'noelia@gmail.com', '3795643897', '$2y$10$NOgUh4JpLYtsWOiIbIUGqeOHnnLS7bAZ5nFQrt4iHqtqtaCh.a58C', 1, 1, '2025-10-27 00:00:34'),
	(16, 'María López', '', 'mari@gmail.com', '3777654653', '$2y$10$BSH0PgIBahnHeMnGFIlW7.Jvo.2IFg2T0wyZlg5tqw18Wzx6k6XNC', 1, 1, '2025-10-27 03:46:51');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
