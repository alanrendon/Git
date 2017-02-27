CREATE TABLE IF NOT EXISTS `llx_contab_inpc` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `enero` decimal(10,4) DEFAULT '0.0000',
  `febrero` decimal(10,4) DEFAULT '0.0000',
  `marzo` decimal(10,4) DEFAULT NULL,
  `abril` decimal(10,4) DEFAULT NULL,
  `mayo` decimal(10,4) DEFAULT '0.0000',
  `junio` decimal(10,4) DEFAULT '0.0000',
  `julio` decimal(10,4) DEFAULT '0.0000',
  `agosto` decimal(10,4) DEFAULT '0.0000',
  `septiembre` decimal(10,4) DEFAULT '0.0000',
  `octubre` decimal(10,4) DEFAULT '0.0000',
  `noviembre` decimal(10,4) DEFAULT NULL,
  `diciembre` decimal(10,4) DEFAULT '0.0000',
  PRIMARY KEY (`rowid`),
  UNIQUE KEY `anio` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
