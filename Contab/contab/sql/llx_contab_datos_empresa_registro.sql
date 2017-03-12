CREATE TABLE IF NOT EXISTS `llx_contab_datos_empresa_registro` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `empresa` varchar(200) NOT NULL,
  `pais` varchar(50) NOT NULL,
  `ciudad` varchar(100) NOT NULL,
  `cp` int(11) NOT NULL,
  `tel` varchar(10) NOT NULL,
  `direccion` text NOT NULL,
  `fk_datos_contacto_paso2` int(11) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
