CREATE TABLE IF NOT EXISTS `llx_contab_sat_ctas` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL DEFAULT '1',
  `cta` varchar(24) NOT NULL DEFAULT '',
  `descta` varchar(200) NOT NULL DEFAULT '',
  `subctade` int(11) NOT NULL DEFAULT '0',
  `import_key` varchar(14) DEFAULT '',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
