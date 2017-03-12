CREATE TABLE IF NOT EXISTS `llx_contab_grupos` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `grupo` varchar(50) NOT NULL,
  `fk_codagr_rel` int(11) NOT NULL,
  `fk_codagr_ini` int(11) NOT NULL,
  `fk_codagr_fin` int(11) NOT NULL,
  `fk_grupo` int(5) DEFAULT NULL,
  `tipo_edo_financiero` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
