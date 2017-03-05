CREATE TABLE IF NOT EXISTS `llx_facturas_consulta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `date_creation` datetime NOT NULL,
  `date_remove` datetime DEFAULT NULL,
  `fk_user_created` int(11) DEFAULT NULL,
  `fk_user_remove` int(11) DEFAULT NULL,
  `fk_consulta` int(11) DEFAULT NULL,
  `fk_factura` int(11) DEFAULT NULL,
  `statut` tinyint(4) DEFAULT NULL,
  `entity` int(11) DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;