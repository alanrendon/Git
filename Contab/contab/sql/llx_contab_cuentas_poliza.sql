CREATE TABLE IF NOT EXISTS `llx_contab_cuentas_poliza` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_tipo` varchar(3) DEFAULT NULL,
  `codagr` varchar(24) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tipo_poliza_cuenta` (`fk_tipo`,`codagr`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
