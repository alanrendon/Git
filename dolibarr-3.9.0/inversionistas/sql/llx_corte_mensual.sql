CREATE TABLE IF NOT EXISTS `llx_corte_mensual` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `mes` int(11) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  `fk_cuenta` int(11) DEFAULT NULL,
  `fk_factura` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
