CREATE TABLE IF NOT EXISTS  `llx_factory_proccess` (

  `rowid` int(11) NOT NULL AUTO_INCREMENT,

  `fk_propal` int(11) DEFAULT NULL,

  `fk_product` int(11) DEFAULT NULL,

  `fk_operator` int(11) DEFAULT NULL,

  `dateStart` datetime DEFAULT NULL,

  `dateEnd` datetime DEFAULT NULL,

  `status` int(11) DEFAULT NULL,

  `hours` int(11) DEFAULT NULL,

  `minutes` int(11) DEFAULT NULL,

  PRIMARY KEY (`rowid`)

) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;

