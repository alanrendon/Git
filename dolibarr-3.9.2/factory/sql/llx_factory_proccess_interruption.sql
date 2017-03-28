CREATE TABLE IF NOT EXISTS `llx_factory_proccess_interruption` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_proccess` int(11) DEFAULT NULL,
  `dateIni` datetime DEFAULT NULL,
  `dateEnd` datetime DEFAULT NULL,
  `comment` longtext,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
