
CREATE TABLE IF NOT EXISTS `llx_contab_datos_contacto_registro` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nombres` varchar(100) NOT NULL,
  `apelidos` varchar(125) NOT NULL,
  `email` varchar(80) NOT NULL,
  `tel` varchar(10) NOT NULL,
  `fk_dominio_Paso1` int(11) NOT NULL,
  PRIMARY KEY (`rowid`),
  KEY `fk_dominio_Paso1` (`fk_dominio_Paso1`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

