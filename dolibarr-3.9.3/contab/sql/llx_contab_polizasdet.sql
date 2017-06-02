CREATE TABLE IF NOT EXISTS `llx_contab_polizasdet` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_poliza` int(11) NOT NULL DEFAULT '0',
  `asiento` int(11) NOT NULL,
  `cuenta` varchar(50) NOT NULL,
  `debe` double NOT NULL,
  `haber` double NOT NULL,
  `fk_proveedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`),
  KEY `idx` (`cuenta`)  
);

ALTER TABLE `llx_contab_polizasdet` ADD `iva` tinyint(4);
