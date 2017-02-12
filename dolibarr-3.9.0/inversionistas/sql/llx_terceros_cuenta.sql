CREATE TABLE IF NOT EXISTS `llx_terceros_cuenta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_tercero` int(11) DEFAULT NULL,
  `fk_cuenta` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
