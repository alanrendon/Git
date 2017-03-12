
CREATE TABLE IF NOT EXISTS `llx_contab_cat_ctas` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) DEFAULT NULL,
  `nivel` tinyint(4) NOT NULL DEFAULT '0',
  `codagr` varchar(24) DEFAULT '',
  `descripcion` varchar(200) NOT NULL,
  `natur` char(1) NOT NULL DEFAULT '',
  `codsat` varchar(24) DEFAULT '0',
  `afectacion` int(2) DEFAULT NULL,
  `import_key` varchar(14) DEFAULT '',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

