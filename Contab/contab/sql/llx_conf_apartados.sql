CREATE TABLE IF NOT EXISTS `llx_conf_apartados` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `apartado` varchar(255) NOT NULL,
  `tipo` int(2) NOT NULL,
  `reporte` int(2) NOT NULL,
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `apartado_conf` (`apartado`,`tipo`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;
