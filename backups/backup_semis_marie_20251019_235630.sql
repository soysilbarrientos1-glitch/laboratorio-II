-- Backup generado por scripts/convert-to-innodb.php
-- Fecha: 2025-10-19 23:56:30

-- Estructura de la tabla citas
DROP TABLE IF EXISTS `citas`;
CREATE TABLE `citas` (
  `id_cita` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NOT NULL,
  `id_servicio` int NOT NULL,
  `id_empleado` int NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `total` decimal(20,6) NOT NULL DEFAULT (0),
  `estado` enum('pendiente','confirmada','completada','cancelada') DEFAULT 'pendiente',
  PRIMARY KEY (`id_cita`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_servicio` (`id_servicio`),
  KEY `id_empleado_pedicura` (`id_empleado`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos para citas
INSERT INTO `citas` (`id_cita`,`id_cliente`,`id_servicio`,`id_empleado`,`fecha`,`hora`,`total`,`estado`) VALUES ('1','17','11','4','2025-10-21','12:00:00','5000.000000','');
INSERT INTO `citas` (`id_cita`,`id_cliente`,`id_servicio`,`id_empleado`,`fecha`,`hora`,`total`,`estado`) VALUES ('2','17','9','4','2025-10-21','14:00:00','20000.000000','');
INSERT INTO `citas` (`id_cita`,`id_cliente`,`id_servicio`,`id_empleado`,`fecha`,`hora`,`total`,`estado`) VALUES ('3','17','9','4','2025-10-21','15:00:00','20000.000000','');
INSERT INTO `citas` (`id_cita`,`id_cliente`,`id_servicio`,`id_empleado`,`fecha`,`hora`,`total`,`estado`) VALUES ('4','17','9','4','2025-10-21','16:00:00','20000.000000','');

-- Estructura de la tabla notificaciones
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id_notificacion` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `mensaje` text NOT NULL,
  `leida` tinyint(1) DEFAULT '0',
  `tipo` enum('cita','pago','promocion','sistema') DEFAULT 'cita',
  `fecha_envio` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_notificacion`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos para notificaciones
INSERT INTO `notificaciones` (`id_notificacion`,`id_usuario`,`titulo`,`mensaje`,`leida`,`tipo`,`fecha_envio`) VALUES ('1','4','','Nueva cita asignada: Lola Beltrán el 2025-10-21 a las 14:00','0','cita','2025-10-19 20:03:58');
INSERT INTO `notificaciones` (`id_notificacion`,`id_usuario`,`titulo`,`mensaje`,`leida`,`tipo`,`fecha_envio`) VALUES ('2','4','','Nueva cita asignada: Lola Beltrán el 2025-10-21 a las 15:00','0','cita','2025-10-19 20:36:53');
INSERT INTO `notificaciones` (`id_notificacion`,`id_usuario`,`titulo`,`mensaje`,`leida`,`tipo`,`fecha_envio`) VALUES ('3','4','','Nueva cita asignada: Lola Beltrán el 2025-10-21 a las 16:00','0','cita','2025-10-19 20:45:40');

-- Estructura de la tabla servicios
DROP TABLE IF EXISTS `servicios`;
CREATE TABLE `servicios` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `duracion_minutos` int DEFAULT '30',
  `tipo` enum('manicura','pedicura','combinado') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `imagen_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_servicio`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos para servicios
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('1','Manicura Combinada','La manicura combinada es una técnica de preparación de uñas que integra el uso de un torno eléctrico con fresas y herramientas manuales para limpiar y dar forma a la cutícula, logrando un acabado impecable y duradero, ideal para la posterior aplicación de esmaltes o sistemas artificiales. Su principal beneficio es la limpieza profunda de la cutícula, lo que permite aplicar el esmalte desde la raíz, generando un aspecto más profesional y extendiendo su durabilidad. ','10000.00','30','manicura','/Imagenes/manicura-combinada.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('2','Esmaltado Semipermanente','El esmaltado semipermanente es un tipo de manicura que utiliza esmaltes de gel que, tras su aplicación, se secan y endurecen bajo una lámpara UV o LED, lo que proporciona un acabado brillante, resistente y duradero que puede durar varias semanas (aproximadamente 2-3).','10000.00','30','manicura','/Imagenes/esmalte-semi.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('3','Capping Gel','El Capping gel es una técnica para fortalecer y proteger las uñas naturales, aplicando una capa fina de gel sobre la superficie de la uña sin añadir largo. Esta capa actúa como una barrera protectora que brinda resistencia y grosor a las uñas débiles, quebradizas o escamadas, acompañando su crecimiento natural. ','5000.00','30','manicura','/Imagenes/capping-gel.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('4','Capping Polygel','El Capping de polygel es una técnica que aplica una capa delgada de polygel sobre la uña natural para fortalecerla y protegerla, sin añadirle largo. Es una opción para uñas quebradizas o para quienes desean una base resistente, lo que ayuda a que la uña natural crezca más fuerte. El proceso incluye preparar la uña, aplicar y moldear una pequeña perla de polygel, curarla en lámpara y luego limarla y esmaltarla si se desea. ','10000.00','50','manicura','/Imagenes/poly-gel.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('5','Soft Gel','Las uñas soft gel son extensiones hechas de un gel suave y flexible que se adhieren a la uña natural mediante un gel especial y se curan con una lámpara LED. Este método es una alternativa más rápida y natural a las uñas acrílicas o de gel duro, permitiendo obtener una manicura de mayor longitud de forma rápida, con una duración de hasta 2 a 4 semanas si se realiza correctamente. ','25000.00','80','manicura','/Imagenes/personalizado.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('6','Esculpidas en Polygel','Las uñas esculpidas en polygel son extensiones de uñas creadas con un material híbrido de gel y acrílico, que ofrece una mayor flexibilidad y resistencia, resultando en un acabado natural y duradero. El proceso implica la preparación de la uña natural, la aplicación del polygel en un molde para darle forma, el curado en una lámpara UV/LED y un posterior limado para perfeccionar la estructura. Este método destaca por su menor olor en comparación con el acrílico y permite un mayor tiempo de trabajo para moldear el producto antes del curado. ','30000.00','120','manicura','/Imagenes/esculpidas.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('7','Belleza de Pies','La \"belleza de pies\" se refiere a un tratamiento estético centrado en el embellecimiento y cuidado de pies sanos, que incluye el corte y limado de uñas, la limpieza y suavizado de cutículas, la exfoliación y el esmaltado de las uñas para que los pies luzcan delicados y cuidados. A diferencia de la pedicura, que también tiene un componente terapéutico para tratar afecciones, la belleza de pies es un servicio meramente estético. ','20000.00','60','pedicura','/Imagenes/pie1.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('8','Spa de Pies','Un spa de pies es un tratamiento completo de pedicura que combina beneficios estéticos y terapéuticos para la salud y relajación de los pies y las piernas. Va más allá de un arreglo de uñas tradicional e incluye baños con sales y aceites, exfoliación de la piel, limado de durezas, aplicación de mascarillas hidratantes y un masaje para activar la circulación sanguínea. ','30000.00','80','pedicura','/Imagenes/pie1.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('9','Combos Manos y Pies','Esmaltado Semipermanente en Manos y Pies, un solo tono.','20000.00','60','','/Imagenes/combo.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('10','Remoción Manos','Para retirar el esmaltado de uñas, se lima la capa superior brillante del esmalte para que el producto penetre mejor, luego se empapa algodón con removedor (preferiblemente con acetona), colocándolo sobre la uña y envolviéndola con papel aluminio. Se deja actuar 15 minutos, luego se retira los restos de esmalte con un palillo o empujador de cutículas. Finalmente, se lava tus manos e hidratan las uñas y cutículas para evitar que se sequen. ','8000.00','25','manicura','/Imagenes/polimanos.jpg');
INSERT INTO `servicios` (`id_servicio`,`nombre`,`descripcion`,`precio`,`duracion_minutos`,`tipo`,`imagen_url`) VALUES ('11','Remoción Pies','Para quitar el esmaltado permanente de los pies, se lima la capa brillante de la uña, se empapa un algodón con acetona pura y se coloca sobre la uña cubriéndolo con papel de aluminio; déjalo actuar 15 minutos para que el esmalte se ablande y luego retira los restos con un empujador de cutículas o palillo, terminando con una limpieza y cuidado de la uña. \r\n','5000.00','25','pedicura','/Imagenes/pie1.jpg');

-- Estructura de la tabla usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_rol` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Datos para usuarios
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('1','Betiana','Fernández','bety32@gmail.com','3794567890','$2y$10$I3/KXSbASUVTaq9tayuo7.oIwM6DBUaXxFZsrw/oCwAiMUkrjW76C','0','1','2025-10-12 23:05:36');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('3','Guadalupe','Díaz','lupita@gmail.com','3794243414','$2y$10$xtmEa/YTZMNUJVgpJuW9k.SMDaVN3O2iy8fp42796Y3XRhf8hLbgS','2','1','2025-10-12 23:49:56');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('4','Mariel','Barrientos','mariel@semis.com','11111111','$2y$10$B4rsRDtUubym/2Jq76anW.Xs0zwUPWhctx0wsljO7GbzRuSFiNrJK','4','1','2025-10-13 01:14:18');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('5','Andrea','Catalano','andrea@semis.com','22222222','$2y$10$Tu12wpbc0QT6jlXc7XYPrO2eGe04SgMiPFBLm5oeS4jXNLVgCVDVa','5','1','2025-10-13 01:14:19');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('6','Sara','Ganes','sarita34@htmail.com','3795678798','$2y$10$0ttdiq2ts1FKWUs8CtMJtOj8zSRLynPSflUI7BD8EfpWqAbtscS4e','2','1','2025-10-13 19:30:17');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('7','Florentina','Acosta','florbea93@gmail.com','3794588329','$2y$10$y1a.4oQsEoOGzfTWBo0Z6uECEnofYxB1pg2LVSjnK1JnkKVlrr50S','2','1','2025-10-13 21:46:20');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('8','Susana','Sosa','susu@gmail.com','3795467898','$2y$10$vbXMEfEUf3jCMP4zr2lpOeiQykVIxSSvuKuvlcQ1yOGwY0cwEQ8sO','2','1','2025-10-13 22:07:53');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('9','Daniela','fernández','dani12@gmail.com','34895678','$2y$10$oizDeyfBk5wyGHrjmDMZtuvR8MBIkS8XAlkwZF8/bj/ihGBMB43dO','2','1','2025-10-15 20:35:53');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('10','silvia','','sil1234@hotmail.com','3777345623','$2y$10$UnhFZ.kuHQ7TrfWDReM.oOkjQS51vZmd3lTyI4OcQjCYGDVuJu/m.','2','1','2025-10-19 01:19:33');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('11','Paula Salvatore','','pau1234@gmail.com','3795673421','$2y$10$5OpC4KAW6zNBoPjY/U6CH.WdGRC5IRF61tV1pHCfOk.LPjl38DUpu','2','1','2025-10-19 01:36:50');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('12','Paula Salvatore','','pausal@hotmail.com','3794345426','$2y$10$rWv292LXU9fBlfTztPksaOF4gouyUsP/Et1h2BU3R2w5LJ.xPtKju','2','1','2025-10-19 01:38:33');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('13','Sofía Bolaños','','sofinar@gmail.com','3795670987','$2y$10$YLs1BPFVppLabRwPOnlpx.rVbcUDfSVWgM5G3SjY8TZcVWdLeUQRi','2','1','2025-10-19 01:45:29');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('14','Cristina Seniquel','','cris82@gamil.com','3795678765','$2y$10$o0JumguyVmxaO1.6nC.HQO5nFOSKft3E6cvtZleoieGRNjIDSsSWC','2','1','2025-10-19 02:58:19');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('15','Cristiana Surez','','crissu@gmail.com','3777986754','$2y$10$H9bWIUTU8/zksZ8OeooaPeOxc824EyxEMwa6/pPQ7BMDgTO0SZxgm','2','1','2025-10-19 03:00:36');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('16','Silvina Martínez','','silma34@gmail.com','3795378291','$2y$10$o824g.qiQaAANT8dmZnbtOEZ6kuj3XsKzysBAsFUQYMT1PYu6oYZK','2','1','2025-10-19 03:10:30');
INSERT INTO `usuarios` (`id_usuario`,`nombre`,`apellido`,`email`,`telefono`,`password`,`id_rol`,`activo`,`fecha_registro`) VALUES ('17','Lola Beltrán','','lolab@gmail.com','3795670934','$2y$10$HSQBSFOdbzY05JxvTb688ea8.TAXf7Wj8Pur3b/9FmHCB1xZiNtW2','2','1','2025-10-19 19:37:25');

