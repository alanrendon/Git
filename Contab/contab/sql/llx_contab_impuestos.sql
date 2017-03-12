CREATE TABLE IF NOT EXISTS `llx_contab_impuestos` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(25) NOT NULL,
  `impuesto` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `impuesto_nombre` (`nombre`,`impuesto`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
