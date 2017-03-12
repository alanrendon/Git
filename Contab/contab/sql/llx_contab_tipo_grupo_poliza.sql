
CREATE TABLE IF NOT EXISTS `llx_contab_tipo_grupo_poliza` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `abr` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tipo_grupo` (`nombre`),
  UNIQUE KEY `abr_nombre_grupo` (`abr`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;



