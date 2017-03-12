CREATE TABLE IF NOT EXISTS `llx_contab_cuentas_rel` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `fk_type` int(11) NOT NULL,
  `fk_object` int(11) NOT NULL,
  `fk_cuenta` int(11) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
