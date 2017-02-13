CREATE TABLE IF NOT EXISTS `llx_antecedentes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(250) NOT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `descripcion` text,
  `fk_user_create` int(11) DEFAULT NULL,
  `fk_user_update` int(11) DEFAULT NULL,
  `fk_pacientes` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
