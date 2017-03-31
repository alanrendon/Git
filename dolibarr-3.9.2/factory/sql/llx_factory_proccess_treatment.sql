
CREATE TABLE IF NOT EXISTS `llx_factory_proccess_treatment` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_factory_proccess` int(11) DEFAULT NULL,
  `fk_operator` int(11) DEFAULT NULL,
  `dateStart` datetime DEFAULT NULL,
  `dateEnd` datetime DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `minutes` int(11) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
